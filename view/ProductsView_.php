<?PHP

/**
 * Этот класс использует шаблон products.tpl
 *
 */
 
require_once('View.php');

class ProductsView extends View
{
	public function __construct(){
		dtimer::log(__METHOD__." start");
	}
	
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
		dtimer::log(__METHOD__ . " start");
				
		$module = $this->coMaster->uri_arr['path']['module'] ? $this->coMaster->uri_arr['path']['module'] : null;
		$url = $this->coMaster->uri_arr['path']['url'];
		 
		switch($module){
		case 'brands':
			if(isset($this->coMaster->uri_arr['path']['brand'])){
				$this->coMaster->uri_arr['path']['brand'][] = $url;
			} else {
				$this->coMaster->uri_arr['path']['brand'] = array($url);
			}
			break;
		case 'catalog':
			$category_url = $url;
			break;
		}

		
		$filter = array();
		
		//если у нас есть фильтрация по бренду, нам понадобятся id брендов для формирования $filter
		//получим их
		if( isset($this->coMaster->uri_arr['path']['brand']) ){
			$brands_urls = $this->coMaster->uri_arr['path']['brand'];
			//для экономии памяти присваиваем по ссылке
			$brands_ids = $this->brands->get_brands_ids(array('return' => array('col' => 'id', 'key'=> 'trans')) );
			$filter['brand_id'] = array_intersect_key($brands_ids, array_flip($brands_urls));
		}
				
		
		
		$filter['visible'] = 1;	

		// Если задан бренд, выберем его из базы
		if (  (!empty($brands_urls)) ) {
			if(is_array($brands_urls)){
				$brand = $this->brands->get_brand( reset($brands_urls) );
			}
			
			if (empty($brand)){
				dtimer::log(__METHOD__ . __LINE__ ." empty brand ", 2 );
				return false;
			}
			$this->design->assign('brand', $brand);
		}
				
		
		// Выберем текущую категорию
		if (  (!empty($category_url)) ) {
							

			$category = $this->categories->get_category((string)$category_url);

			
			//301 moved permanently
			if(isset($category['url2']) && $category['trans2'] !== $category['trans'] && $category['url2'] == $category_url){
				$arr = $this->coMaster->uri_arr['path'];
				$arr['url'] = $category['trans'];
				$url = '/'.$this->coMaster->gen_uri($arr);
				//~ print_r($url);
				header("Location: $url",TRUE,301);
			}
			

			if (empty($category) || (!$category['visible'] && empty($_SESSION['admin']))){
				dtimer::log(__METHOD__ . __LINE__ ." empty category ", 2 );
				return false;
			}
			$this->design->assign('category', $category);
			$filter['category_id'] = $category['children'];
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
			$filter['sort'] = 'pos';			
		$this->design->assign('sort', $filter['sort']);
				
		
		// Свойства товаров
		if ( (!empty($category))  ) {
			//получим включенные для фильтра на сайте свойства товаров для конкретной категории
			if($features = $this->features->get_features(array('category_id'=>$category['id'], 'in_filter'=>1))){
				$filter['feature_id'] = array_keys($features);
				$this->design->assign('features', $features);
			}
			
			
			if($filter = $this->uri_to_api_filter($this->coMaster->uri_arr, $filter, $features)){
				if(isset($filter['redirect'])){
					$url = '/'.$this->coMaster->gen_filter_to_uri( $filter );
					print_r($url);
					//header("Location: $url",TRUE,301);					
				}
				
				$options = $this->features->get_options_mix($filter);
				$this->design->assign('options', $options);
			} else {
				return false;
			}
 		}
		

		
		// Если не задана, то равна 1
		$current_page = isset($this->coMaster->uri_arr['path']['page']) ? $this->coMaster->uri_arr['path']['page'] : 1;
		$this->design->assign('current_page_num', $current_page);
		// Вычисляем количество страниц
		$products_count = $this->products->count_products($filter);
		
	
		// Показать все страницы сразу
		if($this->request->get('page') == 'all'){
			$items_per_page = $products_count;
		}
		
		$pages_num = ceil($products_count/$items_per_page);
		$this->design->assign('total_pages_num', $pages_num);
		$this->design->assign('total_products_num', $products_count);

		$filter['pages'] = $pages_num;
		$filter['products_count'] = $products_count;
		$filter['page'] = $current_page;
		$filter['limit'] = $items_per_page;
		
		///////////////////////////////////////////////
		// Постраничная навигация END
		///////////////////////////////////////////////
		

		$discount = 0;
		if(isset($_SESSION['user_id']) && $user = $this->users->get_user(intval($_SESSION['user_id'])))
			$discount = $user['discount'];
			
		// Товары получаем их сразу массивом
		$products = $this->products->get_products($filter);
			
		// Если искали товар и найден ровно один - перенаправляем на него
		if(!empty($keyword) && $products_count == 1){
			$p = (array)$products;
			$p = reset($p);
			header('Location: '.$this->config->root_url.'/products/'.$p['trans']);
		}
		
		if( !empty($products) )
		{
			$pids = array_keys($products);

			$variants = $this->variants->get_variants(array('grouped' => 'product_id', 
			'product_id'=>$pids));
			

			if(is_array($products)){
				foreach($products as $pid=>&$product){
					$product['variants'] = isset($variants[$pid]) && is_array($variants[$pid]) ? $variants[$pid] : array();
				}
			}
			
			$this->design->assign('products', $products);
			
			//ajax
			if(!empty($_GET['ajax'])){
				$html = $this->design->fetch('products_content.tpl');
				
				//~ return false;
				print json_encode($html);
				die;
			}
 		}
		
		// Выбираем бренды, они нужны нам в шаблоне	
		if(!empty($category))
		{
			$brands = $this->brands->get_brands(array('category_id'=>$category['children'], 'visible'=>1));
			$category['brands'] = $brands;
		}
		
		// Устанавливаем мета-теги в зависимости от запроса
		if($this->page)
		{
			$this->design->assign('meta_title', $this->page['meta_title']);
			$this->design->assign('meta_keywords', $this->page['meta_keywords']);
			$this->design->assign('meta_description', $this->page['meta_description']);
		}
		elseif(isset($category))
		{
			$this->design->assign('category', $category);
			$this->design->assign('meta_title', $category['meta_title']);
				
			$this->design->assign('meta_keywords', $category['meta_keywords']);
			$this->design->assign('meta_description', $category['meta_description']);
		}
		elseif(isset($brand))
		{
			$this->design->assign('meta_title', $brand['meta_title']);
			$this->design->assign('meta_keywords', $brand['meta_keywords']);
			$this->design->assign('meta_description', $brand['meta_description']);
		}
		elseif(isset($keyword))
		{
			$this->design->assign('meta_title', $keyword);
		}
		
		//передадим фильтр в шаблон
		$this->design->assign('filter', $filter);
		$this->body = $this->design->fetch('products.tpl');
		dtimer::log(__METHOD__ . " return ");
		return $this->body;
	}
	
