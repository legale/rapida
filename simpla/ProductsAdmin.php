<?PHP

require_once('api/Simpla.php');

class ProductsAdmin extends Simpla
{
    function fetch()
    {

        $filter = array();
        //добавим в фильтр параметр, чтобы получить данные без кеша
        $filter['force_no_cache'] = true;

        $filter['page'] = max(1, $this->request->get('page', 'integer'));

        $filter['limit'] = $this->settings->products_num_admin;

        // Категории
        $categories = $this->categories->categories_tree;
        $this->design->assign('categories', $categories);

        // Текущая категория
        $category_id = $this->request->get('category_id', 'integer');
        if ($category_id && $category = $this->categories->get_category($category_id))
            $filter['category_id'] = $category['children'];

        // Бренды категории
        $brands = $this->brands->get_brands(array('category_id' => $category_id));
        $this->design->assign('brands', $brands);

        // Все бренды
        $all_brands = $this->brands->get_brands();
        $this->design->assign('all_brands', $all_brands);

        // Текущий бренд
        $brand_id = $this->request->get('brand_id', 'integer');
        if ($brand_id && $brand = $this->brands->get_brand($brand_id))
            $filter['brand_id'] = $brand->id;

        // Текущий фильтр
        if ($f = $this->request->get('filter', 'string')) {
            if ($f == 'featured')
                $filter['featured'] = 1;
            elseif ($f == 'discounted')
                $filter['discounted'] = 1;
            elseif ($f == 'visible')
                $filter['visible'] = 1;
            elseif ($f == 'hidden')
                $filter['visible'] = 0;
            elseif ($f == 'outofstock')
                $filter['in_stock'] = 0;
            elseif ($f == 'no_images')
                $filter['no_images'] = 1;
            $this->design->assign('filter', $f);
        }

        // Поиск
        $keyword = $this->request->get('keyword');
        if (!empty($keyword)) {
            $filter['keyword'] = $keyword;
            $this->design->assign('keyword', $keyword);
        }

        // Обработка действий
        if ($this->request->method('post')) {
            // Сохранение цен и наличия
            $prices = $this->request->post('price');
            $stocks = $this->request->post('stock');

            if ($prices) {
                foreach ($prices as $id => $price) {
                    $stock = $stocks[$id];
                    if ($stock == '∞' || $stock == '')
                        $stock = null;

                    $this->variants->update_variant(array('id' => $id, 'price' => $price, 'stock' => $stock));
                }
            }

            // Сортировка
            $poss = $this->request->post('poss');
            $cur_poss = $this->request->post('cur_poss');
//            print_r($_POST);
            sort($cur_poss);
            $min_pos = reset($cur_poss);
            $poss = array_reverse($poss);
            foreach ($poss as $pos => $pid) {
                $this->products->update_product($pid, array('pos' => (int)$min_pos + (int)$pos));
            }


            // Действия с выбранными
            $ids = $this->request->post('check');
            if (!empty($ids))
                switch ($this->request->post('action')) {
                    case 'disable':
                        {
                            $this->products->update_product($ids, array('visible' => 0));
                            break;
                        }
                    case 'enable':
                        {
                            $this->products->update_product($ids, array('visible' => 1));
                            break;
                        }
                    case 'set_featured':
                        {
                            $this->products->update_product($ids, array('featured' => 1));
                            break;
                        }
                    case 'unset_featured':
                        {
                            $this->products->update_product($ids, array('featured' => 0));
                            break;
                        }
                    case 'delete':
                        {
                            foreach ($ids as $id)
                                $this->products->delete_product($id);
                            break;
                        }
                    case 'duplicate':
                        {
                            foreach ($ids as $id)
                                $this->products->duplicate_product(intval($id));
                            break;
                        }
                    case 'move_to_page':
                        {
                            $target_page = $this->request->post('target_page', 'integer');


                            break;
                        }
                    case 'move_to_category':
                        {
                            $category_id = $this->request->post('target_category', 'integer');
                            $filter['page'] = 1;
                            $category = $this->categories->get_category($category_id);
                            $filter['category_id'] = $category['children'];

                            foreach ($ids as $id) {
                                $query = $this->db->placehold("DELETE FROM __products_categories WHERE category_id=? AND product_id=? LIMIT 1", $category_id, $id);
                                $this->db->query($query);
                                $query = $this->db->placehold("UPDATE IGNORE __products_categories set category_id=? WHERE product_id=? ORDER BY pos DESC LIMIT 1", $category_id, $id);
                                $this->db->query($query);
                                if ($this->db->affected_rows() == 0)
                                    $query = $this->db->query("INSERT IGNORE INTO __products_categories set category_id=?, product_id=?", $category_id, $id);

                            }
                            break;
                        }
                    case 'move_to_brand':
                        {
                            $brand_id = $this->request->post('target_brand', 'integer');
                            $brand = $this->brands->get_brand($brand_id);
                            $filter['page'] = 1;
                            $filter['brand_id'] = $brand_id;
                            $query = $this->db->placehold("UPDATE __products set brand_id=? WHERE id in (?@)", $brand_id, $ids);
                            $this->db->query($query);

                            // Заново выберем бренды категории
                            $brands = $this->brands->get_brands(array('category_id' => $category_id));
                            $this->design->assign('brands', $brands);

                            break;
                        }
                }
        }

        // Отображение
        if (isset($brand))
            $this->design->assign('brand', $brand);
        if (isset($category))
            $this->design->assign('category', $category);

        $products_count = $this->products->count_products($filter);
        // Показать все страницы сразу
        if ($this->request->get('page') == 'all')
            $filter['limit'] = $products_count;

        if ($filter['limit'] > 0)
            $pages_count = ceil($products_count / $filter['limit']);
        else
            $pages_count = 0;
        $filter['page'] = min($filter['page'], $pages_count);
        $this->design->assign('products_count', $products_count);
        $this->design->assign('pages_count', $pages_count);
        $this->design->assign('current_page', $filter['page']);

        //~ print_r($filter);
        $products = $this->products->get_products($filter);


        if (!empty($products)) {

            // Товары
            $products_ids = array_keys($products);
            foreach ($products as &$product) {
                $product['variants'] = array();
                $product['properties'] = array();
            }


            if ($variants = $this->variants->get_variants(array('product_id' => $products_ids))) {
                foreach ($variants as &$variant) {
                    $products[$variant['product_id']]['variants'][] = $variant;
                }
            }

        }

        $this->design->assign('products', $products);

        return $this->design->fetch('products.tpl');
    }
}
