<?php

require_once(dirname(__FILE__) . '/api/Xmlcsv.php');
$xml = new Xmlcsv();
$realpath = dirname(__FILE__) . '/yandex400.xml';
$z = $xml->xml_open($realpath);

print "<pre>";
$xml->node_goto('offer');
print "\n name:". $z->name . "\n";
print $z->getAttribute('id');

$xml->node_next('offer');
print "\n name:". $z->name . "\n";
print $z->getAttribute('id');

$xml->node_next('offer');
print "\n name:". $z->name . "\n";
print $z->getAttribute('id');


//
//while ($z->read() && $z->name !== 'offer'); {
//
//    $count = '';
//    // now that we're at the right depth, hop to the next <loc/> until the end of the tree
//    while ($z->name === 'offer')
//    {
//        // either one should work
//        $node = new SimpleXMLElement($z->readOuterXML());
//        //$node = simplexml_import_dom($doc->importNode($z->expand(), true));
//
//        // now you can use $node without going insane about parsing
//
//
//        // выводим в массив все названия
//
//        // Добавляем offer_id
//        $fields['offer_id'] = 'offer_id';
//
//        $names = array();
//        foreach($node as $elem) {
//            $name = $elem->getName();
//
//            if(array_key_exists($name,$names)){
//                $key = $name.count($names[$name]);
//            }else{
//                $key = $name;
//            }
//            $names[$name][] = '';
//
//
//            if ($elem->attributes()) {
//                $attribs = array();
//                foreach ( $elem->attributes() as $nnn)
//                    $attribs[] = $nnn;
//                $fields[$name.'_'.implode('","', $attribs)] = ''  ;
//            } else {
//                $fields[$key] = '' ;
//            }
//        }
//
//        // go to next <product />
//        $z->next('offer');
//
//        $count++;
//    }
//
//
//    // делаем шаблон
//    $fields_tpl = array_flip(array_keys($fields));
//    ksort($fields_tpl);
//
//
//    //print_r($fields_tpl);
//    //print_r($fields);
//
//
//
////    // выводим кавычки в начало
////    file_put_contents( $filename_csv, '"', FILE_APPEND);
////
////    // выводим в файл уникальные заголовки
////    file_put_contents( $filename_csv, implode( '","' , array_keys($fields_tpl)), FILE_APPEND);
////
////    // выводим кавычки в конец
////    file_put_contents( $filename_csv, "\"\n", FILE_APPEND);
//
//
//    //die;
//
//}
//print_r($fields);
//
////создаем исходный пустой массив
//$fields_clear = $fields;
