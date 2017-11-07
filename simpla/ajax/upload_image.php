<?php

session_start();

require_once('../../api/Simpla.php');

$simpla = new Simpla();


if ( $simpla->request->method('post') && $simpla->request->files("file") )
{
	$images = $simpla->request->files("file");
	$pid = $simpla->request->post("product_id");
	$res = array();
	//~ print_r($_FILES);
	//~ print_r($images);
	//~ die;
	foreach($images['name'] as $k=>$name){
		$file = $images['tmp_name'][$k];
		if ($saved = $simpla->image->upload_image($file, $name))
		{
			if($id = $simpla->products->add_image($pid, $saved)){
				$res[] = array('id'=> $id, 'name' => $saved);
			}
		}
	}
	
header("Content-type: application/json; charset=UTF-8");
header("Cache-Control: must-revalidate");
header("Pragma: no-cache");
header("Expires: -1");		
		
$json = json_encode($res);

print $json;
}
