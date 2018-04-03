<?php
// переменные
$csv_delete = 1; // удаляет после выполнения csv файл
$xml_delete = 1; // удаляет после выполнения XML файл
$filename_csv = dirname(__FILE__) . '/csv_update.csv';

// тут не используется 
//$filename_xml = dirname(__FILE__) . '/xml.xml';


//устанавливаем параметр _GET['p'] для передачи скрипту XMLreader.php ссылки для скачивания
//берем значение из командной строки из массива $argv первый элемент массива - имя самого скрипта, второй и далее параметры из
//командной строки пример: php hello.php hello my_friend даст в $argv = array([0] => hello.php , [1] => hello , [2] => my_friend );
if (!empty($argv[1])) {
    $url = $argv[1];
} elseif (!empty($_GET['p'])) {
    $url = $_GET['p'];
} else {
    $url = "http://vokruglamp.ru/export/get.php?id=sevenlight";
}

echo "<pre>";
require_once(dirname(__FILE__) . '/api/Simpla.php');
$simpla = new Simpla();

// Пишем запросы в переменные

$simpla->db->query("SET SQL_BIG_SELECTS=1");

function drop_table($table)
{
    global $simpla;
    $query = "DROP TABLE $table;";
    return $simpla->db->query($query);
}

function create_t_auto_update()
{
    global $simpla;
    $query = "CREATE table t_auto_update
				(
				 offer_id varchar(200),
				 name varchar(1024),
				 artikul varchar(256),
				 brand varchar(100),
				 color varchar(100),
				 stock varchar(11),
				 price varchar(11)
				)
				ENGINE = MYISAM;
	";
    return $simpla->db->query($query);
}

function load_t_auto_update()
{
    global $simpla, $filename_csv;
    $query = "LOAD DATA LOCAL INFILE '$filename_csv' 
				INTO TABLE t_auto_update
				FIELDS TERMINATED BY ',' ENCLOSED BY '\"'
				LINES TERMINATED BY '\\n'
				IGNORE 1 LINES
	;";
    //print_r($query);
    return $simpla->db->query($query);
}

function select_t_auto_update()
{
    global $simpla;
    $query = "SELECT * 
				FROM t_auto_update 
				WHERE 1
				LIMIT 3
	;";

    $simpla->db->query($query);
    $results = $simpla->db->results_array();
    foreach ($results as $result)
        $out[] = $result['name'];
    return implode(",\n", $out);
}

function create_t_update_help()
{
    global $simpla;
    $q = "create table t_update_help
			SELECT 
			o.product_id, 
			p.name,
			`1` as 1,  
			`2` as 2
			from s_options o";
    return $simpla->db->query($q);
}

function change_t_update_help()
{
    global $simpla;
    $query = "ALTER TABLE  `t_update_help` 
			CHANGE  `brand`  `brand` VARCHAR( 50 ),
			CHANGE  `artikul`  `artikul` VARCHAR( 100 ), 
			CHANGE  `color`  `color` VARCHAR( 100 ), 
			CHANGE  `tip`  `tip` VARCHAR( 50 ),
			CHANGE  `podtip`  `podtip` VARCHAR( 50 ),
			CHANGE  `tip_80perc`  `tip_80perc` VARCHAR( 50 ),
			CHANGE  `podtip_80perc`  `podtip_80perc` VARCHAR( 50 ),
			ENGINE = MYISAM 
	;";


    return $simpla->db->query($query);
}


function alter_t_update_help()
{
    global $simpla;
    $q = "ALTER TABLE
			t_update_help
				add index (`product_id`)
				";
    return $simpla->db->query($q);
}


function create_t_update_join()
{
    global $simpla;
    $q = "CREATE TABLE 
			t_update_join  
			(
			product_id INT(11),
			offer_id varchar(200),
			name varchar(1024),
			artikul varchar(256),
			brand varchar(100),
			color varchar(100),
			stock varchar(11),
			price varchar(11)
			)
			ENGINE = MYISAM
	;";
    $q1 = "ALTER TABLE
			t_update_join
				add index (`product_id`), 
				add index (`name`), 
				add index (`artikul`),
				add index (`color`),
				add index (`brand`)
	;";
    $out[] = 'q: ' . $simpla->db->query($q);
    $out[] = 'q1: ' . $simpla->db->query($q1);
    return implode(", ", $out);
}


