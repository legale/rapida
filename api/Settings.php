<?php

/**
 * Управление настройками магазина, хранящимися в базе данных
 * В отличие от класса Config оперирует настройками доступными админу и хранящимися в базе данных.
 *
 */

require_once ('Simpla.php');

class Settings extends Simpla
{
	private $vars = array();

	function __construct()
	{
		parent::__construct($force_load = false);
	    dtimer::log(__METHOD__ . " start ");
		// Выбираем из базы
        if(function_exists('apcu_fetch') && apcu_exists($this->config->host . __CLASS__)) {
            $this->vars = apcu_fetch($this->config->host . 'all_categories');
        } else{
            $this->db->query('SELECT name, value FROM __settings');
            foreach($this->db->results_array() as $row){
                $this->vars[$row["name"]] = $row["value"];
            }
            if(function_exists('apcu_fetch')) apcu_store($this->config->host . __CLASS__, $this->vars);
        }

	}

	public function __get($name)
	{
		if ($res = parent::__get($name))
			return $res;

		if (isset($this->vars[$name]))
			return $this->vars[$name];
		else
			return null;
	}

	public function __set($name, $value)
	{
		$this->vars[$name] = $value;

		if (is_array($value))
			$value = serialize($value);
		else
			$value = (string)$value;

		$this->db->query('SELECT count(*) as count FROM __settings WHERE name=?', $name);
		if ($this->db->result_array('count') > 0)
			$this->db->query('UPDATE __settings SET value=? WHERE name=?', $value, $name);
		else
			$this->db->query('INSERT INTO __settings SET value=?, name=?', $value, $name);
	}
}
