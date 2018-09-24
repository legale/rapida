<?php
if(defined('PHP7')) {
     eval("declare(strict_types=1);");
}

/**
 * Simpla CMS
 *
 * @copyright	2013 Denis Pikusov
 * @link		http://simplacms.ru
 * @author		Denis Pikusov
 *
 */

require_once ('Simpla.php');

class Feedback extends Simpla
{



	public function get_feedback($filter = array(), $new_on_top = false)
	{	
		// По умолчанию
		$limit = 0;
		$page = 1;
		$keyword_filter = '';

		if (isset($filter['limit']))
			$limit = max(1, intval($filter['limit']));

		if (isset($filter['page']))
			$page = max(1, intval($filter['page']));

		$sql_limit = $this->db->placehold(' LIMIT ?, ? ', ($page - 1) * $limit, $limit);

		if (!empty($filter['keyword']))
			{
			$keywords = explode(' ', $filter['keyword']);
			foreach ($keywords as $keyword)
				$keyword_filter .= $this->db->placehold('AND f.name LIKE "%' . $this->db->escape(trim($keyword)) . '%" OR f.message LIKE "%' . $this->db->escape(trim($keyword)) . '%" OR f.email LIKE "%' . $this->db->escape(trim($keyword)) . '%" ');
		}

		if ($new_on_top)
			$sort = 'DESC';
		else
			$sort = 'ASC';

		$query = $this->db->placehold("SELECT f.id, f.name, f.email, f.ip, f.message, f.date
										FROM __feedback f WHERE 1 $keyword_filter ORDER BY f.id $sort $sql_limit");

		$this->db->query($query);
		return $this->db->results_array();
	}

	public function count_feedback($filter = array())
	{
		$keyword_filter = '';

		if (!empty($filter['keyword']))
			{
			$keywords = explode(' ', $filter['keyword']);
			foreach ($keywords as $keyword)
				$keyword_filter .= $this->db->placehold('AND f.name LIKE "%' . $this->db->escape(trim($keyword)) . '%" OR f.message LIKE "%' . $this->db->escape(trim($keyword)) . '%" OR f.email LIKE "%' . $this->db->escape(trim($keyword)) . '%" ');
		}

		$query = $this->db->placehold("SELECT count(distinct f.id) as count
										FROM __feedback f WHERE 1 $keyword_filter");

		$this->db->query($query);
		return $this->db->result_array('count');

	}


	public function add_feedback($feedback)
	{
		if (is_object($feedback)) {
			$feedback = (array)$feedback;
		}
		//удалим id, если он сюда закрался, при создании id быть не должно
		if (isset($feedback['id'])) {
			unset($feedback['id']);
		}

		foreach ($feedback as $k => $e) {
			if (empty_($e)) {
				unset($feedback[$k]);
			}
		}
		$query = $this->db->placehold('INSERT INTO __feedback
		SET ?%', $feedback);

		if (!$this->db->query($query))
			return false;

		$id = $this->db->insert_id();
		return $id;
	}


	public function update_feedback($id, $feedback)
	{
		$date_query = '';
		if (isset($feedback->date))
			{
			$date = $feedback->date;
			unset($feedback->date);
			$date_query = $this->db->placehold(', date=STR_TO_DATE(?, ?)', $date, $this->settings->date_format);
		}
		$query = $this->db->placehold("UPDATE __feedback SET ?% $date_query WHERE id in(?@) LIMIT 1", $feedback, (array)$id);
		$this->db->query($query);
		return $id;
	}


	public function delete_feedback($id)
	{
		if (!empty($id))
			{
			$query = $this->db->placehold("DELETE FROM __feedback WHERE id=? LIMIT 1", intval($id));
			$this->db->query($query);
		}
	}
}
