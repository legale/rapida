<?php

/**
 * Работа с товарами
 *
 * @copyright 	2011 Denis Pikusov
 * @link 		http://simplacms.ru
 * @author 		Denis Pikusov
 *
 */

require_once('Simpla.php');

class Products extends Simpla
{
	/**
	* Функция возвращает товары
	* Возможные значения фильтра:
	* id - id товара или их массив
	* category_id - id категории или их массив
	* brand_id - id бренда или их массив
	* page - текущая страница, integer
	* limit - количество товаров на странице, integer
	* sort - порядок товаров, возможные значения: position(по умолчанию), name, price
	* keyword - ключевое слово для поиска
	* features - фильтр по свойствам товара, массив (id свойства => значение свойства)
	*/
	public function get_products($filter = array())
	{		
		//сначала уберем из фильтра лишние параметры, которые не влияют на результат, но влияют на хэширование
		$filter_ = $filter;
		dtimer::log("get_products start filter: " . var_export($filter_, true));
		unset($filter_['method']);
		if (isset($filter_['force_no_cache'])){
			$force_no_cache = true;
			unset($filter_['force_no_cache']);
		}
		
		
		//сортируем фильтр, чтобы порядок данных в нем не влиял на хэш
		ksort($filter_);
		$filter_string = var_export($filter_, true);
		$keyhash =  hash('md4', 'get_products'. $filter_string);

		//если запуск был не из очереди - пробуем получить из кеша
		if(!isset($force_no_cache)){
			dtimer::log("get_products normal run keyhash: $keyhash");
			$res = $this->cache->get_cache_nosql($keyhash, false);
		
		
		
			//запишем в фильтр параметр force_no_cache, чтобы при записи задания в очередь
			//функция выполнялась полностью
			$filter_['force_no_cache'] = true;
			$filter_string = var_export($filter_, true);
			dtimer::log("get_products add task force_no_cache keyhash: $keyhash");

			$task = '$this->products->get_products(';
			$task .= $filter_string;
			$task .= ');';
			$this->queue->addtask($keyhash, isset($filter['method']) ? $filter['method'] : '', $task);
		}		
		
		if(isset($res) && !empty_($res) ){
			dtimer::log("get_cache get_products HIT! res count: " . count($res));
			return $res;
		}		
		
		
		
		
		// По умолчанию
		$limit = 100;
		$page = 1;
		$category_id_filter = '';
		$brand_id_filter = '';
		$product_id_filter = '';
		$features_filter = '';
		$keyword_filter = '';
		$visible_filter = '';
		$is_featured_filter = '';
		$discounted_filter = '';
		$in_stock_filter = '';
		$no_images_filter = '';
		$group_by = '';
		$order = 'p.position DESC';

		if(isset($filter['limit']))
			$limit = max(1, intval($filter['limit']));

		if(isset($filter['page']))
			$page = max(1, intval($filter['page']));

		$sql_limit = $this->db->placehold(' LIMIT ?, ? ', ($page-1)*$limit, $limit);

		if(!empty($filter['id']))
			$product_id_filter = $this->db->placehold('AND p.id in(?@)', (array)$filter['id']);

		if(!empty($filter['category_id']))
		{
			$category_id_filter = $this->db->placehold('INNER JOIN __products_categories pc ON pc.product_id = p.id AND pc.category_id in(?@)', (array)$filter['category_id']);
			$group_by = "GROUP BY p.id";
		}

		if(!empty($filter['brand_id']))
			$brand_id_filter = $this->db->placehold('AND p.brand_id in(?@)', (array)$filter['brand_id']);

		if(isset($filter['no_images']))
			$no_images_filter = 'AND p.id NOT IN (SELECT DISTINCT product_id FROM __images)';

		if(isset($filter['featured']))
			$is_featured_filter = $this->db->placehold('AND p.featured=?', intval($filter['featured']));

		if(isset($filter['in_stock']) ){
			if ( (bool)$filter['in_stock'] == true) {
				$in_stock_filter = 'AND p.id IN (SELECT product_id FROM s_variants WHERE 1 AND price>0 AND stock != 0)';
			}else {
				$in_stock_filter = 'AND p.id IN (SELECT product_id FROM s_variants WHERE 1 AND price>0 AND stock = 0)';
			}
		}
		
		if(isset($filter['discounted']))
			$discounted_filter = 'AND p.id IN (SELECT DISTINCT product_id FROM __variants WHERE price < compare_price)';

		if(isset($filter['visible']))
			$visible_filter = $this->db->placehold('AND p.visible=?', intval($filter['visible']));

		if(!empty($filter['sort']))
			switch ($filter['sort'])
			{
				case 'position':
				$order = 'p.position DESC';
				break;
				case 'name':
				$order = 'p.name';
				break;
				case 'created':
				$order = 'p.created DESC';
				break;
				case 'price':
				//$order = 'pv.price IS NULL, pv.price=0, pv.price';
				$order = '(SELECT -pv.price FROM __variants pv WHERE (pv.stock IS NULL OR pv.stock>0) AND p.id = pv.product_id AND pv.position=(SELECT MIN(position) FROM __variants WHERE (stock>0 OR stock IS NULL) AND product_id=p.id LIMIT 1) LIMIT 1) DESC';
				break;
			}

		if(!empty($filter['keyword']))
		{
			$keywords = explode(' ', $filter['keyword']);
			foreach($keywords as $keyword)
			{
				$kw = $this->db->escape(trim($keyword));
				if($kw!=='')
					$keyword_filter .= $this->db->placehold("AND (p.name LIKE '%$kw%' OR p.meta_keywords LIKE '%$kw%' OR p.id in (SELECT product_id FROM __variants WHERE sku LIKE '%$kw%'))");
			}
		}

		//фильтрация по свойствам товаров
		if( !empty($filter['features']) ){
			foreach($filter['features'] as $fid=>$vids){
				if(is_array($vids)){
					$features_filter .= $this->db->placehold(" AND `$fid` in (?@)", $vids);
				}
			}
			$features_filter = "AND p.id in (SELECT product_id FROM __options WHERE 1 $features_filter )";
		}
		$query = $this->db->placehold("SELECT  
					p.id,
					p.url,
					p.brand_id,
					p.name,
					p.annotation,
					p.body,
					p.position,
					p.created as created,
					p.image,
					p.visible, 
					p.featured, 
					p.meta_title, 
					p.meta_keywords, 
					p.meta_description
				#	, 
				#	b.name as brand,
				#	b.url as brand_url
					
				FROM __products p		
				$category_id_filter 
			#	LEFT JOIN __brands b ON p.brand_id = b.id
				WHERE 
					1
					$no_images_filter
					$product_id_filter
					$brand_id_filter
					$features_filter
					$keyword_filter
					$is_featured_filter
					$discounted_filter
					$in_stock_filter
					$visible_filter
				$group_by
				ORDER BY $order
					$sql_limit");

		dtimer::log(__METHOD__ . " query: $query ");
		//~ dtimer::show();
		//~ die;
		$this->db->query($query);
		
		if($res = $this->db->results_array(null,'id') ) {
			dtimer::log(__METHOD__ . " set_cache_nosql key: $keyhash");
			$this->cache->set_cache_nosql($keyhash, $res);
			return $res;
		} else {
			return false;
		}
	}

	/**
	* Функция возвращает количество товаров
	* Возможные значения фильтра:
	* category_id - id категории или их массив
	* brand_id - id бренда или их массив
	* keyword - ключевое слово для поиска
	* features - фильтр по свойствам товара, массив (id свойства => значение свойства)
	*/
	public function count_products($filter = array())
	{	
		//сначала уберем из фильтра лишние параметры, которые не влияют на результат, но влияют на хэширование
		$filter_ = $filter;
		dtimer::log("count_product start filter: " . var_export($filter_, true));
		unset($filter_['method'], $filter_['sort'], $filter_['page'], $filter_['limit']);
		if (isset($filter_['force_no_cache'])){
			$force_no_cache = true;
			unset($filter_['force_no_cache']);
		}
		
		
		//сортируем фильтр, чтобы порядок данных в нем не влиял на хэш
		ksort($filter_);
		$filter_string = var_export($filter_, true);
		$keyhash =  hash('md4', 'count_products'. $filter_string);

		//если запуск был не из очереди - пробуем получить из кеша
		if(!isset($force_no_cache)){
			dtimer::log("count_products normal run keyhash: $keyhash");
			$res = $this->cache->get_cache_integer($keyhash);
		
		
		
			//запишем в фильтр параметр force_no_cache, чтобы при записи задания в очередь
			//функция выполнялась полностью
			$filter_['force_no_cache'] = true;
			$filter_string = var_export($filter_, true);
			dtimer::log("count_products add task force_no_cache keyhash: $keyhash");

			$task = '$this->products->count_products(';
			$task .= $filter_string;
			$task .= ');';
			//~ dtimer::log("count_products add task: $keyhash " . $filter['method']);
			$this->queue->addtask($keyhash, isset($filter['method']) ? $filter['method'] : '', $task);
		}		
		
		if(isset($res) && !empty_($res) ){
			dtimer::log("get_cache count_products HIT! value: '$res'");
			return $res;
		}
		
		$category_id_filter = '';
		$brand_id_filter = '';
		$product_id_filter = '';
		$keyword_filter = '';
		$visible_filter = '';
		$is_featured_filter = '';
		$in_stock_filter = '';
		$discounted_filter = '';
		$features_filter = '';
		$no_images_filter = '';
		
		if(!empty($filter['category_id']))
			$category_id_filter = $this->db->placehold('INNER JOIN __products_categories pc ON pc.product_id = p.id AND pc.category_id in(?@)', (array)$filter['category_id']);

		if(!empty($filter['brand_id']))
			$brand_id_filter = $this->db->placehold('AND p.brand_id in(?@)', (array)$filter['brand_id']);

		if(!empty($filter['id']))
			$product_id_filter = $this->db->placehold('AND p.id in(?@)', (array)$filter['id']);
		
		if(isset($filter['keyword']))
		{
			$keywords = explode(' ', $filter['keyword']);
			foreach($keywords as $keyword)
			{
				$kw = $this->db->escape(trim($keyword));
				if($kw!=='')
					$keyword_filter .= $this->db->placehold("AND (p.name LIKE '%$kw%' OR p.meta_keywords LIKE '%$kw%' OR p.id in (SELECT product_id FROM __variants WHERE sku LIKE '%$kw%'))");
			}
		}

		if(isset($filter['no_images']))
			$no_images_filter = 'AND p.id NOT IN (SELECT DISTINCT product_id FROM __images)';

		if(isset($filter['featured']))
			$is_featured_filter = $this->db->placehold('AND p.featured=?', intval($filter['featured']));

		if(isset($filter['in_stock']) ){
			if ( (bool)$filter['in_stock'] == true) {
				$in_stock_filter = 'AND p.id IN (SELECT product_id FROM s_variants WHERE 1 AND price>0 AND stock != 0)';
			}else {
				$in_stock_filter = 'AND p.id IN (SELECT product_id FROM s_variants WHERE 1 AND price>0 AND stock = 0)';
			}
		}
		
		if(isset($filter['discounted']))
			$discounted_filter = 'AND p.id IN (SELECT DISTINCT product_id FROM __variants WHERE price < compare_price)';

		if(isset($filter['visible']))
			$visible_filter = $this->db->placehold('AND p.visible=?', intval($filter['visible']));
		
		
		//фильтрация по свойствам товаров
		if( !empty($filter['features']) ){
			foreach($filter['features'] as $fid=>$vids){
				if(is_array($vids)){
					$features_filter .= $this->db->placehold(" AND `$fid` in (?@)", $vids);
				}
			}
			$features_filter = "AND p.id in (SELECT product_id FROM __options WHERE 1 $features_filter )";
		}
		
		$query =$this->db->placehold("SELECT count(distinct p.id) as count
				FROM __products AS p
				$category_id_filter
				WHERE 1
					$no_images_filter
					$brand_id_filter
					$product_id_filter
					$keyword_filter
					$is_featured_filter
					$in_stock_filter
					$discounted_filter
					$visible_filter
					$features_filter ");

		dtimer::log(__METHOD__ . " query: $query");
		$this->db->query($query);
		$res = $this->db->result('count');
		dtimer::log("set_cache_integer key: $keyhash");
		$this->cache->set_cache_integer($keyhash, $res);
		return $res;

	}


	/**
	* Функция возвращает товар по id
	* @param	$id
	* @retval	object
	*/
	public function get_product($id)
	{
		if(is_int($id))
			$filter = $this->db->placehold('p.id = ?', $id);
		else
			$filter = $this->db->placehold('p.url = ?', $id);
			
		$query = "SELECT DISTINCT
					p.id,
					p.url,
					p.brand_id,
					p.name,
					p.annotation,
					p.body,
					p.position,
					p.created as created,
					p.visible, 
					p.featured, 
					p.meta_title, 
					p.meta_keywords, 
					p.meta_description
				FROM __products AS p
				WHERE $filter
				GROUP BY p.id
				LIMIT 1";
		$this->db->query($query);
		$product = $this->db->result_array();
		return $product;
	}

	public function update_product($id, $product)
	{
		$query = $this->db->placehold("UPDATE __products SET ?% WHERE id in (?@) LIMIT ?", $product, (array)$id, count((array)$id));
		if($this->db->query($query))
			return $id;
		else
			return false;
	}
	
	public function add_product($product){
		if( is_object($product) ){
			$product = (array)$product;
		}
		//удалим id, если он сюда закрался, при создании id быть не должно
		if( isset($product['id']) ){
			unset($product['id']);
		}
		
		foreach ($product as $k=>$e){
			if( empty_($e) ){
				unset($product[$k]);
			}
		}
		$product = (array) $product;
		
		if(empty($product['url']))
		{
			$product['url'] = preg_replace("/[\s]+/ui", '-', $product['name']);
			$product['url'] = strtolower(preg_replace("/[^0-9a-zа-я\-]+/ui", '', $product['url']));
		}

		// Если есть товар с таким URL, добавляем к нему число
		while($this->get_product((string)$product['url']))
		{
			if(preg_match('/(.+)_([0-9]+)$/', $product['url'], $parts))
				$product['url'] = $parts[1].'_'.($parts[2]+1);
			else
				$product['url'] = $product['url'].'_2';
		}

		if($this->db->query("INSERT INTO __products SET ?%", $product))
		{
			$id = $this->db->insert_id();
			$this->db->query("UPDATE __products SET position=id WHERE id=?", $id);		
			return $id;
		}
		else
			return false;
	}
	
	
	/*
	*
	* Удалить товар
	*
	*/	
	public function delete_product($id)
	{
		if(!empty($id))
		{
			// Удаляем варианты
			if ( $variants = $this->variants->get_variants(array('product_id'=>$id)) ) {
				foreach($variants as $v) {
					$this->variants->delete_variant($v->id);
				}
			}
			
			// Удаляем изображения
			if( $images = $this->get_images(array('product_id'=>$id)) ) {
				foreach($images as $i) {
					$this->delete_image($i->id);
				}
			}
			
			// Удаляем категории
			if ( $categories = $this->categories->get_categories(array('product_id'=>$id)) ) {
				foreach($categories as $c) {
					$this->categories->delete_product_category($id, $c->id);
				}
			}

			// Удаляем свойства
			if ( $options = $this->features->get_options(array('product_id'=>$id)) ) { 
				foreach($options as $o) {
					$this->features->delete_option($id, $o->feature_id);
				}
			}
			
			// Удаляем связанные товары
			if ( $related = $this->get_related_products($id) ) {
				foreach($related as $r) {
					$this->delete_related_product($id, $r->related_id);
				}
			}
			
			// Удаляем товар из связанных с другими
			$query = $this->db->placehold("DELETE FROM __related_products WHERE related_id=?", intval($id));
			$this->db->query($query);
			
			// Удаляем отзывы
			if ( $comments = $this->comments->get_comments(array('object_id'=>$id, 'type'=>'product')) ) {
				foreach($comments as $c) {
					$this->comments->delete_comment($c->id);
				}
			}
			
			// Удаляем из покупок
			$this->db->query('UPDATE __purchases SET product_id=NULL WHERE product_id=?', intval($id));
			
			// Удаляем товар
			$query = $this->db->placehold("DELETE FROM __products WHERE id=? LIMIT 1", intval($id));
			if($this->db->query($query))
				return true;			
		}
		return false;
	}	
	
	public function duplicate_product($id)
	{
		$product = $this->get_product($id);
		$product->id = null;
		$product->external_id = '';
		$product->created = null;

		// Сдвигаем товары вперед и вставляем копию на соседнюю позицию
		$this->db->query('UPDATE __products SET position=position+1 WHERE position>?', $product->position);
		$new_id = $this->products->add_product($product);
		$this->db->query('UPDATE __products SET position=? WHERE id=?', $product->position+1, $new_id);
		
		// Очищаем url
		$this->db->query('UPDATE __products SET url="" WHERE id=?', $new_id);
		
		// Дублируем категории
		$categories = $this->categories->get_product_categories($id);
		foreach($categories as $c)
			$this->categories->add_product_category($new_id, $c->category_id);
		
		// Дублируем изображения
		$images = $this->get_images(array('product_id'=>$id));
		foreach($images as $image)
			$this->add_image($new_id, $image->filename);
			
		// Дублируем варианты
		$variants = $this->variants->get_variants(array('product_id'=>$id));
		foreach($variants as $variant){
			$variant->product_id = $new_id;
			unset($variant->id);
			if($variant->infinity)
				$variant->stock = null;
			unset($variant->infinity);
			$variant->external_id = '';
			$this->variants->add_variant($variant);
		}
		
		// Дублируем свойства
		$options = $this->features->get_options(array('product_id'=>$id));
		foreach($options as $o)
			$this->features->update_option($new_id, $o->feature_id, $o->value);
			
		// Дублируем связанные товары
		$related = $this->get_related_products($id);
		foreach($related as $r)
			$this->add_related_product($new_id, $r->related_id);
			
			
		return $new_id;
	}

	
	public function get_related_products($product_id = array())
	{
		if(empty($product_id))
			return array();

		$product_id_filter = $this->db->placehold('AND product_id in(?@)', (array)$product_id);
				
		$query = $this->db->placehold("SELECT product_id, related_id, position
					FROM __related_products
					WHERE 
					1
					$product_id_filter   
					ORDER BY position       
					");
		
		$this->db->query($query);
		$res = $this->db->results_array(null, 'related_id');
		return $res;
	}
	
	// Функция возвращает связанные товары
	public function add_related_product($product_id, $related_id, $position=0)
	{
		$query = $this->db->placehold("INSERT IGNORE INTO __related_products SET product_id=?, related_id=?, position=?", $product_id, $related_id, $position);
		$this->db->query($query);
		return $related_id;
	}
	
	// Удаление связанного товара
	public function delete_related_product($product_id, $related_id)
	{
		$query = $this->db->placehold("DELETE FROM __related_products WHERE product_id=? AND related_id=? LIMIT 1", intval($product_id), intval($related_id));
		$this->db->query($query);
	}
	
	
	function get_images($filter = array())
	{		
		$product_id_filter = '';
		$group_by = '';

		if(!empty($filter['product_id']))
			$product_id_filter = $this->db->placehold('AND i.product_id in(?@)', (array)$filter['product_id']);

		// images
		$query = $this->db->placehold("SELECT i.id, i.product_id, i.name, i.filename, i.position
									FROM __images AS i WHERE 1 $product_id_filter $group_by ORDER BY i.product_id, i.position");
		$this->db->query($query);
		return $this->db->results_array();
	}
	
	/* Метод для добавления изображений
	 * позиция изображения (поле position) устанавливается по порядку
	 */
	public function add_image($pid, $filename, $name = '')
	{
		$query = $this->db->placehold("SELECT max(position) as position FROM __images WHERE product_id=?", $pid);
		$this->db->query($query);
		$res = $this->db->result();
		
		//Если кол-во затронутых строк больше 0, берем макс. позицию и прибавляем к ней 1, иначе 0
		if(!is_null($res->position)){
			$pos = $res->position + 1;
		} else {
			$pos = 0;
			$this->db->query("UPDATE __products SET `image` = '$filename' WHERE `id`='$pid'");
		}
		
		$query = $this->db->placehold("INSERT INTO __images SET product_id=?, filename=?, position=?", $pid, $filename, $pos);
		dtimer::log(__METHOD__." query: '$query'");
		$this->db->query($query);		
		$id = $this->db->insert_id();
		return($id);
	}
	
	/*
	 * Обновляем изображение в таблице изображений
	 * Изображение с минимальной позицией, заносим в таблицу _products, чтобы не делать join
	 * для получения изображений
	 */
	public function update_image($id, $image)
	{
		$id = (int)$id;
		$this->db->query("UPDATE __images SET ?% WHERE id=?", $image, $id);
		$this->db->query("SELECT `product_id` as `pid` FROM __images WHERE id=?", $id);
		$pid = (int)$this->db->result('pid');
		$this->db->query("SELECT `filename` FROM __images WHERE `product_id`='$pid' ORDER BY `position` ASC LIMIT 1");
		$filename = $this->db->result('filename');
		$this->db->query("UPDATE __products SET `image` = '$filename' WHERE `id`='$pid'");
		
		//зачем тут id, надо посмотреть где используется.
		return($id);
	}
	
	public function delete_image($id)
	{
		$query = $this->db->placehold("SELECT filename FROM __images WHERE id=?", $id);
		$this->db->query($query);
		$filename = $this->db->result('filename');
		$query = $this->db->placehold("DELETE FROM __images WHERE id=? LIMIT 1", $id);
		$this->db->query($query);
		$query = $this->db->placehold("SELECT count(*) as count FROM __images WHERE filename=? LIMIT 1", $filename);
		$this->db->query($query);
		$count = $this->db->result('count');
		if($count == 0)
		{			
			$file = pathinfo($filename, PATHINFO_FILENAME);
			$ext = pathinfo($filename, PATHINFO_EXTENSION);
			
			// Удалить все ресайзы
			$rezised_images = glob($this->config->root_dir.$this->config->resized_images_dir.$file.".*x*.".$ext);
			if(is_array($rezised_images))
			foreach (glob($this->config->root_dir.$this->config->resized_images_dir.$file.".*x*.".$ext) as $f)
				@unlink($f);

			@unlink($this->config->root_dir.$this->config->original_images_dir.$filename);		
		}
	}
		
	/*
	*
	* Следующий товар
	*
	*/	
	public function get_next_product($id)
	{
		$this->db->query("SELECT position FROM __products WHERE id=? LIMIT 1", $id);
		$position = $this->db->result('position');
		
		$this->db->query("SELECT pc.category_id FROM __products_categories pc WHERE product_id=? ORDER BY position LIMIT 1", $id);
		$category_id = $this->db->result('category_id');

		$query = $this->db->placehold("SELECT id FROM __products p, __products_categories pc
										WHERE pc.product_id=p.id AND p.position>? 
										AND pc.position=(SELECT MIN(pc2.position) FROM __products_categories pc2 WHERE pc.product_id=pc2.product_id)
										AND pc.category_id=? 
										AND p.visible ORDER BY p.position limit 1", $position, $category_id);
		$this->db->query($query);
 
		return $this->get_product((integer)$this->db->result('id'));
	}
	
	/*
	*
	* Предыдущий товар
	*
	*/	
	public function get_prev_product($id)
	{
		$this->db->query("SELECT position FROM __products WHERE id=? LIMIT 1", $id);
		$position = $this->db->result('position');
		
		$this->db->query("SELECT pc.category_id FROM __products_categories pc WHERE product_id=? ORDER BY position LIMIT 1", $id);
		$category_id = $this->db->result('category_id');

		$query = $this->db->placehold("SELECT id FROM __products p, __products_categories pc
										WHERE pc.product_id=p.id AND p.position<? 
										AND pc.position=(SELECT MIN(pc2.position) FROM __products_categories pc2 WHERE pc.product_id=pc2.product_id)
										AND pc.category_id=? 
										AND p.visible ORDER BY p.position DESC limit 1", $position, $category_id);
		$this->db->query($query);
 
		return $this->get_product((integer)$this->db->result('id'));	}
	
		
}
