<?php

$pause = 0;
if($pause == 1){
	die();
}


//подключаем основной класс Simpla
if(file_exists(dirname(__FILE__) . '/../api/Simpla.php')) {
require_once(dirname(__FILE__) . '/../api/Simpla.php');
}


echo "<pre>\n";
$simpla = new Simpla();
dtimer::$enabled = true;
$task = 0;
dtimer::log("start queue");


$task = $simpla->queue->execlasttask();



dtimer::show();


?>
