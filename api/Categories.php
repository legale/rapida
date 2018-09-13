<?php


require_once('Simpla.php');

class Categories extends Simpla
{
    // Список указателей на категории в дереве категорий (ключ = id категории)
    public $all_categories;
    // Дерево категорий
    public $categories_tree;

    public function __construct()
    {
        $this->init_categories(false);
    }

    // Функция возвращает массив категорий
    public function get_categories($filter = array())
    {
        if ($this->all_categories && isset($filter['reinit'])) {
            $this->init_categories(true);
        }


        if (!empty($filter['product_id'])) {
            $query = $this->db->placehold("SELECT category_id FROM __products_categories WHERE product_id in(?@) ", (array)$filter['product_id']);
            $this->db->query($query);
            $categories_ids = $this->db->results_array('category_id');
            $result = array();
            if (!empty($categories_ids)) {
                foreach ($categories_ids as $id)
                    if (isset($this->all_categories[$id]))
                        $result[$id] = $this->all_categories[$id];
                return $result;
            }
        }

        return $this->all_categories;
    }

    // Функция возвращает id категорий для заданного товара
    public function get_product_categories($product_id)
    {
        $query = $this->db->placehold("SELECT * FROM __products_categories WHERE product_id in(?@)", (array)$product_id);
        $this->db->query($query);
        return $this->db->results_array(null, 'category_id');
    }

    // Функция возвращает id категорий для всех товаров
    public function get_products_categories()
    {
        $query = $this->db->placehold("SELECT * FROM __products_categories");
        $this->db->query($query);
        $res = $this->db->results_array(null, 'category_id');
        return $res;
    }


    // Функция возвращает заданную категорию
    public function get_category($id)
    {
        dtimer::log(__METHOD__ . " start: $id");
        if ($id == strval((int)$id)) {
            $id = (int)$id;
        } else {
            $id = $id;
        }

        if (is_int($id) && array_key_exists($id, $this->all_categories))
            return $category = $this->all_categories[$id];
        else if (is_string($id)) {
            foreach ($this->all_categories as $cat)
                if ($cat['trans'] == $id || $cat['trans2'] == $id) {
                    return $cat;
                }
        }

        return false;
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
    private function init_categories($reinit = false)
    {
        dtimer::log(__METHOD__ . " start reinit flag: " . var_export($reinit, true));
        if ($reinit === false && function_exists('apcu_fetch')) {
            dtimer::log(__METHOD__ . " ACPU CACHE CATEGORIES READ ");
            $this->all_categories = apcu_exists($this->config->host . 'all_categories') ? apcu_fetch($this->config->host . 'all_categories') : null;
            $this->categories_tree = &$this->all_categories[0]['subcategories'];
            unset($this->all_categories[0]); //remove root element
            if ($this->all_categories) {
                return;
            }
        }

        // Дерево категорий
        $tree = [];
        $tree['subcategories'] = [];
        $tree['path'] = [];
        $tree['level'] = 0;
        $tree['visible_count'] = 0;


        // Выбираем все категории
        $query = $this->db->placehold("SELECT * FROM __categories");

        $this->db->query($query);
        $cats = $this->db->results_array(null, 'id');

        // Указатели на узлы дерева
        $cats[0] = &$tree;


        // Проходим все выбранные категории
        foreach ($cats as $cid => &$cat) {
            if ($cid === 0) {
                continue; //skip root element with index 0
            }
            $cat['visible_count'] = 0;
            $cat['children'] = [];
            $cat['vchildren'] = [];

            if ($cat['visible']) {
                ++$cats[$cat['parent_id']]['visible_count'];
            }

            $cats[$cat['parent_id']]['subcategories'][] = &$cat;
            $cats[$cat['parent_id']]['children'][] = $cid;
            $cats[$cat['vparent_id']]['vchildren'][] = $cid;

            $cats[$cat['id']]['path'] = array_merge($cats[$cat['parent_id']]['path'], array(&$cats[$cid]));

            // Уровень вложенности категории
            $cats[$cat['id']]['level'] = ++$cats[$cat['parent_id']]['level'];
        }

        if (function_exists('apcu_store')) {
            dtimer::log(__METHOD__ . " update categories APCU");
            apcu_store($this->config->host . 'all_categories', $cats, 7200);
        }
        unset($cat, $cats[0]); //unset root element

        $this->all_categories = &$cats;
        $this->categories_tree = &$tree['subcategories'];

    }


}