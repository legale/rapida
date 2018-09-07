<?php
header("Content-type: application/xml; charset=UTF-8");
//это BOM символ (не используем, остался на память) 
//$string =  (pack('CCC', 0xef, 0xbb, 0xbf));

//название сайта
$site = 'https://sevenlight.ru';

//лимит количества товаров
$limit = 1000000;

require_once( dirname(__FILE__) . '/../api/Simpla.php');
$rapida = new Simpla();
dtimer::$enabled = true; //debugger


// переменные
$file_tmp = dirname(__FILE__) . '/../yandex_full.xml_';
$file = dirname(__FILE__) . '/../yandex_full.xml.gz';

// удаляем временный файл, если он есть
if (file_exists($file_tmp)) {
	@unlink($file_tmp);
}
// открываем файл
$fopen = fopen($file_tmp, 'a');

// Названия свойств
$features = $rapida->features->get_features();

$rapida->db->query("SELECT MAX(id) as size FROM s_options_uniq");
$size = (int)$rapida->db->result_array('size');
$options_uniq = new SplFixedArray($size+1); //делаем размер массива на 1 больше, чем макс. id
$rapida->db->query("SELECT id, val FROM s_options_uniq");

while ($row = $rapida->db->res->fetch_assoc()) {
	$options_uniq[$row['id']] = $row['val'];
}



// Заголовок
$string = 
"<?xml version='1.0' encoding='UTF-8'?>
<!DOCTYPE yml_catalog SYSTEM 'shops.dtd'>
<yml_catalog date='".date('Y-m-d H:i')."'>
<shop>
<name>".$rapida->settings->site_name."</name>
<company>".$rapida->settings->company_name."</company>
<url>".$site."</url>
";
fwrite($fopen, $string);


// Валюты
$currencies = $rapida->money->get_currencies(array('enabled'=>1));
$main_currency = reset($currencies);
$string = "<currencies>
";
fwrite($fopen, $string);

foreach($currencies as $c){
	if($c['enabled']){
		$cur_code = $c['code'];
		$cur_rate = $c['rate'];
		$string =  "<currency id='$cur_code' rate='$cur_rate'/>
		";
		fwrite($fopen, $string);
	}
}
$string =  "</currencies>
";
fwrite($fopen, $string);

// Категории
$categories = $rapida->categories->get_categories();
$string = "<categories>
";
fwrite($fopen, $string);

foreach($categories as $c){
$string = "<category id='" . $c['id'] . "'";
fwrite($fopen, $string);

if($c['parent_id']>0) {
	$string = " parentId='" . $c['parent_id'] . "'";
	fwrite($fopen, $string);
}

$string =  ">" . htmlspecialchars($c['name']) . "</category>
";
fwrite($fopen, $string);

}
$string =  "</categories>
";
fwrite($fopen, $string);

$string = "<delivery-options>
<option cost='500' days='1-3'/>
</delivery-options>
";

fwrite($fopen, $string);


$stock_filter = '';
$brand_filter = '';

//~ $brand_id = array(5,6,11,19,24,30,38,44,45,48,49,50,53,54,56,57,59,60,61,62,63,64,65,66,67,68,71,72,73,74,81,86,91,92);
//~ $stock_filter = $rapida['settings']->yandex_export_not_in_stock ? '' : ' AND v.stock >0 AND v.stock != 7777777 ';
//~ $brand_filter = $rapida['db2']->placehold("AND p.brand_id not in ( ?@ )", $brand_id);

