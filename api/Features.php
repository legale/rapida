<?php

require_once('Simpla.php');

/**
 * Class Features
 */
class Features extends Simpla
{
    private $tokeep = array(
        'id',
        'gid',
        'category_id',
        'feature_id',
        'features',
        'brand_id',
        'product_id',
        'force_no_cache',
        'visible',
        'in_filter'
    );
    //тут будут хранится значения опций
    public $options;

    //тут будут хранится опции по id
    public $features;

    //тут будут хранится опции по имени транслитом
    public $ftrans;

    //тут будут хранится опции по имени альтернативные транслитом
    public $ftrans2;

    //тут будут хранится опции по группам
    public $fgroups;

    //тут будут хранится опции по категориям
    public $fcats;

    //тут будет массив свойств в фильтре
    public $in_filter;

    //тут будет массив свойств
    public $visible;


    //время жизни для кеша
    public $ttl = 2529000; //25209000 = 1 месяц время жизни кеша. по истечении времени, задания на обновления будут добавляться в очередь


    public function __construct()
    {
        $this->init_features();
    }

    /**
     * @param bool $reinit
     */
    public function init_features($reinit = false): void
    {
        dtimer::log(__METHOD__ . " start reinit: $reinit");
        if ($reinit === false) {
            //если свойства уже инициализированы
            if ($this->features !== null) {
                return;
            }
            if (function_exists('apcu_fetch')) {
                dtimer::log(__METHOD__ . " ACPU CACHE FEATURES READ ");
                if (apcu_exists($this->config->host . 'features')) {
                    $features = apcu_fetch($this->config->host . 'features');
                    dtimer::log(__METHOD__ . " ACPU CACHE FEATURES FETCHED ");
                    $this->features = &$features[0];
                    $this->fgroups = &$features[1];
                    $this->ftrans = &$features[2];
                    $this->ftrans2 = &$features[3];
                    $this->fcats = &$features[4];
                    $this->in_filter = &$features[5];
                    $this->visible = &$features[6];
                    dtimer::log(__METHOD__ . " ACPU CACHE FEATURES LOADED");
                    return;
                } else {
                    dtimer::log(__METHOD__ . " ACPU CACHE FEATURES NOT FOUND");
                }
            }
        }


        $features = $this->db3->getInd("id", "SELECT * FROM s_features ORDER BY pos");
        $fgroups = $this->db3->getInd("id", "SELECT *, NULL as features FROM s_options_groups ORDER BY pos");


        $in_filter = $visible = $ftrans = $ftrans2 = [];
        //добавим все свойства в свои группы, а также в массивы in_filter и visible
        foreach ($features as $fid => &$feature) {
            if ($feature["in_filter"]) {
                $in_filter[$feature["id"]] = $feature;
            }
            if ($feature["visible"]) {
                $visible[$feature["id"]] = $feature;
            }

            $fgroups[$feature["gid"]]["features"][$feature["id"]] = $feature;
            $ftrans[$feature["trans"]] = $feature;
            if ($feature["trans2"] !== "") {
                $ftrans2[$feature["trans2"]] = $feature;
            }
        }

        $fcats = [];
        $features_categories = $this->db3->getAll("SELECT * FROM s_categories_features");
        foreach ($features_categories as $row) {
            $fcats[(int)$row["category_id"]][$row["feature_id"]] = &$features[(int)$row["feature_id"]];
        }


        $this->features =  &$features;
        $this->fgroups = &$fgroups;
        $this->ftrans = &$ftrans;
        $this->ftrans2 = &$ftrans2;
        $this->fcats =  SplFixedArray::fromArray($fcats);
        $this->in_filter = &$in_filter;
        $this->visible = &$visible;

        if (function_exists('apcu_store')) {
            apcu_store($this->config->host . 'features',
                [
                    &$this->features,
                    &$this->fgroups,
                    &$this->ftrans,
                    &$this->ftrans2,
                    &$this->fcats,
                    &$this->in_filter,
                    &$this->visible,
                ], 14400);
            dtimer::log(__METHOD__ . " ACPU CACHE FEATURES STORED");
        }
        return;
    }


    public function id2trans($filter_features): ?array
    {
        //массив для результата
        $res = [];

        foreach ($filter_features as $fid => $vids) {
            $res[$this->features[$fid]["trans"]] = $this->db3->getIndCol("id", "SELECT id, trans FROM s_options_uniq WHERE id IN (?a)", $vids);
        }
        return $res;
    }

