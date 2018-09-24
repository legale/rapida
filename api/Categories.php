<?php


require_once('Simpla.php');

class Categories extends Simpla
{
    // Список указателей на категории в дереве категорий (ключ = id категории)
    public $all_categories;
    // Дерево категорий
    public $categories_tree;
    //массив для пар имя транслитом - id категории
    public $categories_uri;

    public function __construct()
    {
        $this->init_categories(false);
    }

    // Функция возвращает массив категорий
    public function &get_categories()
    {
        if (!$this->all_categories) {
            $this->init_categories();
        }
        return $this->all_categories;
    }

    // Функция возвращает id категорий для заданного товара
    public function get_product_categories($product_id)
    {
        return $this->db3->getAll("SELECT * FROM s_products_categories WHERE product_id = ?i", $product_id);
    }

    // Функция возвращает id категорий для всех товаров
    public function get_products_categories(array $pids)
    {
        return $this->db3->getAll("SELECT * FROM s_products_categories WHERE product_id in (?a)", $pids);
    }


    // Функция возвращает заданную категорию
    public function &get_category($id): ?array
    {
        dtimer::log(__METHOD__ . " start: $id");
        if ($id == strval((int)$id)) {
            $id = (int)$id;
        } else {
            $id = (string)$id;
        }

        if (!$this->all_categories) {
            $this->init_categories();
        }


        if (is_int($id) && array_key_exists($id, $this->all_categories)) {
            return $this->all_categories[$id];
        }else if (is_string($id) && array_key_exists($id, $this->categories_uri)) {
            return $this->all_categories[$this->categories_uri[$id]];
        }
        $null = null;
        return $null;
    }

    // Добавление категории
    public function add_category($cat)
    {
        dtimer::log(__METHOD__ . ' start');
        //удалим пустые
        foreach ($cat as $k => $e) {
            if (empty_($e)) {
                unset($cat[$k]);
            }
        }
        if (!isset($cat['auto_meta_title'])) {
            $cat['auto_meta_title'] = '';
        }
        if (!isset($cat['auto_annotation'])) {
            $cat['auto_annotation'] = '';
        }
        if (!isset($cat['auto_description'])) {
            $cat['auto_description'] = '';
        }
        if (!isset($cat['auto_meta_keywords'])) {
            $cat['auto_meta_keywords'] = '';
        }
        if (!isset($cat['auto_meta_description'])) {
            $cat['auto_meta_description'] = '';
        }
        if (!isset($cat['enabled'])) {
            $cat['enabled'] = true;
        }

        if (!isset($cat['annotation'])) {
            $cat['annotation'] = '';
        }
        if (!isset($cat['description'])) {
            $cat['description'] = '';
        }


        //удалим id, если он сюда закрался, при создании id быть не должно
        if (isset($cat['id'])) {
            unset($cat['id']);
        }
        //если имя не задано - останавливаемся
        if (!isset($cat['name'])) {
            dtimer::log(__METHOD__ . " name is not set! abort. ", 1);
            return false;
        } else {
            $cat['name'] = filter_spaces(filter_ascii($cat['name']));
            $cat['trans'] = translit_ya($cat['name']);
        }


        //если такое свойство уже есть, вернем его id
        $res = $this->get_category($cat['trans']);
        if ($res) {
            return $res['id'];
        }

        $this->db->query("INSERT INTO __categories SET ?%", $cat);
        $id = $this->db->insert_id();
        $this->db->query("UPDATE __categories SET pos=id WHERE id=?", $id);
        unset($this->categories_tree);
        unset($this->all_categories);
        return $id;
    }

    // Изменение категории
    public function update_category($id, $cat)
    {
        dtimer::log(__METHOD__ . ' start ' . var_export($cat, true));
        $id = (int)$id;

        if (isset($cat['id'])) {
            unset($cat['id']);
        }
        if (count($cat) === 0) {
            dtimer::log(__METHOD__ . " cat is empty! abort. ", 1);
            return false;
        }
        //если имя задано, чистим его от лишних пробелов и непечатаемых символов
        if (isset($cat['name'])) {
            $cat['name'] = filter_spaces(filter_ascii($cat['name']));
            $cat['trans'] = translit_ya($cat['name']);
        }


        $query = $this->db->placehold("UPDATE __categories SET ?% WHERE id=? LIMIT 1", $cat, intval($id));
        $this->db->query($query);
        $this->init_categories(true);
        return $id;
    }

    // Удаление категории
    public function delete_category($ids)
    {
        $ids = (array)$ids;
        foreach ($ids as $id) {
            if ($category = $this->get_category(intval($id)))
                $this->delete_image($category['children']);
            if (!empty($category['children'])) {
                $query = $this->db->placehold("DELETE FROM __categories WHERE id in(?@)", $category['children']);
                $this->db->query($query);
                $query = $this->db->placehold("DELETE FROM __products_categories WHERE category_id in(?@)", $category['children']);
                $this->db->query($query);
            }
        }
        $this->init_categories(true);
        return $id;
    }

