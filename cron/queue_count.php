<?php
//подключаем основной класс Simpla
if(file_exists(dirname(__FILE__) . '/../api/Simpla.php')) {
require_once(dirname(__FILE__) . '/../api/Simpla.php');
}


$simpla = new Simpla();

echo "<pre>";

print("tasks count: ".$simpla->queue->count_tasks()."\n");
print("tasks count full queue: ".$simpla->queue->count_tasks_full()."\n");
