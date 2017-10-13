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

$task = 0;
dtimer::log("start queue");

$i = 0;
while($i < 30000 && $pause != 1) {
$i++;

$task = $simpla->queue->execlasttask();


	if(is_int($task)) {
		echo " task id ".$task."\n";
	} else {
		echo " no task to exec\n";
		break;
	}
}

dtimer::show();


?>
