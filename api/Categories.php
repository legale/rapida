<?php

/**
 * Simpla CMS
 *
 * @copyright    2011 Denis Pikusov
 * @link        http://simplacms.ru
 * @author        Denis Pikusov
 *
 */

require_once('Simpla.php');

class Categories extends Simpla
{
    // Список указателей на категории в дереве категорий (ключ = id категории)
    private $all_categories;
    // Дерево категорий
    private $categories_tree;

    // Функция возвращает массив категорий
    public function get_categories($filter = array())
    {
        $reinit = isset($filter['reinit']) ? true : false;

        if (!isset($this->categories_tree)) {
            $this->init_categories($reinit);
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

    // Функция возвращает дерево категорий
    public function get_categories_tree($reinit = false)
    {
        dtimer::log(__METHOD__ . " start reinit: " . var_export($reinit, true));
        if (!isset($this->categories_tree))
            $this->init_categories($reinit);

        return $this->categories_tree;
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

        if (!isset($this->all_categories)) {
            $this->init_categories();
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
        unset($this->categories_tree);
        unset($this->all_categories);
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

    // Удалить изображение категории
    public function delete_image($categories_ids)
    {
        $categories_ids = (array)$categories_ids;
        $query = $this->db->placehold("SELECT image FROM __categories WHERE id in(?@)", $categories_ids);
        $this->db->query($query);
        $filenames = $this->db->results_array('image');
        if (!empty_($filenames)) {
            $query = $this->db->placehold("UPDATE __categories SET image=NULL WHERE id in(?@)", $categories_ids);
            $this->db->query($query);
            foreach ($filenames as $filename) {
                if (!empty_($filename)) {
                    $query = $this->db->placehold("SELECT count(*) as count FROM __categories WHERE image=?", $filename);
                    $this->db->query($query);
                    $count = $this->db->result_array('count');
                    if ($count == 0) {
                        @unlink($this->config->root_dir . $this->config->categories_images_dir . $filename);
                    }
                }
            }
            unset($this->categories_tree);
            unset($this->all_categories);
        }
    }


    // Инициализация категорий, после которой категории будем выбирать из локальной переменной
    private function init_categories($reinit = false)
    {
        dtimer::log(__METHOD__ . " start reinit flag: " . var_export($reinit, true));
        if ($reinit === false && function_exists('apcu_fetch')) {
            dtimer::log(__METHOD__ . " ACPU CACHE CATEGORIES READ ");
            $this->categories_tree = apcu_exists('categories_tree') ? apcu_fetch('categories_tree') : null;
            $this->all_categories = apcu_exists('categories_tree') ? apcu_fetch('all_categories') : null;
            if ($this->categories_tree && $this->all_categories) {
                return;
            }
        }

        // Дерево категорий
        $tree = array();
        $tree['subcategories'] = array();

        // Указатели на узлы дерева
        $pointers = array();
        $pointers[0] = &$tree;
        $pointers[0]['path'] = array();
        $pointers[0]['level'] = 0;
        $pointers[0]['visible_count'] = 0;

        // Выбираем все категории
        $query = $this->db->placehold("SELECT * FROM __categories c ORDER BY c.parent_id, c.pos");

        $this->db->query($query);
        $categories = $this->db->results_array(null, 'id');

        $finish = false;
        // Не кончаем, пока не кончатся категории, или пока ни одну из оставшихся некуда приткнуть
        while (!empty($categories) && !$finish) {
            $flag = false;
            // Проходим все выбранные категории
            foreach ($categories as $k => $category) {
                if (isset($pointers[$category['parent_id']])) {
                    // В дерево категорий (через указатель) добавляем текущую категорию
                    $pointers[$category['id']] = $category;
                    $pointers[$category['id']]['visible_count'] = 0;
                    $pointers[$category['parent_id']]['subcategories'][] = &$pointers[$category['id']];
                    //~ print_r($pointers);
                    // Путь к текущей категории
                    $curr = &$pointers[$category['id']];
                    $pointers[$category['id']]['path'] = array_merge($pointers[$category['parent_id']]['path'], array(&$curr));

                    // Уровень вложенности категории
                    $pointers[$category['id']]['level'] = 1 + $pointers[$category['parent_id']]['level'];

                    // Убираем использованную категорию из массива категорий
                    unset($categories[$k]);
                    $flag = true;
                }
            }
            if (!$flag) {
                $finish = true;
            }
        }
        //~ print_r($pointers);

        // Для каждой категории id всех ее деток узнаем
        $ids = array_reverse(array_keys($pointers));
        foreach ($ids as $id) {
            if (!empty($pointers[$id]['visible'])) {
                $pointers[$pointers[$id]['parent_id']]['visible_count']++;
            }

            if ($id > 0) {
                $pointers[$id]['children'][] = $id;

                if (isset($pointers[$pointers[$id]['parent_id']]['children'])) {
                    $pointers[$pointers[$id]['parent_id']]['children'] = array_merge($pointers[$id]['children'], $pointers[$pointers[$id]['parent_id']]['children']);
                } else {
                    $pointers[$pointers[$id]['parent_id']]['children'] = $pointers[$id]['children'];
                }
            }
        }
        unset($pointers[0]);
        unset($ids);


        $this->categories_tree = $tree['subcategories'];
        $this->all_categories = $pointers;

        if (function_exists('apcu_store')) {
            dtimer::log(__METHOD__ . " update categories APCU");
            apcu_store('categories_tree', $tree['subcategories'], 7200);
            apcu_store('all_categories', $pointers, 7200);
        }
    }
}
