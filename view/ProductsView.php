<?PHP

/**
 * Simpla CMS
 *
 * @copyright 	2011 Denis Pikusov
 * @link 		http://simplacms.ru
 * @author 		Denis Pikusov
 *
 * Этот класс использует шаблон products.tpl
 *
 */
 
require_once('View.php');

class ProductsView extends View
{
 	/**
	 *
	 * Отображение списка товаров
	 *
	 */	
	function fetch()
	{
		// Раньше все параметры брались из get query, теперь парсятся coMaster->parse_uri() из самой адресной строки
		//~ print "<PRE>";
		//~ print_r($this->coMaster->uri_arr);
		//~ print "</PRE>";
		$category_url = $this->coMaster->uri_arr['path_arr']['url'];
		
		$filter = array();
		
		//если у нас есть фильтрация по бренду, нам понадобятся id брендов для формирования $filter
		//получим их
		if( isset($this->coMaster->uri_arr['path_arr']['brand']) ){
			$brands_urls = $this->coMaster->uri_arr['path_arr']['brand'];
			//для экономии памяти присваиваем по ссылке
			$this->brands->get_brands_ids();
			$brands_ids =& $this->brands->brands_ids;
			$filter['brand_id'] = array_intersect_key($brands_ids, array_flip($brands_urls));
		}
		
		
		$filter['visible'] = 1;	

		// Если задан бренд, выберем его из базы
		if (  (!empty($brands_urls)) ) {
			if(is_array($brands_urls)){
				$brand = $this->brands->get_brand( reset($brands_urls) );
			}
			
			if (empty($brand)){
				return false;
			}
			$this->design->assign('brand', $brand);
		}
		
		// Выберем текущую категорию
		if (  (!empty($category_url)) ) {
			$category = $this->categories->get_category((string)$category_url);
			if (empty($category) || (!$category->visible && empty($_SESSION['admin'])))
				return false;
			$this->design->assign('category', $category);
			$filter['category_id'] = $category->children;
		}

		// Если задано ключевое слово
		$keyword = $this->request->get('keyword');
		if (  (!empty($keyword)) ) {
			$this->design->assign('keyword', $keyword);
			$filter['keyword'] = $keyword;
		}

		// Сортировка товаров, сохраняем в сесси, чтобы текущая сортировка оставалась для всего сайта
		if($sort = $this->request->get('sort', 'string'))
			$_SESSION['sort'] = $sort;		
		if (!empty($_SESSION['sort']))
			$filter['sort'] = $_SESSION['sort'];			
		else
			$filter['sort'] = 'position';			
		$this->design->assign('sort', $filter['sort']);
		
		// Свойства товаров
		
		if ( (!empty($category))  ) {
			//тут получим имена транслитом и id для преобразования параметров заданных в адресной строке
			$features_trans = $this->features->get_features_trans(array('in_filter'=>1));
			$features = $this->features->get_features(array('category_id'=>$category->id, 'in_filter'=>1));

			//~ print_r($features_trans);
			//~ print_r($features);
			//~ print_r($this->coMaster->uri_arr['path_arr']);
			//тут фильтр в ЧПУ виде
			if( isset($this->coMaster->uri_arr['path_arr']['filter']) ){
				//перебираем массив
				foreach($this->coMaster->uri_arr['path_arr']['filter'] as $name=>$vals){
					
					//если заданный в адресной строке у нас есть, получим хеш опции для поиска в таблице s_options_uniq 
					if( isset($features_trans[$name]) ){
						//~ print $name . "\n";
						foreach($vals as &$v){
							$v = hash('md4', $v);
						}
						unset($v);
						//получим id уникальных значений по их хешам
						$ids = $this->features->get_options_md4($vals);
						//~ print_r($ids);
						//добавим в фильтр по свойствам массив с id значений опций
						$filter['features'][$features_trans[$name]] = $ids;
					}
				}
			}
			//~ print_r($features_trans);
			//~ print_r($filter['features']);
			

			$options_filter['visible'] = 1;
			
			if (  ( !empty($features) )  ) {
				$features_ids = array_keys((array)$features);
			}
			
			if(!empty($features_ids)){
				$options_filter['feature_id'] = $features_ids;
			}
			$options_filter['category_id'] = $category->children;
			if( isset($filter['features']) ){
				$options_filter['features'] =& $filter['features'];
			}
			if(!empty($brands_urls)) {
				$options_filter['brand_id'] =& $filter['brand_id'];
			}
			$options = $this->features->get_options($options_filter);
			//~ print_r($options);
			
			if(isset($features) && !empty_($features)){
				foreach($features as $k=>&$feature) {
					if( isset($options->{$k}) ){
						$feature->options = $options->{$k};
					}
				}
			}
			
			if (  ( !empty($features) )  ) {
				foreach($features as $i=>&$feature) { 
					if ( (empty($feature->options))  ) {
						unset($features->{$i});
					}
				}
			}

			$this->design->assign('features', $features);
 		}

		// Постраничная навигация
		$items_per_page = $this->settings->products_num;		
		// Текущая страница в постраничном выводе
		$current_page = $this->request->get('page', 'integer');
		// Если не задана, то равна 1
		$current_page = max(1, $current_page);
		$this->design->assign('current_page_num', $current_page);
		// Вычисляем количество страниц
		$products_count = $this->products->count_products($filter);
		
		// Показать все страницы сразу
		if($this->request->get('page') == 'all')
			$items_per_page = $products_count;	
		
		$pages_num = ceil($products_count/$items_per_page);
		$this->design->assign('total_pages_num', $pages_num);
		$this->design->assign('total_products_num', $products_count);

		$filter['page'] = $current_page;
		$filter['limit'] = $items_per_page;
		
		///////////////////////////////////////////////
		// Постраничная навигация END
		///////////////////////////////////////////////
		

		$discount = 0;
		if(isset($_SESSION['user_id']) && $user = $this->users->get_user(intval($_SESSION['user_id'])))
			$discount = $user->discount;
			
		// Товары получаем их сразу массивом
		$products = $this->products->get_products($filter);
			
		// Если искали товар и найден ровно один - перенаправляем на него
		if(!empty($keyword) && $products_count == 1){
			$p = (array)$products;
			$p = reset($p);
			header('Location: '.$this->config->root_url.'/products/'.$p->url);
		}
		
		if( !empty($products) )
		{
			$products_ids = array_keys((array)$products);
			foreach($products as &$product)
			{
				$product->variants = array();
				$product->images = array();
				$product->properties = array();
			}
	
			if($variants = $this->variants->get_variants(array('product_id'=>$products_ids, 'in_stock'=>true)) ) {
				foreach($variants as &$variant){
					//$variant->price *= (100-$discount)/100;
					$products->{$variant->product_id}->variants[] = $variant;
				}
				unset($variant);
			}
	
			$images = $this->products->get_images(array('product_id'=>$products_ids));
			foreach($images as $image)
				$products->{$image->product_id}->images[] = $image;

			foreach($products as &$product)
			{
				if(isset($product->variants[0]))
					$product->variant = $product->variants[0];
				if(isset($product->images[0]))
					$product->image = $product->images[0];
			}
	
			/*
			$properties = $this->features->get_options(array('product_id'=>$products_ids));
			foreach($properties as $property)
				$products[$property->product_id]->options[] = $property;
			*/
	
			$this->design->assign('products', $products);
 		}
		
		// Выбираем бренды, они нужны нам в шаблоне	
		if(!empty($category))
		{
			$brands = $this->brands->get_brands(array('category_id'=>$category->children, 'visible'=>1));
			$category->brands = $brands;		
		}
		
		// Устанавливаем мета-теги в зависимости от запроса
		if($this->page)
		{
			$this->design->assign('meta_title', $this->page->meta_title);
			$this->design->assign('meta_keywords', $this->page->meta_keywords);
			$this->design->assign('meta_description', $this->page->meta_description);
		}
		elseif(isset($category))
		{
			$this->design->assign('meta_title', $category->meta_title);
			$this->design->assign('meta_keywords', $category->meta_keywords);
			$this->design->assign('meta_description', $category->meta_description);
		}
		elseif(isset($brand))
		{
			$this->design->assign('meta_title', $brand->meta_title);
			$this->design->assign('meta_keywords', $brand->meta_keywords);
			$this->design->assign('meta_description', $brand->meta_description);
		}
		elseif(isset($keyword))
		{
			$this->design->assign('meta_title', $keyword);
		}
		
			
		$this->body = $this->design->fetch('products.tpl');
		return $this->body;
	}
	
	

}
