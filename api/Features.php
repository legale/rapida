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

class Features extends Simpla
{
	//тут будут хранится уникальные значения опций с ключами по id
	public $options_ids;

	function get_features($filter = array())
	{
		dtimer::log(__METHOD__ . ' start');
		$category_id_filter = '';
		if (isset($filter['category_id']))
			$category_id_filter = $this->db->placehold('AND id in(SELECT feature_id FROM __categories_features AS cf WHERE cf.category_id in(?@))', (array)$filter['category_id']);

		$in_filter_filter = '';
		if (isset($filter['in_filter']))
			$in_filter_filter = $this->db->placehold('AND f.in_filter=?', intval($filter['in_filter']));

		$id_filter = '';
		if (!empty($filter['id']))
			$id_filter = $this->db->placehold('AND f.id in(?@)', (array)$filter['id']);
		
		// Выбираем свойства
		$query = $this->db->placehold("SELECT id, name, trans, position, in_filter FROM __features AS f
									WHERE 1
									$category_id_filter $in_filter_filter $id_filter ORDER BY f.position");
		$this->db->query($query);
		dtimer::log(__METHOD__ . " query: '$query'");
		$res = $this->db->results_object(null, 'id');
		dtimer::log(__METHOD__ . ' return');
		return $res;
	}
	
	function get_features_trans($filter = array())
	{
		dtimer::log(__METHOD__ . ' start');
		$category_id_filter = '';
		if (isset($filter['category_id']))
			$category_id_filter = $this->db->placehold('AND id in(SELECT feature_id FROM __categories_features AS cf WHERE cf.category_id in(?@))', (array)$filter['category_id']);

		$in_filter_filter = '';
		if (isset($filter['in_filter']))
			$in_filter_filter = $this->db->placehold('AND f.in_filter=?', intval($filter['in_filter']));

		$id_filter = '';
		if (!empty($filter['id']))
			$id_filter = $this->db->placehold('AND f.id in(?@)', (array)$filter['id']);
		
		// Выбираем свойства
		$query = $this->db->placehold("SELECT id, trans FROM __features AS f
									WHERE 1
									$category_id_filter $in_filter_filter $id_filter ORDER BY f.position");
		$this->db->query($query);
		dtimer::log(__METHOD__ . " query: '$query'");
		$res = $this->db->results_array('id', 'trans');
		dtimer::log(__METHOD__ . ' return');
		return $res;
	}

	function get_feature($id)
	{
		// Выбираем свойство
		$query = $this->db->placehold("SELECT id, name, trans, position, in_filter FROM __features WHERE id=? LIMIT 1", (int)$id);
		$this->db->query($query);
		return $this->db->result();
	}

	function get_feature_categories($id)
	{
		$query = $this->db->placehold("SELECT cf.category_id as category_id FROM __categories_features cf
										WHERE cf.feature_id = ?", $id);
		$this->db->query($query);
		$res = $this->db->results_array('category_id');
		return $res;
	}
	


	 

	
	/* Добавляет свойство товара по новой системе
	 */

	public function add_feature($feature)
	{
		if (is_object($feature)) {
			$feature = (array)$feature;
		}
		//удалим id, если он сюда закрался, при создании id быть не должно
		if (isset($feature['id'])) {
			unset($feature['id']);
		}

		foreach ($feature as $k => $e) {
			if (empty_($e)) {
				unset($feature[$k]);
			} else {
				$feature[$k] = trim($e);
			}
		}
		
		//используем транслит собственного приготовления
		$feature['trans'] = translit_url($feature['name']);

		//чтобы избежать повторов поля trans, проверим на уникальность в базе
		//крутим пока не получим уникальное имя
		while ($this->db->query("SELECT trans FROM __features WHERE trans = ?", $feature['trans']) && $this->db->num_rows() !== 0) {
			if (preg_match('/(.+)([0-9]+)$/', $feature['trans'], $parts)) {
				$feature['trans'] = $parts[1] . '' . ($parts[2] + 1);
			}
			else {
				$feature['trans'] = $feature['trans'] . '2';
			}
		}

		//вытаскиваем макс позицию из свойств
		$query = "SELECT MAX(position) as pos FROM __features";

		if ($this->db->query($query) !== false) {
			//макс. позиция в таблице
			$pos = $this->db->result_array('pos');
		}
		//если что-то есть на выходе, делаем $pos = 0, иначе $pos++
		if ($pos !== null) {
			$feature['position'] = $pos + 1;
		}
		else {
			$feature['position'] = 0;
		}

		$query = $this->db->placehold("INSERT INTO __features SET ?%", $feature);
		dtimer::log(__METHOD__ . " query: $query");

		//прогоняем запрос (метод query в случае успеха выдает true)
		if ($this->db->query($query) !== true) {
			return false;
		}
		$id = $this->db->insert_id();
		
		/* 
		 * Тут часть, касающаяся таблицы со свойствами
		 */
		
		//сначала проверим, есть ли целевая таблица, создадим ее, если ее еще нет
		if (!$this->db->query("SELECT 1 FROM __options LIMIT 1")) {
			$this->db->query("CREATE TABLE __options (`product_id` INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT) ENGINE=MyISAM CHARSET=utf8");
		}
		if (!$this->db->query("SELECT 1 FROM __options_uniq LIMIT 1")) {
			$this->db->query("CREATE TABLE __options_uniq (`id` INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT, `val` VARCHAR(1024) NOT NULL, `md4` BINARY(16) UNIQUE KEY NOT NULL) ENGINE=MyISAM CHARSET=utf8");
			//$this->db->query("ALTER TABLE __options_uniq ADD INDEX `val` (`val`)");

		}
		if (!$this->db->query("SELECT `$id` FROM __options LIMIT 1")) {
			$this->db->query("ALTER TABLE __options ADD `$id` MEDIUMINT NULL");
			//делаем индекс, только если это свойство будет в фильтре
			if (isset($feature['in_filter']) && (bool)$feature['in_filter'] === true) {
				$this->db->query("ALTER TABLE __options ADD INDEX `$id` (`$id`)");
			}
		}
		return $id;
	}



	public function update_feature($id, $feature)
	{
		if (is_object($feature)) {
			$feature = (array)$feature;
		}

		if (!is_array($feature) || !is_int($id)) {
			$t1 = gettype($id);
			$t2 = gettype($feature);
			trigger_error(__METHOD__ . " argument type error $t1 $t2", E_USER_WARNING);
			return false;
		}
		foreach ($feature as $k => $e) {
			if (empty_($e)) {
				unset($feature[$k]);
			} else {
				$feature[$k] = trim($e);
			}
		}

		$this->db->query("UPDATE __features SET ?% WHERE id = ?", $feature, $id);
		if (isset($feature['in_filter']) && (bool)$feature['in_filter'] === true) {
			$this->db->query("ALTER TABLE __options ADD INDEX `$id` (`$id`)");
		}
		else {
			$this->db->query("ALTER TABLE __options DROP INDEX `$id` ");
		}
		return $id;
	}



	public function delete_feature($id = array())
	{
		if (!empty($id))
			{
			$this->db->query("DELETE FROM __features WHERE id=? LIMIT 1", intval($id));
			$this->db->query("ALTER TABLE __options DROP ?!", (int)$id);
			$this->db->query("DELETE FROM __categories_features WHERE feature_id=?", (int)$id);
		}
	}




	public function update_option($product_id, $feature_id, $value)
	{
		dtimer::log(__METHOD__ . " arguments '$product_id' '$feature_id' '$value'");
		if (!isset($product_id) || !isset($feature_id) || !isset($value)) {
			trigger_error(__METHOD__ . " arguments error 3 args needed '$product_id' '$feature_id' '$value'", E_USER_WARNING);
			return false;
		}
		
		//получим значение для записи в таблицу options из таблицы s_options_uniq
		//сделаем хеш 
		$val = trim((string)$value);
		$fid = (int)$feature_id;
		$pid = (int)$product_id;
		//Хеш будем получать не по чистому значению $val, а по translit_url($val), чтобы можно было из ЧПУ вернуться к хешу
		$optionhash = hash('md4', translit_url($val));
		$this->db->query("SELECT `id` FROM __options_uniq WHERE `md4`= 0x$optionhash ");
		
		//Если запись уже есть - продолжаем работу, если нет добавляем запись в таблицу
		if ($this->db->affected_rows() > 0) {
			$vid = $this->db->result('id');
		}
		else {
			$trans = translit_url($val);
			$this->db->query("INSERT INTO __options_uniq SET `val`= '$val', `trans` = '$trans', `md4` = 0x$optionhash ");
			$vid = $this->db->insert_id();
		}

		$query = $this->db->placehold(
			"INSERT INTO __options SET `product_id` = ? , ?! = ?
		ON DUPLICATE KEY UPDATE ?! = ?",
			$pid,
			$fid,
			$vid,
			$fid,
			$vid
		);
		if ($this->db->query($query)) {
			return $vid;
		}
		else {
			return false;
		}
	}
	
	/* 
	 * Этот метод позволяет писать свойства товаров напрямую, минуя таблицу options_uniq
	 * в которой содержатся уникальные значения свойств и их id.
	 * Тут $value должен быть сразу в виде числа с id значения из таблицы options_uniq
	 */
	public function update_option_direct($product_id, $feature_id, $value)
	{
		if (!isset($product_id) || !isset($feature_id) || !isset($value)) {
			trigger_error(__METHOD__ . " arguments error 3 args needed '$product_id' '$feature_id' '$value'", E_USER_WARNING);
			return false;
		}
		
		//тут мы пишем все сразу, специальная функция для прямой записи 
		$fid = (int)$feature_id;
		$pid = (int)$product_id;
		$vid = (int)$value;

		$query = $this->db->placehold(
			"INSERT INTO __options SET `product_id` = ? , ?! = ?
		ON DUPLICATE KEY UPDATE ?! = ?",
			$pid,
			$fid,
			$vid,
			$fid,
			$vid
		);

		if ($this->db->query($query)) {
			return $vid;
		}
		else {
			return false;
		}

	}



	public function add_feature_category($id, $category_id)
	{
		$query = $this->db->placehold("INSERT IGNORE INTO __categories_features SET feature_id=?, category_id=?", $id, $category_id);
		$this->db->query($query);
	}



	public function update_feature_categories($id, $categories)
	{
		$id = intval($id);
		$query = $this->db->placehold("DELETE FROM __categories_features WHERE feature_id=?", $id);
		$this->db->query($query);


		if (is_array($categories))
			{
			$values = array();
			foreach ($categories as $category)
				$values[] = "($id , " . intval($category) . ")";

			$query = $this->db->placehold("INSERT INTO __categories_features (feature_id, category_id) VALUES " . implode(', ', $values));
			return $this->db->query($query);
		}
		else {
			return false;
		}
	}


	public function get_options_uniq($ids = null, $reverse = false)
	{
		$res = array();
		$id_filter = '';

		if (isset($ids)) {
			$id_filter = $this->db->placehold(" AND `id` in ( ?@ )", (array)$ids);
		}
		$this->db->query("SELECT id, val, trans, md4 FROM __options_uniq WHERE 1 $id_filter");
		if ($reverse === true) {
			$res = $this->db->results_array(null, 'id', true);
		}
		else {
			$res = $this->db->results_array(null, 'val', true);
		}

		return $res;
	}


	public function get_options_ids($ids = null)
	{
		dtimer::log(__METHOD__ . " start");
		//переменные
		$id_filter = '';

		//фильтр
		if (is_null($ids)) {
			if(isset($this->options_ids)){
				dtimer::log(__METHOD__ . " end");
				return $this->options_ids;
			}
		}
		elseif (!is_null($ids) && is_array($ids) && count($ids) > 0) {
			$id_filter = $this->db->placehold("AND id in (?@)", $ids);
		}
		else {
			return false;
		}

		$this->db->query("SELECT * FROM __options_uniq WHERE 1 $id_filter");
		$res = $this->db->results_array( array('md4', 'id', 'val'), array('id', 'md4', 'id') );
		//Если у нас был запуск без параметров, сохраним результат в переменную класса.
		if(is_null($ids)){
			$this->options_ids = $res;
		}
		dtimer::log(__METHOD__ . " end");
		return $res;
	}
	
	public function get_options_md4($ids = null)
	{
		dtimer::log(__METHOD__ . " start");
		//переменные
		$id_filter = '';

		//фильтр
		if (is_null($ids)) {
			if(isset($this->options_ids)){
				dtimer::log(__METHOD__ . " end");
				return $this->options_ids;
			}
		}
		elseif (!is_null($ids) && is_array($ids) && count($ids) > 0) {
			$id_filter = $this->db->placehold("AND md4 in (?$)", $ids);
		}
		else {
			return false;
		}

		$this->db->query("SELECT id, md4 FROM __options_uniq WHERE 1 $id_filter");
		$res = $this->db->results_array( 'id', 'md4' );
		//Если у нас был запуск без параметров, сохраним результат в переменную класса.
		if(is_null($ids)){
			$this->options_ids = $res;
		}
		dtimer::log(__METHOD__ . " end");
		return $res;
	}



	public function get_options_new($filter = array())
	{
		//сначала уберем из фильтра лишние параметры, которые не влияют на результат, но влияют на хэширование
		$filter_ = $filter;
		dtimer::log(__METHOD__ . " start filter: " . var_export($filter_, true));
		unset($filter_['method'], $filter_['sort'], $filter_['page'], $filter_['limit']);
		if (isset($filter_['force_no_cache'])) {
			$force_no_cache = true;
			unset($filter_['force_no_cache']);
		}
		
		
		//сортируем фильтр, чтобы порядок данных в нем не влиял на хэш
		ksort($filter_);
		$filter_string = var_export($filter_, true);
		$keyhash = hash('md4', 'get_products' . $filter_string);

		//если запуск был не из очереди - пробуем получить из кеша
		if (!isset($force_no_cache)) {
			dtimer::log(__METHOD__ . " normal run keyhash: $keyhash");
			$res = $this->cache->get_cache_nosql($keyhash, false);
		
		
		
			//запишем в фильтр параметр force_no_cache, чтобы при записи задания в очередь
			//функция выполнялась полностью
			$filter_['force_no_cache'] = true;
			$filter_string = var_export($filter_, true);
			dtimer::log(__METHOD__ . " add task force_no_cache keyhash: $keyhash");

			$task = '$this->features->get_options(';
			$task .= $filter_string;
			$task .= ');';
			$this->queue->addtask($keyhash, isset($filter['method']) ? $filter['method'] : '', $task);
		}

		if (isset($res) && !empty_($res)) {
			dtimer::log(__METHOD__ . " return cache res count: " . count($res));
			return $res;
		}


		$product_id_filter = '';
		$category_id_filter = '';
		$visible_filter = '';
		$brand_id_filter = '';
		$features_filter = '';
		$products_join = '';
		$products_join_flag = false;
		$select = '';
		
		//так запросы к БД повисают
		//~ //если не заданы id нужных свойств, выбираем всё
		//~ if(!isset($filter['feature_id'])){
			//~ $this->db2->query("SELECT id FROM __features WHERE 1");
			//~ $filter['feature_id'] = $this->db2->results_array('id');
		//~ /* Если есть $filter['features'] - проверяем, все ли свойства, которые запрошены, есть там,
		//~ *  если чего-то, то не хватает, добавляем.
		//~ */
		//~ }

		if (isset($filter['features'])) {
			$features_ids = array_keys($filter['features']);
			//если в фильтрах свойств что-то задано, но этого нет в запрошенных фильтрах, добавляем.
			foreach ($features_ids as $fid) {
				if (!in_array($fid, $filter['feature_id'])) {
					$filter['feature_id'][] = $fid;
				}
			}
		//если у нас не заданы фильтры опций и не запрошены сами опции - останавливаем

		}
		elseif (!isset($filter['feature_id'])) {
			return false;
		}
		
		
		// если у нас нет свойств, то и не может быть их значений - останавливаем
		if (count($filter['feature_id']) == 0) {
			return false;
		}
		
		
		
		//собираем столбцы, которые нам понадобятся для select
		$select = implode(', ', array_map(function ($a) {
			return '`' . $a . '`';
		}, $filter['feature_id']));


		if (isset($filter['category_id'])) {
			$category_id_filter = $this->db2->placehold(' AND o.product_id in(SELECT DISTINCT product_id from s_products_categories where category_id in (?@))', (array)$filter['category_id']);
		}

		if (isset($filter['product_id'])) {
			$product_id_filter = $this->db2->placehold(' AND o.product_id in (?@)', (array)$filter['product_id']);
		}

		if (isset($filter['brand_id'])) {
			$products_join_flag = true;
			$brand_id_filter = $this->db2->placehold(' AND p.brand_id in (?@)', (array)$filter['brand_id']);
		}

		if (isset($filter['visible'])) {
			$products_join_flag = true;
			$visible_filter = $this->db2->placehold(' AND p.visible=?', (int)$filter['visible']);
		}       
		
		//фильтрация по свойствам товаров
		if (!empty($filter['features'])) {
			foreach ($filter['features'] as $fid => $vids) {
				if(is_array($vids)){
					$features_filter .= $this->db->placehold(" AND `$fid` in (?@)", $vids);
				}
			}
		}

		if ($products_join_flag === true) {
			$products_join = "INNER JOIN __products p on p.id = o.product_id";
		}

		$query = $this->db2->placehold("SELECT `o`.`product_id` as `pid`, $select
		    FROM __options o
		    $products_join
			WHERE 1 
			$product_id_filter 
			$brand_id_filter 
			$features_filter 
		    $visible_filter
			$category_id_filter
			");

		if (!$this->db2->query($query)) {
			trigger_error(__METHOD__ . " query error: '$query'", E_USER_WARNING);
			return false;
		}



	}
	

	public function get_options($filter = array())
	{
		//сначала уберем из фильтра лишние параметры, которые не влияют на результат, но влияют на хэширование
		$filter_ = $filter;
		dtimer::log(__METHOD__ . " start filter: " . var_export($filter_, true));
		unset($filter_['method'], $filter_['sort'], $filter_['page'], $filter_['limit']);
		if (isset($filter_['force_no_cache'])) {
			$force_no_cache = true;
			unset($filter_['force_no_cache']);
		}
		
		
		//сортируем фильтр, чтобы порядок данных в нем не влиял на хэш
		ksort($filter_);
		$filter_string = var_export($filter_, true);
		$keyhash = hash('md4', 'get_products' . $filter_string);

		//если запуск был не из очереди - пробуем получить из кеша
		if (!isset($force_no_cache)) {
			dtimer::log(__METHOD__ . " normal run keyhash: $keyhash");
			$res = $this->cache->get_cache_nosql($keyhash, false);
		
		
		
			//запишем в фильтр параметр force_no_cache, чтобы при записи задания в очередь
			//функция выполнялась полностью
			$filter_['force_no_cache'] = true;
			$filter_string = var_export($filter_, true);
			dtimer::log(__METHOD__ . " add task force_no_cache keyhash: $keyhash");

			$task = '$this->features->get_options(';
			$task .= $filter_string;
			$task .= ');';
			$this->queue->addtask($keyhash, isset($filter['method']) ? $filter['method'] : '', $task);
		}

		if (isset($res) && !empty_($res)) {
			dtimer::log(__METHOD__ . " return cache res count: " . count($res));
			return $res;
		}


		$product_id_filter = '';
		$category_id_filter = '';
		$visible_filter = '';
		$brand_id_filter = '';
		$features_filter = '';
		$products_join = '';
		$products_join_flag = false;
		$select = '';

		if (isset($filter['features'])) {
			$features_ids = array_keys($filter['features']);
			//если в фильтрах свойств что-то задано, но этого нет в запрошенных фильтрах, добавляем.
			foreach ($features_ids as $fid) {
				if (!in_array($fid, $filter['feature_id'])) {
					$filter['feature_id'][] = $fid;
				}
			}
		}
		
		//если у нас не заданы фильтры опций и не запрошены сами опции, будем брать все.
		if (!isset($filter['feature_id']) && count($filter['feature_id']) === 0 ) {
			$filter['feature_id'] = array_values( $this->features->get_features_trans() );
		}

		//собираем столбцы, которые нам понадобятся для select
		$select = "SELECT `o`.`product_id` as `pid`, " . implode(', ', array_map(function ($a) {
			return '`' . $a . '`';
		}, $filter['feature_id']));		

		if (isset($filter['category_id'])) {
			$category_id_filter = $this->db2->placehold(' AND o.product_id in(SELECT DISTINCT product_id from s_products_categories where category_id in (?@))', (array)$filter['category_id']);
		}

		if (isset($filter['product_id'])) {
			$product_id_filter = $this->db2->placehold(' AND o.product_id in (?@)', (array)$filter['product_id']);
		}

		if (isset($filter['brand_id'])) {
			$products_join_flag = true;
			$brand_id_filter = $this->db2->placehold(' AND p.brand_id in (?@)', (array)$filter['brand_id']);
		}

		if (isset($filter['visible'])) {
			$products_join_flag = true;
			$visible_filter = $this->db2->placehold(' AND p.visible=?', (int)$filter['visible']);
		}       
		
		//фильтрация по свойствам товаров
		if (!empty($filter['features'])) {
			foreach ($filter['features'] as $fid => $vids) {
				if(is_array($vids)){
					$features_filter .= $this->db->placehold(" AND `$fid` in (?@)", $vids);
				}
			}
		}

		if ($products_join_flag === true) {
			$products_join = "INNER JOIN __products p on p.id = o.product_id";
		}

		$query = $this->db2->placehold("$select
		    FROM __options o
		    $products_join
			WHERE 1 
			$product_id_filter 
			$brand_id_filter 
			$features_filter 
		    $visible_filter
			$category_id_filter
			");

		if (!$this->db2->query($query)) {
			trigger_error(__METHOD__ . " query error: $query", E_USER_WARNING);
			return false;
		}
		//вытащим значения из options_uniq
		if (!$options_uniq = $this->features->get_options_uniq(null, true)) {
			dtimer::log(__METHOD__ . " unable to get_options_unique ");
			trigger_error(__METHOD__ . " unable to get_options_unique ", E_USER_WARNING);
			return false;
		}

		//обрабатываем выборку из базы построчно
		$res = array();
		$obj = new stdClass();
		for ($i = 0; $row = $this->db2->result_array(); $i++) {


			foreach ($filter['feature_id'] as $fid) {

				if (isset($row[$fid]) && !isset($res[$fid][$row[$fid]])) {

					$res[$fid][$row[$fid]] = '';
					
					//приходится так заморачиваться для объектов, с массивом все гораздо проще
					if (!isset($obj->$fid)) {
						$obj->$fid = new stdClass();
					}
					$obj->$fid->{$i} = (object)array(
					'feature_id' => $fid, 
					'vid' => $row[$fid], 
					'trans' => $options_uniq[$row[$fid]]['trans'], 
					'value' => $options_uniq[$row[$fid]]['val']
					);
				}
			}
			//теперь соберем id товаров
			if (!isset($res['pid'][$row['pid']])) {
				$res['pid'][$row['pid']] = '';
			}
		}
		dtimer::log("set_cache_nosql key: $keyhash");
		$this->cache->set_cache_nosql($keyhash, $obj);
		dtimer::log(__METHOD__ . ' return db');
		return $obj;
	}
	

	/* 
	 * Этот метод предназначен для получения данных о свойствах напрямую из таблицы options. 
	 * Т.е. возвращает не сами значения свойств товаров, а только id этих значений.
	 */
	public function get_product_options_direct($product_id)
	{

		if (!isset($product_id)) {
			return false;
		}
		else {
			$product_id = (int)$product_id;
		}

		!$this->db->query("SELECT * FROM __options WHERE 1 AND `product_id` = ?", $product_id);
		$res = $this->db->result_array();
		if (isset($res['product_id'])) {
			unset($res['product_id']);
			return $res;
		}
		else {
			return false;
		}
	}

	public function get_product_options($product_id)
	{

		if (!isset($product_id)) {
			return false;
		}
		else {
			$product_id = (int)$product_id;
		}

		!$this->db->query("SELECT * FROM __options WHERE 1 AND `product_id` = ?", $product_id);
		$options = $this->db->result_array();
		
		//Если ничего не нашлось - возвращаем false
		if (isset($options['product_id'])) {
			unset($options['product_id']);
		}
		else {
			return false;
		}
		//выбираем значений опций из соответствующей таблицы
		if ($this->db->query("SELECT id, val FROM __options_uniq WHERE id in (?@)", $options)) {
			$vals = $this->db->results_array(null, 'id', true);
			foreach ($options as $fid => &$option) {
				if (!empty_($option)) {
					$option = array('fid' => $fid, 'vid' => $option, 'val' => $vals[$option]['val']);
				}
			}
			return $options;
		} 
		//если не получилось вернуть $options, вернем false
		return false;
	}
}
