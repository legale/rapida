<?php

/**
 * Класс моделей вариантов товаров
 */

require_once ('Simpla.php');

class Variants extends Simpla
{
	//в этой переменном у нас записано к какому типу данных относятся поля в таблице s_variants
	private $types = array(
		'id' => 'i',
		'product_id' => 'i',
		'name' => 's',
		'sku' => 's',
		'price' => 'f',
		'price1' => 'f',
		'price2' => 'f',
		'price3' => 'f',
		'stock' => 'i',
		'old_price' => 'f',
		'position' => 'i',
	);
	
	/**
	 * Функция возвращает варианты 1 конкретного товара
	 * @param	$pid
	 * @retval	array
	 */
	public function get_product_variants($pid)
	{
		dtimer::log(__METHOD__ . " start $pid");
		//проверка аргумента
		if(!is_scalar($pid)){
			dtimer::log(__METHOD__ . " pid is not a scalar value");
		}
		//$pid у нас только число
		$pid = (int)$pid;
		
		if (!$this->db->query("SELECT * FROM __variants WHERE `product_id` = $pid")){
			return false;
		}
		
		$res = $this->db->results_array(null, 'id');
		return $res;
	}
	
	/**
	 * Функция возвращает варианты товара
	 * @param	$filter
	 * @retval	array
	 */
	public function get_variants($filter = array())
	{
		$product_id_filter = '';
		$variant_id_filter = '';
		$instock_filter = '';

		if (!empty($filter['product_id']))
			$product_id_filter = $this->db->placehold('AND v.product_id in(?@)', (array)$filter['product_id']);

		if (!empty($filter['id']))
			$variant_id_filter = $this->db->placehold('AND v.id in(?@)', (array)$filter['id']);

		if (!empty($filter['in_stock']) && $filter['in_stock'])
			$instock_filter = $this->db->placehold('AND (v.stock>0 OR v.stock IS NULL)');

		if (!$product_id_filter && !$variant_id_filter)
			return array();

		$q = $this->db->placehold("SELECT *
					FROM __variants AS v
					WHERE 
					1
					$product_id_filter          
					$variant_id_filter  
					$instock_filter 
					ORDER BY v.position       
					", $this->settings->max_order_amount);

		if (!$this->db->query($q)){
			return false;
		}
		$res = $this->db->results_array(null, 'id');
		return $res;
	}


	public function get_variant($id)
	{
		if (empty($id))
			return false;

		$q = $this->db->placehold("SELECT *
					FROM __variants v WHERE v.id=?
					LIMIT 1", $id);

		if (!$this->db->query($q)){
			return false;
		}
		$res = $this->db->result_array();
		return $res;
	}

	public function update_variant($variant)
	{
		//получим varid
		if(isset($variant['id'])){
			$varid = $variant['id'];
			unset($variant['id']);
		}else{
			dtimer::log(__METHOD__." varid is not set!", 2);
			return false;
		}

		//поставим правильный тип для полей
		foreach ($variant as $k => &$e) {
			switch(@$this->types[$k]){
				case 'i':
				$e = (int)$e;
				break;
				
				case 'f':
				$e = (float)$e;
				break;
				
				case 's':
				$e = (string)$e;
				break;
			}
		}
		unset($e);
		
		$query = $this->db->placehold("UPDATE __variants SET ?% WHERE id=? LIMIT 1", $variant, $varid);
		if ($this->db->query($query)){
			return $varid;
		}else{
			return false;
		}
	}
	
	
	
	public function add_variant($variant){
		if (is_object($variant)) {
			$variant = (array)$variant;
		}
		//получим pid
		if(!isset($variant['product_id']) || empty_($variant['product_id'])){
			dtimer::log(__METHOD__." pid is not set!", 2);
			return false;
		} else {
			$pid = $variant['product_id'];
		}

		//проверим артикул, он не может быть пустым
		if(!isset($variant['sku']) || empty_($variant['sku'])){
			dtimer::log(__METHOD__." sku is not set!", 2);
			return false;
		}

		//удалим id, если он сюда закрался, при создании id быть не должно
		if (isset($variant['id'])) {
			unset($variant['id']);
		}


		//поставим правильный тип для полей
		foreach ($variant as $k => &$e) {
			switch(@$this->types[$k]){
				case 'i':
				$e = (int)$e;
				break;
				
				case 'f':
				$e = (float)$e;
				break;
				
				case 's':
				$e = (string)$e;
				break;
			}
		}
		unset($e);
		
		//узнаем позицию последнего варианта для этого товара
		$this->db->query("SELECT MAX(position) as pos FROM __variants WHERE product_id = $pid");
		$pos = $this->db->result_array('pos');
		if( !empty_($pos) ){
			$pos = $pos + 1;
		} else {
			$pos = 0;
		}
		$variant['position'] = $pos;


		$query = $this->db->placehold("INSERT INTO __variants SET ?%", $variant);
		if (!$this->db->query($query)) {
			return false;
		}
		return $this->db->insert_id();
	}

	public function delete_variant($id)
	{
		if (empty_($id)){
			dtimer::log(__METHOD__." id is empty!", 2);
			return false;
		}

		$q = $this->db->placehold("DELETE FROM __variants WHERE id = ? LIMIT 1", intval($id));
		$q1 = $this->db->placehold("UPDATE __purchases SET variant_id=0 WHERE variant_id=?", intval($id));

		if(!$this->db->query($q) 
		|| !$this->db->query($q1)
		){
			return false;
		}
		return true;
	}
}
