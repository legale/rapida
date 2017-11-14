<?php

/**
 * Работа с вариантами товаров
 *
 * @copyright 	2011 Denis Pikusov
 * @link 		http://simplacms.ru
 * @author 		Denis Pikusov
 *
 */

require_once ('Simpla.php');

class Variants extends Simpla
{
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
		
		$this->db->query("SELECT * FROM __variants WHERE `product_id` = $pid");
		if($res = $this->db->results_array(null, 'position')){
			return $res;
		}else {
			return false;
		}
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

		$query = $this->db->placehold("SELECT v.id, v.product_id , v.price, NULLIF(v.compare_price, 0) as compare_price, v.sku, IFNULL(v.stock, ?) as stock, (v.stock IS NULL) as infinity, v.name, v.attachment, v.position
					FROM __variants AS v
					WHERE 
					1
					$product_id_filter          
					$variant_id_filter  
					$instock_filter 
					ORDER BY v.position       
					", $this->settings->max_order_amount);

		$this->db->query($query);
		$res = $this->db->results_array(null, 'id');
		return $res;
	}


	public function get_variant($id)
	{
		if (empty($id))
			return false;

		$query = $this->db->placehold("SELECT v.id, v.product_id , v.price, NULLIF(v.compare_price, 0) as compare_price, v.sku, IFNULL(v.stock, ?) as stock, (v.stock IS NULL) as infinity, v.name, v.attachment
					FROM __variants v WHERE v.id=?
					LIMIT 1", $this->settings->max_order_amount, $id);

		$this->db->query($query);
		$variant = $this->db->result_array();
		return $variant;
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

		foreach ($variant as $k => $e) {
			if (empty_($e)) {
				unset($variant[$k]);
			}
		}
		$query = $this->db->placehold("UPDATE __variants SET ?% WHERE id=? LIMIT 1", $variant, $varid);
		if($this->db->query($query)){
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
		if(!isset($variant['product_id'])){
			dtimer::log(__METHOD__." pid is not set!", 2);
			return false;
		}

		//удалим id, если он сюда закрался, при создании id быть не должно
		if (isset($variant['id'])) {
			unset($variant['id']);
		}

		foreach ($variant as $k => $e) {
			if (empty_($e)) {
				unset($variant[$k]);
			}
		}

		$query = $this->db->placehold("INSERT INTO __variants SET ?%", $variant);
		if ($this->db->query($query)) {
			return $this->db->insert_id();
		}
		else {
			return false;
		}
	}

	public function delete_variant($id)
	{
		if (!empty($id))
			{
			$this->delete_attachment($id);
			$query = $this->db->placehold("DELETE FROM __variants WHERE id = ? LIMIT 1", intval($id));
			$this->db->query($query);
			$this->db->query('UPDATE __purchases SET variant_id=NULL WHERE variant_id=?', intval($id));
		}
	}
}