    public function trans2id($uri_features): ?array
    {
        //массив для результата
        $res = [];
        //тут получим имена транслитом и id для преобразования параметров заданных в адресной строке
        $ftrans = &$this->ftrans;
        $ftrans2 = &$this->ftrans2;
        foreach ($uri_features as $name => $vals) {
            //если такое свойство есть у нас в массивах с именами
            if (key_exists($name, $ftrans)) {
                $fid = $ftrans[$name]["id"];
            } else if (key_exists($name, $ftrans2)) {
                $fid = $ftrans2[$name]["id"];
            } else {
                return null;
            }

            //преобразуем значения опций в их id (vid)
            $parsed = $this->db3->parse("?a", $vals);
            $vids = $this->db3->getIndCol("id", "SELECT id, id as val FROM s_options_uniq WHERE trans IN (?p) OR trans2 in (?p)", $parsed, $parsed);
            if (count($vids) === count($vals)) {
                $res[$fid] = $vids;
            } else {
                return null;
            }
        }
        return $res;
    }

    /**
     * @param array $filter
     * @return array|bool
     */
    function get_features_ids($filter = array())
    {
        dtimer::log(__METHOD__ . ' start');
        //это вариант по умолчанию id=>val
        $col = isset($filter['return']['col']) ? $filter['return']['col'] : 'name';
        $key = isset($filter['return']['key']) ? $filter['return']['key'] : 'id';

        if (isset($this->features[$key . "_" . $col])) {
            dtimer::log(__METHOD__ . ' return class var');
            return $this->features[$key . "_" . $col];
        }

        $in_filter_filter = '';
        if (isset($filter['in_filter'])) {
            $in_filter_filter = $this->db->placehold('AND in_filter=?', intval($filter['in_filter']));
        }

        // Выбираем свойства
        $q = $this->db->placehold("SELECT * FROM __features WHERE 1 $in_filter_filter");
        $q = $this->db->query($q);
        if ($q === false) {
            return false;
        }
        $this->features[$key . "_" . $col] = $this->db->results_array($col, $key);
        dtimer::log(__METHOD__ . ' return');
        return $this->features[$key . "_" . $col];
    }

    /**
     * @param array $filter
     * @return mixed
     */
    function get_features($filter = array())
    {
        //сначала уберем из фильтра лишние параметры, которые не влияют на результат, но влияют на хэширование
        dtimer::log(__METHOD__ . " start filter: " . var_export($filter, true));
        $filter = array_intersect_key($filter, array_flip($this->tokeep));
        dtimer::log(__METHOD__ . " filtered filter: " . var_export($filter, true));


        if (!empty($filter['id'])) {
            $res = array_intersect_key($this->features, array_flip((array)$filter['id']));
        } else{
            $res = $this->features;
        }
        
        if (!empty($filter['category_id'])) {
            foreach ((array)$filter['category_id'] as $cid) {
                if (isset($this->fcats[$cid])) {
                    $res = $res + $this->fcats[$cid];
                }
            }
        }

        if (!empty($filter["gid"])) {
            foreach ((array)$filter["gid"] as $gid) {
                $res = array_intersect_key($res, (array)$this->fgroups[$gid]["features"]);
            }
        }

        if (!empty($filter["in_filter"])) {
            $res = array_intersect_key($res, (array)$this->in_filter);
        }

        if (!empty($filter["visible"])) {
            $res = array_intersect_key($res, (array)$this->visible);
        }

        dtimer::log(__METHOD__ . " return");
        return $res;

    }

    /**
     * @param $id
     * @param null $col
     * @return mixed
     */
    function get_feature($id)
    {
        dtimer::log(__METHOD__ . " start $id");
        // Выбираем свойство
        if ($id === (int)$id) {
            $filter = $this->db->placehold("AND id = $id");
        } else if (is_string($id)) {
            $trans = encode_param(translit_ya($id));
            $filter = $this->db->placehold("AND name = ? OR trans = ?", $id, $trans);
        } else {
            dtimer::log(__METHOD__ . " bad arg ", 1);
            return false;
        }

        $query = $this->db->placehold("SELECT * FROM __features WHERE 1 $filter LIMIT 1");
        $this->db->query($query);
        return $this->db->result_array();

    }

