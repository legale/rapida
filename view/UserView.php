<?PHP

 
require_once('View.php');

class UserView extends View
{
	function fetch()
	{
		if(empty($this->user))
		{
			header('Location: '.$this->config->root_url.'/login/login');
			exit();
		}
	
		if($this->request->method('post') && $this->request->post('name'))
		{
			$name			= $this->request->post('name');
			$email			= $this->request->post('email');
			$password		= $this->request->post('password');
			
			$this->design->assign('name', $name);
			$this->design->assign('email', $email);
			
			$this->db->query('SELECT count(*) as count FROM __users WHERE email=? AND id!=?', $email, $this->user['id']);
			$user_exists = $this->db->result_array('count');

			if($user_exists)
				$this->design->assign('error', 'user_exists');
			elseif(empty($name))
				$this->design->assign('error', 'empty_name');
			elseif(empty($email))
				$this->design->assign('error', 'empty_email');
			elseif($user_id = $this->users->update_user(array('id' => $this->user['id'], 'name'=>$name, 'email'=>$email)))
			{
				$this->user = $this->users->get_user(intval($user_id));
				$this->design->assign('name', $this->user['name']);
				$this->design->assign('user', $this->user);
				$this->design->assign('email', $this->user['email']);
				$this->design->assign('error', 'updated');
				
			if(!empty($password))
			{
				if( $this->users->update_user(array('id'=> $this->user['id'], 'password'=>$password)) ){
					$this->design->assign('error', 'updated');
				} else {
					$this->design->assign('error', 'not_updated');
				}

			}
				
				
			}
			else
			{
				$this->design->assign('error', 'unknown error');
			}
			

	
		}
		else
		{
			// Передаем в шаблон
			$this->design->assign('name', $this->user['name']);
			$this->design->assign('email', $this->user['email']);		
		}
	
		$orders = $this->orders->get_orders(array('user_id'=>$this->user['id']));
		$this->design->assign('orders', $orders);
		
		$this->design->assign('meta_title', $this->user['name']);
		$body = $this->design->fetch('user.tpl');
		
		return $body;
	}
}
