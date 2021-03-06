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

class Delivery extends Simpla
{

	public function get_delivery($id)
	{

		$query = $this->db->placehold("SELECT id, name, description, free_from, price, enabled, pos, separate_payment FROM __delivery WHERE id=? LIMIT 1", intval($id));

		$this->db->query($query);
		return $this->db->result_array();
	}

	public function get_deliveries($filter = array())
	{	
		// По умолчанию
		$enabled_filter = '';

		if (!empty($filter['enabled'])) {
			$enabled_filter = $this->db->placehold('AND enabled=?', intval($filter['enabled']));
		}
		$query = "SELECT id, name, description, free_from, price, enabled, pos, separate_payment
					FROM __delivery WHERE 1 $enabled_filter ORDER BY pos";

		$this->db->query($query);

		return $this->db->results_array(null, 'id');
	}

	public function update_delivery($id, $delivery)
	{
		$query = $this->db->placehold("UPDATE __delivery SET ?% WHERE id in(?@)", $delivery, (array)$id);
		$this->db->query($query);
		return $id;
	}

	public function add_delivery($delivery)
	{
		if (is_object($delivery)) {
			$delivery = (array)$delivery;
		}
		//удалим id, если он сюда закрался, при создании id быть не должно
		if (isset($delivery['id'])) {
			unset($delivery['id']);
		}

		foreach ($delivery as $k => $e) {
			if (empty_($e)) {
				unset($delivery[$k]);
			}
		}

		$query = $this->db->placehold('INSERT INTO __delivery SET ?%', $delivery);

		if (!$this->db->query($query))
			return false;

		$id = $this->db->insert_id();
		$this->db->query("UPDATE __delivery SET pos=id WHERE id=?", intval($id));
		return $id;
	}

	public function delete_delivery($id)
	{
		// Удаляем связь доставки с методоми оплаты
		$query = $this->db->placehold("SELECT payment_method_id FROM __delivery_payment WHERE delivery_id=?", intval($id));
		$this->db->query($query);

		if (!empty($id))
			{
			$query = $this->db->placehold("DELETE FROM __delivery WHERE id=? LIMIT 1", intval($id));
			$this->db->query($query);
		}
	}


	public function get_delivery_payments($id)
	{
		$query = $this->db->placehold("SELECT payment_method_id FROM __delivery_payment WHERE delivery_id=?", intval($id));
		$this->db->query($query);
		$res = $this->db->results_array('payment_method_id');
		return $res;
	}

	public function update_delivery_payments($id, $payment_methods_ids)
	{
		$query = $this->db->placehold("DELETE FROM __delivery_payment WHERE delivery_id=?", intval($id));
		$this->db->query($query);
		if (is_array($payment_methods_ids))
			foreach ($payment_methods_ids as $p_id)
			$this->db->query("INSERT INTO __delivery_payment SET delivery_id=?, payment_method_id=?", $id, $p_id);
	}

}
