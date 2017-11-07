<?php

/*
 * Это класс контроллера для панели управления администратора.
 */

require_once ('api/Simpla.php');

class ControllerAdmin extends Simpla
{

	public function __construct()
	{
		dtimer::log(__METHOD__ . ' constructor');
	}

	public function action()
	{
		@ini_set('session.gc_maxlifetime', 86400); // 86400 = 24 часа
		@ini_set('session.cookie_lifetime', 0); // 0 - пока браузер не закрыт

		// Кеширование в админке нам не нужно
		Header("Cache-Control: no-cache, must-revalidate");
		header("Expires: -1");
		Header("Pragma: no-cache");

		$_SESSION['id'] = session_id();
		
		//Проверим авторизацию 
		if(!isset($_SESSION['admin'])){
			print 'Please log in first!';
		} else {

			require_once ('simpla/IndexAdmin.php');
			$backend = new IndexAdmin();
			$simpla = new Simpla();
			
			// Проверка сессии для защиты от xss
			if ($backend->request->check_session()){
					print $backend->fetch();
			} else {
				print 'Session expired!';
			}
		}
	}

}
