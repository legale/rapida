<?php
if (defined('PHP7')) {
    eval("declare(strict_types=1);");
}

/*
 * Это класс главного контроллера, через него осуществляется вызов всех остальных контроллеров
 * Главная задача этого контроллера, разобрать uri, вызвать нужный контроллер и передать ему полученные в uri 
 * параметры.
 */

require_once('Simpla.php');

class ControllerMaster extends Simpla
{
    public $uri_arr;
    public $ctrl;
    public $uri;

    //здесь массив соответствия модулей и контроллеров
    private $modules = array(
        'simpla' => 'coAdmin',
        '/' => 'coSimpla',
        'page' => 'coSimpla',
        'catalog' => 'coSimpla',
        'products' => 'coSimpla',
        'search' => 'coSimpla',
        'brands' => 'coSimpla',
        'contact' => 'coSimpla',
        'user' => 'coSimpla',
        'login' => 'coSimpla',
        'register' => 'coSimpla',
        'cart' => 'coSimpla',
        'order' => 'coSimpla',
        'blog' => 'coSimpla',
        'wishlist' => 'coSimpla',
        'img' => 'coResize',
        'xhr' => 'coXhr',
    );


    public function __construct()
    {
        dtimer::log(__METHOD__ . ' start');
        //если запуск не из командной строки
        if (isset($_SERVER)) {
            $this->uri = $this->get_uri();
            $this->uri_arr = $this->parse_uri($this->uri);
            dtimer::log(__METHOD__ . " uri: " . $this->uri);
            dtimer::log(__METHOD__ . " parsed uri: " . var_export($this->uri_arr, true));
        }
    }

    public function action()
    {
        if (isset($this->uri_arr['query']['xhr'])) {
            $ctrl = 'xhr';
        } else if (isset($this->uri_arr['path']['module'])) {
            $ctrl = $this->uri_arr['path']['module'];
        }

        if (isset($this->modules[$ctrl])) {
            $this->ctrl = $this->modules[$ctrl];
        } else {
            return $this->coSimpla->action('404');
        }
        return $this->{$this->ctrl}->action();
    }

    //генерируем uri из массива фильтра $filter
    public function gen_uri_from_filter($uri_arr, $filter)
    {
//        print_r($uri_arr);
//        print_r($filter);
//        die;
        //сначала префикс
        $res = $uri_arr['scheme'] . '://' . $uri_arr['host'];
        //модуль
        if (isset($uri_arr['path']['module'])) {
            $res .= '/' . $uri_arr['path']['module'];
        } else {
            return false;
        }
        //теперь url, если есть
        if (isset($uri_arr['path']['url'])) {
            $res .= '/' . $uri_arr['path']['url'];
        } else {
            return false;
        }
        //теперь бренды если они есть
        if (isset($filter['brand_id']) && is_array($filter['brand_id']) && count($filter['brand_id']) > 0) {
            $brands_trans = $this->brands->get_brands_ids(array('return' => array('key' => 'id', 'col' => 'trans')));
            $brands = array_intersect_key($brands_trans, array_flip($filter['brand_id']));
            $res .= '/' . 'brand-' . implode('.', $brands);
        }

        //теперь опции, если они есть
        if (isset($filter['features']) && is_array($filter['features']) && count($filter['features']) > 0) {
            $features_trans = $this->features->get_features_ids(array('return' => array('key' => 'id', 'col' => 'trans')));


            foreach ($filter['features'] as $fid => $v) {
                if (isset($features_trans[$fid])) {
                    if (is_array($v) && count($v) > 0) {
                        $options_trans = $this->features->get_options_ids(array('id' => $v, 'return' => array('key' => 'id', 'col' => 'trans')));
                        $s = '';
                        $last = end($v);
                        foreach ($v as $vid) {
                            $s .= $options_trans[$vid];
                            if ($vid !== $last) {
                                $s .= '.';
                            }
                        }
                        $res .= "/" . $features_trans[$fid] . "-" . $s;
                    }
                }
            }
        }
        //сортировка
        if (isset($filter['sort'])) {
            $res .= '/' . 'sort-' . $filter['sort'];
        }
        //страница
        if (isset($filter['page'])) {
            $res .= '/' . 'page-' . $filter['page'];
        }
        return $res;
    }


