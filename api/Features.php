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
	//тут будут хранится уникальные значений имен свойств и id
	public $features_ids;

	function get_features_ids()
	{
		dtimer::log(__METHOD__ . ' start');
		if(isset($this->features_ids)){
			dtimer::log(__METHOD__ . ' return class var');
			return $this->features_ids;
		}
		
		// Выбираем свойства
		$q = $this->db->placehold("SELECT id, name, trans FROM __features");
		if(!$this->db->query($q)){
			dtimer::log(__METHOD__ . ' query error', 1);
			return false;
		}
		$this->features_ids = $this->db->results_array(array('id','name', 'trans'), array('name','id', 'id'));
		dtimer::log(__METHOD__ . ' return');
		return $this->features_ids;
	}
	
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
		$res = $this->db->results_array(null, 'id');
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
		return $this->db->result_array();
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
			dtimer::log(__METHOD__ . " argument type error $t1 $t2", 1);
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
			$this->db->query("ALTER TABLE __options ADD INDEX `$id` (`$id`, `product_id`)");
		}
		else {
			$this->db->query("ALTER TABLE __options DROP INDEX `$id` ");
		}
		return $id;
	}



	public function delete_feature($id)
	{
		$this->db->query("DELETE FROM __features WHERE id=? LIMIT 1", intval($id));
		$this->db->query("ALTER TABLE __options DROP ?!", (int)$id);
		$this->db->query("DELETE FROM __categories_features WHERE feature_id=?", (int)$id);
	}
	
	public function delete_options($id)
	{
		$this->db->query("DELETE FROM __options WHERE product_id=?", (int)$id);
	}


	public function update_option($product_id, $feature_id, $value)
	{
		dtimer::log(__METHOD__ . " arguments '$product_id' '$feature_id' '$value'");
		if (!isset($product_id) || !isset($feature_id) || !isset($value)) {
			dtimer::log(__METHOD__ . " arguments error 3 args needed '$product_id' '$feature_id' '$value'", 1);
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
			dtimer::log(__METHOD__ . " arguments error 3 args needed '$product_id' '$feature_id' '$value'", 1);
			return false;
		}

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
		}else {
			return false;
		}

	}
	
	/* 
	 * Этот метод сделан для быстрого импорта в таблицу опций, за 1 запрос добавляются 
	 * сразу несколько значений
	 */
	public function update_options_direct($filter)
	{
		dtimer::log(__METHOD__ . ' start');

		if(!isset($filter['product_id'])){
			dtimer::log(__METHOD__. ' args error - pid', 1);
			return false;
		}else{
			$pid = (int)$filter['product_id'];
		}
		
		if(!isset($filter['features']) || !is_array($filter['features'])){
			dtimer::log(__METHOD__. ' args error - features', 1);
			return false;
		}else{
			$features = $filter['features'];
		}
		
		$set_options = $this->db->placehold("?%", $features);
		$q = $this->db->placehold("INSERT INTO __options 
		SET `product_id` = ? , $set_options ON DUPLICATE KEY UPDATE $set_options", $pid);

		
		if ($this->db->query($q)) {
			dtimer::log(__METHOD__ . ' end ok');
			return true;
		}else {
			dtimer::log(__METHOD__ . ' end error', 1);
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


	public function get_options_uniq($filter = array() )
	{
		
		//сначала уберем из фильтра лишние параметры, которые не влияют на результат, но влияют на хэширование
		$filter_ = $filter;
		dtimer::log(__METHOD__ . " start filter: " . var_export($filter_, true));
		unset($filter_['method']);
		if (isset($filter_['force_no_cache'])) {
			$force_no_cache = true;
			unset($filter_['force_no_cache']);
		}
		
		
		//сортируем фильтр, чтобы порядок данных в нем не влиял на хэш
		ksort($filter_);
		$filter_string = var_export($filter_, true);
		$keyhash = hash('md4', 'get_options_uniq' . $filter_string);

		//если запуск был не из очереди - пробуем получить из кеша
		if (!isset($force_no_cache)) {
			dtimer::log(__METHOD__ . " normal run keyhash: $keyhash");
			$res = $this->cache->get_cache_nosql($keyhash, true);
		
		
		
			//запишем в фильтр параметр force_no_cache, чтобы при записи задания в очередь
			//функция выполнялась полностью
			$filter_['force_no_cache'] = true;
			$filter_string = var_export($filter_, true);
			dtimer::log(__METHOD__ . " add task force_no_cache keyhash: $keyhash");

			$task = '$this->features->get_options_uniq(';
			$task .= $filter_string;
			$task .= ');';
			$this->queue->addtask($keyhash, isset($filter['method']) ? $filter['method'] : '', $task);
		}

		if (isset($res) && !empty_($res)) {
			dtimer::log(__METHOD__ . " return cache res count: " . count($res));
			return $res;
		}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		$ids = null;
		$reverse = false; 
		$res = array(); 
		$id_filter = '';
		
		if(isset($filter['ids'])){
			$id_filter = $this->db->placehold(" AND `id` in ( ?@ )", (array)$filter['ids']);
		}
		
		$this->db->query("SELECT id, val, trans, md4 FROM __options_uniq WHERE 1 $id_filter");
		if ($reverse === true) {
			$res = $this->db->results_array(null, 'id', true);
		}
		else {
			$res = $this->db->results_array(null, 'val', true);
		}

		dtimer::log("set_cache_nosql key: $keyhash");
		$this->cache->set_cache_nosql($keyhash, $res);
		dtimer::log(__METHOD__ . ' return db');
		return $res;
	}


	public function get_options_ids($filter = array())
	{
		dtimer::log(__METHOD__ . " start");
		dtimer::log(__METHOD__ . " filter: ". var_export($filter, true));
		
		//фильтр
		if (!isset($filter['ids'])) {
			$ids = null;
			if(isset($this->options_ids)){
				dtimer::log(__METHOD__ . " using saved class variable");
				return $this->options_ids;
			}
		}


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
		$keyhash = hash('md4', 'get_options_ids' . $filter_string);

		//если запуск был не из очереди - пробуем получить из кеша
		if (!isset($force_no_cache)) {
			dtimer::log(__METHOD__ . " normal run keyhash: $keyhash");
			$res = $this->cache->get_cache_nosql($keyhash, true, false);

			//Если у нас был запуск без параметров, сохраним результат в переменную класса.
			if(is_null($ids)){
				$this->options_ids = $res;
			}		
		
		
			//запишем в фильтр параметр force_no_cache, чтобы при записи задания в очередь
			//функция выполнялась полностью
			$filter_['force_no_cache'] = true;
			$filter_string = var_export($filter_, true);
			dtimer::log(__METHOD__ . " force_no_cache keyhash: $keyhash");

			$task = '$this->features->get_options_ids(';
			$task .= $filter_string;
			$task .= ');';
			$this->queue->addtask($keyhash, isset($filter['method']) ? $filter['method'] : '', $task);
		}

		if (isset($res) && !empty_($res)) {
			dtimer::log(__METHOD__ . " return cache res count: " . count($res));
			return $res;
		}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


		//переменные
		$id_filter = '';


		if (!is_null($ids) && is_array($ids) && count($ids) > 0) {
			$id_filter = $this->db->placehold("AND id in (?@)", $ids);
		}

		$this->db->query("SELECT id, val, trans, HEX(md4) as md4 FROM __options_uniq WHERE 1 $id_filter");
		$res = $this->db->results_array( array('md4', 'id', 'val', 'trans'), array('id', 'md4', 'id', 'id') );
		//Если у нас был запуск без параметров, сохраним результат в переменную класса.
		if(is_null($ids)){
			dtimer::log(__METHOD__ . " save res to class variable");
			$this->options_ids = $res;
		}
		dtimer::log(__METHOD__ . " set_cache_nosql key: $keyhash");
		$this->cache->set_cache_nosql($keyhash, $res, false);
		dtimer::log(__METHOD__ . ' return db');
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


	/*
	 * Этот метод предоставляет комбинированные данные опций, в т.ч. все возможные опции без учета уже выбранных,
	 * доступные для выбора опции с учетом уже выбранных. Т.е. если выбрана страна, например, Россия, другие 
	 * страны будут также доступны для выбора. 
	 */
	public function get_options_mix($filter = array())
	{
		dtimer::log(__METHOD__ . " start");
		$res = array();
		
		//Самый простой вариант - если не заданы фильтры по свойствам
		if(!isset($filter['features'])){
			
			//2 массив со значениями
			$vals = $this->get_options_ids()[2];
			//3 массив с транслитом
			$trans = $this->get_options_ids()[3];
			if($res['filter'] = $this->get_options_raw($filter)){
				foreach($res['filter'] as $fid=>$ids){
					$res['full'][$fid] = array(
					'vals' => array_intersect_key($vals, $res['filter'][$fid]),
					'trans' => array_intersect_key($trans, $res['filter'][$fid])
					);
				}
			} else {
				return false;
			} 
		} else 
		{
			/*
			 * Это фильтрованные результаты. Логика:
			 * делается выборка для каждого свойства, исключая заданные опции по этому свойству
			 */
			 
			//это результат со всеми заданными $fid
			$filter_ = $filter;
			$res['filter'] = $this->get_options_raw($filter_);

			//тут получим полные результаты для отдельных $fid
			foreach($filter['features'] as $fid=>$vid){
				//копируем фильтр
				$filter_ = $filter;
				//оставляем только нужный нам $fid
				$filter_['feature_id'] = array($fid);
				//убираем из массива заданных фильтров искомый $fid
				unset($filter_['features'][$fid]);
				
				$raw = $this->get_options_raw($filter_);
				$res['filter'][$fid] = $raw[$fid];
			}
			
			//это полный результат, поэтому убираем все фильтры 
			$filter_ = $filter;
			unset($filter_['features']);
			$res['full'] = $this->get_options_raw($filter_);
			//2 массив со значениями
			$vals = $this->get_options_ids()[2];
			//3 массив с транслитом
			$trans = $this->get_options_ids()[3];
			foreach($res['full'] as $fid=>$ids){
				$res['full'][$fid] = array(
				'vals' => array_intersect_key($vals, $res['full'][$fid]),
				'trans' => array_intersect_key($trans, $res['full'][$fid])
				);
			} 
		}
		dtimer::log(__METHOD__ . " end");
		return $res;
	}

	/*
	 * Этим методом можно получить необработанные данные из таблицы s_options
	 * Используется для получения входных данных для метода get_options_mix()
	 */
	 
	public function get_options_raw($filter = array())
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
		$keyhash = hash('md4', 'get_options_raw' . $filter_string);

		//если запуск был не из очереди - пробуем получить из кеша
		if (!isset($force_no_cache)) {
			dtimer::log(__METHOD__ . " normal run keyhash: $keyhash");
			$res = $this->cache->get_cache_nosql($keyhash, true);
		
		
		
			//запишем в фильтр параметр force_no_cache, чтобы при записи задания в очередь
			//функция выполнялась полностью
			$filter_['force_no_cache'] = true;
			$filter_string = var_export($filter_, true);
			dtimer::log(__METHOD__ . " add task force_no_cache keyhash: $keyhash");

			$task = '$this->features->get_options_raw(';
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
		$res = array();
		
		//если у нас не заданы фильтры опций и не запрошены сами опции, будем брать все.
		if (!isset($filter['feature_id']) || count($filter['feature_id']) === 0 ) {
			if($f = $this->features->get_features_trans(array('in_filter'=>1))){
				$filter['feature_id'] = array_values( $f );
			} else {
				//если у нас нет свойств в фильтре, значит и выбирать нечего
				return false;
			}
		}

		if (isset($filter['features'])) {
			$features_ids = array_keys($filter['features']);
			//если в фильтрах свойств что-то задано, но этого нет в запрошенных фильтрах, добавляем.
			foreach ($features_ids as $fid) {
				if (!in_array($fid, $filter['feature_id'])) {
					$filter['feature_id'][] = $fid;
				}
			}
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
			dtimer::log(__METHOD__ . " query error: $query", 1);
			return false;
		}
		
		
		//вывод обрабатываем построчно
		while( $row = $this->db2->result_array(null, 'pid', true) ){
			//~ $res['pid'][] = $row['pid'];
			//~ unset($row['pid']);
			
			foreach($row as $fid=>$vid){
				if($vid !== null && !isset($res[$fid][$vid])){
					$res[$fid][$vid] = '';
				}
			}
		}


		dtimer::log("set_cache_nosql key: $keyhash");
		$this->cache->set_cache_nosql($keyhash, $res);
		dtimer::log(__METHOD__ . ' return db');
		return $res;
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
