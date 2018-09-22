<?php
//подключаем основной класс Simpla
if(file_exists(dirname(__FILE__) . '/../api/Simpla.php')) {
    require_once(dirname(__FILE__) . '/../api/Simpla.php');
}
$rapida = new Simpla();

echo $rapida->queue->redis_qreset() ? " done!\n" : " error!\n";
