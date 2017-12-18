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


class Money extends Simpla
{
	private $currencies = array();
	private $currency;

	public function __construct()
	{
		parent::__construct();

		if (isset($this->settings->price_decimals_point))
			$this->decimals_point = $this->settings->price_decimals_point;

		if (isset($this->settings->price_thousands_separator))
			$this->thousands_separator = $this->settings->price_thousands_separator;

		$this->design->smarty->registerPlugin('modifier', 'convert', array($this, 'convert'));

		$this->init_currencies();
	}

	private function init_currencies()
	{
		$this->currencies = array();
		// Выбираем из базы валюты
		$q = "SELECT * FROM __currencies ORDER BY `pos` ASC";
		$this->db->query($q);

		if($this->currencies = $this->db->results_array(null, 'id')){
			$this->currency = reset($this->currencies);
			return true;
		} else {
			return false;
		}

	}


	public function get_currencies($filter = array())
	{
		$currencies = array();
		if(is_array($this->currencies)){
		foreach ($this->currencies as $id => $currency){
			if ( (isset($filter['enabled']) && $filter['enabled'] == 1 && $currency['enabled']) || empty($filter['enabled']))
				$currencies[$id] = $currency;
			}
		}
		return $currencies;
	}

	public function get_currency($id = null)
	{
		if (!empty($id) && is_integer($id) && isset($this->currencies[$id])){
			return $this->currencies[$id];
		}
		if (!empty($id) && is_string($id) && is_array($this->currencies)){
			foreach ($this->currencies as $currency){
				if ($currency['code'] == $id){
					return $currency;
				}
			}
		} else {
			return false;
		}
	}


	public function add_currency($currency)
	{
		dtimer::log(__METHOD__. " start");
		if (is_object($currency)) {
			$currency = (array)$currency;
		}
		//удалим id, если он сюда закрался, при создании id быть не должно
		if (isset($currency['id'])) {
			unset($currency['id']);
		}
		foreach ($currency as $k => $e) {
			if (empty_($e)) {
				unset($currency[$k]);
			}
		}
		
		//get max position
		$this->db->query("SELECT MAX(`pos`) as pos FROM __currencies");
		$pos = $this->db->result_array('pos');
		if( !empty_($pos) ){
			$currency['pos'] = $pos + 1;
		} else {
			$currency['pos'] = 0;
		}
		
		
		$q = $this->db->placehold("INSERT INTO __currencies SET ?%", $currency);

		if (!$this->db->query($q))
			return false;

		$id = $this->db->insert_id();
		$this->init_currencies();

		return $id;
	}

	public function update_currency($id, $currency)
	{
		dtimer::log(__METHOD__. " start");
		$query = $this->db->placehold("UPDATE __currencies SET ?% WHERE id in (?@)", $currency, (array)$id);
		if (!$this->db->query($query))
			return false;

		$this->init_currencies();
		return $id;
	}

	public function delete_currency($id)
	{
		if (!empty($id))
			{
			$query = $this->db->placehold("DELETE FROM __currencies WHERE id=? LIMIT 1", intval($id));
			$this->db->query($query);
		}
		$this->init_currencies();
	}


	public function convert($price, $currency_id = null, $format = true)
	{
		if (isset($currency_id))
			{
			if (is_numeric($currency_id))
				$currency = $this->get_currency((integer)$currency_id);
			else
				$currency = $this->get_currency((string)$currency_id);
		}
		elseif (isset($_SESSION['currency_id']))
			$currency = $this->get_currency($_SESSION['currency_id']);
		else
			$currency = current($this->get_currencies(array('enabled' => 1)));

		$result = $price;

		if (!empty($currency))
			{		
			// Умножим на курс валюты
			$result = $result *  $currency['rate'];
			
			// Точность отображения, знаков после запятой
			$precision = isset($currency['cents']) ? $currency['cents'] : 2;
		}
		
		// Форматирование цены
		if ($format)
			$result = number_format($result, $precision, $this->settings->decimals_point, $this->settings->thousands_separator);
		else
			$result = round($result, $precision);

		return $result;
	}


}
