<?php
require_once('api/Simpla.php');
$s = new Simpla();

//~ $s->db->query("SELECT id, trans2 FROM s_options_uniq WHERE 1");
//~ $res = $s->db->results_array();
//~ $cnt = 0;
//~ foreach($res as $r){
	//~ if(!empty($r['trans2'])){
		//~ $id = $r['id'];
		//~ $hash = hash('MD4', $r['trans2']);
		//~ $q = "UPDATE s_options_uniq SET `md42` = 0x$hash WHERE `id` = $id";
		//~ print "\n $q \n";
		//~ $s->db2->query($q);
		//~ $cnt++;
	//~ }
//~ }

$cnt = 2;
$q = "SELECT id, val, trans, HEX(md4) as md4 FROM s_options_uniq WHERE 1";

//~ $i = 0;
//~ while($i < $cnt){
	//~ dtimer::log("start");
	//~ $i++;
	//~ $r = $s->db->real_query($q);
	//~ $s->db->res = $s->db->mysqli->use_result();
	//~ while( $res = $s->db->res->fetch_row() );
	//~ unset($res);
	//~ dtimer::log("real_query");
//~ }

//~ $i =0;
//~ while($i < $cnt){
	//~ dtimer::log("start");
	//~ $i++;
	//~ $r = $s->db->query($q);
	
	//~ $res = $s->db->results_array();
	//~ foreach($res as $row);
	//~ unset($res);
	//~ dtimer::log("query results array");
//~ }

//~ $i =0;
//~ while($i < $cnt){
	//~ dtimer::log("start");
	//~ $i++;
	//~ $r = $s->db->query($q);
	
	//~ while($res = $s->db->result_array());
	//~ unset($res);
	//~ dtimer::log("query result array in while");
//~ }


function rq(&$s, $q, $t){
	$r = $s->db->real_query($q);
	$s->db->res = $s->db->mysqli->store_result();
	foreach($s->db->res->fetch_fields() as $f){
		$fields[] = $f->name;
	}
	//~ print_r($fields);
	while($row = $s->db->res->fetch_row()){
		$res[0] = array_combine($fields, $row);
	}
	return $res;
}

function sq($s, $q, $t){
	$r = $s->db->query($q);
	while($row = $s->db->res->fetch_assoc()){
		$res[] = $row;
	}

	return $res;
}

function res($s, $q, $t){
	for($i = 0; $i < $t; $i++){
		$r = $s->db->query($q);
		//~ print_r($fields);
		while($row = $s->db->res->fetch_assoc()){
			$res[0][] = array($row['id']=>$row['trans']);
			$res[1][] = array($row['trans']=>$row['id']);
		}
	//~ print_r(reset($res));
	}
	return $res;
}

function res2($s, $q, $t){
	for($i = 0; $i < $t; $i++){
		$r = $s->db->query($q);
		//~ print_r($fields);
		foreach($s->db->results_array() as $row ){
			$res[0][] = array($row['id']=>$row['trans']);
			$res[1][] = array($row['trans']=>$row['id']);
		}
		//~ print_r(reset($res));
	}
	return $res;
}

function read_cache($s, $t){
	for($i = 0; $i < $t; $i++){
		$r = $s->cache->get_cache_nosql('key_write');
	}
	return $r;
}

function write_cache($s, $q, $t){
	for($i = 0; $i < $t; $i++){
		$r = $s->cache->set_cache_nosql('key_write', $q);
	}
	return $r;
}

function apcu_read($s, $t){
	for($i = 0; $i < $t; $i++){
		$r = apcu_fetch('key_write');
	}
	return $r;
}

function apcu_write($s, $q, $t){
	for($i = 0; $i < $t; $i++){
		$r = apcu_add('key_write', $q);
	}
	return $r;
}

$saved[0] = $s->config->cache; 
$s->cache::$enabled = true;
dtimer::$enabled = true;


$t = 1;
$a = res($s, $q, $t);
//~ print_r(reset($a)[0]);

//~ print "\n".levenshtein('привет', 'прювет');
//~ print "\n".levenshtein('привет', 'привед');



$s->cache->get_cache_nosql('key_write');

timer('write_cache', array(&$s, $a, $t) );
timer('read_cache', array(&$s, $t) );

timer('apcu_write', array(&$s, $a, $t) );
timer('apcu_read', array(&$s, $t) );


dtimer::show();