// Ищем от более строгих правил к менее строгим
function insert_t_update_join()
{
    global $simpla;
    $q = "INSERT IGNORE INTO `t_update_join`
				SELECT NULL as product_id, u.* 
				FROM t_auto_update u
				;";

    $q1 = "UPDATE t_update_join u
				INNER JOIN
				t_update_help h 
					ON 
						u.brand LIKE CONCAT('%', h.brand, '%') AND u.artikul = h.artikul AND u.color = h.color
					AND
						( 
						LEFT(u.name, 15) LIKE CONCAT('%', tip_80perc, '%') 
						OR 
						LEFT(u.name, 10) LIKE CONCAT('%', podtip_80perc, '%')
						)
				SET u.product_id = h.product_id
				WHERE u.product_id is NULL
	;";

    $q1_1 = "DELETE h FROM t_update_help h
			INNER JOIN t_update_join u 
			ON u.product_id = h.product_id
	;";

    $q2 = "UPDATE t_update_join u
				INNER JOIN
				t_update_help h 
					ON 
						u.brand LIKE CONCAT('%', h.brand, '%') AND u.artikul = h.artikul 
					AND
						( 
						LEFT(u.name, 15) LIKE CONCAT('%', tip_80perc, '%') 
						OR 
						LEFT(u.name, 10) LIKE CONCAT('%', podtip_80perc, '%')
						)
				SET u.product_id = h.product_id
				WHERE u.product_id is NULL
	;";

    $q2_1 = "DELETE h FROM t_update_help h
			INNER JOIN t_update_join u 
			ON u.product_id = h.product_id
	;";

    $q3 = "UPDATE t_update_join u
				INNER JOIN
				t_update_help h 
					ON 
						u.brand LIKE CONCAT('%', h.brand, '%') AND u.artikul = h.artikul 
					AND
						( 
						LEFT(u.name, 20) LIKE CONCAT('%', tip_80perc, '%') 
						OR 
						LEFT(u.name, 15) LIKE CONCAT('%', podtip_80perc, '%')
						)
				SET u.product_id = h.product_id
				WHERE u.product_id is NULL
	;";

    $q3_1 = "DELETE h FROM t_update_help h
			INNER JOIN t_update_join u 
			ON u.product_id = h.product_id
	;";

    $q4 = "UPDATE t_update_join u
				INNER JOIN
				t_update_help h 
					ON 
						u.brand LIKE CONCAT('%', h.brand, '%') AND u.artikul = h.artikul 
					AND
						( 
						LEFT(u.name, 25) LIKE CONCAT('%', tip_80perc, '%') 
						OR 
						LEFT(u.name, 20) LIKE CONCAT('%', podtip_80perc, '%')
						)
				SET u.product_id = h.product_id
				WHERE u.product_id is NULL
	;";

    $q4_1 = "DELETE h FROM t_update_help h
			INNER JOIN t_update_join u 
			ON u.product_id = h.product_id
	;";

    $q5 = "UPDATE t_update_join u
				INNER JOIN
				t_update_help h 
					ON 
						u.brand LIKE CONCAT('%', h.brand, '%') AND u.artikul = h.artikul 
					AND
						( 
						LEFT(u.name, 30) LIKE CONCAT('%', tip_80perc, '%') 
						OR 
						LEFT(u.name, 25) LIKE CONCAT('%', podtip_80perc, '%')
						)
				SET u.product_id = h.product_id
				WHERE u.product_id is NULL
	;";

    $q5_1 = "DELETE h FROM t_update_help h
			INNER JOIN t_update_join u 
			ON u.product_id = h.product_id
	;";

    $q6 = "UPDATE t_update_join u
				INNER JOIN
				t_update_help h 
					ON 
						u.brand LIKE CONCAT('%', h.brand, '%') AND u.artikul = h.artikul 
					AND
						( 
						LEFT(u.name, 35) LIKE CONCAT('%', tip_80perc, '%') 
						OR 
						LEFT(u.name, 30) LIKE CONCAT('%', podtip_80perc, '%')
						)
				SET u.product_id = h.product_id
				WHERE u.product_id is NULL
	;";

    $q6_1 = "DELETE h FROM t_update_help h
			INNER JOIN t_update_join u 
			ON u.product_id = h.product_id
	;";

    $q7 = "UPDATE t_update_join u
				INNER JOIN
				t_update_help h 
					ON 
						u.brand LIKE CONCAT('%', h.brand, '%') AND u.artikul = h.artikul 
				SET u.product_id = h.product_id
				WHERE u.product_id is NULL
	;";

    $q7_1 = "DELETE h FROM t_update_help h
			INNER JOIN t_update_join u 
			ON u.product_id = h.product_id
	;";

    $out[] = 'q: ' . $simpla->db->q($q);
    $out[] = 'q1: ' . $simpla->db->q($q1);
    $out[] = 'q1_1: ' . $simpla->db->q($q1_1);
    $out[] = 'q2: ' . $simpla->db->q($q2);
    $out[] = 'q2_1: ' . $simpla->db->q($q2_1);
    $out[] = 'q3: ' . $simpla->db->q($q3);
    $out[] = 'q3_1: ' . $simpla->db->q($q3_1);
    $out[] = 'q4: ' . $simpla->db->q($q4);
    $out[] = 'q4_1: ' . $simpla->db->q($q4_1);
    $out[] = 'q5: ' . $simpla->db->q($q5);
    $out[] = 'q5_1: ' . $simpla->db->q($q5_1);
    $out[] = 'q6: ' . $simpla->db->q($q6);
    $out[] = 'q6_1: ' . $simpla->db->q($q6_1);
    $out[] = 'q7: ' . $simpla->db->q($q7);
    $out[] = 'q7_1: ' . $simpla->db->q($q7_1);
    return implode(", ", $out);
}

