<?php


require_once(dirname(__FILE__) . '/' . 'Simpla.php');
require_once(dirname(dirname(__FILE__)) . '/Smarty/libs/Smarty.class.php');

class Design extends Simpla
{
    public $smarty;

    public function __construct()
    {
        parent::__construct();

        // Создаем и настраиваем Смарти
        $this->smarty = new Smarty();
        $this->smarty->compile_check = $this->config->smarty_compile_check;
        $this->smarty->caching = $this->config->smarty_caching;
        $this->smarty->cache_lifetime = $this->config->smarty_cache_lifetime;
        $this->smarty->debugging = $this->config->smarty_debugging;
        //$this->smarty->error_reporting = E_ALL & ~E_NOTICE;

        // Берем тему из настроек
        $theme = $this->settings->theme;


        $this->smarty->compile_dir = $this->config->root_dir . '/compiled/' . $theme;
        $this->smarty->template_dir = $this->config->root_dir . '/design/' . $theme . '/html';

        // Создаем папку для скомпилированных шаблонов текущей темы
        if (!is_dir($this->smarty->compile_dir))
            mkdir($this->smarty->compile_dir, 0777, true);

        $this->smarty->cache_dir = 'cache';

        $this->smarty->registerPlugin('modifier', 'resize', array($this, 'resize_modifier'));
        $this->smarty->registerPlugin('modifier', 'token', array($this, 'token_modifier'));
        $this->smarty->registerPlugin('modifier', 'plural', array($this, 'plural_modifier'));
        $this->smarty->registerPlugin('function', 'url', array($this, 'url_modifier'));
        $this->smarty->registerPlugin('function', 'chpu_url', array($this, 'chpu_url_modifier'));
        $this->smarty->registerPlugin('modifier', 'first', array($this, 'first_modifier'));
        $this->smarty->registerPlugin('modifier', 'cut', array($this, 'cut_modifier'));
        $this->smarty->registerPlugin('modifier', 'date', array($this, 'date_modifier'));
        $this->smarty->registerPlugin('modifier', 'time', array($this, 'time_modifier'));
        $this->smarty->registerPlugin('function', 'api', array($this, 'api_plugin'));

        // Настраиваем плагины для смарти
        $this->smarty->registerPlugin("function", "get_posts", array($this, 'get_posts_plugin'));
        $this->smarty->registerPlugin("function", "get_brands", array($this, 'get_brands_plugin'));
        $this->smarty->registerPlugin("function", "get_browsed_products", array($this, 'get_browsed_products'));
        $this->smarty->registerPlugin("function", "get_featured_products", array($this, 'get_featured_products_plugin'));
        $this->smarty->registerPlugin("function", "get_new_products", array($this, 'get_new_products_plugin'));
        $this->smarty->registerPlugin("function", "get_discounted_products", array($this, 'get_discounted_products_plugin'));

        $this->smarty->registerPlugin("function", "bender_help", array($this, 'bender_help_plugin'));
        $this->smarty->registerPlugin("function", "bender", array($this, 'bender_plugin'));


        if ($this->config->smarty_html_minify) {
            $this->smarty->loadFilter('output', 'trimwhitespace');
        }
    }

    public function fetch($template)
    {
        // Передаем в дизайн то, что может понадобиться в нем
        $this->assign('config', $this->config);
        $this->assign('settings', $this->settings);
        return $this->smarty->fetch($template);
    }

    public function assign($var, $value)
    {
        return $this->smarty->assign($var, $value);
    }

    public function set_templates_dir($dir)
    {
        $this->smarty->template_dir = $dir;
    }

    public function set_compiled_dir($dir)
    {
        $this->smarty->compile_dir = $dir;
    }

    public function get_var($name)
    {
        return $this->smarty->getTemplateVars($name);
    }

    public function clear_cache()
    {
        $this->smarty->clearAllCache();
    }


    public function resize_modifier($basename, $type, $id, $w, $h)
    {
        dtimer::log(__METHOD__ . " start type: $type id: $id basename: $basename w: $w h: $h");
		if(empty($basaname)){
			return '';
		}
		
        if($this->image->is_url($basename)){
            $url = parse_url($basename);
            return '/img/' . $type . '_' . $id . '/' . $w . 'x' . $h . '/?' . http_build_query($url);
        }

        $dir = $this->image->gen_original_dirname($type);
        $filepath = $dir . $w . 'x' . $h . '/' . $basename;
        return '/' . $filepath;
    }

