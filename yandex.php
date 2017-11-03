<?php
require_once ('api/Simpla.php');
$simpla = new Simpla();

header("Content-type: text/xml; charset=UTF-8");
// Заголовок
print
	"<?xml version='1.0' encoding='UTF-8'?>
<!DOCTYPE yml_catalog SYSTEM 'shops.dtd'>
<yml_catalog date='" . date('Y-m-d H:i') . "'>
<shop>
<name>" . $simpla->settings->site_name . "</name>
<company>" . $simpla->settings->company_name . "</company>
<url>" . $simpla->config->root_url . "</url>
";

// Валюты
$currencies = $simpla->money->get_currencies(array('enabled' => 1));
$main_currency = reset($currencies);
print "<currencies>
";
foreach ($currencies as $c)
	if ($c->enabled)
	print "<currency id='" . $c->code . "' rate='" . $c->rate_to / $c->rate_from * $main_currency->rate_from / $main_currency->rate_to . "'/>
";
print "</currencies>
";


// Категории
$categories = $simpla->categories->get_categories();
print "<categories>
";
foreach ($categories as $c)
	{
	print "<category id=' $c->id'";
	if ($c->parent_id > 0)
		print " parentId=' $c->parent_id'";
	print ">" . htmlspecialchars($c->name) . "</category>
";
}
print "</categories>
";

// Товары
$simpla->db2->query("SET SQL_BIG_SELECTS=1");
// Товары
$simpla->db2->query("SELECT v.price, v.id as variant_id, p.name as product_name, v.name as variant_name, v.position as variant_position, p.id as product_id, p.url, p.annotation, pc.category_id
					FROM __variants v 
					LEFT JOIN __products p ON v.product_id=p.id
					LEFT JOIN __products_categories pc ON p.id = pc.product_id AND pc.position=(SELECT MIN(position) FROM __products_categories WHERE product_id=p.id LIMIT 1)
					WHERE p.visible AND (v.stock >0 OR v.stock is NULL) GROUP BY v.id ORDER BY p.id, v.position ");
print "<offers>
";


$currency_code = reset($currencies)->code;

// В цикле мы используем не results(), a result(), то есть выбираем из базы товары по одному,
// так они нам одновременно не нужны - мы всё равно сразу же отправляем товар на вывод.
// Таким образом используется памяти только под один товар
$prev_product_id = null;
while ($p = $simpla->db2->result())
	{

//тут массив с картинками
	$p_images = array();
	foreach ($simpla->products->get_images(array('product_id' => $p->product_id)) as $image) {
		$p_images[$image->product_id][] = $image->filename;
	}
//тут массив со свойствами товаров
	$features = array();
	$features[$p->product_id] = $simpla->features->get_product_options(array('product_id' => $p->product_id));



	$variant_url = '';
	if ($prev_product_id === $p->product_id)
		$variant_url = '?variant=' . $p->variant_id;
	$prev_product_id = $p->product_id;

	$price = round($simpla->money->convert($p->price, $main_currency->id, false), 2);
	print
		"
<offer id=' $p->variant_id' available='true'>
<url>" . $simpla->config->root_url . '/products/' . $p->url . $variant_url . "</url>";
	print "
<price>$price</price>
<currencyId>" . $currency_code . "</currencyId>
<categoryId>" . $p->category_id . "</categoryId>
";

//выводим картинки
	if (!empty($p_images[$p->product_id])) {
		foreach ($p_images[$p->product_id] as $img) {
			$string = htmlspecialchars($simpla->config->root_url . '/files/originals/' . $img);
			print "
<picture>" . $string . "</picture>";
		}
	}



	print "<name>" . htmlspecialchars($p->product_name) . ($p->variant_name ? ' ' . htmlspecialchars($p->variant_name) : '') . "</name>
<description>" . htmlspecialchars(strip_tags($p->annotation)) . "</description>
";


//тут пишем свойства товара
	if (!empty($features[$p->product_id])) {
		foreach ($features[$p->product_id] as $feature) {
			$comma_count = strrpos($feature->name, ", ");
			if ($feature->name != 'vId' && $feature->name != 'vURL' && $feature->name != '')
				if ($comma_count) {
				$string = "<param name='" . htmlspecialchars(substr($feature->name, 0, $comma_count)) . "' unit='" . htmlspecialchars(substr($feature->name, $comma_count + 2, 999)) . "'>" . htmlspecialchars(strip_tags(trim($feature->value))) . "</param>
			";
			}
			else {
				$string = "<param name='" . htmlspecialchars(strip_tags(trim($feature->name))) . "'>" . htmlspecialchars(strip_tags(trim($feature->value))) . "</param>
			";
			}
			print $string;
		}
	}


	print "</offer>
";
}

print "</offers>
";
print "</shop>
</yml_catalog>
";