function check_changes()
{
    global $simpla;
    $query = "SELECT count(v.product_id) as count 
	FROM s_variants v
		INNER JOIN t_update_join j 
			ON v.product_id = j.product_id 
	WHERE v.stock != j.stock
	;";
    $query2 = "SELECT count(v.product_id) as count 
	FROM s_variants v
		INNER JOIN t_update_join j 
			ON v.product_id = j.product_id 
	WHERE v.stock = j.stock 
	;";
    $simpla->db->query($query);
    $results = $simpla->db->results_array();
    $out[] = "changed: " . $results[0]['count'];

    $simpla->db->query($query2);
    $results = $simpla->db->results_array();
    $out[] = "not changed: " . $results[0]['count'];
    return implode(", ", $out);
}

function data_update()
{
    global $simpla;


    $drop_procedure = "DROP PROCEDURE `update_positions`";
    $create_procedure = "
	CREATE DEFINER=`dynamicl_root`@`localhost` PROCEDURE `update_positions`() 
	NOT DETERMINISTIC MODIFIES SQL DATA SQL SECURITY DEFINER
	BEGIN
	# sql запрос на установку позиций отображения на сайте, сначала в наличии и дорогие 
	set @i = 0;

	create temporary table t_pos 
	SELECT (@i := @i + 1) as pos, p.id, v.stock, v.price FROM s_products p
	inner join s_variants v on v.product_id = p.id
	order by v.stock asc, v.price asc;
	alter table t_pos add index (id);

	update s_products p2
	inner join t_pos pos on pos.id = p2.id
	set p2.position = pos.pos;

	# sql запрос на включение видимости товаров, цена которых выше нуля и отключение видимости - для товаров с ценой 0 

	create temporary table tp_created
	SELECT id FROM `s_products` WHERE 
	created < NOW() - interval 45 day
	LIMIT 60;

	update s_products p
	inner join s_variants v on p.id = v.product_id
	set p.visible = CASE
	WHEN v.price = 0
	THEN 0
	ELSE 1
	END,
	p.created = CASE
	WHEN (v.stock != 0 OR v.price != 0)
	AND p.id in (select * from tp_created)
	THEN now()
	END
	;
	END
	";


    $query = "UPDATE s_variants v 
		INNER JOIN t_update_join j 
			ON v.product_id = j.product_id 
		INNER JOIN 
		s_products p 
			ON p.id = j.product_id
			SET 
				p.last_modify = CASE
					WHEN (v.price != j.price OR v.stock != j.stock) AND j.price > 0 AND j.stock > 0
					THEN now()
					ELSE NULL
					END,
					v.compare_price = CASE 
					WHEN v.price > j.price AND ( v.price / j.price ) >= 1.05
					THEN v.price
					WHEN v.price > j.price AND ( v.price / j.price ) < 1.05 
					THEN j.price * (1.05 + (RAND() * 0.2)) 
					WHEN v.price = j.price
					THEN v.compare_price
					ELSE NULL 
					END,
				v.price = CASE
					#тут поставим дисконт 8% на продукцию uniel
					WHEN p.brand_id = 28 AND j.price > 0
					THEN j.price * 0.92
					WHEN j.price = 0
					THEN v.price
					ELSE j.price
					END,
				v.stock = j.stock
	;";


    $query2 = "CALL update_positions();";

    $out[] = "drop procedure update_positions: " . $simpla->db->query($drop_procedure);
    $out[] = "create procedure update_positions: " . $simpla->db->query($create_procedure);


    $out[] = "price or stock updated: " . $simpla->db->query($query);

    $out[] = "call procedure update positions: " . $simpla->db->query($query2);

    return implode(", ", $out);
}


