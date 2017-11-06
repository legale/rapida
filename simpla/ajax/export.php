<?php

require_once('../../api/Simpla.php');

//сделаем функцию для преобразования всего в cp1251
function transcode($n) {
    return iconv("UTF-8", "CP1251",$n);
}


class ExportAjax extends Simpla
{	
	private $columns_names = array(
			'category'=>         'Категория',
			'name'=>             'Товар',
			'price'=>            'Цена',
			'url'=>              'Адрес',
			'visible'=>          'Видим',
			'featured'=>         'Рекомендуемый',
			'brand'=>            'Бренд',
			'variant'=>          'Вариант',
			'compare_price'=>    'Старая цена',
			'sku'=>              'Артикул',
			'stock'=>            'Склад',
			'meta_title'=>       'Заголовок страницы',
			'meta_keywords'=>    'Ключевые слова',
			'meta_description'=> 'Описание страницы',
			'annotation'=>       'Аннотация',
			'body'=>             'Описание',
			'images'=>           'Изображения'
			);
			
	private $column_delimiter = ';';
	private $subcategory_delimiter = '/';
	private $products_count = 10;
	private $export_files_dir = '../files/export/';
	private $filename = 'export.csv';

	public function fetch()
	{
		/* синхронизируем таблицы features и options, чтобы количество свойств из feautures 
		 * соответствовало столбцам в options
		 * Если функция возвращает false, значит что-то пошло не так, останавливаемся
		 */
		if ( !$this->sys->sync_options() ) {
			print "ERROR SYNC FEATURES AND OPTIONS!";
			die;
		}
		
		if(!$this->managers->access('export'))
			return false;

	
		// Страница, которую экспортируем
		$page = $this->request->get('page');
		if(empty($page) || $page==1)
		{
			$page = 1;
			// Если начали сначала - удалим старый файл экспорта
			if(is_writable($this->export_files_dir.$this->filename))
				unlink($this->export_files_dir.$this->filename);
		}
		
		// Открываем файл экспорта на добавление
		$f = fopen($this->export_files_dir.$this->filename, 'ab');
		
		// Добавим в список колонок свойства товаров
		if( $features = $this->features->get_features() ){
			foreach($features as $feature){
				$this->columns_names[$feature->name] = $feature->name;
			}
		}
 			
		// Если начали сначала - добавим в первую строку названия колонок
		if($page == 1)
		{
			fputcsv($f, array_map('transcode', $this->columns_names) , $this->column_delimiter );
		}
		
		// Все товары
		$products = $this->products->get_products(array('page'=>$page, 'limit'=>$this->products_count));
 		foreach($products as &$p){
	 		// Свойства товаров
	 		if ( isset($features) && $options = $this->features->get_product_options($p['id']) ) {
				foreach($options as $fid=>$option) {
					$feature = $features->{$fid}->name;
					$p['$feature'] = str_replace(',', '.', trim($option['val']));
				}
			}
 		}
		//Уберем $p, чтобы никто больше не воспользовался этой ссылок
 		unset($p);
 		
 		if(empty($products)){
 			return false;
 		}
 		
 		// Категории товаров
 		foreach($products as $p_id=>&$product)
 		{
	 		$categories = array();
	 		$cats = $this->categories->get_product_categories($p_id);
	 		foreach($cats as $category){
	 			$path = array();
	 			$cat = $this->categories->get_category((int)$category->category_id);
	 			if(!empty($cat))
 				{
	 				// Вычисляем составляющие категории
	 				foreach($cat->path as $p)
	 					$path[] = str_replace($this->subcategory_delimiter, '\\'.$this->subcategory_delimiter, $p->name);
	 				// Добавляем категорию к товару 
	 				$categories[] = implode('/', $path);
 				}
	 		}
	 		$product->category = implode(', ', $categories);
 		}
 		
 		// Изображения товаров
 		$images = $this->products->get_images(array('product_id'=>array_keys((array)$products)));
 		foreach($images as $image)
 		{
 			// Добавляем изображения к товару через запятую
 			if(empty($products->{$image->product_id}->images))
 				$products->{$image->product_id}->images = $image->filename;
 			else
 				$products->{$image->product_id}->images .= ', '.$image->filename;
 		}
 
 		$variants = $this->variants->get_variants(array('product_id'=>array_keys((array)$products)));

		foreach($variants as $variant)
 		{
 			if(isset($products->{$variant->product_id}))
 			{
	 			$v                    = array();
	 			$v['variant']         = $variant->name;
	 			$v['price']           = $variant->price;
	 			$v['compare_price']   = $variant->compare_price;
	 			$v['sku']             = $variant->sku;
	 			$v['stock']           = $variant->stock;
	 			if($variant->infinity)
	 				$v['stock']           = '';
				$products->{$variant->product_id}->variants[] = $v;
	 		}
		}
		
		foreach($products as &$product)
 		{
 			$variants = $product->variants;
 			unset($product->variants);
 			
 			if(isset($variants))
 			foreach($variants as $variant)
 			{
 				$result = array();
 				$result =  $product;
 				foreach($variant as $name=>$value)
 					$result->{$name}=$value;

	 			foreach($this->columns_names as $internal_name=>$column_name)
	 			{
	 				if(isset($result->{$internal_name}))
		 				$res[$internal_name] = $result->{$internal_name};
	 				else
		 				$res[$internal_name] = '';
	 			}
	 			fputcsv($f, array_map('transcode', $res) , $this->column_delimiter);

	 		}
		}
		
		$total_products = $this->products->count_products();
		
		if($this->products_count*$page < $total_products)
			return array('end'=>false, 'page'=>$page, 'totalpages'=>$total_products/$this->products_count);
		else
			return array('end'=>true, 'page'=>$page, 'totalpages'=>$total_products/$this->products_count);		

		fclose($f);

	}
	
}

$export_ajax = new ExportAjax();
$data = $export_ajax->fetch();
if($data)
{
	header("Content-type: application/json; charset=utf-8");
	header("Cache-Control: must-revalidate");
	header("Pragma: no-cache");
	header("Expires: -1");
	$json = json_encode($data);
	print $json;
}
