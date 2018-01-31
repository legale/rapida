<?php
require_once('api/Simpla.php');
$s = new Simpla();

$cats = $s->categories->get_categories();
//~ print_r($cats);
$cnt = 0;
foreach($cats as $c){
	$cnt++;	
	$s->categories->update_category($c['id'], array('name'=> $c['name']));
}
print "$cnt cats updated!\n";
//~ print 'hell';