    //генерируем uri из массива параметров
    public function gen_uri($arr = null, $filter = null)
    {
        dtimer::log(__METHOD__ . ' filter ' . var_export($filter, true));
        //если начальный массив параметров не задан, возьмем его из $this->uri_arr['path']
        if (!isset($arr)) {
            if (isset($this->uri_arr['path'])) {
                $arr = $this->uri_arr['path'];
            } else {
                return false;
            }
        }

        if (isset($filter['page'], $arr['page'])) {
            unset($arr['page']);
        }
        if (isset($filter['sort'], $arr['sort'])) {
            unset($arr['sort']);
        }

        if (isset($filter)) {
            $arr = $this->merge_arrays_keys($arr, $filter);
        }

        dtimer::log(__METHOD__ . ' arr: ' . print_r($arr, true));

        $res = '';

        //Если у нас нет модуля - останавливаемся
        if (!isset($arr['module'])) {
            return false;
        }


        //сначала модуль
        $res .= $arr['module'];

        //теперь url, если есть
        if (isset($arr['url']) && $arr['url'] !== '') {
            $res .= '/' . $arr['url'];
        }

        //теперь бренды, если они есть
        if (isset($arr['brand']) && is_array($arr['brand']) && count($arr['brand']) > 0) {
            $res .= '/brand-' . implode('.', array_keys($arr['brand']));
        }


        //теперь опции, если они есть
        if (isset($arr['features']) && is_array($arr['features']) && count($arr['features']) > 0) {
            foreach ($arr['features'] as $name => $v) {
                if (is_array($v) && count($v) > 0) {
                    $res .= "/$name-" . implode('.', array_keys($v));
                }
            }
        }
        //теперь сортировка, если они есть
        if (isset($arr['sort']) && $arr['sort'] !== '') {
            $res .= '/sort-' . $arr['sort'];
        }

        //теперь страница, если есть
        if (isset($arr['page']) && $arr['page'] !== '') {
            $res .= '/page-' . $arr['page'];
        }

        return $res;

    }


    //соединяем рекурсивно 2 массива между собой по ключам, убирая полные совпадения
    private function merge_arrays_keys($a, $b)
    {
        if (!is_array($a) || !is_array($b)) {
            return false;
        }

        foreach ($a as $a_key => $a_val) {
            //если массив и такой элемент есть в массиве b запускаем рекурсию
            if (is_array($a_val) && isset($b[$a_key])) {
                $a[$a_key] = merge_arrays_keys($a_val, $b[$a_key]);
            } else {
                //иначе проверяем если в массиве b есть такое значение, убираем его из обоих массивов
                if (isset($b[$a_key])) {
                    unset($a[$a_key], $b[$a_key]);
                }
            }
        }
        return $a + $b;
    }


    //парсим uri
    public function parse_uri__($uri = null)
    {
        dtimer::log(__METHOD__ . " start");
        if (!isset($uri)) {
            $res = parse_url($_SERVER['REQUEST_URI']);
            $this->uri_arr = $res;
        } else {
            $res = parse_url($uri);
        }


        if (isset($res['query'])) {
            //Тут можно просто взять $_GET, но у нас свой лунапарк

            $a = explode('&', $res['query']);
            $c = array();
            foreach ($a as $b) {
                $b = explode('=', $b);
                if (count($b) === 2) {
                    $c[urldecode($b[0])] = urldecode($b[1]);
                } else {
                    dtimer::log(__METHOD__ . ' parse uri query part failed ', 2);
                    dtimer::log(__METHOD__ . __LINE__ . " error", 2);
                    return false;
                }
            }

            if (!isset($uri)) {
                $this->uri_arr['query_arr'] = $c;
            }
            $res['query_arr'] = $c;
        }

        //Если у нас в get запросе есть xhr, то сразу поставим контроллер туда, дальше ничего не парсим
        if (isset($res['query_arr']['xhr'])) {
            $res['ctrl'] = 'coXhr';
            if (!isset($uri)) {
                $this->uri_arr['ctrl'] = $res['ctrl'];
            }
            $this->ctrl = $res['ctrl'];
            return $res;
        }

        //Тут обрабатываем путь
        if (isset($res['path'])) {
            $res['path'] = $this->parse_uri_path($res['path']);

            //если у нас нет соответствующего модулю контроллера  - ставим ''
            dtimer::log(__METHOD__ . " before module choice: " . $res['path']['module']);
            if (isset($this->modules[$res['path']['module']])) {
                $res['ctrl'] = $this->modules[$res['path']['module']];
            } else {
                $res['ctrl'] = '';
            }
        }

        if (!isset($uri)) {
            $this->uri_arr['path'] = $res['path'];
            $this->ctrl = $res['ctrl'];
        }

        return $res;

    }

