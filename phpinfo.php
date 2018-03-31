<?php
//подключаем нашего летописца
require_once(dirname(__FILE__).'/api/Dtimer.php');


//эта функция для создания массивов
function create($keys) {
    for ($i = 1; $i <= $keys; $i++) {
        $arr['key-' . $i] = $i;
    }
    return $arr;
}

function demo(){
$i = 0;
$cnt = 10;
while($i < $cnt){
	$i++;
	//тут будем создавать большие массивы, чтобы увидеть, как на глазах растет потребление памяти
	$garbage[] = create(500000);
	dtimer::log('hello, baby!');
}
}

//этим методом мы пишем запись в журнал. Есть 2 возможных аргумента 1 (обязательный) - сообщение в журнал, 
//2 (необязательный) - тип сообщения. Тип (1, 2 или 3) влияет на цвет строчки записи при отображении журнала
dtimer::log(__FILE__ .' ' . __LINE__ .'first message default type is 3');
dtimer::log(__FILE__ .' ' . __LINE__ .'second message type is 2', 2);
demo();
dtimer::log(__FILE__ .' ' . __LINE__ .'third and last message type is 1', 1);

//Этим методом мы показываем наш журнал
print "Так я могу показывать таблицы для работы из браузера!\n";
dtimer::show();

print "<PRE>";
//в качестве аргумента можно задать ширину таблицы в символах
print "Так я могу показывать псевдографические таблицы для работы из консоли!\n";
dtimer::show_console(180);
print "</PRE>";