function update_check()
{
    global $simpla;
    $query = "SELECT COUNT(*) as count
				FROM t_update_join 
				WHERE product_id is NULL
	;";

    $query2 = "SELECT COUNT(*) as count
				FROM t_update_join 
				WHERE product_id is not NULL
	;";

    $simpla->db->query($query);
    $results = $simpla->db->results_array();
    $out[] = "not found: " . $results[0]['count'];

    $simpla->db->query($query2);
    $results = $simpla->db->results_array();
    $out[] = "updated: " . $results[0]['count'];
    return implode(", ", $out);
}

// КОНЕЦ

if (!file_exists($filename_csv)) {
    printf("Parse XML Errormessage: %s\n", require_once('XMLreader_forUpdate.php'));
}

if (!file_exists($filename_csv)) {
    print($filename_csv . " not found\n");
    die;
}
printf("drop table t_auto_update Errormessage: %s\n", drop_table('t_auto_update'));

printf("create table t_auto_update Errormessage: %s\n", create_t_auto_update());

printf("load data into table t_auto_update Errormessage: %s\n", load_t_auto_update());

printf("test data table t_auto_update select: \n%s\n", select_t_auto_update());

printf("drop table t_update_help Errormessage: %s\n", drop_table('t_update_help'));

printf("create table t_update_help Errormessage: %s\n", create_t_update_help());

//printf("change table t_update_help Errormessage: %s\n", change_t_update_help());

printf("add index t_update_help Errormessage: %s\n", alter_t_update_help());

sleep(1);

printf("drop t_update_join Errormessage: %s\n", drop_table('t_update_join'));

sleep(1);

printf("create table t_update_join with index Errormessage: %s\n", create_t_update_join());

printf("insert into table t_update_join Errormessage: %s\n", insert_t_update_join());

printf("check changes: %s\n", check_changes());

printf("data update Errormessage: %s\n", data_update());

printf("update check: %s\n", update_check());

//удаляем csv и xml, если задано
if ($csv_delete == 1 && file_exists($filename_csv)) {
    printf("Удаляем csv файл: \n%s\n", unlink($filename_csv));
}
if ($xml_delete == 1 && file_exists($filename_xml)) {
    printf("Удаляем xml файл: \n%s\n", unlink($filename_xml));
}

echo '</pre>';



