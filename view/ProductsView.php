<?PHP

/**
 * Этот класс использует шаблон products.tpl
 *
 */

require_once('View.php');

class ProductsView extends View
{
    public $filter = array();

    public function __construct()
    {
        dtimer::log(__METHOD__ . " start");
    }


    //метод для получения страницы
    public function fetch()
    {
        $canonical = null;
        $noindex = null;
        $nofollow = null;
        $robots = null;

        //url категории
        if (isset($this->root->uri_arr['path']['url'])) {
            $this->filter['category_url'] = $this->root->uri_arr['path']['url'];
        } else if (isset($this->root->uri_arr['query']['keyword'])) {
            if (!empty($this->root->uri_arr['query']['cat'])) {
                $this->filter['category_url'] = $this->root->uri_arr['query']['cat'];
            }
        } else {
            dtimer::log(__METHOD__ . " category url is not set! aborting.", 1);
            return false;
        }

        //получаем категорию

        if (isset($this->filter['category_url'])) {
            dtimer::log(__METHOD__ . __LINE__ . " " . $this->filter['category_url']);
            $cat = $this->categories->get_category($this->filter['category_url']);

            //Остановимя если категории не существует или категория невидимая, а сессия не админская
            if (!$cat) {
                dtimer::log(__METHOD__ . __LINE__ . " category is not exists ", 2);
                return false;
            } else if (!$cat['enabled'] && !isset($_SESSION['admin'])) {
                dtimer::log(__METHOD__ . __LINE__ . " disabled category. available only for admin session. ", 2);
                return false;
            }
        }


        //REDIRECT
        //проверяем альтернативное имя
        //301 moved permanently
        if (isset($cat) && isset($cat['trans2']) && $cat['trans'] !== $cat['trans2'] && $cat['trans2'] == $this->filter['category_url']) {
            $arr = $this->root->uri_arr['path'];
            $arr['url'] = $cat['trans'];
            $url = '/' . $this->root->gen_uri($arr);
            header("Location: $url", TRUE, 301);
        }

        //преобразуем и запишем себе разобранную адресную строку в виде фильтра, пригодного для api
        $this->filter = $this->uri_to_api_filter($this->root->uri_arr, $this->filter);
        if (!$this->filter) {
            return false;
        }


        if (isset($this->filter['keyword'])) {
            $this->design->assign('keyword', $this->filter['keyword']);

        } else if (isset($cat)) {
            //добавляем в фильтр все дочерние категории
            $this->filter['category_id'] = $cat['children'];
        }

        // Кол-во товаров на странице
        $this->filter['limit'] = $this->settings->products_num;

        // Вычисляем количество страниц
        dtimer::log(__METHOD__ . " Вычисляем кол-во страниц");
        $this->filter['products_count'] = $this->products->count_products($this->filter);
        dtimer::log("count products result: " . $this->filter['products_count']);
        if ($this->filter['products_count'] === 0) {
            dtimer::log("0 products found. return 404", 2);
            return false;
        }

        //если товаров в разделе нет, сделаем кол-во страниц 1, иначе будет 0
        $this->filter['pages'] = max(1, ceil($this->filter['products_count'] / $this->filter['limit']));

        $this->filter['page'] = isset($this->root->uri_arr['path']['page']) ? $this->root->uri_arr['path']['page'] : 1;

        if (isset($this->filter['redirect'])) {
            $uri = $this->root->gen_uri_from_filter($this->root->uri_arr, $this->filter);
            header("Location: $uri", TRUE, 301);
        } else if ($this->filter['page'] > $this->filter['pages']) {
            $this->filter['page'] = $this->filter['pages'];
            $uri = $this->root->gen_uri_from_filter($this->root->uri_arr, $this->filter);
            header("Location: $uri", TRUE, 301);
        } else if ($this->filter['page'] < 1 || (isset($this->root->uri_arr['path']['page']) && $this->root->uri_arr['path']['page'] == 1)) {
            $this->filter['page'] = 1;
            $uri = $this->root->gen_uri_from_filter($this->root->uri_arr, $this->filter);
            header("Location: $uri", TRUE, 301);
        }


        //REDIRECT END

        //сделаем массив для пагинации
        $range = 12;
        $total = $this->filter['pages'];
        $page = $this->filter['page'];
        $first = max(1, $page - $range / 2);
        $last = min($first + $range, $total);
        $this->filter['nav']['first'] = $first;
        $this->filter['nav']['last'] = $last;
        $this->filter['nav']['left'] = $page > 1 ? $page - 1 : null;
        $this->filter['nav']['right'] = $last > $page ? $page + 1 : null;


        // Товары получаем их сразу массивом
        $products = $this->products->get_products($this->filter);

        //добавим варианты
        if (!empty($products)) {
            $pids = array_keys($products);
            $variants = $this->variants->get_variants(array(
                'grouped' => 'product_id',
                'product_id' => $pids
            ));

            if (is_array($products)) {
                foreach ($products as $pid => &$product) {
                    $product['variants'] = isset($variants[$pid]) && is_array($variants[$pid]) ? $variants[$pid] : array();
                }
            }
        }


        $this->design->assign('products', $products);


        // Выбираем бренды, они нужны нам в шаблоне
        $brand_filter['visible'] = 1;
        if (isset($cat['children'])) {
            $brand_filter['category_id'] = $cat['children'];
        }
        $brands = $this->brands->get_brands($brand_filter);

        //~ print_r($brands);
        //тут запилим все бренды (надо оценить необходимость)
        $cat['brands'] = $brands;
        //Если выбран только 1 бренд, запилим его в шаблон
        if (isset($this->filter['brand_id']) && count($this->filter['brand_id']) === 1) {
            $bid = reset($this->filter['brand_id']);
            if (isset($brands[$bid])) {
                $cat['brand'] = $brands[$bid];
            } else {
                dtimer::log(__METHOD__ . ' brand_id $bid not found for this category ', 1);
                return false;
            }
        }


//        print_r($cat['brand']);

        // Свойства товаров
        //получим включенные для фильтра на сайте свойства товаров для конкретной категории
        $filter['in_filter'] = 1;
        if (isset($cat['id'])) {
            $filter['category_id'] = $cat['id'];
        }
        $features = $this->features->get_features($filter);
        if ($features) {
            $this->filter['feature_id'] = array_keys($features);
            $this->design->assign('features', $features);
        }

        if ($options = $this->features->get_options_mix($this->filter)) {
            $this->design->assign('options', $options);
        }


        //тут записываем выбранные фильтры в отдельную переменную
        $meta_filter = array();
        $selected_count = 0;
        //Сначала бренды
        if (isset($this->filter['brand_id'])) {
            $selected_count += count($this->filter['brand_id']);
            if ($selected_count > 1) {//если выбрано больше 1 бренда
                $noindex = true;
                $canonical = true;
            }
            foreach ($this->filter['brand_id'] as $bid) {
                if (isset($brands[$bid]['name'])) {
                    $meta_filter[] = $brands[$bid]['name'];
                }
            }
        }

        //теперь свойства
        if (isset($this->filter['features'])) {
            $selected_count += count($this->filter['features']);

            foreach ($this->filter['features'] as $fid => $vids) {
                if (!$canonical && count($vids) > 1) {
                    $noindex = true;
                    $canonical = true;
                }


                if (isset($features[$fid]['name']) && isset($options['full'][$fid]['vals'])) {
                    $vals_text = array_intersect_key($options['full'][$fid]['vals'], $vids);
                    if (empty($vals_text)) {
                        continue;
                    }
                    $vals_string = implode(', ', $vals_text);
                    $meta_filter[] = $features[$fid]['name'] . ' ' . $vals_string;
                }
            }
        }

        $meta_filter = implode(' - ', $meta_filter);

        //посчитаем выбранные в фильтре параметры и сделаем nofollow, если нужно
        if (!$noindex && $selected_count > 3) {
            $noindex = true;
            $canonical = true;
        }


        //кладем meta_filter в обычный фильтр, чтобы можно было видеть его из браузера.
        $this->filter['meta_filter'] = $meta_filter;

        //~ // Свойства товаров END
        if ($this->filter['page'] !== 1) {
            $noindex = true;
        }

        //ставим flag canonical <link rel="canonical" href="http://site.com/canonical-link.html"/>
        if ($canonical) {
            $filter = $this->filter;
            $filter['page'] = 1; //ставим страницу 1
            unset($filter['price']); //удаляем фильтр цены
            if (isset($filter['brand_id']) && count($filter['brand_id']) > 1) { //удаляем если больше 1 бренда
                unset($filter['brand_id']);
            }
            if (isset($filter['features'])) {
                if (count($filter['features']) > 3) {
                    unset($filter['features']);
                } else {
                    foreach ($filter['features'] as $fid => $vids) {
                        if (count($vids) > 1) {
                            unset($filter['features'][$fid]); //убираем все фильтры, где больше 1 элемента
                        }
                    }
                }
            }


            $uri = $this->root->gen_uri_from_filter($this->root->uri_arr, $filter);
            $canonical = $uri;
            header("Link: <$uri>; rel=\"canonical\"", TRUE);
        } else if (isset($this->filter['keyword'])) {
            $canonical = $this->config->root_url;
        }

        if ($noindex || $nofollow) {
            $sum = $noindex ? 1 : 0;
            $sum += $nofollow ? 2 : 0;
            switch ($sum) {
                case 3:
                    $robots = 'noindex, nofollow';
                    break;
                case 2:
                    $robots = 'index, nofollow';
                    break;
                case 1:
                    $robots = 'noindex, follow';
                    break;
            }
        }

        $this->design->assign('robots', $robots);
        $this->design->assign('canonical', $canonical);


        //передаем фильтр
        $this->design->assign('filter', $this->filter);

        //ajax
        if (isset($_GET['ajax'])) {
            $html = $this->design->fetch('products_content.tpl');
            print json_encode($html);
            die;
        }


        if (isset($cat['id'])) {
            $auto_meta_title = $cat['meta_title'];
            $auto_meta_keywords = $cat['meta_keywords'];
            $auto_meta_description = $cat['meta_description'];

            $pairs = array(
                '{$meta_filter}' => $meta_filter,
                '{$category}' => $cat['name'] ? $cat['name'] . ' ' : '',
                '{$products_count}' => $this->filter['products_count'] . ' ',
                '{$sitename}' => $this->settings->site_name ? $this->settings->site_name . ' ' : '',
//                '{$filter}' => $cat['meta_title'] ? $cat['meta_title'] .' ' : '' ,
            );
            if (is_array($features)) {
                foreach ($features as $fid => $f) {
                    if ($f['tpl'] == 0) {
                        continue;
                    }
                    $pairs['{$' . $f['trans'] . '}'] = " " . $f['name'];
                    $pairs['{$' . $f['trans'] . '_list}'] = '';
                    //$cycler = 0;
                    if (isset($options['full'][$fid]['vals']) && is_array($options['full'][$fid]['vals'])) {
                        $pairs['{$' . $f['trans'] . '_list}'] = " " . implode(", ", array_slice($options['full'][$fid]['vals'], 0, 3));
                    }
                }
            }

            $pat = '/\{\$.+\}/u';//шаблон для удаление неиспользованных переменных

            $auto_meta_title = preg_replace($pat, '', strtr($auto_meta_title, $pairs));
            $auto_meta_keywords = preg_replace($pat, '', strtr($auto_meta_keywords, $pairs));
            $auto_meta_description = preg_replace($pat, '', strtr($auto_meta_description, $pairs));

            // добавим слова страница № для страниц с пагинацией
            if ($this->filter['page'] > 1) {
                $page = $this->filter['page'];
                $p_num_txt = "-$page- ";
                $auto_meta_title = $p_num_txt . $auto_meta_title;
                $auto_meta_description = $p_num_txt . $auto_meta_description;
            }
        }
        dtimer::log(__METHOD__ . ' after auto meta tags ' . __LINE__);
        //Автоматическая генерация мета тегов для категории END


        //передаем данные в шаблоны
        if (isset($cat['id'])) {
            $this->design->assign('category', $cat);
            $this->design->assign('meta_filter', $meta_filter);
            $this->design->assign('meta_title', $auto_meta_title);
            $this->design->assign('meta_keywords', $auto_meta_keywords);
            $this->design->assign('meta_description', $auto_meta_description);
        }

        $this->body = $this->design->fetch('products.tpl');
        dtimer::log(__METHOD__ . " return ");
        return $this->body;
    }


//функция по обработке фильтров из адресной строки и преобразованию их в фильтр для api
    private function uri_to_api_filter($uri_arr, $filter)
    {
        // Если задано ключевое слово
        if (isset($uri_arr['query']['keyword'])) {

            $filter['keyword'] = str_replace('+', ' ', $uri_arr['query']['keyword']);
        }

        $uri_path = isset($uri_arr['path']) ? $uri_arr['path'] : null;
        if (!isset($uri_path)) {
            dtimer::log(__METHOD__ . " uri_arr['path'] is not set - returning filter unchanged ");
            return $filter;
        }
        if (isset($uri_arr['module'])) {
            $filter['module'] = $uri_arr['module'];
        }

        //если задан фильтр по свойствам
        if (isset($uri_path['features'])) {
            //если не получается преобразовать обычные имена - пробуем альтернативные (повторная попытка происходит внутри функции)
            $filter = $this->uri_to_ids_filter($uri_path['features'], $filter);
            if (!$filter) {
                return false;
            }
        }


        //Если есть бренд
        if (isset($uri_path['brand'])) {
            //если не получается преобразовать обычные имена - пробуем альтернативные (повторная попытка происходит внутри функции)
            $filter = $this->uri_brand_to_ids_filter($uri_path['brand'], $filter);
            if (!$filter) {
                return false;
            }

        }

        //Если есть цена
        if (isset($uri_path['price'])) {
            $filter['price'] = array_flip($uri_path['price']);
        }


        //сортировка
        if (isset($uri_path['sort'])) {
            $filter['sort'] = $uri_path['sort'];
        }
        dtimer::log(__METHOD__ . " return: " . var_export($filter, true));
        return $filter;
    }

//функция для преобразования ЧПУ части uri с брендом $uri_path['brand']
//флаг служит для задания преобразования по альтернативным названиям параметров trans2
    private function uri_brand_to_ids_filter($uri_brand, $filter, $flag = false)
    {
        dtimer::log(__METHOD__ . " start " . var_export($uri_brand, true));
        dtimer::log(__METHOD__ . " filter array: " . var_export($filter, true));
        //обычный поиск просходит по полям trans в таблице features и md4 в таблице options_uniq
        //альтернативный поиск - по полям trans2 и md42 соответственно.
        $key = $flag ? 'trans2' : 'trans';
        if ($flag) {
            $filter['redirect'] = true;
        }

        //тут получим имена транслитом и id для преобразования параметров заданных в адресной строке
        $brands_trans = $this->brands->get_brands_ids(array($key => array_keys($uri_brand), 'in_filter' => 1, 'return' => array('key' => $key, 'col' => 'id')));
        if (!empty($brands_trans)) {
            $ids = array_values($brands_trans);
            $filter['brand_id'] = array_combine($ids, $ids);
        } else {
            dtimer::log(__METHOD__ . ' nothing found! return false', 2);
            //запускаем снова, если это был первый запуск
            return $flag ? false : $this->uri_brand_to_ids_filter($uri_brand, $filter, true);
        }
        dtimer::log(__METHOD__ . ' return filter["brand_id"]:' . var_export($filter['brand_id'], true));
        return $filter;
    }


//функция для преобразования ЧПУ части uri с фильтрами по свойствам $uri_path['features']
//флаг служит для задания преобразования по альтернативным названиям параметров trans2
    private function uri_to_ids_filter($uri_features, $filter, $flag = false)
    {
        dtimer::log(__METHOD__ . " start " . var_export($uri_features, true));
        dtimer::log(__METHOD__ . " filter array: " . var_export($filter, true));
        //обычный поиск просходит по полям trans в таблице features и md4 в таблице options_uniq
        //альтернативный поиск - по полям trans2 и md42 соответственно.
        $key = $flag ? 'trans2' : 'trans';


        //массив для результата
        $filter['features'] = array();

        //тут получим имена транслитом и id для преобразования параметров заданных в адресной строке
        $features_trans = $this->features->get_features_ids(array('in_filter' => 1, 'return' => array('key' => $key, 'col' => 'id')));

        //перебираем массив фильтра из адресной строки
        foreach ($uri_features as $name => $vals) {

            //если ничего не нашлось - останавливаемся
            if (!isset($features_trans[$name])) {
                dtimer::log(__METHOD__ . " feature '$name' not found! return false ", 2);
                //запускаем снова, если это был первый запуск
                return $flag ? false : $this->uri_to_ids_filter($uri_features, $filter, true);
            }
            //переворачиваем массив, потому что значения у нас идут в ключах, а нам нужно наоборот
            $vals = array_flip($vals);

            //получим id уникальных значений по их хешам
            $ids = $this->features->get_options_ids(array($key => $vals, 'return' => array('key' => 'id', 'col' => 'id')));


            //тут проверим количество переданных значений опций и количество полученных из базы,
            //если не совпадает - return false
            if ($ids === false || count($ids) !== count($vals)) {
                dtimer::log(__METHOD__ . ' given names and founded ids not equal. return false ' . var_export($ids, true) . var_export($vals, true), 2);
                //запускаем снова, если это был первый запуск
                return $flag ? false : $this->uri_to_ids_filter($uri_features, $filter, true);
            } else {
                //добавим в фильтр по свойствам массив с id значений опций
                //а также правильные названия транслитом
                if ($flag) {
                    $features_trans2 = $this->features->get_features_ids(array('in_filter' => 1, 'return' => array('key' => 'id', 'col' => 'trans2')));
                } else {
                    $features_trans2 = $this->features->get_features_ids(array('in_filter' => 1, 'return' => array('key' => 'id', 'col' => 'trans')));
                }
                $filter['translit'][$features_trans2[$features_trans[$name]]] = $this->features->get_options_ids(array('id' => $ids, 'return' => array('key' => 'trans', 'col' => 'id')));
                $filter['features'][$features_trans[$name]] = $ids;
            }
        }
        if ($flag) {
            $filter['redirect'] = true;
        }
        dtimer::log(__METHOD__ . ' return filter["features"]:' . var_export($filter['features'], true));
        return $filter;
    }

}
