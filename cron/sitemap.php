<?php
$time_start = microtime(true);

echo "Rapida sitemap generator v0.0.4 \r\n";


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
$params = ['path' => null, 'fopen' => null, 'counter' => 0, 'name_tpl' => 'sitemap', 'names' => 0];


function gzconvert(string $src):? string
{
    if (!file_exists($src)) {
        return false;
    }

    $tmpfile = $src . "_.gz" ;
    $newfile = $src . ".gz" ;
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


function &open_close(array &$params): array
{
    //~echo $params['counter'] . "\n";
    if ($params['counter'] % 50000 === 0) {
        ++$params['names']; //increment names
        if (is_resource($params['fopen'])) {
            fwrite($params["fopen"], '</urlset>' . "\n");
            fclose($params['fopen']);
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
	open_close($params);
    fwrite($params['fopen'], gen_url_string(HOSTNAME));
    ++$params['counter'];
    return open_close($params);
}


// Страницы
function &pages_gen(array &$params, &$rapida): array
{
    open_close($params);
    $counter = $params['counter'] % 50000;
    

    $pages = $rapida->pages->get_pages();
    if (!$pages) {
        return $params;
    }
    foreach ($pages as $p) {
        fwrite($params['fopen'], gen_url_string(HOSTNAME . $p['trans']));
        ++$params['counter'];
        if (++$counter === 50000) {
            $params = open_close($params);
            $counter = 0;
        }
    }
    return open_close($params, $counter);
}

//  Бренды
function &brands_gen(array &$params, &$rapida): array
{
    open_close($params);
    $counter = $params['counter'] % 50000;

    $brands = $rapida->brands->get_brands(['visible' => 1]);
    if (!$brands) {
        return $params;
    }
    foreach ($brands as $b) {
        $p_count = $rapida->products->count_products(array('visible' => 1, 'brand_id' => $b['id']));
        $pages = (int)ceil($p_count / ITEMS);
        $brand = $b['trans'];

        while ($pages > 0) {
            $url = HOSTNAME . 'catalog/' . $brand;
            $pages > 1 ? $url .= '/page-' . $pages : null;
            fwrite($params['fopen'], gen_url_string($url));
            --$pages;
			++$params['counter'];
			if (++$counter === 50000) {
				$params = open_close($params);
				$counter = 0;
			}
        }

    }

    return open_close($params, $counter);
}

//  Категории
function &categories_gen(array &$params, &$rapida): array
{
    open_close($params);
    $counter = $params['counter'] % 50000;


    $categories = $rapida->categories->get_categories(['visible' => 1]);
    if (!$categories) {
        return $params;
    }
    foreach ($categories as $c) {
        $p_count = $rapida->products->count_products(array('visible' => 1, 'category_id' => $c['children']));
        $pages = (int)ceil($p_count / ITEMS);
        $cat = $c['trans'];

        while ($pages > 0) {
            $url = HOSTNAME . 'catalog/' . $cat;
            $pages > 1 ? $url .= '/page-' . $pages : null;
            fwrite($params['fopen'], gen_url_string($url));
            --$pages;
			++$params['counter'];
			if (++$counter === 50000) {
				$params = open_close($params);
				$counter = 0;
			}
        }
    }

    return open_close($params, $counter);
}

//  Категории + бренды
function &categories_brands_gen(array &$params, &$rapida): array
{
    open_close($params);
    $counter = $params['counter'] % 50000;

	
    $categories = $rapida->categories->get_categories(['visible' => 1]);
    if (!$categories) {
        return $params;
    }
    foreach ($categories as $c) {
        $brands = $rapida->brands->get_brands(['category_id' => $c['children']]);
        if (!$brands) {
            continue;
        }
        foreach ($brands as $b) {
            $p_count = $rapida->products->count_products(array('visible' => 1, 'category_id' => $c['children'], 'brand_id' => $b['id']));
            $pages = (int)ceil($p_count / ITEMS);
            $cat = $c['trans'];
            $brand = $b['trans'];

            while ($pages > 0) {
                $url = HOSTNAME . 'catalog/' . $cat . '/brand-' . $brand;
                $pages > 1 ? $url .= '/page-' . $pages : null;
                fwrite($params['fopen'], gen_url_string($url));
                --$pages;
				++$params['counter'];
				if (++$counter === 50000) {
					$params = open_close($params);
					$counter = 0;
				}
            }
        }
    }

    return open_close($params, $counter);
}

//  Категории + свойства
function &categories_features_gen(array &$params, &$rapida): array
{
    open_close($params);
    $counter = $params['counter'] % 50000;
    
    $filter = ['visible' => 1, 'in_filter' => 1];

    if (!$categories = $rapida->categories->get_categories($filter)) {
        return $params;
    }
    foreach ($categories as &$c) {
        $filter['category_id'] = $c['children'];
        if (!$features = $rapida->features->get_features($filter)) {
            continue;

        }

        $filter['feature_id'] = array_keys($features);

        $options = $rapida->features->get_options_mix($filter);
        if (empty($options['filter'])) {
            continue;
        }


        foreach ($options['filter'] as $fid => &$vids) {


            foreach ($options['full'][$fid]['trans'] as $vid => &$opt) {
                $filter['features'][$fid] = $vid;
                $p_count = $rapida->products->count_products($filter);
                unset($filter['features'][$fid]);
                $pages = (int)ceil($p_count / ITEMS);

                while ($pages > 0) {
                    $url = HOSTNAME . 'catalog/' . $c['trans'] . "/" . $features[$fid]['trans'] . "-" . $opt;
                    $pages > 1 ? $url .= '/page-' . $pages : null;
                    //print $counter.$url."\n";
                    fwrite($params['fopen'], gen_url_string($url));
                    --$pages;
					++$params['counter'];
					if (++$counter === 50000) {
						$params = open_close($params);
						$counter = 0;
					}
                }
            }
        }
    }

    return open_close($params, $counter);
}


//товары

function &products_gen(array &$params, &$rapida)
{
    open_close($params);
    $counter = $params['counter'] % 50000;

    $rapida->db->query("SELECT trans FROM s_products WHERE visible=1");
    if (!$products = $rapida->db->results_array()) {
        return $params;
    }

    foreach ($products as &$p) {
        fwrite($params['fopen'], gen_url_string(HOSTNAME ."products/" . $p['trans']));
		++$params['counter'];
		if (++$counter === 50000) {
			$params = open_close($params);
			$counter = 0;
		}
    }
    return open_close($params, $counter);

}

function &sitemap_gen(array &$params){
    fwrite($params["fopen"], '</urlset>' . "\n");
    fclose($params['fopen']);
    gzconvert($params['path']);
    $params['path'] = null;

    $last_modify = date("Y-m-d");
    $dirpath = dirname(__FILE__) . "/../" ;
    $tmpfullpath_sitemap =  $dirpath . "_" . $params["name_tpl"] . ".xml";
    $newfullpath_sitemap = $dirpath . $params["name_tpl"] . ".xml";
    $params['fopen'] = fopen($tmpfullpath_sitemap, 'w');

    fwrite($params['fopen'], '<?xml version="1.0" encoding="UTF-8"?>' . "\n");
    fwrite($params['fopen'], '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n");

    for ($i = 1; $i <= $params['names']; ++$i) {
        $basename = $params['name_tpl'] . "_". $i .".xml.gz";
        $tmpfullpath = dirname(__FILE__) . "/../_".$basename ;
        $newfullpath = dirname(__FILE__) . "/../".$basename ;
        $url = HOSTNAME  . $basename;

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
    rename($tmpfullpath_sitemap, $newfullpath_sitemap);
    return $params;
}


//main_page_gen($params, $rapida);
pages_gen($params, $rapida);
brands_gen($params, $rapida);
categories_gen($params, $rapida);
categories_brands_gen($params, $rapida);
categories_features_gen($params, $rapida);
products_gen($params, $rapida);
sitemap_gen($params, $rapida);


echo "generated urls: ".$params['counter'];
