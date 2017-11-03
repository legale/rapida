<?php
session_start();
if (isset($_SESSION['admin'])) {
	require_once (dirname(__FILE__) . '/api/Simpla.php');
	$simpla = new Simpla();
	
	//корневая директория системы 
	$dir = dirname(__FILE__) . '/';

	//имя создаваемого архива
	$filename = $dir . 'distro/rapida_' . $simpla->config->version . '.zip';

	$dbfile = 'rapida.sql';


	function glob_recurse($glob)
	{
		$res = array();
		foreach (glob($glob) as $g) {
			$g = realpath($g);
			if (is_file($g)) {
				$res[] = $g;
				print "\n$g";
			}
			else {
				$res = array_merge($res, glob_recurse($glob . '/*'));
			}
		}
		return $res;
	}

	function filesize_remote($url)
	{
		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, TRUE);
		curl_setopt($ch, CURLOPT_NOBODY, TRUE);

		$data = curl_exec($ch);
		$size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

		curl_close($ch);
		return $size;
	}

	function copy_remote($url, $dest)
	{
		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, TRUE);
		curl_setopt($ch, CURLOPT_NOBODY, TRUE);

		$data = curl_exec($ch);
		$size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

		curl_close($ch);
		return $size;
	}

	//**********************************
	print "HELLO!\n";
	print "<pre>";
	//**********************************


	//~ $res = $simpla->features->get_options(array('force_no_cache' => true, 'feature_id' => array(1,2,4), 'features' => array(2=>'смартфон/коммуникатор') ));
	//~ $res = $simpla->features->get_options(array ('product_id' => '1', 'force_no_cache' => true));
	//~ $res = $simpla->features->get_options_uniq();
	
	//~ $simpla->db->query("INSERT INTO s_orders SET `delivery_id`='1', `name`='Стандарт', `email`='legale.legale@gmail.com', `address`='3252', `phone`='9265723322', `comment`='', `ip`='127.0.0.1', `discount`='0', `url`='3287bc0a51dd9958334d21378515ebfc', date=now()");
	//~ $res = $simpla->db->insert_id();

	$order_id = 8;
	$pid = 10;
	// $res = $simpla->orders->get_order($order_id);
	//~ $res2 = $simpla->features->get_product_options($pid);

	//~ $res3 = filesize_remote('http://sevenlight.ru/logo.png');
	//~ $q = "SELECT product_id as pid, position as pos FROM __images WHERE product_id = 1 LIMIT 1";
	//~ $simpla->db->query($q);
	//~ $num = $simpla->db->num_rows();
	//~ $aff = $simpla->db->affected_rows();
	//~ $aff = $simpla->db->affected_rows();
	//~ $res = $simpla->db->results();

	$res = $simpla->coMaster->parse_uri('https://yandex.ru/catalog/svet/brand-arte_lamp.citylux/sort-name/my-life/his-shit.thing') ;
	$res2 = $simpla->coMaster->parse_uri('https://yandex.ru/cart/something-dsfds-dgsfgdsfg') ;
	$res3 = $simpla->coMaster->parse_uri('https://yandex.ru/user/login') ;
	$res4 = $simpla->brands->get_brands_ids();
	$res5 = $simpla->brands->get_brands_ids();
	$res6 = $simpla->features->get_options_ids();
	
	
	$ar =  array (
    'module' => 'catalog',
    'url' => 'svet',
    'sort' => 'name',
    'brand' =>
    array (
    'arte_lamp',
    'mwlight',
    'citylux',
    ),
    'filter' => 
    array (
      'stil~' => 
      array (
         'retro',
         'klassika',
         'loft',
      ),
      'cvet' => 
      array (
         'red',
      ),
    ),
    );
	$ar2 =  array (
    'brand' =>
    array (
		'mwlight',
		'eglo',
    ),
    'filter' => 
    array (
      'stil~' => 
      array (
        'retro',
        'loft',
		'provans',
      ),
      'cvet' => 
      array (
        'green',
      ),
    ),
    );
    $ar3 = array(
    	'sort'=>'',
		'filter'=>array(
			'stil~'=>array(),
			),
    );
    
	print $simpla->coMaster->gen_uri($ar, $ar3) ."\n";
	//~ $res = $simpla->sys->sync_options();
	//~ $res = $simpla->sys->clear_options();
	//~ $files = glob_recurse('./*');
	
	//делает дамп базы
	//$simpla->db->dump($dir . $dbfile);



	var_export(	$res);
	var_export(	$res2);
	var_export(	$res3);
	print "\n\n\n\n\n";
	print_r($simpla->coMaster->parse_uri('http://ya.ru/files/products/baby.lightt.my.fire'));
	//~ print_r($res6);
	//~ print_r($res3);
	// var_dump($num);
	// var_dump($aff);
	

	print "</PRE>";
	dtimer::show();

}