    /**
     * @param $id
     * @return mixed
     */
    function get_feature_categories($id)
    {
        dtimer::log(__METHOD__ . " start $id");
        $q = $this->db->placehold("SELECT cf.category_id as category_id FROM __categories_features cf
			WHERE cf.feature_id = ?", $id);
        $this->db->query($q);
        $res = $this->db->results_array('category_id');
        return $res;
    }

    /**
     * @return array
     */
    public function get_options_tree()
    {
        dtimer::log(__METHOD__ . " start");
        $groups = array();
        $groups[0] = array('id' => 0, 'name' => '', 'pos' => 0, 'options' => array());

        $groups = array_merge($groups, $this->get_options_groups());
        $opts = $this->get_features(array('visible' => true));
        if ($opts !== false) {
            foreach ($opts as $o) {
                $groups[$o['gid']]['options'][$o['id']] = $o;
            }
        } else {
            return array();
        }


        return $groups;
    }

    /**
     * @return mixed
     */
    public function get_options_groups(): array
    {
        dtimer::log(__METHOD__ . " start");
        return $this->db3->getInd("id", "SELECT * FROM s_options_groups ORDER BY pos");
    }

    /**
     * @param $name
     * @return mixed
     */
    public function add_option_group($name)
    {
        dtimer::log(__METHOD__ . " start");
        $this->db->query("SELECT MAX(`pos`) as `pos` FROM __options_groups");
        $pos = $this->db->result_array('pos');
        if (!empty_($pos)) {
            $pos = $pos + 1;
        } else {
            $pos = 0;
        }
        $name = trim($name);
        $group = array();
        $group['pos'] = (int)$group['pos'];
        $group['name'] = str_replace("\xc2\xa0", '', trim($group['name']));
        $group['pos'] = (int)$group['pos'];
        $q = $this->db->placehold("INSERT INTO __options_groups SET `name` = ?, `pos` = ? ", $name, $pos);
        $this->db->query($q);
        $res = $this->db->results_array(null, 'id');
        return $res;
    }


    public function update_text_option($pid, $name, $value)
    {
        $q = $this->db->placehold("INSERT INTO __text_options SET `product_id` = ? , `name` = ? , `value` = ? ON DUPLICATE KEY UPDATE `value` = ?", $pid, $name, $value, $value);
        return $this->db->query($q);
    }

    /**
     * @param $group
     * @return bool
     */
    public function update_option_group($group)
    {
        dtimer::log(__METHOD__ . " start");
        if (isset($group['id'])) {
            $id = (int)$group['id'];
            unset($group['id']);
        } else {
            dtimer::log(__METHOD__ . " args error", 1);
            return false;
        }
        if (isset($group['name'])) {
            $group['name'] = trim($group['name']);
        }
        if (isset($group['pos'])) {
            $group['pos'] = (int)$group['pos'];
        }
        $q = $this->db->placehold("UPDATE __options_groups SET ?% WHERE id=?", $group, $id);
        return $this->db->query($q);
    }

    /**
     * @param $gid
     * @return mixed
     */
    public
    function get_option_group($gid)
    {
        dtimer::log(__METHOD__ . " start");
        $q = $this->db->placehold("SELECT * FROM __options_groups WHERE id=? LIMIT 1", intval($gid));
        $this->db->query($q);
        return $this->db->result_array();
    }

    /* Добавляет свойство товара по новой системе
     */

    /**
     * @param $feature
     * @return mixed
     */
    public
    function add_feature($feature)
    {
        dtimer::log(__METHOD__ . ' start');

        //удалим пустые
        foreach ($feature as $k => $e) {
            if (empty_($e)) {
                unset($feature[$k]);
            }
        }
        //удалим id, если он сюда закрался, при создании id быть не должно
        if (isset($feature['id'])) {
            unset($feature['id']);
        }

        //если имя не задано - останавливаемся
        if (!isset($feature['name'])) {
            dtimer::log(__METHOD__ . " name is not set! abort. ", 1);
            return false;
        } else {
            $feature['name'] = filter_spaces(filter_ascii($feature['name']));
            $feature['trans'] = encode_param(translit_ya($feature['name']));
        }


        //если такое свойство уже есть, вернем его id
        $res = $this->get_feature($feature['name']);
        if ($res) {
            return $res['id'];
        }

        //вытаскиваем макс позицию из свойств
        $q = $this->db->query("SELECT MAX(pos) as pos FROM __features");
        if ($q !== false) {
            //макс. позиция в таблице
            $pos = $this->db->result_array('pos');
        }
        //если что-то есть на выходе, делаем $pos = 0, иначе $pos++
        if (isset($pos) && $pos !== null) {
            $feature['pos'] = $pos + 1;
        } else {
            $feature['pos'] = 0;
        }

        $query = $this->db->placehold("INSERT INTO __features SET ?%", $feature);
        dtimer::log(__METHOD__ . " query: $query");

        //прогоняем запрос (метод query в случае успеха выдает true)
        if ($this->db->query($query) !== true) {
            return false;
        }
        $id = $this->db->insert_id();

        /*
         * Тут часть, касающаяся таблицы со свойствами
         */
        if (!$this->db->query("SELECT `$id` FROM __options LIMIT 1")) {
            $this->db->query("ALTER TABLE __options ADD `$id` MEDIUMINT UNSIGNED NOT NULL DEFAULT '0'");
            //делаем индекс, только если это свойство будет в фильтре

            if (isset($feature['in_filter']) && (bool)$feature['in_filter'] === true) {
                $this->db->query("ALTER TABLE __options ADD INDEX `$id` (`$id`)");
                $this->db->query("ALTER TABLE __options DROP INDEX `only`");
                $this->db->query("SELECT id FROM __features WHERE in_filter = 1");
                $index = $this->db->results_array('id');
                if (!empty($index)) {
                    $this->db->query("ALTER TABLE __options ADD INDEX `only` (`product_id`, ?^)", $index);
                }
            }
        }
        return $id;
    }


    /**
     * @param $id
     * @param $feature
     * @return bool|int
     */
    public
    function update_feature($id, $feature)
    {
        $id = (int)$id;

        if (isset($feature['id'])) {
            unset($feature['id']);
        }
        if (count($feature) === 0) {
            dtimer::log(__METHOD__ . " nothing to change - feature is empty! abort. ", 1);
            return false;
        }

        //если имя задано, чистим его от лишних пробелов и непечатаемых символов
        if (isset($feature['name'])) {
            $feature['name'] = filter_spaces(filter_ascii($feature['name']));
            $feature['trans'] = encode_param(translit_ya($feature['name']));
        }

        $this->db->query("UPDATE __features SET ?% WHERE id = ?", $feature, $id);
        if (isset($feature['in_filter'])) {
            if ((bool)$feature['in_filter'] === true) {
                $this->db->query("ALTER TABLE __options ADD INDEX `$id` (`$id`)");
                $this->db->query("ALTER TABLE __options DROP INDEX `only`");
                $this->db->query("SELECT id FROM __features WHERE in_filter = 1");
                $index = $this->db->results_array('id');
                if (!empty($index)) {
                    $this->db->query("ALTER TABLE __options ADD INDEX `only` (`product_id`, ?^)", $index);
                }
            } else {
                $this->db->query("ALTER TABLE __options DROP INDEX `$id` ");
                $this->db->query("ALTER TABLE __options DROP INDEX `only`");
                $this->db->query("SELECT id FROM __features WHERE in_filter = 1");
                $index = $this->db->results_array('id');
                if (!empty($index)) {
                    $this->db->query("ALTER TABLE __options ADD INDEX `only` (`product_id`, ?^)", $index);
                }
            }
        }

        return $id;
    }


    /**
     * @param $id
     */
    public
    function delete_feature($id)
    {
        $this->db->query("DELETE FROM __features WHERE id=? LIMIT 1", intval($id));
        $this->db->query("ALTER TABLE __options DROP ?!", (int)$id);
        $this->db->query("ALTER TABLE __options DROP INDEX ?!", (int)$id);
        $this->db->query("DELETE FROM __categories_features WHERE feature_id=?", (int)$id);
    }

    /**
     * @param $id
     */
    public
    function delete_options($id)
    {
        $this->db->query("DELETE FROM __options WHERE product_id=?", (int)$id);
    }


    /**
     * @param $product_id
     * @param $feature_id
     * @param $val
     * @return bool
     */
    public
    function update_option($product_id, $feature_id, $val)
    {
        dtimer::log(__METHOD__ . " arguments '$product_id' '$feature_id' '$val'");


        //неразрывный пробел
        //$val = str_replace("\xc2\xa0", '', $val);

        $fid = (int)$feature_id;
        $pid = (int)$product_id;
        $val = filter_spaces(filter_ascii(($val)));
        $trans = encode_param(translit_ya($val));

        $this->db->query("SELECT `id` FROM __options_uniq WHERE `trans` = ? ", $trans);

        //Если запись уже есть - продолжаем работу, если нет добавляем запись в таблицу
        if ($this->db->affected_rows() > 0) {
            $vid = $this->db->result_array('id');
        } else {
            $q = $this->db->query("INSERT INTO __options_uniq SET `val`= ?, `trans` = ?
              ON DUPLICATE KEY UPDATE `val`= ?, `trans` = ?", $val, $trans, $val, $trans);
            if ($q !== false) {
                $vid = $this->db->insert_id();
            } else {
                dtimer::log(__METHOD__ . " unable to insert row", 1);
                return false;
            }
        }

        $query = $this->db->placehold(
            "INSERT INTO __options SET `product_id` = ? , ?! = ?
		ON DUPLICATE KEY UPDATE ?! = ?",
            $pid,
            $fid,
            $vid,
            $fid,
            $vid
        );
        if ($this->db->query($query)) {
            return $vid;
        } else {
            return false;
        }
    }

    /*
     * Этот метод позволяет писать свойства товаров напрямую, минуя таблицу options_uniq
     * в которой содержатся уникальные значения свойств и их id.
     * Тут $value должен быть сразу в виде числа с id значения из таблицы options_uniq
     */
    /**
     * @param $product_id
     * @param $feature_id
     * @param $value
     * @return bool|int
     */
    public
    function update_option_direct($product_id, $feature_id, $value)
    {
        if (!isset($product_id) || !isset($feature_id) || !isset($value)) {
            dtimer::log(__METHOD__ . " arguments error 3 args needed '$product_id' '$feature_id' '$value'", 1);
            return false;
        }

        $fid = (int)$feature_id;
        $pid = (int)$product_id;
        $vid = (int)$value;

        $query = $this->db->placehold(
            "INSERT INTO __options SET `product_id` = ? , ?! = ?
		ON DUPLICATE KEY UPDATE ?! = ?",
            $pid,
            $fid,
            $vid,
            $fid,
            $vid
        );

        if ($this->db->query($query)) {
            return $vid;
        } else {
            return false;
        }

    }

    /*
     * Этот метод сделан для быстрого импорта в таблицу опций, за 1 запрос добавляются
     * сразу несколько значений
     */
    /**
     * @param $filter
     * @return bool
     */
    public
    function update_options_direct($filter)
    {
        dtimer::log(__METHOD__ . ' start');

        if (!isset($filter['product_id'])) {
            dtimer::log(__METHOD__ . ' args error - pid', 1);
            return false;
        } else {
            $pid = (int)$filter['product_id'];
        }

        if (!isset($filter['features']) || !is_array($filter['features']) || empty($filter['features'])) {
            dtimer::log(__METHOD__ . ' args error - features', 1);
            return false;
        } else {
            $features = $filter['features'];
        }

        $set_options = $this->db->placehold("?%", $features);
        $q = $this->db->placehold("INSERT INTO __options
		SET `product_id` = ? , $set_options ON DUPLICATE KEY UPDATE $set_options", $pid);


        if ($this->db->query($q)) {
            dtimer::log(__METHOD__ . ' end ok');
            return true;
        } else {
            dtimer::log(__METHOD__ . ' end error', 1);
            return false;
        }

    }


    /**
     * @param $id
     * @param $category_id
     */
    public
    function add_feature_category($id, $category_id)
    {
        $query = $this->db->placehold("INSERT IGNORE INTO __categories_features SET feature_id=?, category_id=?", $id, $category_id);
        $this->db->query($query);
    }


    /**
     * @param $id
     * @param $categories
     * @return bool
     */
    public
    function update_feature_categories($id, $categories)
    {
        $id = intval($id);
        $query = $this->db->placehold("DELETE FROM __categories_features WHERE feature_id=?", $id);
        $this->db->query($query);


        if (is_array($categories)) {
            $values = array();
            foreach ($categories as $category)
                $values[] = "($id , " . intval($category) . ")";

            $query = $this->db->placehold("INSERT INTO __categories_features (feature_id, category_id) VALUES " . implode(', ', $values));
            return $this->db->query($query);
        } else {
            return false;
        }
    }


    /**
     * @param array $filter
     * @return array
     */
    public
    function get_options_uniq($filter = array())
    {
        $id_filter = '';
        $trans_filter = '';
        $trans2_filter = '';
        $val_filter = '';
        if (isset($filter['val'])) {
            $val_filter = $this->db->placehold(" AND `val` in ( ?@ )", (array)$filter['val']);
        }
        if (isset($filter['trans'])) {
            $trans_filter = $this->db->placehold(" AND `trans` in ( ?@ )", (array)$filter['trans']);
        }
        if (isset($filter['trans2'])) {
            $trans2_filter = $this->db->placehold(" AND `trans2` in ( ?@ )", (array)$filter['trans2']);
        }
        if (isset($filter['id'])) {
            $id_filter = $this->db->placehold(" AND `id` in ( ?@ )", (array)$filter['id']);
        }

        $this->db->query("SELECT * FROM __options_uniq WHERE 1 $id_filter $trans_filter $trans2_filter $val_filter");
        $res = $this->db->results_array(null, 'id');
        return $res;
    }


    /**
     * @param array $filter
     * @return mixed
     */
    public function get_options_ids($filter = array()): ?array
    {
        dtimer::log(__METHOD__ . " start");
        dtimer::log(__METHOD__ . " filter: " . var_export($filter, true));
        //print var_export($filter, true).PHP_EOL;

        //это вариант по умолчанию id=>val
        $col = isset($filter['return']['col']) ? $filter['return']['col'] : 'val';
        $key = isset($filter['return']['key']) ? $filter['return']['key'] : 'id';

        //выводим из сохраненного массива, если у нас не заданы фильтры по id и trans и не включен force_no_cache
        if (empty($filter['force_no_cache']) && !isset($filter['id']) && !isset($filter['trans']) && !isset($filter['trans2'])) {

            if (isset($this->options[$key . "_" . $col])) {
                dtimer::log(__METHOD__ . " using saved class variable");
                return $this->options[$key . "_" . $col];
            }
        }


        //сначала уберем из фильтра лишние параметры, которые не влияют на результат, но влияют на хэширование
        $filter_ = $filter;
        dtimer::log(__METHOD__ . " start filter: " . var_export($filter_, true));
        unset($filter_['method']);
        if (!empty($filter_['force_no_cache'])) {
            $force_no_cache = true;
            unset($filter_['force_no_cache']);
        } else {
            $force_no_cache = false;
        }
        $res = [];

        //сортируем фильтр, чтобы порядок данных в нем не влиял на хэш
        ksort($filter_);
        $filter_string = var_export($filter_, true);
        $keyhash = md5(__METHOD__ . $filter_string);

        //если запуск был не из очереди - пробуем получить из кеша
        if (!$force_no_cache) {
            dtimer::log(__METHOD__ . " normal run keyhash: $keyhash");
            $res = $this->cache->redis_get_serial($keyhash);
            //если дата создания записи в кеше больше даты последнего импорта, то не будем добавлять задание в очередь на обновление
            if ($res !== false) {
                if ($this->cache->redis_created($keyhash, $this->ttl) > $this->config->cache_date) {
                    return $res;
                }
                //Если у нас был запуск без параметров, сохраним результат в переменную класса.
                if (!isset($filter['id']) && !isset($filter['trans']) && !isset($filter['trans2'])) {
                    $this->options[$key . "_" . $col] = $res;
                }

                //запишем в фильтр параметр force_no_cache, чтобы при записи задания в очередь
                //функция выполнялась полностью
                $filter_['force_no_cache'] = true;
                $filter_string = var_export($filter_, true);
                dtimer::log(__METHOD__ . " force_no_cache keyhash: $keyhash");

                $task = '$this->features->get_options_ids(';
                $task .= $filter_string;
                $task .= ');';
                $var = $this->queue->redis_adddask($keyhash, isset($filter['method']) ? $filter['method'] : '', $task);


                return $res;
            }
        }
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


        //переменные
        $id_filter = '';
        $trans_filter = '';
        $trans2_filter = '';

        if (isset($filter['id']) && count($filter['id']) > 0) {
            $id_filter = $this->db->placehold("AND id in (?@)", $filter['id']);
        }

        if (isset($filter['trans']) && count($filter['trans']) > 0) {
            $trans_filter = $this->db->placehold("AND trans in (?@)", $filter['trans']);
        }

        if (isset($filter['trans2']) && count($filter['trans2']) > 0) {
            $trans2_filter = $this->db->placehold("AND trans2 in (?@)", $filter['trans2']);
        }

        $this->db->query("SELECT id, val, trans FROM s_options_uniq
		WHERE 1 
		$id_filter
		$trans_filter
		$trans2_filter
		");

        if ($col === 'id') {
            while ($row = $this->db->res->fetch_assoc()) {
                $res[$row[$key]] = (int)$row[$col];
            }
        } else {
            while ($row = $this->db->res->fetch_assoc()) {
                $res[$row[$key]] = $row[$col];
            }
        }


        //Если у нас был запуск без параметров, сохраним результат в переменную класса.
        if (!isset($filter['id']) && !isset($filter['trans'])) {
            dtimer::log(__METHOD__ . " save res to class variable");
            $this->options[$key . "_" . $col] = $res;
        }
        dtimer::log(__METHOD__ . " redis set key: $keyhash");
        $this->cache->redis_set_serial($keyhash, $res, $this->ttl);

        dtimer::log(__METHOD__ . " return db ");
        return $res;

    }


    /*
     * Этот метод предоставляет комбинированные данные опций, в т.ч. все возможные опции без учета уже выбранных,
     * доступные для выбора опции с учетом уже выбранных. Т.е. если выбрана страна, например, Россия, другие
     * страны будут также доступны для выбора.
     */
    /**
     * @param array $filter
     * @return array|bool
     */
    public function get_options_mix($filter = array())
    {
        //сначала уберем из фильтра лишние параметры, которые не влияют на результат, но влияют на хэширование
        dtimer::log(__METHOD__ . " start filter: " . var_export($filter, true));
        $filter = array_intersect_key($filter, array_flip($this->tokeep));
        dtimer::log(__METHOD__ . " filtered filter: " . var_export($filter, true));
        $filter_ = $filter;
        if (!empty($filter_['force_no_cache'])) {
            $force_no_cache = true;
            unset($filter_['force_no_cache']);
        } else {
            $force_no_cache = false;
        }


        //сортируем фильтр, чтобы порядок данных в нем не влиял на хэш
        ksort($filter_);
        $filter_string = var_export($filter_, true);
        $keyhash = md5(__METHOD__ . $filter_string);

        //если запуск был не из очереди - пробуем получить из кеша
        if (!$force_no_cache) {
            dtimer::log(__METHOD__ . " normal run keyhash: $keyhash");
            $res = $this->cache->redis_get_serial($keyhash);
            //если дата создания записи в кеше больше даты последнего импорта, то не будем добавлять задание в очередь на обновление
            if ($res !== false) {
                if ($this->cache->redis_created($keyhash, $this->ttl) > $this->config->cache_date) {
                    return $res;
                }
                //запишем в фильтр параметр force_no_cache, чтобы при записи задания в очередь
                //функция выполнялась полностью
                $filter_['force_no_cache'] = true;
                $filter_string = var_export($filter_, true);
                dtimer::log(__METHOD__ . " force_no_cache keyhash: $keyhash");

                $task = '$this->features->get_options_mix(';
                $task .= $filter_string;
                $task .= ');';
                $this->queue->redis_adddask($keyhash, isset($filter['method']) ? $filter['method'] : '', $task);

                return $res;
            }
        }
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


        //это для результата
        $res = array();

        //это понадобится в любом случае
        //массив id=>значение
        $vals = $this->get_options_ids(array('return' => array('col' => 'val', 'key' => 'id')));

        //массив id=>значение транслитом
        $trans = $this->get_options_ids(array('return' => array('col' => 'trans', 'key' => 'id')));


        //Самый простой вариант - если не заданы фильтры по свойствам и брендам
        if (!isset($filter['features']) && !isset($filter['brand_id'])) {
            $filter['brand_id'] = [];
            $raw = $this->get_options_raw($filter);
            $res['filter'] = $raw;

            if (isset($raw['brand_id'])) {
                $res['full']['brand_id'] = $raw['brand_id'];
                unset($raw['brand_id']);
            }

            foreach (array_keys($raw) as $fid) {
                $vals_ = array_intersect_key($vals, $raw[$fid]);
                asort($vals_);
                $res['full'][$fid] = array(
                    'vals' => $vals_,
                    'trans' => array_intersect_key($trans, $raw[$fid])
                );
            }
        } else {
            /*
             * Это фильтрованные результаты. Логика:
             * делается выборка для каждого свойства, исключая заданные опции по этому свойству
             */

            //это результат со всеми заданными $fid
            $filter_ = $filter;
            $res['filter'] = $this->get_options_raw($filter_);

            //получим значения по брендам без указания самого бренда

            $filter_['brand_id'] = [];
            dtimer::log(__METHOD__ . " brand filter");

            $raw = $this->get_options_raw($filter_);
            if (isset($raw['brand_id'])) {
                $res['filter']['brand_id'] = $raw['brand_id'];
            }
            if (isset($filter['features'])) {
                //тут получим полные результаты для отдельных $fid
                foreach ($filter['features'] as $fid => $vid) {
                    //копируем фильтр
                    $filter_ = $filter;
                    //оставляем только нужный нам $fid
                    $filter_['feature_id'] = array($fid);
                    //убираем из массива заданных фильтров искомый $fid
                    unset($filter_['features'][$fid]);

                    $raw = $this->get_options_raw($filter_);
                    if (isset($raw[$fid])) {
                        $res['filter'][$fid] = $raw[$fid];
                    }
                }
            }


            //это полный результат, поэтому убираем все фильтры
            $filter_ = $filter;
            unset($filter_['features']);
            $filter_['brand_id'] = [];
            $raw = $this->get_options_raw($filter_);
            if (isset($raw['brand_id'])) {
                $res['full']['brand_id'] = $raw['brand_id'];
                unset($raw['brand_id']);
            }

            foreach (array_keys($raw) as $fid) {
                $vals_ = array_intersect_key($vals, $raw[$fid]);
                asort($vals_);
                $res['full'][$fid] = array(
                    'vals' => $vals_,
                    'trans' => array_intersect_key($trans, $raw[$fid])
                );
            }

        }


        dtimer::log(__METHOD__ . " redis set key: $keyhash");
        $this->cache->redis_set_serial($keyhash, $res, $this->ttl); // 2592000 is a 1 month in seconds
        dtimer::log(__METHOD__ . " return");


        return $res;


    }

    public function get_range_values(int $fid, $min, $max): ?array
    {

        $options = $this->get_options_ids();
        $min_vid = array_search($min, $options);
        $max_vid = array_search($max, $options);
        $vals = $this->db3->getInd($fid, "SELECT ?n FROM s_options", $fid);
        $vals = array_intersect_key($options, $vals);
        asort($vals);
        $first = array_search(array_search($min, $vals), array_keys($vals));
        $last = array_search(array_search($max, $vals), array_keys($vals));

        $vids = array_slice($vals, $first, $last - $first + 1);
        print_r($vids);
        return $vids;
    }


    /**
     * @param array $filter
     * @return array|bool
     */
    public function get_options_raw($filter = array())
    {
        //сначала уберем из фильтра лишние параметры, которые не влияют на результат, но влияют на хэширование
        dtimer::log(__METHOD__ . " start filter: " . var_export($filter, true));


        $product_id_filter = '';
        $category_id_filter = '';
        $visible_filter = '';
        $brand_id_filter = '';
        $features_filter = '';
        $products_join = '';
        $products_join_flag = false;
        $res = array();

        //если у нас не заданы фильтры опций и не запрошены сами опции, будем брать все.
        if (!isset($filter['feature_id']) || count($filter['feature_id']) === 0) {
            $f = $this->get_features_ids(array('in_filter' => 1, 'return' => array('key' => 'id', 'col' => 'id')));
            if ($f !== false) {
                $filter['feature_id'] = $f;
            } else {
                //если у нас нет свойств в фильтре, значит и выбирать нечего
                return false;
            }
        }

        if (!empty($filter['features']) && is_array($filter['features'])) {
            $features_ids = array_keys($filter['features']);
            //если в фильтрах свойств что-то задано, но этого нет в запрошенных фильтрах, добавляем.
            foreach ($features_ids as $fid) {
                if (!in_array($fid, $filter['feature_id'])) {
                    $filter['feature_id'][] = $fid;
                }
            }
        }


        //собираем столбцы, которые нам понадобятся для select
        if (isset($filter['brand_id'])) {
            //если задан бренд, то соберем все в 1 массив со свойствами
            $select_array = $filter['feature_id'];
            $select_array[] = 'brand_id';
            //флаг присоединения таблицы товаров
            $products_join_flag = true;
        } else {
            //иначе просто возьмем свойства
            $select_array = $filter['feature_id'];
        }
        $select = "SELECT " . implode(', ', array_map(function ($a) {
                return '`' . $a . '`';
            }, $select_array));


        if (!empty($filter['category_id'])) {
            $category_id_filter = $this->db2->placehold(' AND o.product_id in(SELECT DISTINCT product_id from s_products_categories where category_id in (?@))', (array)$filter['category_id']);
        }

        if (!empty($filter['product_id'])) {
            $product_id_filter = $this->db2->placehold(' AND o.product_id in (?@)', (array)$filter['product_id']);
        }

        if (!empty($filter['brand_id'])) {
            $products_join_flag = true;
            $brand_id_filter = $this->db2->placehold(' AND p.brand_id in (?@)', (array)$filter['brand_id']);
        }

        if (!empty($filter['visible'])) {
            $products_join_flag = true;
            $visible_filter = $this->db2->placehold(' AND p.visible=?', (int)$filter['visible']);
        }

        //фильтрация по свойствам товаров
        if (!empty($filter['features'])) {
            foreach ($filter['features'] as $fid => $vids) {
                if (is_array($vids)) {
                    $features_filter .= $this->db->placehold(" AND `$fid` in (?@)", $vids);
                }
            }
        }

        if ($products_join_flag === true) {
            $products_join = "INNER JOIN __products p on p.id = o.product_id";
        }

        $query = $this->db2->placehold("$select
		    FROM __options o
		    $products_join
			WHERE 1 
			$product_id_filter 
			$brand_id_filter 
			$features_filter 
		    $visible_filter
			$category_id_filter
			");

        if (!$this->db2->query($query)) {
            dtimer::log(__METHOD__ . " query error: $query", 1);
            return false;
        }
        if ($this->db2->num_rows() < 1) {
            dtimer::log(__METHOD__ . " empty result", 2);
        }


        //вывод обрабатываем построчно
        while (1) {
            $row = $this->db2->result_array(null, 'pid', true);
            if ($row === false) {
                break;
            }
            //~ $res['pid'][] = $row['pid'];
            //~ unset($row['pid']);

            foreach ($row as $fid => $vid) {
                ($fid !== 'brand_id') ? $fid = (int)$fid : null; //если ключ числовой, то преобразуем его в число
                $vid = (int)$vid; //значения у нас все числовые, поэтому преобразуем их
                if ($vid !== 0 && !isset($res[$fid][$vid])) {
                    $res[$fid][$vid] = '';
                }
            }
        }

        dtimer::log(__METHOD__ . " return");
        return $res;
    }


    /*
     * Этот метод предназначен для получения данных о свойствах напрямую из таблицы options.
     * Т.е. возвращает не сами значения свойств товаров, а только id этих значений.
     */
    /**
     * @param $product_id
     * @return bool
     */
    public function get_product_options_direct($product_id)
    {

        if (!isset($product_id)) {
            return false;
        } else {
            $product_id = (int)$product_id;
        }

        $this->db->query("SELECT * FROM __options WHERE 1 AND `product_id` = ?", $product_id);
        $res = $this->db->result_array();
        if (isset($res['product_id'])) {
            unset($res['product_id']);
            return $res;
        } else {
            return false;
        }
    }


    /**
     * @param $product_id
     * @return bool
     */
    public
    function get_product_options($product_id)
    {
        dtimer::log(__METHOD__ . " start");
        if (!isset($product_id)) {
            return false;
        } else {
            $product_id = (int)$product_id;
        }

        $this->db->query("SELECT * FROM __options WHERE 1 AND `product_id` = ?", $product_id);
        $options = $this->db->result_array();

        //Если ничего не нашлось - возвращаем false
        if (isset($options['product_id'])) {
            unset($options['product_id']);
        } else {
            return false;
        }
        //выбираем значений опций из соответствующей таблицы
        if ($this->db->query("SELECT id, val FROM __options_uniq WHERE id in (?@)", $options)) {
            $vals = $this->db->results_array(null, 'id', true);
            foreach ($options as $fid => &$option) {
                if (empty($option)) {
                    unset($options[$fid]);
                } else {
                    if (isset($vals[$option]['val'])) {
                        $option = array('fid' => $fid, 'vid' => $option, 'val' => $vals[$option]['val']);
                    } else {
                        dtimer::log(__METHOD__ . " value " . var_export($option, true) . " not found!", 2);
                        unset($options[$fid]);
                    }
                }
            }
            return $options;
        }
        //если не получилось вернуть $options, вернем false
        return false;
    }
}
