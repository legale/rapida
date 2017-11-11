<?php

/**
 * Simpla CMS
 *
 * Класс по управлению пользователями
 *
 */

require_once ('Simpla.php');

class Users extends Simpla
{	

	// Тут массив с перечнем разрешений, ключи этого массива хранятся в БД в таблице
	// s_users
	public $perm_list = array(
		0=> 'products', 
		1=> 'categories', 
		2=> 'brands', 
		3=> 'features', 
		4=> 'orders', 
		5=> 'labels',
		6=> 'users', 
		7=> 'groups', 
		8=> 'coupons', 
		9=> 'pages', 
		10=> 'blog', 
		11=> 'comments', 
		12=> 'feedbacks', 
		13=> 'import', 
		14=> 'export',
		15=> 'backup', 
		16=> 'stats', 
		17=> 'design', 
		18=> 'settings', 
		19=> 'currency', 
		20=> 'delivery', 
		21=> 'payment', 
		22=> 'managers', 
	);
	

	function get_users($filter = array())
	{
		$limit = 1000;
		$page = 1;
		$group_id_filter = '';
		$keyword_filter = '';
		$admin_filter = '';

		if (isset($filter['admin'])){
			$admin_filter = $this->db->placehold("AND u.admin=?", (int)$filter['admin']);
		}
		
		if (isset($filter['limit']))
			$limit = max(1, intval($filter['limit']));

		if (isset($filter['page']))
			$page = max(1, intval($filter['page']));

		if (isset($filter['group_id']))
			$group_id_filter = $this->db->placehold('AND u.group_id in(?@)', (array)$filter['group_id']);

		if (isset($filter['keyword']))
			{
			$keywords = explode(' ', $filter['keyword']);
			foreach ($keywords as $keyword)
				$keyword_filter .= $this->db->placehold('AND (u.name LIKE "%' . $this->db->escape(trim($keyword)) . '%" OR u.email LIKE "%' . $this->db->escape(trim($keyword)) . '%"  OR u.last_ip LIKE "%' . $this->db->escape(trim($keyword)) . '%")');
		}

		$order = 'u.name';
		if (!empty($filter['sort']))
			switch ($filter['sort'])
			{
			case 'date' :
				$order = 'u.created DESC';
				break;
			case 'name' :
				$order = 'u.name';
				break;
		}


		$sql_limit = $this->db->placehold(' LIMIT ?, ? ', ($page - 1) * $limit, $limit);
		// Выбираем пользователей
		$query = $this->db->placehold("SELECT u.*, g.discount, g.name as group_name FROM __users u
		                                LEFT JOIN __groups g ON u.group_id=g.id 
										WHERE 1 $group_id_filter $keyword_filter $admin_filter ORDER BY $order $sql_limit");
		$this->db->query($query);
		return $this->db->results_array(null, 'id');
	}

	function count_users($filter = array())
	{
		$group_id_filter = '';
		$keyword_filter = '';

		if (isset($filter['group_id']))
			$group_id_filter = $this->db->placehold('AND u.group_id in(?@)', (array)$filter['group_id']);

		if (isset($filter['keyword']))
			{
			$keywords = explode(' ', $filter['keyword']);
			foreach ($keywords as $keyword)
				$keyword_filter .= $this->db->placehold('AND u.name LIKE "%' . $this->db->escape(trim($keyword)) . '%" OR u.email LIKE "%' . $this->db->escape(trim($keyword)) . '%"');
		}

		// Выбираем пользователей
		$query = $this->db->placehold("SELECT count(*) as count FROM __users u
		                                LEFT JOIN __groups g ON u.group_id=g.id 
										WHERE 1 $group_id_filter $keyword_filter");
		$this->db->query($query);
		return $this->db->result_array('count');
	}

	function get_user($id)
	{
		dtimer::log(__METHOD__ . " start");
		if (gettype($id) == 'string'){
			$where = $this->db->placehold(' WHERE u.email=? ', $id);
		}else{
			$where = $this->db->placehold(' WHERE u.id=? ', intval($id));
		}
	
		// Выбираем пользователя
		$q = $this->db->placehold("SELECT u.*, g.discount, g.name as group_name FROM __users u LEFT JOIN __groups g ON u.group_id=g.id $where LIMIT 1", $id);
		if( $this->db->query($q) ){
			$user = $this->db->result_array();
		} else {
			return false;
		}
		
		if (empty($user)){
			return false;
		}
		$user['discount'] *= 1; // Убираем лишние нули, чтобы было 5 вместо 5.00
		$user['perm'] = explode(':', $user['perm']); 
		$user['perm'] = array_combine( $user['perm'], $user['perm'] );
		dtimer::log(__METHOD__ . " end");
		return $user;
	}

	public function add_user($user)
	{
		if (is_object($user)) {
			$user = (array)$user;
		}
		//удалим id, если он сюда закрался, при создании id быть не должно
		if (isset($user['id'])) {
			unset($user['id']);
		}

		foreach ($user as $k => $e) {
			if (empty_($e)) {
				unset($user[$k]);
			}
		}
		if (isset($user['password']))
			$user['password'] = md5($this->config->salt . $user['password'] . md5($user['password']));

		$query = $this->db->placehold("SELECT count(*) as count FROM __users WHERE email=?", $user['email']);
		$this->db->query($query);

		if ($this->db->result_array('count') > 0)
			return false;

		$query = $this->db->placehold("INSERT INTO __users SET ?%", $user);
		$this->db->query($query);
		return $this->db->insert_id();
	}

	public function update_user($user)
	{
		dtimer::log(__METHOD__ . " start");
		if( isset($user['id']) ){
			$id = $user['id'];
			unset($user['id']);
		}else{
			dtimer::log(__METHOD__ . " args error", 1);
			return false;
		}
		//прошерстим пустышки
		foreach ($user as $k => $e) {
			if (empty_($e)) {
				unset($user[$k]);
			}
		}
		$last_login = '';
		if(isset($user['last_login']) && $user['last_login'] === 'CURRENT_TIMESTAMP()'){
			$last_login = "`last_login` = CURRENT_TIMESTAMP(),";
			unset($user['last_login']);
		}
		
		 
		//сформируем строку с разрешениями, если что-то есть
		if(isset($user['perm']) && is_array($user['perm']) ){
			$user['perm'] = array_intersect_key( $this->perm_list, $user['perm'] );
			$user['perm'] = implode( ':', array_flip($user['perm']) );
		}
		
		if (isset($user['password'])){
			$user['password'] = md5($this->config->salt . $user['password'] . md5($user['password']));
		}
		$query = $this->db->placehold("UPDATE __users SET $last_login ?% WHERE id=? LIMIT 1", $user, intval($id));
		

		dtimer::log(__METHOD__ . " end");
		if($this->db->query($query)){
			return $id;
		}else{
			return false;
		}
	}
	
	/*
	 *
	 * Удалить пользователя
	 * @param $post
	 *
	 */
	public function delete_user($id)
	{
		if (!empty($id))
			{
			$query = $this->db->placehold("UPDATE __orders SET user_id=NULL WHERE id=? LIMIT 1", intval($id));
			$this->db->query($query);

			$query = $this->db->placehold("DELETE FROM __users WHERE id=? LIMIT 1", intval($id));
			if ($this->db->query($query))
				return true;
		}
		return false;
	}

	function get_groups()
	{
		// Выбираем группы
		$query = $this->db->placehold("SELECT g.id, g.name, g.discount FROM __groups AS g ORDER BY g.id ASC");
		$this->db->query($query);
		return $this->db->results_array(null, 'id');
	}

	function get_group($id)
	{
		// Выбираем группу
		$query = $this->db->placehold("SELECT * FROM __groups WHERE id=? LIMIT 1", $id);
		$this->db->query($query);
		$group = $this->db->result_array();

		return $group;
	}


	public function add_group($group)
	{
		if (is_object($group)) {
			$group = (array)$group;
		}
		//удалим id, если он сюда закрался, при создании id быть не должно
		if (isset($group['id'])) {
			unset($group['id']);
		}

		foreach ($group as $k => $e) {
			if (empty_($e)) {
				unset($group[$k]);
			}
		}

		$query = $this->db->placehold("INSERT INTO __groups SET ?%", $group);
		$this->db->query($query);
		return $this->db->insert_id();
	}

	public function update_group($id, $group)
	{
		$query = $this->db->placehold("UPDATE __groups SET ?% WHERE id=? LIMIT 1", $group, intval($id));
		$this->db->query($query);
		return $id;
	}

	public function delete_group($id)
	{
		if (!empty($id))
			{
			$query = $this->db->placehold("UPDATE __users SET group_id=NULL WHERE group_id=? LIMIT 1", intval($id));
			$this->db->query($query);

			$query = $this->db->placehold("DELETE FROM __groups WHERE id=? LIMIT 1", intval($id));
			if ($this->db->query($query))
				return true;
		}
		return false;
	}

	public function check_password($email, $password)
	{
		$encpassword = md5($this->config->salt . $password . md5($password));
		$query = $this->db->placehold("SELECT id FROM __users WHERE email=? AND password=? LIMIT 1", $email, $encpassword);
		$this->db->query($query);
		if ($id = $this->db->result_array('id'))
			return $id;
		return false;
	}
	
	/*
	 * Метод предназначен для проверки наличия конкретного права у пользователя текущей сессии
	 * Возвращает true, если у пользователя есть нужное право, или false во всех остальных случаях
	 * Может быть задан id разрешения или его название. Массив разрешений хранится в 
	 * этом классе в переменной $this->perm_list
	 */
	public function check_access($perm)
	{
		//Если никто не авторизован - стоп
		if(!isset($_SESSION['user_id'])){
			return false;
		}
		//получаем пользователя - если нет - false и выводим в лог ошибку
		if(!$user = $this->users->get_user($_SESSION['user_id'])){
			dtimer::log(__METHOD__ . " unable to get autorized user!!!", 1);
			return false;
		}
		
		/*
		 *  тут мы получаем id требуемого разрешения,
		 * если у нас разрешение задано в виде строки
		 */
		if(is_string($perm)){
			//перевернем массив, чтобы названия разрешений стали его ключами, а значения стали id
			$flip = array_flip($this->users->perm_list);
			//если такое разрешение существует
			if(isset($flip[$perm])){
				$req_perm_id = $flip[$perm];
			}else{
				dtimer::log(__METHOD__ . " unknown permission requested $perm", 2);
				return false;
			}
		} else {
			$req_perm_id = $perm;
		}
		//теперь проверим его у нашего пользователя
		if(isset($user['perm'][$req_perm_id])){
			return true;
		} else {
			return false;
		}
	}

}
