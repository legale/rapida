<?php

require_once('../api/Simpla.php');

$filename = $_GET['file'];

$simpla = new Simpla();



$resized_filename =  $simpla->image->resize($filename);

if(is_readable($resized_filename))
{
	header('Content-type: image');
	print file_get_contents($resized_filename);
}
//~ dtimer::log(__FILE__ . " $filename");
//~ dtimer::show();

