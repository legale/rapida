<?php
require_once('api/Simpla.php');
$s = new Simpla();


//~ print_r($cats);

$s->db->query("SELECT id, name FROM __features WHERE 1");
$vals = $s->db->results_array();
$cnt = 0;

foreach($vals as $v){
	$cnt++;	
	$s->db->query("UPDATE __features SET trans = ? WHERE 1 AND id = ?", translit_ya($v['name']), $v['id']);
}
print "$cnt features updated!\n";


$s->db->query("SELECT id, val FROM __options_uniq WHERE 1");
$vals = $s->db->results_array();
$cnt = 0;

foreach($vals as $v){
	$cnt++;	
	$trans = translit_ya($v['val']);
	$hash = hash('MD4', $trans);
	$s->db->query("UPDATE __options_uniq SET trans = ?, md4 = 0x$hash WHERE 1 AND id = ?", $trans, $v['id']);
}
print "$cnt options updated!\n";
//~ print 'hell';

