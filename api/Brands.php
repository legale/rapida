<?php
if (defined('PHP7')) {
    eval("declare(strict_types=1);");
}

require_once('Simpla.php');

/**
 * Class Brands
 */
class Brands extends Simpla
{
    private $tokeep = array(
        'force_no_cache',
        'visible',
        'category_id',
        'trans',
        'trans2',

    );

    public $brands;

    /*
     *
     * Функция возвращает массив названий брендов с ключами в виде id этих брендов
     * @param $ids array
     *
     */
    /**
     * @param array $filter
     * @return bool
     */
    public function get_brands_ids($filter = array())
    {
        dtimer::log(__METHOD__ . " start");
        //это вариант по умолчанию id=>name
        $col = isset($filter['return']['col']) ? $filter['return']['col'] : 'name';
        $key = isset($filter['return']['key']) ? $filter['return']['key'] : 'id';


        $id_filter = '';
        $trans_filter = '';
        $trans2_filter = '';

        if (!isset($filter['id']) && !isset($filter['trans']) && isset($this->brands[$key . "_" . $col])) {
            return $this->brands[$key . "_" . $col];
        }


        //фильтр
        if (isset($filter['id'])) {
            $id_filter = $this->db->placehold("AND id in (?@)", $filter['id']);
        }

        if (isset($filter['trans2'])) {
            $trans2_filter = $this->db->placehold("AND trans2 in (?@)", $filter['trans2']);
        }

        if (isset($filter['trans'])) {
            $trans_filter = $this->db->placehold("AND trans in (?@)", $filter['trans']);
        }

        $q = $this->db->placehold("SELECT `$col`, `$key` FROM __brands WHERE 1 $id_filter $trans_filter $trans2_filter");
        $this->db->query($q);

        $res = $this->db->results_array($col, $key);


        //Если у нас был запуск без параметров, сохраним результат в переменную класса.
        if (!isset($filter['id']) && !isset($filter['trans'])) {
            $this->brands[$key . "_" . $col] = $res;
        }
        dtimer::log(__METHOD__ . " end");
        return $res;
    }

    /*
     *
     * Функция возвращает массив брендов, удовлетворяющих фильтру
     * @param $filter
     *
     */
    /**
     * @param array $filter
     * @return mixed
     */
    public function get_brands($filter = array())
    {
        //сначала уберем из фильтра лишние параметры, которые не влияют на результат, но влияют на хэширование
        dtimer::log(__METHOD__ . " start filter: " . var_export($filter, true));
        $filter = array_intersect_key($filter, array_flip($this->tokeep));
        dtimer::log(__METHOD__ . " filtered filter: " . var_export($filter, true));
        $filter_ = $filter;
        if (isset($filter_['force_no_cache'])) {
            $force_no_cache = $filter_['force_no_cache'];
            unset($filter_['force_no_cache']);
        }


        //сортируем фильтр, чтобы порядок данных в нем не влиял на хэш
        ksort($filter_);
        $filter_string = var_export($filter_, true);
        $keyhash = md5( __METHOD__ . $filter_string);

        //если запуск был не из очереди - пробуем получить из кеша
        if (!isset($force_no_cache)) {
            dtimer::log(__METHOD__ . " normal run keyhash: $keyhash");
            $res = $this->cache->get_cache_nosql($keyhash);


            //запишем в фильтр параметр force_no_cache, чтобы при записи задания в очередь
            //функция выполнялась полностью
            $filter_['force_no_cache'] = true;
            $filter_string = var_export($filter_, true);
            dtimer::log(__METHOD__ . " force_no_cache keyhash: $keyhash");

            $task = '$this->brands->get_brands(';
            $task .= $filter_string;
            $task .= ');';
            $this->queue->addtask($keyhash, isset($filter['method']) ? $filter['method'] : '', $task);
        }

        if (isset($res) && !empty_($res)) {
            dtimer::log(__METHOD__ . " return cache res count: " . count($res));
            return $res;
        }
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $cat_id_filter = '';
        $visible_filter = '';
        $trans_filter = '';
        $trans2_filter = '';
        $where = '';
        $where_flag = false;


        if (isset($filter['trans'])) {
            $trans_filter = $this->db->placehold("AND b.trans in(?@)", $filter['trans']);
        }
        if (isset($filter['trans2'])) {
            $trans2_filter = $this->db->placehold("AND b.trans2 in(?@)", $filter['trans2']);
        }
        if (isset($filter['visible'])) {
            $visible_filter = $this->db->placehold("AND p.visible=?", intval($filter['visible']));
            $where_flag = true;
        }
        if (!empty($filter['category_id'])) {
            $cat_id_filter = $this->db->placehold("AND p.id in (SELECT product_id FROM __products_categories WHERE category_id in (?@) )", (array)$filter['category_id']);
            $where_flag = true;
        }

        if ($where_flag === true) {
            $where = "AND b.id in (SELECT brand_id FROM __products p WHERE 1 $visible_filter $cat_id_filter)";
        }
        // Выбираем все бренды
        $query = $this->db->placehold("SELECT * FROM __brands b WHERE 1 $trans_filter $trans2_filter $where ");
        $this->db->query($query);

        $res = $this->db->results_array(null, 'id');
        dtimer::log(__METHOD__ . " set_cache_nosql key: $keyhash");
        $this->cache->set_cache_nosql($keyhash, $res);
        dtimer::log(__METHOD__ . " end");
        return $res;
    }


