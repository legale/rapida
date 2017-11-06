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

	public function action()
	{

		require_once ('view/IndexView.php');
		$view = new IndexView();




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
			// Иначе страница об ошибке
			header("http/1.0 404 not found");
			
			// Подменим переменную GET, чтобы вывести страницу 404
			$_GET['page_url'] = '404';
			$_GET['module'] = 'PageView';
			print $view->fetch();
		}

	}

}
