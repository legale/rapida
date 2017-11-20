<?php

/*
 * Это класс контроллера XHR запросов, здесь выполняется разбор XHR запроса, получение необходимых данных,
 * и запуск нужного view/ViewXhr.php
 */

require_once ('api/Simpla.php');

class ControllerSimpla extends Simpla
{

	public function __construct()
	{
		dtimer::log(__METHOD__ . ' constructor');
	}

	public function action($url = null)
	{

		require_once ('view/IndexView.php');
		$view = new IndexView();

		if($url === '404'){
			print $view->fetch('404');
			return true;
		}



		// Если все хорошо
		if ( ($res = $view->fetch()) !== false)
			{
			// Выводим результат
			header("Content-type: text/html; charset=UTF-8");
			print $res;

			// Сохраняем последнюю просмотренную страницу в переменной $_SESSION['last_visited_page']
			if (empty($_SESSION['last_visited_page']) || empty($_SESSION['current_page']) || $_SERVER['REQUEST_URI'] !== $_SESSION['current_page'])
				{
				if (!empty($_SESSION['current_page']) && !empty($_SESSION['last_visited_page']) && $_SESSION['last_visited_page'] !== $_SESSION['current_page'])
					$_SESSION['last_visited_page'] = $_SESSION['current_page'];
				$_SESSION['current_page'] = $_SERVER['REQUEST_URI'];
			}
		}
		else
			{ 
			print $view->fetch('404');
		}

	}

}