    /*
     *
     * Функция возвращает бренд по его id или trans
     * (в зависимости от типа аргумента, int - id, string - trans)
     * @param $id id или trans поста
     *
     */
    /**
     * @param $id
     * @return bool
     */
    public function get_brand($id)
    {
        dtimer::log(__METHOD__ . " start '$id'");
        if ($id === (int)$id) {
            $filter = $this->db->placehold("AND id = $id");
        } else if (is_string($id)) {
            $trans = encode_param(translit_ya($id));
            $filter = $this->db->placehold("AND name = ? OR trans = ?", $id, $trans);
        } else {
            dtimer::log(__METHOD__ . " bad arg ", 1);
            return false;
        }

        $q = "SELECT * FROM __brands b WHERE 1 $filter LIMIT 1";
        if ($this->db->query($q) && $this->db->affected_rows() > 0) {
            return $this->db->result_array();
        } else {
            return false;
        }
    }

    /*
     *
     * Добавление бренда
     * @param $brand
     *
     */
    /**
     * @param $brand
     * @return bool
     */
    public function add_brand($brand)
    {
        dtimer::log(__METHOD__ . " start " . var_export($brand, true));
        //удалим пустые
        foreach ($brand as $k => $e) {
            if (empty_($e)) {
                unset($brand[$k]);
            }
        }

        if (!isset($brand['name'])) {
            dtimer::log(__METHOD__ . " brand name is not set! abort ", 1);
            return false;
        } else {
            //удалим все непечатаемые символы и удалим лишние пробелы
            $brand['name'] = filter_spaces(filter_ascii($brand['name']));
        }

        //удалим id, если он сюда закрался, при создании id быть не должно
        if (isset($brand['id'])) {
            unset($brand['id']);
        }

        $brand['trans'] = encode_param(translit_ya($brand['name']));

        //если такой бренд уже есть, вернем его id
        $res = $this->get_brand($brand['name']);
        if ($res) {
            return $res['id'];
        }


        $this->db->query("INSERT INTO __brands SET ?%", $brand);
        if (($res = $this->db->insert_id()) !== false) {
            dtimer::log(__METHOD__ . " end \$res: '$res'");
        } else {
            dtimer::log(__METHOD__ . " unable to add brand", 1);
        }

        return $res;
    }

    /*
     *
     * Обновление бренда(ов)
     * @param $brand
     *
     */
    /**
     * @param $id
     * @param $brand
     * @return mixed
     */
    public function update_brand($id, $brand)
    {
        dtimer::log(__METHOD__ . " start $id" . var_export($brand, true));
        $id = (int)$id;
        if (isset($brand['id'])) {
            unset($brand['id']);
        }
        if (count($brand) === 0) {
            dtimer::log(__METHOD__ . " nothing to change - brand is empty! abort. ", 1);
            return false;
        }


        if (isset($brand['name'])) {
            //удалим все непечатаемые символы и удалим лишние пробелы
            $brand['name'] = filter_spaces(filter_ascii($brand['name']));
            $brand['trans'] = encode_param(translit_ya($brand['name']));
        }


        $q = $this->db->placehold("UPDATE __brands SET ?% WHERE id=? LIMIT 1", $brand, $id);
        $this->db->query($q);
        return $id;
    }

    /*
     * Удаление бренда
     * @param $id
     */
    public function delete_brand($id)
    {
        if (!empty($id)) {
            $this->delete_image($id);
            $query = $this->db->placehold("DELETE FROM __brands WHERE id=? LIMIT 1", $id);
            $this->db->query($query);
            $query = $this->db->placehold("UPDATE __products SET brand_id=NULL WHERE brand_id=?", $id);
            $this->db->query($query);
        }
    }

    /*
     *
     * Удаление изображения бренда
     * @param $id
     *
     */
    /**
     * @param $brand_id
     */
    public function delete_image($brand_id)
    {
        $query = $this->db->placehold("SELECT image FROM __brands WHERE id=?", intval($brand_id));
        $this->db->query($query);
        $filename = $this->db->result_array('image');
        if (!empty($filename)) {
            $query = $this->db->placehold("UPDATE __brands SET image=NULL WHERE id=?", $brand_id);
            $this->db->query($query);
            $query = $this->db->placehold("SELECT count(*) as count FROM __brands WHERE image=? LIMIT 1", $filename);
            $this->db->query($query);
            $count = $this->db->result_array('count');
            if ($count == 0) {
                $to_unlink = $this->config->root_dir . $this->config->brands_images_dir . $filename;
                if (file_exists($to_unlink)) {
                    unlink($to_unlink);
                }
            }
        }
    }

}