    public function token_modifier($text)
    {
        return $this->config->token($text);
    }

    public function url_modifier($params)
    {
        if (is_array(reset($params)))
            return $this->request->url(reset($params));
        else
            return $this->request->url($params);
    }

    public function chpu_url_modifier($params)
    {
        if (is_array(reset($params))) {
            $params = reset($params);
        }

        //~ return print_r($params, true);
        return $this->coMaster->gen_uri(null, $params);
    }

    public function plural_modifier($number, $singular, $plural1, $plural2 = null)
    {
        $number = abs($number);
        if (!empty($plural2)) {
            $p1 = $number % 10;
            $p2 = $number % 100;
            if ($number == 0)
                return $plural1;
            if ($p1 == 1 && !($p2 >= 11 && $p2 <= 19))
                return $singular;
            elseif ($p1 >= 2 && $p1 <= 4 && !($p2 >= 11 && $p2 <= 19))
                return $plural2;
            else
                return $plural1;
        } else {
            if ($number == 1)
                return $singular;
            else
                return $plural1;
        }

    }

    public function first_modifier($params = array())
    {
        if (!is_array($params))
            return false;
        return reset($params);
    }

    public function cut_modifier($array, $num = 1)
    {
        if ($num >= 0)
            return array_slice($array, $num, count($array) - $num, true);
        else
            return array_slice($array, 0, count($array) + $num, true);
    }

    public function date_modifier($date, $format = null)
    {
        if (empty($date))
            $date = date("Y-m-d");
        return date(empty($format) ? $this->settings->date_format : $format, strtotime($date));
    }

    public function time_modifier($date, $format = null)
    {
        return date(empty($format) ? 'H:i' : $format, strtotime($date));
    }

    public function api_plugin($params, $smarty)
    {
        if (!isset($params['module']))
            return false;
        if (!isset($params['method']))
            return false;

        $module = $params['module'];
        $method = $params['method'];
        $var = $params['var'];
        unset($params['module']);
        unset($params['method']);
        unset($params['var']);
        $res = $this->$module->$method($params);
        $this->smarty->assign($var, $res);
    }

    /**
     *
     * Плагины для смарти
     *
     */
    public function get_posts_plugin($params, $smarty)
    {
        if (!isset($params['visible']))
            $params['visible'] = 1;
        if (!empty($params['var']))
            $smarty->assign($params['var'], $this->blog->get_posts($params));
    }

    public function get_brands_plugin($params, $smarty)
    {
        if (!empty($params['var']))
            $smarty->assign($params['var'], $this->brands->get_brands($params) ? $this->brands->get_brands($params) : array() );
    }

    public function get_browsed_products($params, $smarty)
    {
        if (!empty($_COOKIE['browsed_products'])) {
            $ids = explode(',', $_COOKIE['browsed_products']);
            $ids = array_reverse($ids);
            if (isset($params['limit']))
                $ids = array_slice($ids, 0, $params['limit']);

            $products = $this->products->get_products(array('id' => $ids, 'visible' => 1));


            $smarty->assign($params['var'], $products);
        }
    }

    public function get_featured_products_plugin($params, $smarty)
    {
        if (!isset($params['visible']))
            $params['visible'] = 1;
        $params['featured'] = 1;
        if (!empty($params['var'])) {
            $products = $this->products->get_products($params);

            if (!empty($products)) {
                // id выбраных товаров
                $products_ids = array_keys((array)$products);

                // Выбираем варианты товаров
                $variants = $this->variants->get_variants(array('product_id' => $products_ids, 'in_stock' => true));

                // Для каждого варианта
                if (!empty($variants)) {
                    foreach ($variants as &$variant) {
                        // добавляем вариант в соответствующий товар
                        if (isset($products[$variant['product_id']])) {
                            $products[$variant['product_id']]['variants'][] = $variant;
                        }
                    }
                }


                foreach ($products as $k => &$product) {
                    if (!isset($product['variants'])) {
                        unset($products[$k]);
                    } elseif (isset($product['variants'][0])) {
                        $product['variant'] = $product['variants'][0];
                    }
                }
            }

            $smarty->assign($params['var'], $products);

        }
    }

