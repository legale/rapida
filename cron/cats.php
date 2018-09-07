<?php
print "start";
require_once('../api/Simpla.php');
$s = new Simpla();
$cats = $s->categories->get_categories();

foreach($cats as $c){
$s->categories->update_category($c['id'], array('trans' => translit_ya($c['name'])));
}
