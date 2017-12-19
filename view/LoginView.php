<?PHP

require_once('View.php');

class LoginView extends View
{
	function fetch()
	{
		dtimer::log(__METHOD__ . " start");
		if(!isset($this->coMaster->uri_arr['path_arr']['url'])){
			return false;
		} else {
			$uri = $this->coMaster->uri_arr['path_arr']['url'];
		}
		// Выход
		if($uri == 'logout')
		{
			unset($_SESSION['user_id']);
			unset($_SESSION['admin']);
			header('Location: /');
			exit();
		}
		// Вспомнить пароль
		elseif($uri == 'password_remind')
		{
			// Если запостили email
			if($this->request->method('post') && $this->request->post('email'))
			{
				$email = $this->request->post('email');
				$this->design->assign('email', $email);
				
				// Выбираем пользователя из базы
				$user = $this->users->get_user($email);
				if(!empty($user))
				{
					// Генерируем секретный код и сохраняем в сессии
					$code = md5(uniqid($this->config->salt, true));
					$_SESSION['password_remind_code'] = $code;
					$_SESSION['password_remind_user_id'] = $user['id'];
					
					// Отправляем письмо пользователю для восстановления пароля
					$this->notify->email_password_remind($user['id'], $code);
					$this->design->assign('email_sent', true);
				}
				else
				{
					$this->design->assign('error', 'user_not_found');
				}
			}
			// Если к нам перешли по ссылке для восстановления пароля
			elseif($uri = 'password_remind' && isset($this->coMaster->uri_arr['path_arr']['arg']) )
			{
				// Проверяем существование сессии
				if(!isset($_SESSION['password_remind_code']) || !isset($_SESSION['password_remind_user_id']))
				return false;
				
				// Проверяем совпадение кода в сессии и в ссылке
				if($this->coMaster->uri_arr['path_arr']['arg'] != $_SESSION['password_remind_code']){
					return false;
				}
				
				// Выбераем пользователя из базы
				$user = $this->users->get_user(intval($_SESSION['password_remind_user_id']));
				if(empty($user))
					return false;
				
				// Залогиниваемся под пользователем и переходим в кабинет для изменения пароля
				$_SESSION['user_id'] = $user['id'];
				header('Location: '.$this->config->root_url.'/user');
			}
			return $this->design->fetch('password_remind.tpl');
		}
		// Вход
		elseif($this->request->method('post') && $this->request->post('login'))
		{
			$email			= $this->request->post('email');
			$password		= $this->request->post('password');
			
			$this->design->assign('email', $email);
			if($user_id = $this->users->check_password($email, $password))
			{
				$user = $this->users->get_user($email);
				$this->design->assign('user', $user);
				if($user['enabled'])
				{
					//Если запись администратора - запишем это в сессию
					if($user['admin'] == 1){
						$_SESSION['admin'] = $email;
					}
					$_SESSION['user_id'] = (int)$user['id'];
					
					
					$user_set['id'] = $user['id']; 
					$user_set['last_login'] = 'CURRENT_TIMESTAMP()'; 
					$user_set['last_ip'] = $_SERVER['REMOTE_ADDR'];
					
					$this->users->update_user($user_set);

					
					// Перенаправляем пользователя на прошлую страницу, если она известна
					if(!empty($_SESSION['last_visited_page'])){
						header('Location: '.$_SESSION['last_visited_page']);
					}
				}
				else
				{
					$this->design->assign('error', 'user_disabled');
				}
			}
			else
			{
				$this->design->assign('error', 'login_incorrect');
			}				
		}	
		return $this->design->fetch('login.tpl');
	}	
}