    public function get_new_products_plugin($params, $smarty)
    {
        if (!isset($params['visible']))
            $params['visible'] = 1;
        if (!isset($params['sort']))
            $params['sort'] = 'created';
        if (!empty($params['var'])) {
            //~ print_r($params);
            $products = $this->products->get_products($params);

            if (isset($products) && !empty_($products)) {
                // id выбраных товаров
                $products_ids = array_keys((array)$products);
                // Выбираем варианты товаров
                $variants = $this->variants->get_variants(array('product_id' => $products_ids, 'in_stock' => true));

                // Для каждого варианта
                if (!empty($variants)) {
                    foreach ($variants as &$variant) {
                        // добавляем вариант в соответствующий товар
                        if (isset($products[$variant['product_id']])) {
                            $products[$variant['product_id']]['variants'][] = $variant;
                        }
                    }
                }
                // Выбираем изображения товаров

                foreach ($products as $k => &$product) {
                    if (!isset($product['variants'])) {
                        unset($products[$k]);
                    } elseif (isset($product['variants'][0])) {
                        $product['variant'] = $product['variants'][0];
                    }
                }

            }

            $smarty->assign($params['var'], $products);

        }
    }

    public function get_discounted_products_plugin($params, $smarty)
    {
        if (!isset($params['visible']))
            $params['visible'] = 1;
        $params['discounted'] = 1;
        if (!empty($params['var'])) {
            $products = $this->products->get_products($params);

            if (!empty($products)) {
                // id выбраных товаров
                $products_ids = array_keys((array)$products);

                // Выбираем варианты товаров
                $variants = $this->variants->get_variants(array('product_id' => $products_ids, 'in_stock' => true));

                // Для каждого варианта
                if (!empty($variants)) {
                    foreach ($variants as &$variant) {
                        // добавляем вариант в соответствующий товар
                        if (isset($products[$variant['product_id']])) {
                            $products[$variant['product_id']]['variants'][] = $variant;
                        }
                    }
                }


                foreach ($products as $k => &$product) {
                    if (!isset($product['variants'])) {
                        unset($products[$k]);
                    } elseif (isset($product['variants'][0])) {
                        $product['variant'] = $product['variants'][0];
                    }
                }

            }

            $smarty->assign($params['var'], $products);

        }
    }

    /**
     * Smarty plugin
     * @package Smarty
     * @subpackage plugins
     *
     * Smarty {bender} function plugin
     *
     * Type:     function<br>
     * Name:     bender<br>
     * Date:     October 27, 2013<br>
     * Purpose:  combines and compresses javascript & css<br>
     * Input:
     *         - src    = path to javascript or css file (can be an array)
     *         - output = path to output js / css file (optional)
     *
     * Examples:<br>
     * <pre>
     * {bender src="templates/default/css/style-main.css"} // add first css file
     * {bender src="templates/default/css/style-additional.css"} // add second css file
     * {bender src="templates/default/js/jquery.js"}             // add first javascript file
     * {bender src="templates/default/js/bootstrap.js"}          // add second javascript file
     * {bender output="css/allcss.min.css"} // combine previously added css files, minify and put them into css/allcss.min.css, and insert link to result css file
     * {bender output="js/alljs.min.js"}    // combine previously added js files, minify and put them into js/alljs.min.css, and insert link to result js file
     * </pre>
     * @link http://smarty.php.net/manual/en/language.function.cycle.php {cycle}
     *       (Smarty online manual)
     * @author Alex Raven <bugrov at gmail dot com>
     * @version 0.1
     * @param array
     * @param Smarty
     * @return string|null
     */

    function bender_plugin($params, $smarty)
    {

        $bender = $this->bender;


        $bender->cssmin = "cssmin";
        $bender->jsmin = "jshrink";
        $bender->ttl = -1;
        $bender->root_dir = $this->config->root_dir;
        $src = isset($params['src']) ? $params['src'] : "";
        $output = isset($params['output']) ? $params['output'] : "";

        // enqueue javascript or css
        if ($src) {
            $bender->enqueue($src);
        } elseif ($output) {
            return $bender->output($output);
        }
    }

    function bender_help_plugin()
    {
        ?>
        <h3><?php echo __('What does this tag do?') ?></h3>
        <p><?php echo __('Сombines and compress javascript & css.') ?></p>
        <h3><?php echo __('How do I use it?') ?></h3>
        <p><?php echo __('Just insert the tag into your template like:') ?></p>
        <pre>
		{bender src="{base_path}/css/bootstrap/bootstrap.css"}
		{bender src="{base_path}/css/style.css"}

		{bender output="{base_path}/css/vamshop-packed.css"}

		{bender src="{base_path}/js/bootstrap/bootstrap.min.js"}
		{bender src="{base_path}/js/vamshop.js"}

		{bender output="{base_path}/js/vamshop-packed.js"}
	</pre>

        <h3><?php echo __('What parameters does it take?') ?></h3>
        <ul>
            <li><em><?php echo __('(src)') ?></em> - <?php echo __('Path to js / css file.') ?></li>
            <li><em><?php echo __('(output)') ?></em> - <?php echo __('Path to output js / css file.') ?></li>
        </ul>
        <?php
    }

