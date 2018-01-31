<?php
require_once('api/Simpla.php');
$s = new Simpla();


//~ print_r($cats);
$cnt = 0;

$vals = $s->db->query("SELECT id, name FROM __features WHERE 1");
foreach($vals as $v){
	$cnt++;	
	$s->db->query("UPDATE __features SET trans = ? WHERE 1 AND id = ?", translit_ya($v['name']), $v['id']);
}
print "$cnt features updated!\n";


$vals = $s->db->query("SELECT id, val FROM __options_uniq WHERE 1");
$cnt = 0;
foreach($vals as $v){
	$cnt++;	
	$s->db->query("UPDATE __options_uniq SET trans = ? WHERE 1 AND id = ?", translit_ya($v['val']), $v['id']);
}
print "$cnt options updated!\n";
//~ print 'hell';

