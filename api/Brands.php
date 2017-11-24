<?php

/**
 * Simpla CMS
 *
 * @copyright	2011 Denis Pikusov
 * @link		http://simplacms.ru
 * @author		Denis Pikusov
 *
 */

require_once ('Simpla.php');

class Brands extends Simpla
{
	public $brands;

	/*
	 *
	 * Функция возвращает массив названий брендов с ключами в виде id этих брендов
	 * @param $ids array
	 *
	 */
	public function get_brands_ids( $filter = array() )
	{
		dtimer::log(__METHOD__ . " start");
		//это вариант по умолчанию id=>name
		$col = isset($filter['return']['col']) ? $filter['return']['col'] : 'name';
		$key = isset($filter['return']['key']) ? $filter['return']['key'] : 'id';
		

		$id_filter = '';


		//фильтр

		if ( empty_(@$filter['id']) ) {
			if(isset($this->brands[ $key ."_". $col ])){
				dtimer::log(__METHOD__ . " end");
				return $this->brands[ $key ."_". $col ];
			}
		}
		elseif ( !empty($ids) && count($ids) > 0) {
			$id_filter = $this->db->placehold("AND id in (?@)", $ids);
		}
		else {
			return false;
		}

		$q = $this->db->query("SELECT id, name, url FROM __brands WHERE 1 $id_filter");
		
		
		$res = $this->db->results_array($col , $key  );
		//Если у нас был запуск без параметров, сохраним результат в переменную класса.
		if( empty_(@$filter[' id']) ){
			$this->brands[ $key ."_". $col ] = $res;
		}
		dtimer::log(__METHOD__ . " end");
		return $res;
	}

