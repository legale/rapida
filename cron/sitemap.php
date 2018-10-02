<?php
$time_start = microtime(true);

echo "Rapida sitemap generator v0.0.7 \r\n";


require_once(dirname(__FILE__) . '/../api/Simpla.php');
$rapida = new Simpla();
//отключаем логгер, чтобы экономить память
dtimer::$enabled = false;

//кол-во товаров на странице
define("ITEMS", $rapida->settings->products_num);
define("HOSTNAME", 'https://' . $rapida->config->host . '/');


// ненужные фильтры
$filter_minus = array(27, 12, 34, 35, 36, 13, 14, 8, 15, 3);
$filter_minus_hard = array(27, 12, 34, 35, 36, 13, 14, 8, 15, 3, 18, 19, 21, 22, 27, 10, 4, 24, 16);
$filter_plus = array(2, 3, 4, 9, 10, 13, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 31);


//params structure array
$params = ['path' => null, 'fopen' => null, 'counter' => 0, 'name_tpl' => 'sm1', 'names' => 0];


function time33(string $str)
{
    $hash = 0;
    $len = strlen($str);
    for ($i = 0; i < $len; ++$i) {
        $hash = (($hash << 5) + $hash) + ord($str[i]);
    }
    return $hash;
}


function gzconvert(string $src): ?string
{
    if (!file_exists($src)) {
        return false;
    }

    $tmpfile = $src . "_.gz";
    $newfile = $src . ".gz";
    //пишем все это в сжатом виде
    $input = fopen($src, "r");
    $output = gzopen($tmpfile, "w9");

    //сжимаем и пишем в файл кусками по 4мб
    while (!feof($input)) {
        $data = fread($input, 4000000);
        gzwrite($output, $data);
    }

    fclose($input);
    gzclose($output);
    unlink($src);
    return rename($tmpfile, $newfile) ? $newfile : null;
}


