<?php

//подключаем основной класс Simpla
if (file_exists(dirname(__FILE__) . '/../api/Simpla.php')) {
    require_once(dirname(__FILE__) . '/../api/Simpla.php');
}


$rapida = new Simpla();
echo "<pre>\n";
dtimer::$enabled = false;
dtimer::log("start queue");

$i = 0;
while ($i < 300000) {
    $i++;
    if ($rapida->queue->redis_execlast()) {
        echo "ok\n";
    } else {
        echo "no task to exec\n";
        break;
    }
}

dtimer::show();
