<?php
ob_start();

require_once(dirname(__FILE__) . '/../api/Xmlparse.php');
$xml = new Xmlparse();

//copy('http://vokruglamp.ru/export/get.php?id=sevenlight', dirname(__FILE__) . '/vokruglamp.xml');
$realpath = isset($argv[1]) && $argv[1] !== 'null' ? $argv[1] : dirname(__FILE__) . '/../sandbox/xmlfile.xml';


$z = $xml->xml_open($realpath);
if (!$z) {
    print "\n unable to open xml file $realpath ";
}
$pre_res = loop_xml_file($xml);

//$start = microtime(true);
//$pre_res = get_yml_offers_params($realpath);
//$finish =  microtime(true) - $start;

//print " time: $finish ";
//print "<pre>";
//print_r($pre_res);
//die;

if (!$pre_res) {
    print "\n unable to loop xml file $realpath ";
    die;
} else {
    print "\n offers  parsed: " . $pre_res['count'];
}
$res = loop_tmp_file($pre_res);

$name = 't_xml';
if (!$res) {
    print "\n unable to loop tmp file $name ";
    die;
}

if (create_table($name, $pre_res['fields']) === false) {
    print "\n unable to create table $name ";
    die;
}

if (load_data($name, $res) === false) {
    print "\n unable to load data to the table $name ";
    die;
}


if (isset($argv[2], $argv[3])) {
    $res = create_update_help($argv[2], $argv[3]);
} else {
    $res = create_update_help();
}
if ($res !== true) {
    print "\n unable to create update help table $name. return: $res ";
    die;
}

$res = prepare_update_help();
if ($res === false) {
    print "\n unable to prepare update help table $name. return: $res ";
    die;
} else {
    print "\n products found: $res ";
}

$res = update_tables();
print "\n     stock updated: " . $res['stock'];
print "\n     price updated: " . $res['price'];
print "\n product not found: " . $res['not found'];


//dtimer::show();

/**
 * @param $str
 * @return int
 */
function get_string_type($str)
{
    if (strval((int)$str) === $str) {
        return 1;
    } else if (strval((float)$str) === $str) {
        return 2;
    } else if ($str === 'true' || $str === 'false' || $str === 'TRUE' || $str === 'FALSE') {
        return 0;
    } else {
        return 3;
    }
}

/**
 * @return bool|int
 */
