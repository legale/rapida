<?php
$time_start = microtime(true);

print "<pre>\r\n";
print "Rapida sitemap generator v0.0.1 \r\n";


require_once(dirname(__FILE__) . '/../api/Simpla.php');
$simpla = new Simpla();

//кол-во товаров на странице
$items_per_page = $simpla->settings->products_num;

$smap_xml = dirname(__FILE__) . '/../sitemap';
$smap_n = dirname(__FILE__) . '/../sitemap_';
$smap_n_prefix = dirname(__FILE__) . '/../_sitemap_';
$smap_n_ext = '.xml.gz';
$sitemap_index = 1;
$url_index = 1;

// ненужные фильтры
$filter_minus = array(27, 12, 34, 35, 36, 13, 14, 8, 15, 3);
$filter_minus_hard = array(27, 12, 34, 35, 36, 13, 14, 8, 15, 3, 18, 19, 21, 22, 27, 10, 4, 24, 16);
$filter_plus = array(2, 3, 4, 9, 10, 13, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 31);

// ненужные фильтры


if (file_exists($smap_n_prefix . $sitemap_index . $smap_n_ext)) {
    unlink($smap_n_prefix . $sitemap_index . $smap_n_ext);
}
$sitemaps[] = $sitemap_index;
$fopen = fopen($smap_n_prefix . $sitemap_index . $smap_n_ext, 'a');
fwrite($fopen, '<?xml version="1.0" encoding="UTF-8"?>' . "\n");
fwrite($fopen, '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n");

// Главная страница
//for cron!
$hostname = $simpla->config->host;
$url = $hostname . '/';

fwrite($fopen, "<url>" . "\n");
fwrite($fopen, "<loc>$url</loc>" . "\n");
fwrite($fopen, "</url>" . "\n");


//*
// Страницы
foreach ($simpla->pages->get_pages() as $p) {
    if (isset($p['visible']) && $p['menu_id'] == 1 && $p['trans']) {
        $url = $hostname . '/' . esc($p['trans']);
        fwrite($fopen, "<url>" . "\n");
        fwrite($fopen, "<loc>$url</loc>" . "\n");
        fwrite($fopen, "</url>" . "\n");
        if (++$url_index == 50000) {
            fwrite($fopen, '</urlset>' . "\n");
            fclose($fopen);

            gzconvert($smap_n_prefix . $sitemap_index . $smap_n_ext);

            $url_index = 0;
            $sitemap_index++;
            $sitemaps[] = $sitemap_index;
            if (file_exists($smap_n_prefix . $sitemap_index . $smap_n_ext)) {
                @unlink($smap_n_prefix . $sitemap_index . $smap_n_ext);
            }
            $fopen = fopen($smap_n_prefix . $sitemap_index . $smap_n_ext, 'a');
            fwrite($fopen, '<?xml version="1.0" encoding="UTF-8"?>' . "\n");
            fwrite($fopen, '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n");
        }
    }
//~ print "memory  pages usage: ".memory_get_usage(true)." bytes\r\n";
}
$p = '';
//*/

//*
// Блог
foreach ($simpla->blog->get_posts(array('visible' => 1)) as $p) {
    $url = $hostname . '/blog/' . esc($p['trans']);
    $date = substr($p['date'], 0, 10);
    fwrite($fopen, "<url>" . "\n");
    fwrite($fopen, "<loc>$url</loc>" . "\n");
    fwrite($fopen, "<lastmod>$date</lastmod>" . "\n");
    fwrite($fopen, "</url>" . "\n");
    if (++$url_index == 50000) {
        fwrite($fopen, '</urlset>' . "\n");
        fclose($fopen);
        gzconvert($smap_n_prefix . $sitemap_index . $smap_n_ext);

        $url_index = 0;
        $sitemap_index++;
        $sitemaps[] = $sitemap_index;
        if (file_exists($smap_n_prefix . $sitemap_index . $smap_n_ext)) {
            @unlink($smap_n_prefix . $sitemap_index . $smap_n_ext);
        }
        $fopen = fopen($smap_n_prefix . $sitemap_index . $smap_n_ext, 'a');
        fwrite($fopen, '<?xml version="1.0" encoding="UTF-8"?>' . "\n");
        fwrite($fopen, '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n");
    }
//~ print "memory  blog usage: ".memory_get_usage(true)." bytes\r\n";
}
$p = '';
//*/

/*
// Бренды

foreach($simpla->brands->get_brands() as $b) {
    $p_count = $simpla->products->count_products(array('visible' => 1, 'brand_id' => $b['id'], 'method' => 'sitemap' ));
    $pages_num = ceil($p_count/$items_per_page);
    $page = 1;
    while ($page <= $pages_num) {
        $brands[] = array($b['trans'], $page);
        $page++;
    }
}
dtimer::reset();

foreach($brands as $b) {
    $brand = $b[0];
    $page = $b[1];
    if ($page == 1) {
        $url = $hostname.'/brands/'.$brand;
    } else {
        $url = $hostname.'/brands/'.$brand.'/page-'.$page;
    }

    fwrite($fopen, "<url>"."\n");
    fwrite($fopen, "<loc>$url</loc>"."\n");
    fwrite($fopen, "</url>"."\n");
    if (++$url_index == 50000) {
        fwrite($fopen, '</urlset>'."\n");
        fclose($fopen);
        gzconvert($smap_n_prefix.$sitemap_index.$smap_n_ext);

        $url_index=0;
        $sitemap_index++;
        $sitemaps[] = $sitemap_index;
        if (file_exists($smap_n_prefix.$sitemap_index.$smap_n_ext)) {
            @unlink($smap_n_prefix.$sitemap_index.$smap_n_ext);
        }
        $fopen = fopen($smap_n_prefix.$sitemap_index.$smap_n_ext, 'a');
        fwrite($fopen, '<?xml version="1.0" encoding="UTF-8"?>'."\n");
        fwrite($fopen, '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n");
    }
//~ print "memory  brands only usage: ".memory_get_usage(true)." bytes\r\n";
}

$brands = '';
$b = '';
//*/

/*
// Категории
foreach($simpla->categories->get_categories() as $c) {
    if($c->visible) {
        $p_count = $simpla->products->count_products(array('visible' => 1, 'category_id'=>$c['children'], 'method' => 'sitemap' ));
        $pages_num = ceil($p_count/$items_per_page);
        $page = 1;
        while ($page <= $pages_num) {
            $cat[] = array($c['trans'], $page);
            $page++;
        }
    }
}
dtimer::reset();

foreach($cat as $c) {
    if($c[1] == 1) {
        $url = $hostname.'/catalog/'.$c[0];
    } else {
        $url = $hostname.'/catalog/'.$c[0].'/page-'.$c[1];
    }

    fwrite($fopen, "<url>"."\n");
    fwrite($fopen, "<loc>$url</loc>"."\n");
    fwrite($fopen, "</url>"."\n");
    if (++$url_index == 50000) {
        fwrite($fopen, '</urlset>'."\n");
        fclose($fopen);
        gzconvert($smap_n_prefix.$sitemap_index.$smap_n_ext);

        $url_index=0;
        $sitemap_index++;
        $sitemaps[] = $sitemap_index;
        if (file_exists($smap_n_prefix.$sitemap_index.$smap_n_ext)) {
            @unlink($smap_n_prefix.$sitemap_index.$smap_n_ext);
        }
        $fopen = fopen($smap_n_prefix.$sitemap_index.$smap_n_ext, 'a');
        fwrite($fopen, '<?xml version="1.0" encoding="UTF-8"?>'."\n");
        fwrite($fopen, '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n");
    }
}
//~ print "memory  cat usage: ".memory_get_usage(true)." bytes\r\n";

$cat ='';
$c = '';
//*/

//*
// Категории + бренды
foreach ($simpla->categories->get_categories() as $c) {
    if ($c['visible']) {
        foreach ($simpla->brands->get_brands(array('category_id' => $c['children'])) as $b) {
            $p_count = $simpla->products->count_products(array('visible' => 1, 'brand_id' => $b['id'], 'category_id' => $c['children'], 'method' => 'sitemap'));
            $pages_num = ceil($p_count / $items_per_page);
            $page = 1;
            while ($page <= $pages_num) {
                $brands[] = array($c['trans'], $b['trans'], $page);
                $page++;
            }
        }
    }
}
dtimer::reset();

foreach ($brands as $b) {
    $cat_trans = $b[0];
    $brand = $b[1];
    $page = $b[2];
    if ($page == 1) {
        $url = $hostname . '/catalog/' . $cat_trans . '/brand-' . $brand;
    } else {
        $url = $hostname . '/catalog/' . $cat_trans . '/brand-' . $brand . '/page-' . $page;
    }
    fwrite($fopen, "<url>" . "\n");
    fwrite($fopen, "<loc>$url</loc>" . "\n");
    fwrite($fopen, "</url>" . "\n");
    if (++$url_index == 50000) {
        fwrite($fopen, '</urlset>' . "\n");
        fclose($fopen);
        gzconvert($smap_n_prefix . $sitemap_index . $smap_n_ext);

        $url_index = 0;
        $sitemap_index++;
        $sitemaps[] = $sitemap_index;
        if (file_exists($smap_n_prefix . $sitemap_index . $smap_n_ext)) {
            @unlink($smap_n_prefix . $sitemap_index . $smap_n_ext);
        }
        $fopen = fopen($smap_n_prefix . $sitemap_index . $smap_n_ext, 'a');
        fwrite($fopen, '<?xml version="1.0" encoding="UTF-8"?>' . "\n");
        fwrite($fopen, '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n");
    }
}
dtimer::reset();
//~ print "memory  brand usage: ".memory_get_usage(true)." bytes\r\n";

$c = '';
$b = '';
////*/

//*
// Категории + feature
$cats = $simpla->categories->get_categories();
$len = count($cats);
$cnt = 0;
foreach ($cats as $c) {
    $cnt++;
    if ($c['visible']) {
        print $c['name'] . " $cnt/$len \n";
        //тут сами свойства
        $features = $simpla->features->get_features(array('visible' => 1, 'category_id' => $c['children'], 'method' => 'sitemap'));
//        print_r($fids);

        //тут значения свойств
        $options = $simpla->features->get_options_mix(array('visible' => 1, 'feature_id' => array_keys($features), 'category_id' => $c['children'], 'method' => 'sitemap'));
        // обнуляем ненужный больше массив

        $cat_feat = array();
        if ($features) {
            foreach ($features as $fid => $f) {
                if ($options) {
                    foreach ($options['filter'][$fid] as $vid => $val) {
                        $features_filter = array($fid => array($vid));
                        $p_count = $simpla->products->count_products(array('visible' => 1, 'category_id' => $c['children'], 'features' => $features_filter, 'method' => 'sitemap'));
                        $pages_num = ceil($p_count / $items_per_page);
                        $page = 1;
                        while ($page <= $pages_num) {
                            $cat_feat[] = array($c['trans'], $f['trans'], $options['full'][$fid]['trans'][$vid], $page);
                            $page++;
                        }
                    }
                }
            }
        }


        dtimer::reset();
        if ($cat_feat) {
            foreach ($cat_feat as $cf) {
                $cat_trans = $cf[0];
                $feature_trans = $cf[1] . '-' . $cf[2];
                $page = $cf[3];
                if ($page == 1) {
                    $url = $hostname . '/catalog/' . $cat_trans . '/' . $feature_trans;
                } else {
                    $url = $hostname . '/catalog/' . $cat_trans . '/' . $feature_trans . '/page-' . $page;
                }
                $fopen = fopen($smap_n_prefix . $sitemap_index . $smap_n_ext, 'a');
                fwrite($fopen, "<url>" . "\n");
                fwrite($fopen, "<loc>$url</loc>" . "\n");
                fwrite($fopen, "</url>" . "\n");
                if (++$url_index == 50000) {
                    fwrite($fopen, '</urlset>' . "\n");
                    fclose($fopen);
                    gzconvert($smap_n_prefix . $sitemap_index . $smap_n_ext);

                    $url_index = 0;
                    $sitemap_index++;
                    $sitemaps[] = $sitemap_index;
                    if (file_exists($smap_n_prefix . $sitemap_index . $smap_n_ext)) {
                        @unlink($smap_n_prefix . $sitemap_index . $smap_n_ext);
                    }
                    $fopen = fopen($smap_n_prefix . $sitemap_index . $smap_n_ext, 'a');
                    fwrite($fopen, '<?xml version="1.0" encoding="UTF-8"?>' . "\n");
                    fwrite($fopen, '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n");
                }
            }
        }
        dtimer::reset();
    }
}

unset($cats, $c, $features, $options);
////*/


/*
// Категории + brand + feature

$cats = $simpla->categories->get_categories();
foreach($cats as $c) {
    if($c->visible) {
        $brands = $simpla->brands->get_brands(array('category_id'=>$c->children));
        foreach($brands as $b) {
            //получаем массив свойств для категории
            $f0_object = array();
            $f0_object = $simpla->features->get_features(array('category_id'=>$c->children, 'in_filter'=>1));
            //фильтруем массив, убирая те id, которые есть в $filter_minus
            //и создаем массив из нужных feature_id
            $f0_ids = array();
            for(reset($f0_object), $i = key($f0_object); next($f0_object); $i = key($f0_object)) {
                if(in_array($f0_object[$i]->id, $filter_minus)) {
                    unset($f0_object[$i]);
                } else {
                    $f0_ids[] = $f0_object[$i]->id;
                }
            }
            //print_r($f0_ids);
            //die;
            //создаем массив из features и options
            $feat_opt0 = feat_opt(array('visible' => 1 , 'brand_id' => $b->id , 'feature_id'=>$f0_ids, 'f0_object'=>$f0_object, 'category_id'=>$c->children, 'method' => 'sitemap'));
            // обнуляем ненужный больше массив
            unset($f0_object);
            //print_r($feat_opt0);
            if ($feat_opt0) {
                foreach ($feat_opt0 as $f0) {
                    $features = array();
                    $features[$f0['id']][] = $f0['translit'];
                    $p_count = $simpla->products->count_products(array('visible' => 1, 'brand_id' => $b->id , 'category_id'=>$c->children, 'features'=>$features, 'sort' => 'click_desc', 'method' => 'sitemap' ));
                    $pages_num = ceil($p_count/$items_per_page);
                    $page = 1;
                    while ($page <= $pages_num) {
                        $cat_feat[] = array($c->url, $b->url , $f0['url'], $f0['translit'], $page);
                        $page++;
                    }
                }
            }
            dtimer::reset();
            if ($cat_feat) {
                foreach($cat_feat as $cf) {
                    $cat = $cf[0];
                    $brand = $cf[1];
                    $f_url = $cf[2].'-'.$cf[3];
                    $page = $cf[4];
                    if ($page == 1) {
                        $url = $hostname.'/catalog/'.$cat.'/'.'brand-'.$brand.'/'.$f_url;
                    } else {
                        $url = $hostname.'/catalog/'.$cat.'/'.'brand-'.$brand.'/'.$f_url.'/page-'.$page;
                    }
                    $fopen = fopen($smap_n_prefix.$sitemap_index.$smap_n_ext, 'a');
                    fwrite($fopen, "<url>"."\n");
                    fwrite($fopen, "<loc>$url</loc>"."\n");
                    fwrite($fopen, "</url>"."\n");
                    if (++$url_index == 50000) {
                        fwrite($fopen, '</urlset>'."\n");
                        fclose($fopen);
                        gzconvert($smap_n_prefix.$sitemap_index.$smap_n_ext);

                        $url_index=0;
                        $sitemap_index++;
                        $sitemaps[] = $sitemap_index;
                        if (file_exists($smap_n_prefix.$sitemap_index.$smap_n_ext)) {
                            @unlink($smap_n_prefix.$sitemap_index.$smap_n_ext);
                        }
                        $fopen = fopen($smap_n_prefix.$sitemap_index.$smap_n_ext, 'a');
                        fwrite($fopen, '<?xml version="1.0" encoding="UTF-8"?>'."\n");
                        fwrite($fopen, '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n");
                    }
                }
            }
            $cat_feat = '';
            dtimer::reset();
        }
        unset($brands);
    }
}

$c = '';
$cats = '';
////*/


/*
// Категории + feature + feature


$c = $simpla->categories->get_categories();
dtimer::reset();
for (reset($c), $i3 = key($c); next($c); $i3 = key($c)){
    if($c[$i3]->visible) {
        print "before category cycle + f + f memory usage: ".memory_get_usage(true)." bytes\r\n";
        print($c[$i3]->name."\n");

        //получаем массив свойств для категории
        $f0_object = array();
        $f0_object = $simpla->features->get_features(array('category_id'=>$c->children, 'in_filter'=>1));
        //фильтруем массив, убирая те id, которые есть в $filter_minus
        //и создаем массив из нужных feature_id
        $f0_ids = array();
        for(reset($f0_object), $i = key($f0_object); next($f0_object); $i = key($f0_object)) {
            if(in_array($f0_object[$i]->id, $filter_minus)) {
                unset($f0_object[$i]);
            } else {
                $f0_ids[] = $f0_object[$i]->id;
            }
        }


        //создаем массив из features и options считаем кол-во товаров и делаем пагинацию
        $feat_opt0 = feat_opt(array('visible' => 1, 'feature_id'=>$f0_ids, 'f0_object'=>$f0_object, 'category_id'=>$c[$i3]->children, 'method' => 'sitemap'));
        unset($f0_ids, $f0_object);
        //print_r($feat_opt0);

        $fm = feat_mix($c[$i3]->children, $feat_opt0, $feat_opt0, null);

        unset($feat_opt0);
        //print_r($fm);
        for($i = 0, $count = count($fm); $count > $i; $i++) {

            $p_count = $fm[$i]['p_count'];
            $pages_num = ceil($p_count/$items_per_page);
            $page = 1;
            while ($page <= $pages_num) {
                $cat_feat[] = array($c[$i3]->url, $fm[$i]['url'], $fm[$i]['translit'], $fm[$i]['url2'], $fm[$i]['translit2'], $page);
                $page++;
            }
        }
        unset($fm);
        //print_r($cat_feat);

        if ($cat_feat) {
            foreach($cat_feat as $cf) {
                $cat = $cf[0];
                $f_url = $cf[1].'-'.$cf[2]."/".$cf[3].'-'.$cf[4];
                $page = $cf[5];
                if ($page == 1) {
                    $url = $hostname.'/catalog/'.$cat.'/'.$f_url;
                } else {
                    $url = $hostname.'/catalog/'.$cat.'/'.$f_url.'/page-'.$page;
                }
                $fopen = fopen($smap_n_prefix.$sitemap_index.$smap_n_ext, 'a');
                fwrite($fopen, "<url>"."\n");
                fwrite($fopen, "<loc>$url</loc>"."\n");
                fwrite($fopen, "</url>"."\n");
                if (++$url_index == 50000) {
                    fwrite($fopen, '</urlset>'."\n");
                    fclose($fopen);
                    gzconvert($smap_n_prefix.$sitemap_index.$smap_n_ext);

                    $url_index=0;
                    $sitemap_index++;
                    $sitemaps[] = $sitemap_index;
                    if (file_exists($smap_n_prefix.$sitemap_index.$smap_n_ext)) {
                        @unlink($smap_n_prefix.$sitemap_index.$smap_n_ext);
                    }
                    $fopen = fopen($smap_n_prefix.$sitemap_index.$smap_n_ext, 'a');
                    fwrite($fopen, '<?xml version="1.0" encoding="UTF-8"?>'."\n");
                    fwrite($fopen, '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n");
                }
            }
        }
        unset($cat_feat);
        dtimer::reset();
    }
    unset($c[$i3]);
}
unset($c);

//*/


/*
// Товары
$simpla->db->query("SELECT url, last_modify FROM __products WHERE visible=1");
foreach($simpla->db->results() as $p) {
    $url = $hostname.'/products/'.esc($p->url);
    $last_modify = substr($p->last_modify, 0, 10);
    $fopen = fopen($smap_n_prefix.$sitemap_index.$smap_n_ext, 'a');
    fwrite($fopen, "<url>"."\n");
    fwrite($fopen, "<loc>$url</loc>"."\n");
    fwrite($fopen, "</url>"."\n");
    if (++$url_index == 50000) {
        fwrite($fopen, '</urlset>'."\n");
        fclose($fopen);
        gzconvert($smap_n_prefix.$sitemap_index.$smap_n_ext);

        $url_index=0;
        $sitemap_index++;
        $sitemaps[] = $sitemap_index;
        if (file_exists($smap_n_prefix.$sitemap_index.$smap_n_ext)) {
            @unlink($smap_n_prefix.$sitemap_index.$smap_n_ext);
        }
        $fopen = fopen($smap_n_prefix.$sitemap_index.$smap_n_ext, 'a');
        fwrite($fopen, '<?xml version="1.0" encoding="UTF-8"?>'."\n");
        fwrite($fopen, '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n");
    }
//print "products memory usage: ".memory_get_usage(true)." bytes\r\n";
}
$p = '';
//*/

fwrite($fopen, '</urlset>' . "\n");
fclose($fopen);

// удаляем старый составной sitemap, если он создан
if (file_exists($smap_xml . '.xml')) {
    @unlink($smap_xml . '.xml');
}
// открываем на запись и дополнение файл составного sitemap
$fopen = fopen($smap_xml . '.xml', 'a');

$last_modify = date("Y-m-d");

fwrite($fopen, '<?xml version="1.0" encoding="UTF-8"?>' . "\n");
fwrite($fopen, '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n");
foreach ($sitemaps as $s) {
    $url = $hostname . '/' . pathinfo($smap_n, PATHINFO_FILENAME) . $s . $smap_n_ext;
    if (file_exists($smap_n . $s . $smap_n_ext)) {
        @unlink($smap_n . $s . $smap_n_ext);
    }
    rename($smap_n_prefix . $s . $smap_n_ext, $smap_n . $s . $smap_n_ext);
    fwrite($fopen, "<sitemap>" . "\n");
    fwrite($fopen, "<loc>$url</loc>" . "\n");
    fwrite($fopen, "<lastmod>$last_modify</lastmod>" . "\n");
    fwrite($fopen, "</sitemap>" . "\n");
}
fwrite($fopen, '</sitemapindex>' . "\n");
fclose($fopen);


print "memory   peak usage: " . memory_get_peak_usage(true) . " bytes\r\n";
print "Sitemap generation done!\n\n";


$time_end = microtime(true);
$exec_time = $time_end - $time_start;

print file_get_contents($smap_xml . '.xml') . "\r\n";
print "Generation time: " . $exec_time . " seconds\r\n";


function esc($s)
{
    return (htmlspecialchars($s, ENT_COMPAT, 'UTF-8'));
}


function gzconvert($file)
{
    if (!file_exists($file)) {
        return false;
    }

    $file_new = $file . "_";
    //пишем все это в сжатом виде
    $input = fopen($file, "r");
    $output = gzopen($file_new, "w9");

    //сжимаем и пишем в файл кусками по 4мб
    while (!feof($input)) {
        $data = fread($input, 4000000);
        gzwrite($output, $data);
    }

    fclose($input);
    gzclose($output);
    @unlink($file);
    if (rename($file_new, $file)) {
        return true;
    } else {
        return false;
    }

}


//*
// функции для объединения массивов со значениями и массива с названиями опций
function feat_opt($filter = array())
{
    $simpla = new Simpla();

    if (!isset($filter['feature_id'])) {
        return print("feature_id IS NOT SET\n");
    }
    if (!isset($filter['f0_object'])) {
        return print("f0_object IS NOT SET\n");
    } else {
        $f0_object = $filter['f0_object'];
        unset($filter['f0_object']);
    }
    if (!isset($filter['category_id'])) {
        return print("category_id IS NOT SET\n");
    }

    //print_r($filter);

    $o0_object = $simpla->features->get_options_mix($filter);
    //print_r(array_values($o0_object));
    dtimer::reset();
    if (count($o0_object) == 0) {
        return;
    }
    for ($i = 0, $count = count($o0_object); $count > $i; $i++) {
        for ($i2 = 0, $count2 = count($f0_object); $count2 > $i2; $i2++) {
            if ($f0_object[$i2]->id == $o0_object[$i]->feature_id) {
                //$features_array[] = array($f0_object[$i2]->id=>array($o0_object[$i]->translit));
                $pre[$f0_object[$i2]->id][] = array(
                    'name' => $f0_object[$i2]['name'],
                    'trans' => $f0_object[$i2]['trans'],
                    'id' => $f0_object[$i2]['id'],
                    'value' => $o0_object[$i]['value'],
                    'trans_val' => $o0_object[$i]['trans']);
            }
        }
    }
    //print_r($pre);
    //die;
    // очищаем массив от тех фильтров, где только 1 значение
    $feat_opt = array();
    foreach ($pre as $fe0) {
        if (count($fe0) > 1) {
            $feat_opt = array_merge($feat_opt, $fe0);
        }
    }

    return $feat_opt;
}

// функция для перемножения массивов
function feat_mix($children, $feat0, $feat1)
{
    //print_r($children);
    $simpla = new Simpla();
    $keys = array();
    $k1 = 0;
    for ($i = 0, $count = count($feat0); $count > $i; $i++) {
        $k1++;
        $k2 = 0;
        for ($i2 = 0, $count2 = count($feat1); $count2 > $i2; $i2++) {
            $k2++;
            if ($feat0[$i]['id'] != $feat1[$i2]['id']) {
                $key = array($k1, $k2);
                asort($key);
                $key = (integer)implode("", $key);

                if (!isset($keys[$key])) {
                    $keys[$key] = 1;


                    // сброс переменной с кол-вом товаров с заданными параметрами фильтров
                    $p_count = '';
                    // создаем массив $features_mix из 2 пар параметр-значение для подсчета кол-ва товаров
                    $features_mix = array();
                    $features_mix[$feat0[$i]['id']][] = $feat0[$i]['trans_val'];
                    $features_mix[$feat1[$i2]['id']][] = $feat1[$i2]['trans_val'];
                    //print_r($features_mix);
                    // считаем кол-во товаров с заданными параметрами фильтров
                    $p_count = $simpla->products->count_products(array('visible' => 1, 'category_id' => $children, 'features' => $features_mix, 'method' => 'sitemap'));
                    dtimer::reset();
                    //print("\n".$p_count."\n");
                    // добавляем в итоговый массив только то, что с кол-вом товаров больше 0


                    if ($p_count > 0) {
                        $feat_opt_mix[] = array(
                            'p_count' => $p_count,
                            'key' => $key,
                            'name' => $feat0[$i]['name'],
                            'trans' => $feat0[$i]['trans'],
                            'id' => $feat0[$i]['id'],
                            'value' => $feat0[$i]['value'],
                            'trans_val' => $feat0[$i]['trans_val'],
                            'name2' => $feat1[$i2]['name'],
                            'trans2' => $feat1[$i2]['trans'],
                            'id2' => $feat1[$i2]['id'],
                            'value2' => $feat1[$i2]['value'],
                            'trans_val2' => $feat1[$i2]['trans_val']
                        );
                    }
                }
            }
        }
    }
    return $feat_opt_mix;
}
////*/