function &open_close(array &$params, string &$buffer = null): array
{
    echo $params['counter'] . "\n";
    if (!empty($params["fopen"]) && $buffer !== null) {
        fwrite($params["fopen"], $buffer);
    }
    if ($params['counter'] % 50000 === 0) {
        ++$params['names']; //increment names
        if (is_resource($params['fopen'])) {
            fwrite($params["fopen"], '</urlset>' . "\n");
            fclose($params['fopen']);
            $params['fopen'] = null;
            gzconvert($params['path']);
            $params['path'] = null;

            $tmpname = dirname(__FILE__) . "/../" . "_" . $params["name_tpl"] . "_" . $params['names'] . ".xml";
            $params['fopen'] = fopen($tmpname, 'w');
            $params['path'] = $tmpname;
        } else {
            $tmpname = dirname(__FILE__) . "/../" . "_" . $params["name_tpl"] . "_" . $params['names'] . ".xml";
            $params['fopen'] = fopen($tmpname, 'w');
            $params['path'] = $tmpname;
        }
        fwrite($params["fopen"], '<?xml version="1.0" encoding="UTF-8"?>' . "\n");
        fwrite($params["fopen"], '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n");
    }
    return $params;
}

function gen_url_string(string $url): string
{
    return "<url>\n" . "<loc>$url</loc>\n" . "</url>\n";
}

// Главная страница
function &main_page_gen(array &$params, &$rapida): array
{
    $buffer = "";
    open_close($params);
    $buffer .= gen_url_string(HOSTNAME);
    ++$params['counter'];
    return open_close($params, $buffer);
}


// Страницы
function &pages_gen(array &$params, &$rapida): array
{
    $buffer = "";
    open_close($params);
    $counter = $params['counter'] % 50000;


    $pages = $rapida->pages->get_pages();
    if (!$pages) {
        return $params;
    }
    foreach ($pages as $p) {
        $buffer .= gen_url_string(HOSTNAME . $p['trans'].'/');
        ++$params['counter'];
        if (++$counter === 50000) {
            $params = open_close($params, $buffer);
            $buffer = "";
            $counter = 0;
        }
    }
    return open_close($params, $buffer);
}

//  Бренды
function &brands_gen(array &$params, &$rapida): array
{
    $buffer = "";
    open_close($params);
    $counter = $params['counter'] % 50000;

    $brands = $rapida->brands->get_brands(['visible' => 1]);
    if (!$brands) {
        return $params;
    }
    foreach ($brands as $b) {
        $p_count = $rapida->products->count_products(array('visible' => 1, 'brand_id' => $b['id']));
        //это для подсчета страниц пагинации (не используется сейчас)
        //$pages = (int)ceil($p_count / ITEMS);
        $brand = $b['trans'];

        if ($p_count) {
            $url = HOSTNAME . 'catalog/' . $brand.'/';
            $buffer .= gen_url_string($url);
            ++$params['counter'];
            if (++$counter === 50000) {
                $params = open_close($params, $buffer);
                $buffer = "";
                $counter = 0;
            }
        }

    }

    return open_close($params, $buffer);
}

//  Категории
function &categories_gen(array &$params, &$rapida): array
{
    $buffer = "";
    open_close($params);
    $counter = $params['counter'] % 50000;


    $categories = $rapida->categories->get_categories();
    if (!$categories) {
        return $params;
    }
    foreach ($categories as $c) {
        $p_count = $rapida->products->count_products(array('visible' => 1, 'category_id' => $c['children']));
        //это для подсчета страниц пагинации (не используется сейчас)
        //$pages = (int)ceil($p_count / ITEMS);
        $cat = $c['trans'];
        if (!isset($c['trans'])) {
            print_r($c['id']);
            die;
        }
        if ($p_count) {
            $url = HOSTNAME . 'catalog/' . $cat.'/';
            $buffer .= gen_url_string($url);
            ++$params['counter'];
            if (++$counter === 50000) {
                $params = open_close($params, $buffer);
                $buffer = "";
                $counter = 0;
            }
        }
    }

    return open_close($params, $buffer);
}

//  Категории + бренды
function &categories_brands_gen(array &$params, &$rapida): array
{
    $buffer = "";
    open_close($params);
    $counter = $params['counter'] % 50000;


    $categories = $rapida->categories->get_categories();
    if (!$categories) {
        return $params;
    }
    foreach ($categories as $c) {
        $filter = ['category_url' => $c['trans'], 'category_id' => $c['children']];
        $brands = $rapida->brands->get_brands($filter);
        if (!$brands) {
            continue;
        }
        foreach ($brands as $b) {
            $_filter = $filter;
            $_filter['brand_id'] = $b['id'];
            $p_count = $rapida->products->count_products($_filter);
            //это для подсчета страниц пагинации (не используется сейчас)
            //$pages = (int)ceil($p_count / ITEMS);
            $cat = $c['trans'];
            $brand = $b['trans'];

            if ($p_count) {
                $url = HOSTNAME . 'catalog/' . $cat . '/brand-' . $brand.'/';
                $buffer .= gen_url_string($url);
                ++$params['counter'];
                if (++$counter === 50000) {
                    $params = open_close($params, $buffer);
                    $buffer = "";
                    $counter = 0;
                }
            }
        }
    }

    return open_close($params, $buffer);
}

//  Категории + свойства
function &categories_features_gen(array &$params, &$rapida): array
{
    $buffer = "";
    open_close($params);
    $counter = $params['counter'] % 50000;


    if (!$categories = $rapida->categories->get_categories()) {
        return $params;
    }

    foreach ($categories as &$c) {
        if (!$c['enabled']) continue;
        $init_filter = ['in_filter' => 1, 'category_url' => $c['trans'], 'category_id' => $c['children']];
        if (!$features = $rapida->features->get_features($init_filter)) {
            continue;
        }
        $init_filter['feature_id'] = array_keys($features);


        $options = $rapida->features->get_options_mix($init_filter);


        if (empty($options['filter'])) {
            continue;
        }

        unset($options['filter']['brand_id']);
        unset($options['full']['brand_id']);


        foreach ($options['filter'] as $fid => &$vids) {

            foreach (array_keys($vids) as $vid) {
                $_filter = $init_filter;
                $_filter['features'][$fid] = [$vid => $vid];
                $p_count = $rapida->products->count_products($_filter);
                //это для подсчета страниц пагинации (не используется сейчас)
                //$pages = (int)ceil($p_count / ITEMS);

                if ($p_count) {
                    $url = HOSTNAME . 'catalog/' . $c['trans'] . "/" . $features[$fid]['trans'] . "-" . $options['full'][$fid]['trans'][$vid].'/';
                    $buffer .= gen_url_string($url);
                    ++$params['counter'];
                    if (++$counter === 50000) {
                        $params = open_close($params, $buffer);
                        $buffer = "";
                        $counter = 0;
                    }
                }
            }
        }
    }

    return open_close($params, $buffer);
}


//  Категории + свойство + свойство
function &categories_features2_gen(array &$params, &$rapida): array
{
    $buffer = "";
    open_close($params);
    $counter = $params['counter'] % 50000;

    if (!$categories = $rapida->categories->get_categories()) {
        return $params;
    }


    foreach ($categories as &$c) {
        if (!$c['enabled']) continue;
        $init_filter = ['in_filter' => 1, 'category_url' => $c['trans'], 'category_id' => $c['children']];
        if (!$features = $rapida->features->get_features($init_filter)) {
            continue;
        }
        $init_filter['feature_id'] = array_keys($features);


        $options = $rapida->features->get_options_mix($init_filter);
        if (empty($options['filter'])) {
            continue;
        }

        unset($options['filter']['brand_id']);
        unset($options['full']['brand_id']);


        $fixed = mix_features($options);


        foreach ($fixed as $array) {
            list($fid, $fid2, $vid, $vid2) = $array;

            $_filter = $init_filter;
            $_filter['features'][$fid] = [$vid => $vid];
            $_filter['features'][$fid2] = [$vid2 => $vid2];
            $p_count = $rapida->products->count_products($_filter);


            //это для подсчета страниц пагинации (не используется сейчас)
            //$pages = (int)ceil($p_count / ITEMS);

            if ($p_count) {
                $part1 = $features[$fid]['trans'] . "-" . $options['full'][$fid]['trans'][$vid];
                $part2 = $features[$fid2]['trans'] . "-" . $options['full'][$fid2]['trans'][$vid2];
                $url = HOSTNAME . 'catalog/' . $c['trans'] . "/" . $part1 . "/" . $part2.'/';


                $buffer .= gen_url_string($url);
                ++$params['counter'];
                if (++$counter === 50000) {
                    $params = open_close($params, $buffer);
                    $buffer = "";
                    $counter = 0;
                }
            }
        }
    }

    return open_close($params, $buffer);
}

function &mix_features(array &$options): object
{
    $res = [];
    $htable = [];
    foreach ($options['filter'] as $fid => &$vids) {
        foreach ($vids as $vid => $empty) {
            foreach ($options['filter'] as $fid2 => &$vids2) {
                foreach ($vids2 as $vid2 => $empty2) {
                    if ($fid !== $fid2 && $vid !== $vid2) {
                        $hash = (($fid << 5) + $fid) + (($vid << 5) + $vid) + (($fid2 << 5) + $fid2) + (($vid2 << 5) + $vid2);
                        if (!array_key_exists($hash, $htable)) {
                            $htable[$hash] = null;
                            $res[] = SplFixedArray::fromArray([$fid, $fid2, $vid, $vid2]);
                        }
                    }
                }
            }
        }
    }
    $res = SplFixedArray::fromArray($res);
    return $res;
}

//  Категории + brand + свойства
function &categories_brands_features_gen(array &$params, &$rapida): array
{
    $buffer = "";
    open_close($params);
    $counter = $params['counter'] % 50000;

    if (!$brands = $rapida->brands->get_brands()) {
        return $params;
    }

    if (!$categories = $rapida->categories->get_categories()) {
        return $params;
    }
    foreach ($categories as &$c) {
        if (!$c['enabled']) continue;
        //echo memory_get_usage().PHP_EOL;

        $init_filter = ['in_filter' => 1, 'category_url' => $c['trans'], 'category_id' => $c['children']];

        if (!$features = $rapida->features->get_features($init_filter)) {
            continue;
        }

        $init_filter['feature_id'] = array_keys($features);

        $options = $rapida->features->get_options_mix($init_filter);
        if (empty($options['filter'])) {
            continue;
        }

        $bids = array_keys($options['filter']['brand_id']);

        unset($options['filter']['brand_id']);
        unset($options['full']['brand_id']);


        foreach ($bids as $bid) {
            $_filter = $init_filter;
            $_filter['brand_id'] = $bid;
            $part1 = "brand-" . $brands[$bid]['trans'];
            foreach ($options['filter'] as $fid => &$vids) {
                foreach (array_keys($vids) as $vid) {
                    $_filter['features'][$fid] = [$vid => $vid];
                    $p_count = $rapida->products->count_products($_filter);
                    unset($_filter['features'][$fid]);
                    //это для подсчета страниц пагинации (не используется сейчас)
                    //$pages = (int)ceil($p_count / ITEMS);
                    if ($p_count) {

                        $part2 = $features[$fid]['trans'] . "-" . $options['full'][$fid]['trans'][$vid];
                        $url = HOSTNAME . 'catalog/' . $c['trans'] . "/" . $part1 . "/" . $part2.'/';
                        $buffer .= gen_url_string($url);
                        ++$params['counter'];
                        if (++$counter === 50000) {
                            $params = open_close($params, $buffer);
                            $buffer = "";
                            $counter = 0;
                        }
                    }
                }
            }
        }
    }

    return open_close($params, $buffer);

}


//товары

function &products_gen_static(array &$params, &$rapida)
{
    $buffer = "";
    open_close($params);
    $counter = $params['counter'] % 50000;

    $rapida->db->query("SELECT trans FROM s_products WHERE visible=1");
    if (!$products = $rapida->db->results_array()) {
        return $params;
    }

    foreach ($products as &$p) {
        $buffer .= gen_url_string(HOSTNAME . "products/" . $p['trans'].'/');
        ++$params['counter'];
        if (++$counter === 50000) {
            $params = open_close($params, $buffer);
            $buffer = "";
            $counter = 0;
        }
    }
    return open_close($params, $buffer);

}

function &products_gen(array &$params, &$rapida)
{
    $buffer = "";
    open_close($params);
    $counter = $params['counter'] % 50000;

    $cats = $rapida->categories->get_categories();
    foreach($cats as &$c){
        if (!$c['enabled']){
            continue;
        }

        $products = $rapida->db3->getAll("SELECT trans FROM s_products WHERE 1 
        AND visible=1 AND id IN (SELECT product_id FROM s_products_categories WHERE category_id IN (?a))", $c['children']);
        if (!$products) {
            return $params;
        }

        foreach ($products as &$p) {
            $buffer .= gen_url_string(HOSTNAME . "vproducts/". $c["trans"] . "/buy-" . $p['trans'].'/');
            ++$params['counter'];
            if (++$counter === 50000) {
                $params = open_close($params, $buffer);
                $buffer = "";
                $counter = 0;
            }
        }

    }

    return open_close($params, $buffer);

}

function &sitemap_gen(array &$params)
{
    fwrite($params["fopen"], '</urlset>' . "\n");
    fclose($params['fopen']);
    $params['fopen'] = null;
    gzconvert($params['path']);
    $params['path'] = null;

    $last_modify = date("Y-m-d");
    $dirpath = dirname(__FILE__) . "/../";
    $tmpfullpath_sitemap = $dirpath . "_" . $params["name_tpl"] . ".xml";
    $newfullpath_sitemap = $dirpath . $params["name_tpl"] . ".xml";
    $params['fopen'] = fopen($tmpfullpath_sitemap, 'w');

    fwrite($params['fopen'], '<?xml version="1.0" encoding="UTF-8"?>' . "\n");
    fwrite($params['fopen'], '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n");

    for ($i = 1; $i <= $params['names']; ++$i) {
        $basename = $params['name_tpl'] . "_" . $i . ".xml.gz";
        $tmpfullpath = dirname(__FILE__) . "/../_" . $basename;
        $newfullpath = dirname(__FILE__) . "/../" . $basename;
        $url = HOSTNAME . $basename;

        if (file_exists($newfullpath)) {
            unlink($newfullpath);
        }
        rename($tmpfullpath, $newfullpath);
        fwrite($params['fopen'], "<sitemap>" . "\n");
        fwrite($params['fopen'], "<loc>$url</loc>" . "\n");
        fwrite($params['fopen'], "<lastmod>$last_modify</lastmod>" . "\n");
        fwrite($params['fopen'], "</sitemap>" . "\n");
    }
    fwrite($params['fopen'], '</sitemapindex>' . "\n");
    fclose($params['fopen']);
    $params['fopen'] = null;
    rename($tmpfullpath_sitemap, $newfullpath_sitemap);
    return $params;
}


main_page_gen($params, $rapida);
pages_gen($params, $rapida);
brands_gen($params, $rapida);
categories_gen($params, $rapida);
categories_brands_gen($params, $rapida);
categories_brands_features_gen($params, $rapida);
categories_features_gen($params, $rapida);
categories_features2_gen($params, $rapida);
products_gen($params, $rapida);
sitemap_gen($params, $rapida);


echo "generated urls: " . $params['counter'] . PHP_EOL;

