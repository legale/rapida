<?php

//phpinfo();

$db = new SQLite3("sqlite.sqlite");


$q = "insert into bank ( `timestamp`, `amount`,`client_id`, `text`) VALUES(1550747460, -244, NULL, '/add_to_bank 21.02.2019 12:11 зачисление 244р')";
$db->query($q);

$res = $db->query("SELECT * FROM bank ORDER BY `id` DESC");

$array = $res->fetchArray(SQLITE3_ASSOC);

print_r($array);