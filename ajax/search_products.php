<?php
	require_once('../api/Simpla.php');
	$simpla = new Simpla();
	$limit = 30;
	
	$keyword = $simpla->request->get('query', 'string');
	$kw = $simpla->db->escape($keyword);
	$filter = array('keyword'=>$kw, 'limit'=> $limit);
	$products = $simpla->products->get_products($filter);

	$suggestions = array();
	
	if(!empty($products)){
		foreach($products as $product){
			$suggestion = new stdClass();
			if(!empty($product['image']))
				$product['image'] = $simpla->design->resize_modifier($product['image'], 35, 35);
				
			$suggestion->value = $product['name'];
			$suggestion->data = $product;
			$suggestions[] = $suggestion;
		}
	}
	$res = new stdClass;
	$res->query = $keyword;
	$res->suggestions = $suggestions;
	header("Content-type: application/json; charset=UTF-8");
	header("Cache-Control: must-revalidate");
	header("Pragma: no-cache");
	header("Expires: -1");		
	print json_encode($res);