    private function is_mobile_browser()
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $http_accept = isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : '';

        if (eregi('iPad', $user_agent))
            return false;

        if (stristr($user_agent, 'windows') && !stristr($user_agent, 'windows ce'))
            return false;

        if (eregi('windows ce|iemobile|mobile|symbian|mini|wap|pda|psp|up.browser|up.link|mmp|midp|phone|pocket', $user_agent))
            return true;

        if (stristr($http_accept, 'text/vnd.wap.wml') || stristr($http_accept, 'application/vnd.wap.xhtml+xml'))
            return true;

        if (!empty($_SERVER['HTTP_X_WAP_PROFILE']) || !empty($_SERVER['HTTP_PROFILE']) || !empty($_SERVER['X-OperaMini-Features']) || !empty($_SERVER['UA-pixels']))
            return true;

        $agents = array(
            'acs-' => 'acs-',
            'alav' => 'alav',
            'alca' => 'alca',
            'amoi' => 'amoi',
            'audi' => 'audi',
            'aste' => 'aste',
            'avan' => 'avan',
            'benq' => 'benq',
            'bird' => 'bird',
            'blac' => 'blac',
            'blaz' => 'blaz',
            'brew' => 'brew',
            'cell' => 'cell',
            'cldc' => 'cldc',
            'cmd-' => 'cmd-',
            'dang' => 'dang',
            'doco' => 'doco',
            'eric' => 'eric',
            'hipt' => 'hipt',
            'inno' => 'inno',
            'ipaq' => 'ipaq',
            'java' => 'java',
            'jigs' => 'jigs',
            'kddi' => 'kddi',
            'keji' => 'keji',
            'leno' => 'leno',
            'lg-c' => 'lg-c',
            'lg-d' => 'lg-d',
            'lg-g' => 'lg-g',
            'lge-' => 'lge-',
            'maui' => 'maui',
            'maxo' => 'maxo',
            'midp' => 'midp',
            'mits' => 'mits',
            'mmef' => 'mmef',
            'mobi' => 'mobi',
            'mot-' => 'mot-',
            'moto' => 'moto',
            'mwbp' => 'mwbp',
            'nec-' => 'nec-',
            'newt' => 'newt',
            'noki' => 'noki',
            'opwv' => 'opwv',
            'palm' => 'palm',
            'pana' => 'pana',
            'pant' => 'pant',
            'pdxg' => 'pdxg',
            'phil' => 'phil',
            'play' => 'play',
            'pluc' => 'pluc',
            'port' => 'port',
            'prox' => 'prox',
            'qtek' => 'qtek',
            'qwap' => 'qwap',
            'sage' => 'sage',
            'sams' => 'sams',
            'sany' => 'sany',
            'sch-' => 'sch-',
            'sec-' => 'sec-',
            'send' => 'send',
            'seri' => 'seri',
            'sgh-' => 'sgh-',
            'shar' => 'shar',
            'sie-' => 'sie-',
            'siem' => 'siem',
            'smal' => 'smal',
            'smar' => 'smar',
            'sony' => 'sony',
            'sph-' => 'sph-',
            'symb' => 'symb',
            't-mo' => 't-mo',
            'teli' => 'teli',
            'tim-' => 'tim-',
            'tosh' => 'tosh',
            'treo' => 'treo',
            'tsm-' => 'tsm-',
            'upg1' => 'upg1',
            'upsi' => 'upsi',
            'vk-v' => 'vk-v',
            'voda' => 'voda',
            'wap-' => 'wap-',
            'wapa' => 'wapa',
            'wapi' => 'wapi',
            'wapp' => 'wapp',
            'wapr' => 'wapr',
            'webc' => 'webc',
            'winw' => 'winw',
            'winw' => 'winw',
            'xda-' => 'xda-'
        );

        if (!empty($agents[substr($_SERVER['HTTP_USER_AGENT'], 0, 4)]))
            return true;
    }


}
