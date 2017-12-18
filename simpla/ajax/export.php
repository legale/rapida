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
			'old_price'=>    'Старая цена',
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
	private $products_count = 200;
	private $export_files_dir = '../files/export/';
	private $filename = 'export.csv';

	public function fetch()
	{
		/* синхронизируем таблицы features и options, чтобы количество свойств из features 
		 * соответствовало столбцам в options
		 * Если функция возвращает false, значит что-то пошло не так, останавливаемся
		 */
		if ( !$this->sys->sync_options() ) {
			print "ERROR SYNC FEATURES AND OPTIONS!";
			die;
		}
		
		if(!$this->users->check_access('export')){
			return false;
		}
		
		$filter = array('return' => array('key' => 'id', 'col' => 'name')) ;
		//получим все значения id=>название свойства
		if( !$features = $this->features->get_features_ids() ){
			return false;
		}
 		ksort($features);
 		//~ print_r($features);
 		//~ die;
		
		// Сначала получим уникальные значения свойств товаров, чтобы, не искать их постоянно
		// должно значительное ускорить экспорт
		
		//поставим выполнение запроса без кеша только для начала экспорта
		
		
		$filter = array('return' => array('key' => 'id', 'col' => 'val')) ;
		if( !isset($_GET['page']) || $_GET['page'] != 1 )
		{
			$filter['force_no_cache'] = true;
		}
		
		if(!$options = $this->features->get_options_ids($filter)){
			return false;
		}	
	
		//получим бренды
		if(!$brands = $this->brands->get_brands_ids()){
			return false;
		}	
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
		$this->columns_names = array_merge($this->columns_names, array_combine($features,$features));
			
		// Если начали сначала - добавим в первую строку названия колонок
		if($page == 1)
		{
			fputcsv($f, array_map('transcode', $this->columns_names) , $this->column_delimiter );
		}
		
		//Несколько товаров
		$products = $this->products->get_products(array('force_no_cache'=> true, 'page'=>$page, 'limit'=>$this->products_count));
 		
 		//получим опции от них
 		$this->db->query("SELECT * FROM __options WHERE 1 AND product_id in (?@)", array_keys($products)); 
 
 		$options_raw = $this->db->results_array(null, 'product_id', true);
		
		foreach($products as &$p){
			foreach($options_raw[$p['id']] as $fid=>$vid){
				$p[$features[$fid]] = !empty_($vid) && isset($options[$vid]) ? $options[$vid] : '';
			}
		}
		unset($p);


 		if(empty($products)){
 			return false;
 		}
 		
 		// Категории товаров
 		foreach($products as $pid=>&$p)
 		{
	 		$categories = array();
	 		$cats = $this->categories->get_product_categories($pid);
	 		foreach($cats as $category){
	 			$path = array();
	 			$cat = $this->categories->get_category((int)$category['category_id']);
	 			if(!empty($cat))
 				{
	 				// Вычисляем составляющие категории
	 				foreach($cat['path'] as $el){
	 					$path[] = str_replace($this->subcategory_delimiter, '\\'.$this->subcategory_delimiter, $el['name']);
	 				}
	 				// Добавляем категорию к товару 
	 				$categories[] = implode('/', $path);
 				}
	 		}
	 		$p['category'] = implode(', ', $categories);
 		}
 		unset($p);
 		
 		$pids = array_keys($products);
 		// Изображения товаров
 		foreach($pids as $id){
			$images = $this->image->get('products', array('item_id'=>$id));
			foreach($images as $i)
			{
				// Добавляем изображения к товару через запятую
				if(empty($products[$i['item_id']]['images'])){
					$products[$i['item_id']]['images'] = $i['basename'];
				}else{
					$products[$i['item_id']]['images'] .= ', '.$i['basename'];
				}
			}
		}
 
 		$variants = $this->variants->get_variants(array('product_id'=>array_keys($products)));

		foreach($variants as $variant)
 		{
 			if(isset($products[$variant['product_id']]))
 			{
	 			$v                    = array();
	 			$v['variant']         = $variant['name'];
	 			$v['price']           = $variant['price'];
	 			$v['old_price']   = $variant['old_price'];
	 			$v['sku']             = $variant['sku'];
	 			$v['stock']           = $variant['stock'];

				$products[$variant['product_id']]['variants'][] = $v;
	 		}
		}
		
		foreach($products as &$p)
 		{
 			$variants = $p['variants'];
 			unset($p['variants']);
 			
 			if(isset($variants))
 			foreach($variants as $v)
 			{
 				$result = array();
 				$result =  $p;
 				foreach($variant as $name=>$value)
 					$result[$name]=$value;

	 			foreach($this->columns_names as $internal_name=>$column_name)
	 			{
	 				if(isset($result[$internal_name]))
		 				$res[$internal_name] = $result[$internal_name];
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
