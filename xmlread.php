<?php

require_once(dirname(__FILE__).'/api/Xmlcsv.php');

$xmlclass = new Xmlcsv();

$realpath = dirname(__FILE__).'/yandex400.xml';


$z = $xmlclass->xml_open($realpath);

print "<pre>";

$count = 0;
while($z->read()){
    print $count++;
    print $z->name;
    print $z->readOuterXML();
}