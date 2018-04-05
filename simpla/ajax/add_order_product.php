<?php
require_once('../../api/Simpla.php');
$simpla = new Simpla();
$limit = 100;

if (!$simpla->managers->access('orders'))
    return false;

$keyword = $simpla->request->get('query', 'string');
if (empty_($keyword)) {
    return false;
}


$filter = array(
    'keyword' => $keyword,
    'force_no_cache' => true,
);

if ($products = $simpla->products->get_products($filter)) {
    if ($variants = $simpla->variants->get_variants(array('product_id' => array_keys($products), 'in_stock' => true))) {
        foreach ($variants as &$variant) {
            $variant['stock'] = $variant['stock'] === null ? 99 : $variant['stock'];
            $products[$variant['product_id']]['variants'][] = $variant;
        }
        unset($variant);
    }
}


$hints = array();

if (!empty_($products) && is_array($products)) {
    foreach ($products as $p) {
        if (!empty($p['variants'])) {
            $hint = array();
            if (!empty($p['image'])) {
                $p['image'] = $simpla->design->resize_modifier($p['image'], 'products', $p['image_id'], 35, 35);
            }
            $hint['value'] = $p['name'];
            $hint['data'] = $p;
            $hints[] = $hint;
        }
    }
}


$res = array();
$res['query'] = $keyword;
$res['suggestions'] = $hints;
header("Content-type: application/json; charset=UTF-8");
header("Cache-Control: must-revalidate");
header("Pragma: no-cache");
header("Expires: -1");
print json_encode($res);
