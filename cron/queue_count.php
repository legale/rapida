<?php
//подключаем основной класс Simpla
if(file_exists(dirname(__FILE__) . '/../api/Simpla.php')) {
require_once(dirname(__FILE__) . '/../api/Simpla.php');
}
$rapida = new Simpla();

echo("tasks count: ".$rapida->queue->redis_count()."\n");
