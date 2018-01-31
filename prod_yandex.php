<?php
require_once('api/Simpla.php');
$s = new Simpla();

$pr = $s->products->get_products(array('limit'=> 99999999999999));
//~ print_r($cats);
$cnt = 0;
foreach($pr as $p){
	$cnt++;	
	$s->products->update_product(array('id'=> $p['id'], 'name'=> $p['name']));
}
print "$cnt products updated!\n";
//~ print 'hell';