// Товары
$rapida->db2->query("SET SQL_BIG_SELECTS=1");
$rapida->db2->query("SELECT 
		b.name as vendor, 
		v.stock, 
		v.old_price, 
		v.sku, 
		v.price, 
		v.id as variant_id, 
		p.name as product_name, 
		v.name as variant_name,
		p.trans, 
		p.description,
		p.annotation, 
		o.*
	FROM s_variants v 
	INNER JOIN s_products p ON v.product_id=p.id
	INNER JOIN s_brands b on (b.id = p.brand_id)
	INNER JOIN s_options o ON v.product_id = o.product_id
	WHERE 
		1
		$stock_filter
		$brand_filter
	LIMIT $limit");
	
$string =  "<offers>
";

fwrite($fopen, $string);

$res = $rapida->db2->result_array();

$currency_code = reset($currencies)['code'];
$prev_product_id = null;
while($p = $rapida->db2->result_array()){
//echo $p['product_id'] . "\n";
//отрезаем от массива хвост с опциями
$options = array_slice($p, 13, null, true);
array_splice($p, 13);


$url = $p['trans'];
$prev_product_id = $p['product_id'];

	$price = $p['price'];
	$old_price = $p['old_price'];
	$stock = $p['stock'];
	$string = 
	"
	<offer id='" . $p['sku'] . "' type='vendor.model' available='" . ($p['stock'] > 0 || $p['stock'] === null ? 'true' : 'false') . "'>
	<param name='product_id'>".$p['product_id']."</param>
	<param name='stock'>$stock</param>
	<param name='product_url'>$url</param>
	<url>$site/products/$url</url>
	";

	$rapida->db->query("select `category_id` as cid from __products_categories where product_id = ?", $p['product_id']);
	$cats_ = $rapida->db->results_array('cid');
	$category_id = empty($cats_) ? null : reset($cats_);
	$cats = array();
	$urls = array();
	if(!empty($cats_)){
		foreach($cats_ as $cid){
			$path = array();
			if(!isset($categories[$cid])){
				continue;
			}
			foreach($categories[$cid]['path'] as $pa){
				$path[] = $pa['name'];
			}
			$cats[] = implode('/', $path); 
			$urls[] = $categories[$cid]['trans']; 
		}
		$cats = htmlspecialchars(implode('|', $cats));
		$urls = htmlspecialchars(implode('|', $urls));
		$string .=  "
	<param name='categories'>$cats</param>
	<param name='categories_url'>$urls</param>
	";
	}
	$string .=  "
	<price>$price</price>
	<oldprice>$old_price</oldprice>";
	
	
	if($price > 4999){
	$string .= "
	<delivery-options>
	<option cost='0' days='1-3'/>
	</delivery-options>
	";
	};
	
	$string .= "<currencyId>".$currency_code."</currencyId>
	<categoryId>" . $category_id . "</categoryId>
	";
fwrite($fopen, $string);

	// изображения
	$images = $rapida->image->get('products', [ 'item_id' => $p['product_id'] ]);

	if(!empty($images)) {
		$i = 0;
		foreach($images as $img) {
			if($i < 50){
				$i++;
			}else{
				break;
			}
			$string =  htmlspecialchars($site.'/img/products/'.$img['basename']);
			$string = "<picture>".$string."</picture>
	";
			fwrite($fopen, $string);
		}
		unset($i);
	}

	
	$string =  "
	<store>true</store>
	<pickup>true</pickup>
	<delivery>true</delivery>
	<vendor>" . $p['vendor'] . "</vendor>
	<vendorCode>" . $p['sku'] . "</vendorCode>
	";
fwrite($fopen, $string);

	$discount = '';
	$discount = min( 100, round( $price * 0.08 , 0) );
	$string =  "<model>".htmlspecialchars($p['product_name']).($p['variant_name']?' '.htmlspecialchars($p['variant_name']):'')."</model>
	<description>" . $p['description'] . "</description>
	<sales_notes>" . $p['annotation'] . "</sales_notes>
	";
fwrite($fopen, $string);

	
	$string =  "<manufacturer_warranty>". 'true'  ."</manufacturer_warranty>
	<seller_warranty>". 'true' ."</seller_warranty>
			";
fwrite($fopen, $string);

	//записываем опции товара
	if (!empty($options)) {
		foreach($options as $fid => $vid) {
			if($options_uniq[$vid] === null){
				continue;
			}
			$string = "<param name='"
			. $features[$fid]['name'] . "'>" 
			. $options_uniq[$vid] . "</param>
			";
			fwrite($fopen, $string);
		}
	}
	$string =  "</offer>
";
fwrite($fopen, $string);

	$p = '';
	$options = '';
}

$string =  "</offers>
";
fwrite($fopen, $string);

$string =  "</shop>
</yml_catalog>
";
fwrite($fopen, $string);


// закрываем временный файл
fclose($fopen);

// удаляем файл, если он есть
if (file_exists($file)) {
	@unlink($file);
}

//пишем все это в сжатом виде
$input = fopen($file_tmp, "r");
$output = gzopen($file, "w9");

//сжимаем и пишем в файл кусками по 2мб
while (!feof($input)) {
$data = fread($input, 2097152);
gzwrite($output, $data);
}

fclose($input);
gzclose($output);
unlink($file_tmp);

dtimer::show_console(100, 3);
