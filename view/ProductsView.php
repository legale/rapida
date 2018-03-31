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
        //url категории
        if (isset($this->root->uri_arr['path']['url'])) {
            $this->filter['category_url'] = $this->root->uri_arr['path']['url'];
        } else if (isset($this->root->uri_arr['query']['cat'])){
            $this->filter['category_url'] = $this->root->uri_arr['query']['cat'];
        }else {
            dtimer::log(__METHOD__ . " category url is not set! aborting.", 1);
            return false;
        }

        //получаем категорию
        $cat = $this->categories->get_category($this->filter['category_url']);

        //Остановимя если категории не существует или категория невидимая, а сессия не админская
        if (empty($cat)) {
            dtimer::log(__METHOD__ . __LINE__ . " empty category ", 2);
            return false;
        } elseif (!$cat['visible'] && empty($_SESSION['admin'])) {
            dtimer::log(__METHOD__ . __LINE__ . " invisible category ");
            return false;
        }

        //REDIRECT
        //проверяем альтернативное имя
        //301 moved permanently
        if (isset($cat['url2']) && $cat['url2'] !== $cat['url'] && $cat['url2'] == $this->filter['category_url']) {
            $arr = $this->root->uri_arr['path'];
            $arr['url'] = $cat['url'];
            $url = '/' . $this->root->gen_uri($arr);
            //~ print_r($url);
            header("Location: $url", TRUE, 301);
        }

        //преобразуем и запишем себе разобранную адресную строку в виде фильтра, пригодного для api
        $this->filter = $this->uri_to_api_filter($this->root->uri_arr, $this->filter);
//		print_r($this->filter);

        if (isset($this->filter['redirect'])) {
            $uri = $this->root->gen_uri_from_filter($this->root->uri_arr, $this->filter);
            header("Location: $uri", TRUE, 301);
        }
        //REDIRECT END

        //добавляем в фильтр все дочерние категории
        $this->filter['category_id'] = $cat['children'];


        // Кол-во товаров на странице
        $this->filter['limit'] = $this->settings->products_num;

        // Вычисляем количество страниц
        $this->filter['products_count'] = $this->products->count_products($this->filter);
        $this->filter['pages'] = ceil($this->filter['products_count'] / $this->filter['limit']);
        $this->filter['page'] = isset($this->root->uri_arr['path']['page']) ? $this->root->uri_arr['path']['page'] : 1;
        //проверяем есть ли у нас такая страница, если нет - переправляем на последнюю из возможных
        if ($this->filter['page'] > $this->filter['pages']) {
            $this->filter['page'] = $this->filter['pages'];
            $uri = $this->root->gen_uri_from_filter($this->root->uri_arr, $this->filter);
            header("Location: $uri", TRUE, 301);
        }

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

        //ajax
        if (isset($_GET['ajax'])) {
            $html = $this->design->fetch('products_content.tpl');
            print json_encode($html);
            die;
        }

        // Выбираем бренды, они нужны нам в шаблоне

        $brands = $this->brands->get_brands(array('category_id' => $cat['children'], 'visible' => 1));

        //~ print_r($brands);
        $cat['brands'] = $brands;
        //~ print_r($cat);

        // Свойства товаров
        //получим включенные для фильтра на сайте свойства товаров для конкретной категории
        if ($features = $this->features->get_features(array('category_id' => $cat['id'], 'in_filter' => 1))) {
            $filter['feature_id'] = array_keys($features);
            $this->design->assign('features', $features);
        }

        if ($options = $this->features->get_options_mix($this->filter)) {
            $this->design->assign('options', $options);
        }
        //~ // Свойства товаров END

        //~ //передаем данные в шаблоны
        $this->design->assign('category', $cat);

        $this->design->assign('meta_title', $cat['meta_title']);
        $this->design->assign('meta_keywords', $cat['meta_keywords']);
        $this->design->assign('meta_description', $cat['meta_description']);

        $this->design->assign('filter', $this->filter);

        $this->design->assign('current_page_num', $this->filter['page']);
        $this->design->assign('total_pages_num', $this->filter['pages']);
        $this->design->assign('total_products_num', $this->filter['products_count']);

        $this->body = $this->design->fetch('products.tpl');
        dtimer::log(__METHOD__ . " return ");
        return $this->body;
    }


    //функция по обработке фильтров из адресной строки и преобразованию их в фильтр для api
    private function uri_to_api_filter($uri_arr, $filter)
    {
        // Если задано ключевое слово
        if (isset($uri_arr['query']['keyword'])) {
            $filter['keyword'] = $uri_arr['query']['keyword'];
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
            //если не получается преобразовать обычные имена - пробуем альтернативные
            if ($filter = $this->uri_to_ids_filter($uri_path['features'], $filter)) {
            } else if ($filter = $this->uri_to_ids_filter($uri_path['features'], $filter, true)) {
            } else {
                return false;
            }
        }


        //Если есть бренд
        if (isset($uri_path['brand'])) {
            $brands_ids = $this->brands->get_brands_ids(array('return' => array('col' => 'id', 'key' => 'trans')));
            $filter['brand_id'] = array_intersect_key($brands_ids, $uri_path['brand']);
        }

        //страница
        if (isset($uri_path['page'])) {
            $filter['page'] = $uri_path['page'];
        }
        //сортировка
        if (isset($uri_path['sort'])) {
            $filter['sort'] = $uri_path['sort'];
        }
        return $filter;
    }

    //функция для преобразования ЧПУ части uri с фильтрами по свойствам $uri_path['features']
    //флаг служит для задания преобразования по альтернативным названиям параметров trans2
    private function uri_to_ids_filter($uri_features, $filter, $flag = false)
    {
        //обычный поиск просходит по полям trans в таблице features и md4 в таблице options_uniq
        //альтернативный поиск - по полям trans2 и md42 соответственно.
        $key = $flag ? 'trans2' : 'trans';
        $hash = $flag ? 'md42' : 'md4';
        if ($flag) {
            $filter['redirect'] = true;
        }

        //массив для результата
        $filter['features'] = array();

        dtimer::log(__METHOD__ . " $key $hash ");
        //тут получим имена транслитом и id для преобразования параметров заданных в адресной строке
        $features_trans = $this->features->get_features_ids(array('in_filter' => 1, 'return' => array('key' => $key, 'col' => 'id')));

        //перебираем массив фильтра из адресной строки
        foreach ($uri_features as $name => $vals) {

            //если заданный в адресной строке у нас есть, получим хеш опции для поиска в таблице s_options_uniq
            if (!isset($features_trans[$name])) {
                dtimer::log(__METHOD__ . " feature '$name' not found! " . print_r($vals, true), 2);
                return false;
            }
            //~ print $name . "\n";
            foreach ($vals as $k => $v) {
                $vals[$k] = hash('md4', $k);
            }
            //~ dtimer::log(__METHOD__ . " options md4: " . print_r($vals, true) );

            //получим id уникальных значений по их хешам
            $ids = $this->features->get_options_ids(array($hash => $vals, 'return' => array('key' => 'id', 'col' => 'id')));


            //тут проверим количество переданных значений опций и количество полученных из базы,
            //если не совпадает - return false
            if ($ids === false || count($ids) !== count($vals)) {
                return false;
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

        return $filter;
    }

}