    public function parse_uri($uri)
    {
        $ar = parse_url($uri);
        $res = array();
        if (isset($ar['scheme'])) {
            $res['scheme'] = $ar['scheme'];
        }
        if (isset($ar['host'])) {
            $res['host'] = $ar['host'];
        }
        if (isset($ar['path'])) {
            $res['path'] = $this->parse_uri_path($ar['path']);
        }
        if (isset($ar['query'])) {
            $res['query'] = $this->parse_uri_query($ar['query']);
        }

        foreach ($res as $r) {
            if ($r === false) {
                return false;
            }
        }

        return $res;
    }

    private function parse_uri_path($path)
    {
        $tpl = array();

        //это массив для результатов
        $res = array();
        $brand = array();

        $a = $path;

        //Если путь / или пусто
        if ($a === '/' || $a === '') {
            return array('module' => '/');
        } else {
            //удалим дроби по краям, если они там есть,
            //а потом делаем массив с разделением через дробь
            $a = explode('/', trim($a, '/'));
        }

        //Если только 1 элемент после дроби, значит это модуль page
        if (count($a) === 1 && !in_array(reset($a), array('wishlist', 'blog', 'cart', 'order', 'register', 'search', 'simpla', 'user'))) {
            return array('module' => 'page', 'url' => array_shift($a));
        }

        $res['module'] = array_shift($a);

        switch ($res['module']) {
            case 'img':
                list($res['dir'], $res['id']) = explode('_', array_shift($a), 2);
                $res['size'] = explode('x', array_shift($a), 2);
                $res['basename'] = array_shift($a);
                break;

            case 'products':
            case 'order':
            case 'page':
            case 'blog':
            case 'wishlist':
                $res['url'] = array_shift($a);
                break;

            case 'login':
                $res['url'] = array_shift($a);
                if (count($a) < 1) {
                } else {
                    $res['arg'] = array_shift($a);
                }
                break;

            default:
                $res['url'] = array_shift($a);

                //Если больше ничего не осталось, останавливаемся
                if (count($a) < 1) {
                    break;
                }

                //иначе продолжаем делить части массива
                $explode = explode('-', $a[0]);
                $cnt = count($explode);
                if ($cnt === 2) {
                    list($f, $o) = $explode;
                } else if ($cnt === 1) {
                    $res['args'] = $a;
                    return $res;
                } else {
                    return false;
                }

                if ($f === 'brand') {
                    $res['brand'] = array_flip(explode('.', $o));
                    //убираем использованный элемент
                    array_shift($a);
                    //Если больше ничего не осталось, останавливаемся
                    if (count($a) < 1) {
                        break;
                    }
                    $explode = explode('-', $a[0]);
                    if (count($explode) === 2) {
                        list($f, $o) = $explode;
                    } else {
                        return false;
                    }
                }


                //перебираем оставшуюся часть массива - тут у нас опции
                foreach ($a as $o) {
                    //сначала разделяем название и значения
                    $explode = explode('-', $o);
                    if (count($explode) === 2) {
                        list($f, $o) = $explode;
                    } else {
                        return false;
                    }
                    if (in_array($f, array('sort', 'page'))) {
                        $res[$f] = $o;
                    } else {
                        $res['features'][$f] = array_flip(explode('.', $o));
                    }
                }
                break;
        }
        return $res;
    }

    private function parse_uri_query($query)
    {
        $a = explode('&', $query);
        $c = array();
        foreach ($a as $b) {
            $b = explode('=', $b);
            if (count($b) === 2) {
                $c[urldecode($b[0])] = urldecode($b[1]);
            } else {
                return false;
            }
        }
        return $c;
    }

    private function get_uri()
    {
        if (!isset($_SERVER)) {
            return false;
        }
        $uri = isset($_SERVER['HTTPS']) ? "https" : "http";
        $uri .= isset($_SERVER['HTTP_HOST']) ? "://" . $_SERVER['HTTP_HOST'] : '';
        $uri .= isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        return $uri;
    }
}
