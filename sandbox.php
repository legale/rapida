<?php
session_start();
if(isset($_SESSION['admin'])){
	require_once(dirname(__FILE__) .'/api/Simpla.php');
	$simpla = new Simpla();
	
	
	//~ $res = $simpla->features->get_options(array('force_no_cache' => true, 'feature_id' => array(1,2,4), 'features' => array(2=>'смартфон/коммуникатор') ));
	//~ $res = $simpla->features->get_options(array ('product_id' => '1', 'force_no_cache' => true));
	//~ $res = $simpla->features->get_options_uniq();
	
	//~ $simpla->db->query("INSERT INTO s_orders SET `delivery_id`='1', `name`='Стандарт', `email`='legale.legale@gmail.com', `address`='3252', `phone`='9265723322', `comment`='', `ip`='127.0.0.1', `discount`='0', `url`='3287bc0a51dd9958334d21378515ebfc', date=now()");
	//~ $res = $simpla->db->insert_id();
	
	$order_id = 8;
	$pid = 10;
	//~ $res = $simpla->orders->get_order($order_id);
	//~ $res = $simpla->features->get_product_options($pid);
	$res = $simpla->features->sync_options();
	print "HELLO!\n";
	print_r($res);
	print "</PRE>";
	dtimer::show();

}