function create_update_help($sku_fid = 5, $brand_fid = 6)
{
    //подключаем движок магазина
    require_once(dirname(__FILE__) . '/../api/Simpla.php');
    $simpla = new Simpla();

    $res = $simpla->db->query("drop table if exists t_update_help");
    if ($res === false) {
        return 1;
    }
    $res = $simpla->db->query("CREATE TABLE t_update_help
        select product_id, u.val as sku, uu.val as brand from s_options o
        inner join s_options_uniq u on o.`$sku_fid` = u.id
        inner join s_options_uniq uu on o.`$brand_fid` = uu.id");
    if ($res === false) {
        return 2;
    }
    $simpla->db->query("SELECT MAX(LENGTH(sku)) as len FROM t_update_help");
    $sku_len = $simpla->db->result_array('len');

    $simpla->db->query("SELECT MAX(LENGTH(brand)) as len FROM t_update_help");
    $brand_len = $simpla->db->result_array('len');

    $res = $simpla->db->query("ALTER TABLE t_update_help CHANGE `sku` `sku` VARCHAR($sku_len) NOT NULL DEFAULT '', CHANGE `brand` `brand` VARCHAR($brand_len) NOT NULL DEFAULT ''");
    if ($res === false) {
        return 3;
    }
    $res = $simpla->db->query("alter table t_update_help 
	add `offer_id` VARCHAR(200) DEFAULT null,
	add primary key (`product_id`) ,
	add index `sku` (`sku`, `brand`, `offer_id`),
	add index `brand` (`brand`, `sku`, `offer_id`)
	");
    if ($res === false) {
        return 4;
    }
    return true;
}


/**
 * @return mixed
 */
function prepare_update_help()
{
    //подключаем движок магазина
    require_once(dirname(__FILE__).'/../api/Simpla.php');
    $simpla = new Simpla();

    $q = "update t_update_help h
        inner join t_xml x on x . `sku` = h . sku and x . `brand` = h . brand 
        set h . offer_id = x . offer_id";

    if ($simpla->db->query($q)) {
        $res = $simpla->db->affected_rows();
    } else {
        $res = false;
    }

    return $res;
}


/**
 * @param $name
 * @param $fields
 * @param bool $drop
 * @return mixed
 */
function create_table($name, $fields, $drop = true)
{

    //подключаем движок магазина
    require_once(dirname(__FILE__).'/../api/Simpla.php');
    $simpla = new Simpla();
    $elems = array();
    $type = '';
    $uniq = array();
    $name_replace = array('param_артикул' => 'sku', 'param_бренд' => 'brand', 'param_остаток поставщика' => 'stock');

    // перебираем массив, делая каждый элемент в кривые кавычки и VARCHAR(512) в конце
    foreach ($fields as $field => $a) {
        switch ($a[1]) {
            case 0:
                $type = 'TINYINT';
                break;
            case 1:
                $max_val = pow(10, $a[0]);
                if ($max_val <= 32767) {
                    $type = 'SMALLINT';
                } else if ($max_val <= 8388607) {
                    $type = 'MEDIUMINT';
                } else if ($max_val <= 2147483647) {
                    $type = 'INT';
                } else if ($max_val > 2147483647) {
                    $type = 'BIGINT';
                }
                break;
            case 2:
                $m = $a[0] - 1;
                $d = $m;
                $type = "FLOAT($m, $d)";
                break;
            case 3:
            default:
                $length = $a[0];
                if ($length < 600) {
                    $type = "VARCHAR($length)";
                } else {
                    $type = "TEXT";
                }
                break;
        }

        $field = mb_strtolower($field);
        $uniq[$field][] = '';
        if (count($uniq[$field]) > 1) {
            $field = $field . count($uniq[$field]);
        }

        $field = isset($name_replace[$field]) ? $name_replace[$field] : $field;
        $elems[] = "`$field` $type DEFAULT NULL";
    }


    //пишем массив в строку через запятую
    $body = implode(', ', $elems);


    $tail = "ENGINE = InnoDB DEFAULT CHARSET = utf8";
    $q = "CREATE TABLE `$name` ($body) $tail";
    if ($drop) {
        $simpla->db->query("DROP TABLE IF EXISTS `$name`");
    }

    $simpla->db->query($q);

    $q = "ALTER TABLE `$name` add primary key(`offer_id`), add index `sku`(`sku`, `brand`, `offer_id`)";
    $res = $simpla->db->query($q);

    // выполняем запрос и возвращаем результат
    return $res;
}

/**
 * @param $name
 * @param $csvrealpath
 * @return mixed
 */
function load_data($name, $csvrealpath)
{
    //подключаем движок магазина
    require_once(dirname(__FILE__) . '/../api/Simpla.php');
    $simpla = new Simpla();

    $csvrealpath = str_replace('\\', '/', $csvrealpath);

    // формируем запрос
    $query = "LOAD DATA LOCAL INFILE '$csvrealpath' 
				INTO TABLE `$name`
				FIELDS TERMINATED BY ',' ESCAPED BY '\\\\' ENCLOSED BY '\"'
				LINES TERMINATED BY '\\r\\n'
				IGNORE 3 LINES";
    // выполняем запрос и возвращаем результат
    $res = $simpla->db->query($query);
    return $res;
}

/**
 * @param $handle
 * @param $fields
 * @param string $delim
 * @return bool|int
 */
function fputcsv_escape($handle, $fields, $delim = ',')
{
    $row = array();
    foreach ($fields as $k => $col) {
        if (is_iterable($col)) {
            print " column $k is iterable!Must be string, int, float or bool ";
            return false;
        }
        if ($col === null) {
            $row[] = 'NULL';
        } else if (is_string($col)) {
            $row[] = '"' . str_replace(array("\n", "\r", "\t", $delim), array('\n', '\r', '\t', "\\" . $delim), $col) . '"';
        } else {
            $row[] = "\"$col\"";
        }
}
return fwrite($handle, implode($delim, $row) . "\r\n");
}

/**
 * @param $sub
 * @return mixed
 */
function replace_crnl($sub)
{
    return str_replace(array("\n", "\r"), array('\n', '\r'), $sub);
}


/**
 * @param $ar
 * @return string
 */
function loop_tmp_file($ar)
{
    $len = array();
    $types = array();
    $csvrealpath = dirname(__FILE__) . '/../simpla/files/import/parsed_xml.csv';
    $fopen = fopen($ar['temppath'], 'r');
    $fwrite = fopen($csvrealpath, 'w');

    $tpl = array();
    foreach ($ar['fields'] as $col => $ar) {
        $tpl[$col] = null;
        $len[] = $ar[0];
        $types[] = $ar[1];
    }
//    var_dump($tpl);
//    var_dump($len);
//    var_dump($types);

    fputcsv_escape($fwrite, array_keys($tpl));
    fputcsv_escape($fwrite, $len);
    fputcsv_escape($fwrite, $types);
    do {
        $s = fgets($fopen);
        if ($s) {
            $row = json_decode($s, true);
            $csv_row = array_merge($tpl, $row);
            fputcsv_escape($fwrite, $csv_row);
        } else {
            break;
        }
    } while (1);
    fclose($fopen);
    fclose($fwrite);
    return $csvrealpath;
}


/**
 * @param $xml
 * @return array|bool
 */
function loop_xml_file($xml)
{
//    $start = microtime(true);
    $temppath = tempnam(sys_get_temp_dir(), 'yml_export'); //временный файл для записи полуфабриката парсинга yml файла
    if (is_readable($temppath)) {
        $fopen = fopen($temppath, 'w');
    } else {
        return false;
    }
    $fields = array();
    $xml->node_goto('offer'); //переходим к первому offer

    $cnt = 0;
    do {//крутим, пока у нас есть offer
//        $cycle = microtime(true);
//        print " \n 1 point $cnt: ".(microtime(true) - $start);
        $cnt++;
        $offer = yml_get_offer($xml->read_raw()); //получаем содержимое каждого блока offer в виде массива
//        print " 2 point: ".(microtime(true) - $cycle);
        $values = array();
        foreach ($offer as $k => $val) {
            if (!isset($fields[$k])) {
                $fields[$k] = array(0, 0);
            }

            if ($val['l'] > $fields[$k][0]) {
                $fields[$k][0] = $val['l'];
            }
            if ($val['t'] > $fields[$k][1]) {
                $fields[$k][1] = $val['t'];
            }
            $values[$k] = $val['v'];
        }

//        print " 3 point: ".(microtime(true) - $cycle);
        fwrite($fopen, json_encode($values, 256) . "\r\n"); //пишем во временный файл полученный массив JSON_UNESCAPED_UNICODE - 256

//        print " last point: ".(microtime(true) - $cycle);
        if (!$xml->node_next('offer')) {
            break;
        }

    } while (1);
    ksort($fields);

//    print "\n count: '$cnt' time:" . (microtime(true) - $start);
    return array('count' => $cnt, 'temppath' => $temppath, 'fields' => $fields);//возвращаем результат работы
}


/**
 * @param $offer
 * @return mixed
 */
function yml_get_offer($offer)
{
    $node = new SimpleXMLElement($offer);

    // выводим в массив все названия
    // Добавляем offer_id
    $v = (string)$node->attributes()['id'];
    $fields['offer_id']['v'] = (string)$v;
    $fields['offer_id']['l'] = mb_strlen($v);
    $fields['offer_id']['t'] = get_string_type($v);

    $keys = array();
    foreach ($node as $elem) {
        $key = (string)$elem->getName();

        if ($elem->attributes()) {
            $attribs = array();
            foreach ($elem->attributes() as $nnn) {
                $attribs[] = (string)$nnn;
            }
            $key = $key . '_' . implode(',', $attribs);
        }

        //если ключ уже есть, добавляем к нему число его повторений

        if (array_key_exists($key, $keys)) {
            $key_ready = $key . count($keys[$key]);
        } else { //иначе используем этот ключ
            $key_ready = $key;
        }
        $keys[$key][] = ''; //пишем ключ для подсчета повторений


        //теперь пишем

        $v = (string)$elem;
        $fields[$key_ready]['v'] = $v;
        $fields[$key_ready]['l'] = mb_strlen($v);
        $fields[$key_ready]['t'] = get_string_type($v);
    }
    return $fields;

}


/**
 * @param $name
 * @param $fields
 * @return mixed
 */
function alter_table($name, $fields)
{
    //подключаем движок магазина

    $si = new Simpla();
    $fields = array_map(function ($s) {
        return '`' . $s . '`';
    }, $fields);
    $list = implode(', ', $fields);
    return $si->db->query("ALTER TABLE `$name` ADD INDEX `index` ($list)");
}


/**
 * @return mixed
 */
function update_tables()
{
    //подключаем движок магазина
    require_once(dirname(__FILE__) . '/../api/Simpla.php');
    $si = new Simpla();

    $q_stock = "UPDATE s_variants v
        INNER JOIN s_products p ON p.id = v.product_id
        INNER JOIN t_update_help h ON h.product_id = v.product_id
        INNER JOIN t_xml x ON h.offer_id = x.offer_id
        set p.stock = CASE
					WHEN x.stock > 0
					THEN 1
					ELSE 0
					END,
				v.stock = x.stock";

    $q_price = "UPDATE s_variants v
        INNER JOIN s_products p ON p.id = v.product_id
        INNER JOIN t_update_help h ON h.product_id = v.product_id
        INNER JOIN t_xml x ON h.offer_id = x.offer_id
        set v.old_price = CASE 
					WHEN v.price > x.price AND ( v.price / x.price ) >= 1.05
					THEN v.price
					WHEN v.price > x.price AND ( v.price / x.price ) < 1.05 
					THEN x.price * (1.05 + (RAND() * 0.2)) 
					WHEN v.price = x.price
					THEN v.old_price
					ELSE 0 
					END,
				v.price = CASE
					WHEN x.price = 0
					THEN v.price
					ELSE x.price
					END";
    $q_nf = "SELECT COUNT(*) as cnt FROM t_update_help h WHERE offer_id is null";
    $res['stock'] = $si->db->query($q_stock) ? $si->db->affected_rows() : false;
    $res['price'] = $si->db->query($q_price) ? $si->db->affected_rows() : false;
    $res['not found'] = $si->db->query($q_nf) ? $si->db->result_array('cnt') : false;

    return $res;
}
