<?php
	require_once('../../api/Simpla.php');
	$simpla = new Simpla();
	$limit = 100;
	
	if(!$simpla->managers->access('orders'))
		return false;
	
	$keyword = $simpla->request->get('query', 'string');
	if(empty_($keyword)){
		return false;
	}

	
	$filter = array(
	'keyword'=> $keyword,
	'force_no_cache' => true,
	);
	
	if($products = $simpla->products->get_products($filter)){
		if($variants = $simpla->variants->get_variants(array('product_id'=>array_keys($products), 'in_stock'=>true)) ) {
			foreach($variants as &$variant){
				$products[$variant['product_id']]['variants'][] = $variant;
			}
			unset($variant);
		}
	}

	
	$suggestions = array();

	if(!empty_($products) && is_array($products)){
		foreach($products as $product)
		{
			if(!empty($product['variants']))
			{
				$suggestion = array();
				if(!empty($product['image'])){
					$product['image'] = $simpla->design->resize_modifier($product['image'], 35, 35);
				}
				$suggestion['value'] = $product['name'];
				$suggestion['data'] = $product;
				$suggestions[] = $suggestion;
			}
		}
	}
	
	
	$res = array();
	$res['query'] = $keyword;
	$res['suggestions'] = $suggestions;
	header("Content-type: application/json; charset=UTF-8");
	header("Cache-Control: must-revalidate");
	header("Pragma: no-cache");
	header("Expires: -1");		
	print json_encode($res);