	/*
	 *
	 * Функция возвращает массив брендов, удовлетворяющих фильтру
	 * @param $filter
	 *
	 */
	public function get_brands($filter = array())
	{
		dtimer::log(__METHOD__ . ' start');
		//сначала уберем из фильтра лишние параметры, которые не влияют на результат, но влияют на хэширование
		$filter_ = $filter;
		dtimer::log(__METHOD__ . " start filter: " . var_export($filter_, true));
		unset($filter_['method']);
		if (isset($filter_['force_no_cache'])) {
			$force_no_cache = $filter_['force_no_cache'];
			unset($filter_['force_no_cache']);
		}
		
		
		//сортируем фильтр, чтобы порядок данных в нем не влиял на хэш
		ksort($filter_);
		$filter_string = var_export($filter_, true);
		$keyhash = hash('md4', 'get_brands' . $filter_string);

		//если запуск был не из очереди - пробуем получить из кеша
		if (!isset($force_no_cache)) {
			dtimer::log(__METHOD__ . " normal run keyhash: $keyhash");
			$res = $this->cache->get_cache_nosql($keyhash);

		
		
			//запишем в фильтр параметр force_no_cache, чтобы при записи задания в очередь
			//функция выполнялась полностью
			$filter_['force_no_cache'] = true;
			$filter_string = var_export($filter_, true);
			dtimer::log(__METHOD__ . " force_no_cache keyhash: $keyhash");

			$task = '$this->brands->get_brands(';
			$task .= $filter_string;
			$task .= ');';
			$this->queue->addtask($keyhash, isset($filter['method']) ? $filter['method'] : '', $task);
		}

		if (isset($res) && !empty_($res)) {
			dtimer::log(__METHOD__ . " return cache res count: " . count($res));
			return $res;
		}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		$category_id_filter = '';
		$visible_filter = '';
		$in_stock_filter = '';
		$where = '';
		$where_flag = false;
		
		if (isset($filter['in_stock'])){
			$in_stock_filter ='';
		}
		if (isset($filter['visible'])){
			$visible_filter = $this->db->placehold("AND p.visible=?", intval($filter['visible']));
			$where_flag = true;
		}
		if (!empty($filter['category_id'])){
			$category_id_filter = $this->db->placehold("AND p.id in (SELECT product_id FROM __products_categories WHERE category_id in (?@) )", (array)$filter['category_id']);
			$where_flag = true;
		}
		
		if($where_flag === true){
			$where = "AND b.id in (SELECT brand_id FROM __products p WHERE 1 $visible_filter $category_id_filter)";
		}
		// Выбираем все бренды
		$query = $this->db->placehold("SELECT b.id, b.name, b.url, b.meta_title,
		 b.meta_keywords, b.meta_description, b.description, b.image
								 		FROM __brands b WHERE 1 $where ");
		$this->db->query($query);
		
		$res = $this->db->results_array(null, 'id');
		dtimer::log(__METHOD__ . " set_cache_nosql key: $keyhash");
		$this->cache->set_cache_nosql($keyhash, $res);
		dtimer::log(__METHOD__ . " end");
		return $res;
	}
	

	/*
	 *
	 * Функция возвращает бренд по его id или url
	 * (в зависимости от типа аргумента, int - id, string - url)
	 * @param $id id или url поста
	 *
	 */
	public function get_brand($id)
	{
		if (is_int($id)) {
			$id = "b.id = '$id'";
		}
		elseif (is_string($id)) {
			$id = translit_url($id);
			$id = "b.url = '$id'";
		}
		else {
			dtimer::log(__METHOD__ . " argument url/id is not set or wrong type! ", 1);
			return false;
		}

		$query = "SELECT b.id, b.name, b.url, b.meta_title, b.meta_keywords, 
		b.meta_description, b.description, b.image
		FROM __brands b WHERE $id LIMIT 1";
		$this->db->query($query);
		return $this->db->result_array();
	}

	/*
	 *
	 * Добавление бренда
	 * @param $brand
	 *
	 */
	public function add_brand($brand)
	{
		dtimer::log(__METHOD__ . " start");
		if (is_object($brand)) {
			$brand = (array)$brand;
		}
		//удалим id, если он сюда закрался, при создании id быть не должно
		if (isset($brand['id'])) {
			unset($brand['id']);
		}

		foreach ($brand as $k => $e) {
			if (empty_($e)) {
				unset($brand[$k]);
			}
		}

		if (!isset($brand['url']) || empty_($brand['url'])) {
			$brand['url'] = translit_url($brand['name']);
		}


		$this->db->query("INSERT INTO __brands SET ?%", $brand);
		if( ( $res = $this->db->insert_id() ) !== false ){
			dtimer::log(__METHOD__ . " end \$res: '$res'");
		}else{
			dtimer::log(__METHOD__ . " unable to add brand", 1);
		}
		
		return $res;
	}

	/*
	 *
	 * Обновление бренда(ов)
	 * @param $brand
	 *
	 */
	public function update_brand($id, $brand)
	{
		$query = $this->db->placehold("UPDATE __brands SET ?% WHERE id=? LIMIT 1", $brand, intval($id));
		$this->db->query($query);
		return $id;
	}
	
	/*
	 *
	 * Удаление бренда
	 * @param $id
	 *
	 */
	public function delete_brand($id)
	{
		if (!empty($id))
			{
			$this->delete_image($id);
			$query = $this->db->placehold("DELETE FROM __brands WHERE id=? LIMIT 1", $id);
			$this->db->query($query);
			$query = $this->db->placehold("UPDATE __products SET brand_id=NULL WHERE brand_id=?", $id);
			$this->db->query($query);
		}
	}
	
	/*
	 *
	 * Удаление изображения бренда
	 * @param $id
	 *
	 */
	public function delete_image($brand_id)
	{
		$query = $this->db->placehold("SELECT image FROM __brands WHERE id=?", intval($brand_id));
		$this->db->query($query);
		$filename = $this->db->result('image');
		if (!empty($filename))
			{
			$query = $this->db->placehold("UPDATE __brands SET image=NULL WHERE id=?", $brand_id);
			$this->db->query($query);
			$query = $this->db->placehold("SELECT count(*) as count FROM __brands WHERE image=? LIMIT 1", $filename);
			$this->db->query($query);
			$count = $this->db->result('count');
			if ($count == 0)
				{
				@unlink($this->config->root_dir . $this->config->brands_images_dir . $filename);
			}
		}
	}

}