    // Добавить категорию к заданному товару
    public function add_product_category($product_id, $category_id)
    {
        $query = $this->db->placehold("INSERT IGNORE INTO __products_categories 
		SET product_id=?, category_id=? ", $product_id, $category_id);
        return $this->db->query($query);
    }

    // Удалить категорию заданного товара
    public function delete_product_category($product_id, $category_id)
    {
        $query = $this->db->placehold("DELETE FROM __products_categories WHERE product_id=? AND category_id=? LIMIT 1", intval($product_id), intval($category_id));
        return $this->db->query($query);
    }



    // Инициализация категорий, после которой категории будем выбирать из локальной переменной
    public function init_categories($reinit = false): void
    {
        dtimer::log(__METHOD__ . " start reinit flag: " . var_export($reinit, true));
        if ($reinit === false && function_exists('apcu_fetch')) {
            dtimer::log(__METHOD__ . " ACPU CACHE CATEGORIES READ ");
            $this->all_categories = apcu_exists($this->config->host . 'all_categories') ? apcu_fetch($this->config->host . 'all_categories') : null;
            $this->categories_tree = &$this->all_categories[0]['subcategories'];
            $this->categories_uri = &$this->all_categories[0]['uri'];
            unset($this->all_categories[0]); //remove root element
            if ($this->all_categories) {
                return;
            }
        }


        // Выбираем все категории
        $cats = $this->db3->getInd("id", "SELECT * FROM s_categories ORDER BY parent_id, pos ASC");
        $ids = array_keys($cats);
        // Дерево категорий
        $tree = [];
        $tree['subcategories'] = [];
        $tree['level'] = 0;
        $tree['children'] = [0];
        $tree['path'] = [];
        $tree['uri'] = [];

        //указатели на узлы дерева
        $ptr[0] = &$tree; //корневой элемент

        // Не кончаем, пока не кончатся категории, или пока ни одну из оставшихся некуда приткнуть
        $finish = false;
        while(!empty($ids) && !$finish) {
            $flag = false;
            foreach ($ids as $i=>$cid) {
                if(!isset($ptr[$cats[$cid]['parent_id']])){
                    continue;
                }
                $cat = &$cats[$cid];
                $cat['id'] = (int)$cid;
                $cat['parent_id'] = (int)$cat['parent_id'];
                $cat['vparent_id'] = (int)$cat['vparent_id'];
                $cat['enabled'] = (bool)$cat['enabled'];
                $cat['visible'] = (bool)$cat['visible'];
                $cat['path'] = [];
                $cat['vchildren'] = [];
                $cat['children'] = [];
                $cat['subcategories'] = [];
                //сначала часть родительского пути
                $cat['path'] = $ptr[$cat['parent_id']]['path'];
                //саму себя в конце
                $cat['path'][] = &$cat;

                //запишемся в массив указателей
                $ptr[$cid] = &$cat;
                //добавимся в дочерние к родительской категории
                $ptr[$cat['parent_id']]['subcategories'][] = &$ptr[$cid];
                // Уровень вложенности категории
                $cat['level'] = 1 + $ptr[$cat['parent_id']]['level'];

                //добавим виртуальные разделы к его родителю
                $cats[$cat['vparent_id']]['vchildren'][] = $cid;

                unset($ids[$i]);
                $flag = true;
            }
            if(!$flag){
                $finish = true;
            }
        }

        $ids = array_keys($ptr);
        unset($ids[0]); //уберем корневой раздел
        $ids = array_reverse($ids); //обратный порядок важен чтобы матрешка собралась правильно
        foreach ($ids as $cid) {
            $cat = &$ptr[$cid];
            //сначала добавим саму себя
            $cat['children'][] = $cid;
            //теперь прибавим к родительскому разделу свои
            $ptr[$cat['parent_id']]['children'] = array_merge($ptr[$cat['parent_id']]['children'], $cat['children']);

            //транслит имена добавим в корневой массив
            $tree['uri'][$cat['trans']] = $cid;
            if($cat['trans2'] !== '') {
                $tree['uri'][$cat['trans2']] = $cid;
            }
        }


        if (function_exists('apcu_store')) {
            dtimer::log(__METHOD__ . " update categories APCU");
            apcu_store($this->config->host . 'all_categories', $ptr, 7200);
        }

        unset($ptr[0]); //unset root element
        $this->all_categories = &$ptr;
        $this->categories_uri = &$tree['uri'];
        $this->categories_tree = &$tree['subcategories'];

    }


}