	//функция по обработке фильтров из адресной строки и преобразованию их в фильтр для api
	private function uri_to_api_filter($uri_arr, $filter, $features){
		
		$urifilter = isset($uri_arr['path']) ? $uri_arr['path'] : null;
		if(!isset($urifilter)){
			dtimer::log(__METHOD__ . " $urifilter is not set return filter unchanged " );
			return $filter;
		}
		//Если есть модуль
		if(isset($urifilter['module'])){
			$filter['module'] = $urifilter['module'];
		}
		//Если есть бренд
		if(isset($urifilter['brand'])){
			$brands_ids = $this->brands->get_brands_ids(array('return' => array('col' => 'id', 'key'=> 'trans')) );
			$filter['brand_id'] = array_intersect_key($brands_ids, array_flip($urifilter['brand']));
		}
		
		if(!isset($urifilter['features'])){
			return $filter;
		}
		//если не получается преобразовать обычные имена - пробуем альтернативные
		if( $filter = $this->uri_to_ids_filter($urifilter['features'], $filter) ){
		}else if ($filter = $this->uri_to_ids_filter($urifilter['features'], $filter, true)){
		} else {
			return false;
		}
		
		return $filter;
	}		
	
	//функция для преобразования uri в id для $filter['features']
	//флаг служит для задания преобразования по альтернативным названиям параметров trans2
	private function uri_to_ids_filter($urifilter, $filter, $flag = false){
		//обычный поиск просходит по полям trans в таблице features в таблице options_uniq
		//альтернативный поиск - по полю trans2.
		$key = $flag ? 'trans2' : 'trans';
		if($flag){
			$filter['redirect'] = true;
		}
		
		//массив для результата
		$filter['features'] = array();
		
		dtimer::log(__METHOD__ . " $key ");
		//тут получим имена транслитом и id для преобразования параметров заданных в адресной строке
		$features_trans = $this->features->get_features_ids( array('in_filter'=>1, 'return' => array('key' => $key, 'col' => 'id')) );

		//перебираем массив фильтра из адресной строки
		foreach($urifilter as $name=>$vals){
			
			//если заданный в адресной строке у нас есть, получим хеш опции для поиска в таблице s_options_uniq 
			if( !isset($features_trans[$name]) ){
				dtimer::log(__METHOD__ . " feature '$name' not found! " . print_r($vals, true), 2 );
				return false;
			}
			//~ print $name . "\n";
			foreach($vals as &$v){
				$v = uri2str( $v);
			}
			unset($v);
			//~ dtimer::log(__METHOD__ . " options md4: " . print_r($vals, true) );

			//получим id уникальных значений по их хешам
			$ids = $this->features->get_options_ids(array($key => $vals, 'return'=>array('key'=>'id', 'col'=>'id')) );
			
				
			//тут проверим количество переданных значений опций и количество полученных из базы,
			//если не совпадает - return false
			if($ids === false || count($ids) !== count($vals)){
				return false;
			}else{
				//добавим в фильтр по свойствам массив с id значений опций
				//а также правильные названия транслитом
				if($flag){
					$features_trans2 = $this->features->get_features_ids( array('in_filter'=>1, 'return' => array('key' => 'id', 'col' => 'trans2')) );
				}else{
					$features_trans2 = $this->features->get_features_ids( array('in_filter'=>1, 'return' => array('key' => 'id', 'col' => 'trans')) );
				}
				$filter['translit'][$features_trans2[$features_trans[$name]]] = $this->features->get_options_ids(array($hash => $vals, 'return'=>array('key'=>'id', 'col'=>'trans')) );
				$filter['features'][$features_trans[$name]] = $ids;
			}
		}
		
		return $filter;
	}
	

}
