<?php
	require_once('../api/Simpla.php');
	$simpla = new Simpla();
	$limit = 30;
	
	$keyword = $simpla->request->get('query', 'string');
	$kw = $simpla->db->escape($keyword);
	$filter = array('keyword'=>$kw, 'limit'=> $limit);
    $res = array();

	if($products = $simpla->products->get_products($filter)) {
        foreach ($products as $p) {
            //~ $res[] = array($p['name'], $simpla->design->resize_modifier($p['image'],'products', $p['id'], 35, 35));
            $res[] = $p['name'];
        }
    }
	header("Content-type: application/json; charset=UTF-8");
	header("Cache-Control: must-revalidate");
	header("Pragma: no-cache");
	header("Expires: -1");		
	print json_encode($res);
