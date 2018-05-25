<?PHP

/**
 *
 * Этот класс использует шаблон product.tpl
 *
 */

require_once('View.php');


class ProductView extends View
{

    function fetch()
    {
        $product_url = $this->root->uri_arr['path']['url'];

        if (empty($product_url)) {
            return false;
        }

        // Выбираем товар из базы
        $product = $this->products->get_product((string)$product_url);
        dtimer::log(__METHOD__ . " product found " . $product['id']);

        //301 moved permanently
        if (isset($product['trans2']) && $product['trans2'] === $product_url) {
            $root = $this->config->root_url . '/';
            $path = $this->root->uri_arr['path']['module'] . '/';
            $url = $root . $path . $product['trans'];
            header("Location: $url", TRUE, 301);
        }


        if (empty($product)) {
            return false;
        }

        //добавляем просмотр товару
        $this->products->add_view($product['id']);

        //картинки
        $product['images'] = $this->image->get('products', array('item_id' => $product['id']));
        array_shift($product['images']);

        //варианты
        $product['variants'] = $this->variants->get_variants(array('product_id' => $product['id'], 'in_stock' => true));

        // Свойства товара
        //~ $features = $this->features->get_features();
        $ogroups = $this->features->get_options_tree();
        //~ print_r($ogroups);
        $product['options'] = $this->features->get_product_options($product['id']);


        $this->design->assign('ogroups', $ogroups);
        //~ $this->design->assign('features', $features);


        // Автозаполнение имени для формы комментария
        if (!empty($this->user->name)) {
            $this->design->assign('comment_name', $this->user['name']);
        } else {
            $this->design->assign('comment_name', '');
        }

        // заводим в шаблон пустую переменную error, чтобы не вышибало ошибку, когда переменная не задана
        $this->design->assign('error', '');

        // Принимаем комментарий
        if ($this->request->method('post') && $this->request->post('comment')) {
            $comment = array();
            $comment['name'] = $this->request->post('name');
            $comment['text'] = $this->request->post('text');
            $captcha_code = $this->request->post('captcha_code', 'string');


            // Проверяем капчу и заполнение формы
            if ($_SESSION['captcha_code'] != $captcha_code || empty($captcha_code)) {
                $this->design->assign('error', 'captcha');
            } elseif (empty($comment['name'])) {
                $this->design->assign('error', 'empty_name');
            } elseif (empty($comment['text'])) {
                $this->design->assign('error', 'empty_comment');
            } else {
                // Создаем комментарий
                $comment['object_id'] = $product['id'];
                $comment['type'] = 'product';
                $comment['ip'] = $_SERVER['REMOTE_ADDR'];

                // Если были одобренные комментарии от текущего ip, одобряем сразу
                $this->db->query("SELECT 1 FROM __comments WHERE approved=1 AND ip=? LIMIT 1", $comment['ip']);
                if ($this->db->num_rows() > 0)
                    $comment['approved'] = 1;

                // Добавляем комментарий в базу
                $comment_id = $this->comments->add_comment($comment);

                // Отправляем email
                $this->notify->email_comment_admin($comment_id);

                // Приберем сохраненную капчу, иначе можно отключить загрузку рисунков и постить старую
                unset($_SESSION['captcha_code']);
                header('location: ' . $_SERVER['REQUEST_URI'] . '#comment_' . $comment_id);
            }
        }

        // Передадим комментарий обратно в шаблон - при ошибке нужно будет заполнить форму
        $this->design->assign('comment_text', isset($comment['text']) ? $comment['text'] : '');
        $this->design->assign('comment_name', isset($comment['name']) ? $comment['name'] : '');

        // Связанные товары
        $rp_ids = array();
        if ($rp = $this->products->get_related_products($product['id'])) {
            $rp_ids = array_keys($rp);
        }
        if (!empty($rp_ids)) {
            $rp = $this->products->get_products(array('id' => $rp_ids, 'in_stock' => 1, 'visible' => 1));
        }

        if (!empty($rp)) {
            $rp_variants = $this->variants->get_variants(array('product_id' => array_keys($rp), 'in_stock' => 1));

            foreach ($rp_variants as $rp_variant) {
                if (isset($rp[$rp_variant['product_id']])) {
                    $rp[$rp_variant['product_id']]['variants'][] = $rp_variant;
                }
            }

            foreach ($rp as $id => $r) {
                if (is_array($r)) {
                    $r['variant'] = &$r['variants'][0];
                } else {
                    unset($rp[$id]);
                }
            }
        }

        //заводим в шаблон связанные товары
        $this->design->assign('related_products', isset($rp) ? $rp : '');

        // Отзывы о товаре
        $comments = $this->comments->get_comments(array('type' => 'product', 'object_id' => $product['id'], 'approved' => 1, 'ip' => $_SERVER['REMOTE_ADDR']));

        // Соседние товары
        $this->design->assign('next_product', $this->products->get_next_product($product['id']));
        $this->design->assign('prev_product', $this->products->get_prev_product($product['id']));

        // И передаем его в шаблон
        $this->design->assign('product', $product);
        $this->design->assign('comments', $comments);

        // Категория и бренд товара
        $cat = $this->categories->get_category((int)$product['cat_id']);
        $brand = $this->brands->get_brand((int)$product['brand_id']);
        $this->design->assign('brand', $brand);
        $this->design->assign('cat', $cat);


        // Добавление в историю просмотров товаров
        $max_visited_products = 100; // Максимальное число хранимых товаров в истории
        $expire = time() + 60 * 60 * 24 * 30; // Время жизни - 30 дней
        if (!empty($_COOKIE['browsed_products'])) {
            $browsed_products = explode(',', $_COOKIE['browsed_products']);
            // Удалим текущий товар, если он был
            if (($exists = array_search($product['id'], $browsed_products)) !== false)
                unset($browsed_products[$exists]);
        }
        // Добавим текущий товар
        $browsed_products[] = $product['id'];
        $cookie_val = implode(',', array_slice($browsed_products, -$max_visited_products, $max_visited_products));
        setcookie("browsed_products", $cookie_val, $expire, "/");

//        print "<PRE>";
//        print_r($pairs);
//        print_r($features);
//        print_r($product['options']);
//        print "</PRE>";


        //авто теги
        $fids = array_keys($product['options']);
        if (!empty($fids)) {
            $features = $this->features->get_features(array('id' => $fids));
        }

        foreach ($features as $fid => $f) {
            if ((bool)$f['tpl']) {
                $pairs['{$' . $f['trans'] . '}'] = $product['options'][$fid]['val'];
            }
        }
        //добавляем переменную {$category}
        $pairs['{$category}'] = $cat['name'];
        $pairs['{$brand}'] = $brand['name'];


        //берем шаблон для тега из категории, если есть, или сразу готовый текст из товара
        $meta_title = $cat['auto_meta_title'] ? $cat['auto_meta_title'] : $product['meta_title'];
        $meta_keywords = $cat['auto_meta_keywords'] ? $cat['auto_meta_keywords'] : $product['meta_keywords'];
        $meta_description = $cat['auto_meta_description'] ? $cat['auto_meta_description'] : $product['meta_description'];
        $annotation = $cat['auto_annotation'] ? $cat['auto_annotation'] : $product['annotation'];
        $description = $cat['auto_description'] ? $cat['auto_description'] : $product['description'];

        //производим замену

        $pat = '/\{\$.+\}/u';//шаблон для удаление неиспользованных переменных
        $meta_title = preg_replace($pat, '',  strtr($meta_title, $pairs));
        $meta_keywords = preg_replace($pat, '',  strtr($meta_keywords, $pairs));
        $meta_description = preg_replace($pat, '',  strtr($meta_description, $pairs));
        $annotation = preg_replace($pat, '',  strtr($annotation, $pairs));
        $description = preg_replace($pat, '',  strtr($description, $pairs));


        $this->design->assign('meta_title', $meta_title);
        $this->design->assign('meta_keywords', $meta_keywords);
        $this->design->assign('meta_description', $meta_description);
        $this->design->assign('annotation', $annotation);
        $this->design->assign('description', $description);

        return $this->design->fetch('product.tpl');
    }


}
