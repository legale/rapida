<?php
require_once('api/Simpla.php');
$s = new Simpla();

$cats = $s->categories->get_categories();
print_r($cats);
