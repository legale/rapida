<?php

/*
 * Это класс главного контроллера, через него осуществляется вызов всех остальных контроллеров
 * Главная задача этого контроллера, разобрать uri, вызвать нужный контроллер и передать ему полученные в uri 
 * параметры
 */

require_once ('api/Simpla.php');

class ControllerMaster extends Simpla
{
	public $uri_arr;
	public $ctrl;
	
	//здесь массив соответствия модулей и контроллеров
	private $modules = array(
	'admin' => 'coAdmin',
	'simpla' => 'coAdmin',
	'/' => 'coSimpla',
	'page' => 'coSimpla',
	'catalog' => 'coSimpla',
	'products'=> 'coSimpla',
	'search'=> 'coSimpla',
	'brands'=> 'coSimpla',
	'contact'=> 'coSimpla',
	'user'=> 'coSimpla',
	'login'=> 'coSimpla',
	'register'=> 'coSimpla',
	'cart'=> 'coSimpla',
	'order'=> 'coSimpla',
	'blog'=> 'coSimpla',
	'files'=> 'coResize',
	'xhr'=> 'coXhr',
	);



	public function __construct()
	{
		dtimer::log(__METHOD__ . ' start');
		if (isset($_SERVER)) {
			//запуск без параметров устанавливает контроллер $this->ctrl
			$this->parse_uri();
			dtimer::log(__METHOD__ . __LINE__ ." parsed_uri: " . var_export($this->uri_arr, true));

		}
		else {
			print "\n" . __CLASS__ . " is not for using via CLI\n";
		}
	}

	public function action()
	{
		//~ print_r($_GET);
		
		dtimer::log(__METHOD__ . ' selected controller: ' . $this->ctrl);
		if(!empty($this->ctrl)){
			return $this->{$this->ctrl}->action();
		} else {
			return $this->coSimpla->action('404');
		}
	}
	
	//генерируем uri из массива параметров
	public function gen_uri($arr = null, $filter = null)
	{	
		//если начальный массив параметров не задан, возьмем его из $this->uri_arr['path_arr']
		if (!isset($arr)) {
			if(isset($this->uri_arr['path_arr'])){
				$arr = $this->uri_arr['path_arr'];
				//~ return print_r($arr,true);
			} else {
				return false;
			}
		}
		$res = '';
		
		//Если у нас нет модуля - останавливаемся
		if (!isset($arr['module'])) {
			return false;
		}
		//соединяем рекурсивно 2 массива между собой, оставляя только разницу по ключам
		if( isset($filter) ){
			foreach($filter as $b=>$e){
				if (is_array($e) ){
					if (count($e) === 0 ){
						unset($arr[$b]);
						continue;
					}
					
					if ( is_array(reset($e)) ) {
						foreach($e as $f=>$o){
							if (count($o) === 0 ){
								unset($arr[$b][$f]);
								continue;
							}
							if( isset($arr[$b][$f]) ){
								$arr[$b][$f] = array_merge( array_diff($arr[$b][$f],$o), array_diff($o,$arr[$b][$f]) );
							} else {
								$arr[$b][$f] = $o;
							}
						}
						continue;
					}
					if( isset($arr[$b]) ){
						$arr[$b] = array_merge( array_diff($arr[$b],$e), array_diff($e,$arr[$b]) );
					} else {
						$arr[$b] = $e;
					}
				} else {
					$arr[$b] = $e;
				}
			}
		}
		
		
		//сначала модуль
		$res .= $arr['module'];
		
		//теперь url, если есть
		if ( isset($arr['url']) && $arr['url'] !== '' ){
			$res .= '/' . $arr['url'] ;
		}
		
		//теперь бренды, если они есть
		if ( isset($arr['brand']) && is_array($arr['brand']) && count($arr['brand']) > 0 ){
			$res .= '/brand-' . implode( '.',  $arr['brand'] );
		}
		
		//теперь сортировка, если они есть
		if ( isset($arr['sort']) && $arr['sort'] !== '' ){
			$res .= '/sort-' . $arr['sort'] ;
		}

		//теперь опции, если они есть
		if ( isset($arr['filter']) && is_array($arr['filter']) && count($arr['filter']) > 0 ){
			foreach($arr['filter'] as $name=>$v){
				if(is_array($v) && count($v) > 0){
					$res .= "/$name-" . implode('.', $v );
				}
			}
		}
		
		return $res;

	}


	//парсим uri
	public function parse_uri($uri = null)
	{
		dtimer::log(__METHOD__. " start");
		if (!isset($uri)) {
			$res = parse_url($_SERVER['REQUEST_URI']);
			$this->uri_arr = $res;
		}
		else {
			$res = parse_url($uri);
		}


		if (isset($res['query'])) {
			//Тут можно просто взять $_GET, но у нас свой лунапарк

			$a = explode('&', $res['query']);
			$c = array();
			foreach ($a as $b) {
				$b = explode('=', $b);
				if (count($b) === 2) {
					$c[urldecode($b[0])] = urldecode($b[1]);
				}
				else {
					dtimer::log(__METHOD__ . ' parse uri query part failed ', 2);
					dtimer::log(__METHOD__ . __LINE__ ." error", 2);
					return false;
				}
			}

			if (!isset($uri)) {
				$this->uri_arr['query_arr'] = $c;
			}
			$res['query_arr'] = $c;
		}
		
		//Если у нас в get запросе есть xhr, то сразу поставим контроллер туда, дальше ничего не парсим
		if (isset($res['query_arr']['xhr'])) {
			$res['ctrl'] =  'coXhr';
			if (!isset($uri)) {
				$this->uri_arr['ctrl'] = $res['ctrl'];
			}
			$this->ctrl = $res['ctrl'];
			return $res;
		}
		
		//Тут обрабатываем путь
		if (isset($res['path'])) {
			$res['path_arr'] = $this->parse_uri_path($res['path']);
			
			//если у нас нет соответствующего модулю контроллера  - ставим ''
			if(isset($this->modules[$res['path_arr']['module']]) ){
				$res['ctrl'] = $this->modules[$res['path_arr']['module']];
			} else {
				$res['ctrl'] = '';
			}
		}

		if (!isset($uri)) {
			$this->uri_arr['path_arr'] = $res['path_arr'];
			$this->ctrl = $res['ctrl'];
		}

		return $res;

	}
	
	/*
	 * Парсит часть uri - path. Логика работы:
	 * Первый элемент - всегда название контроллера (в терминах Simpla - модуля). 
	 */
	private function parse_uri_path($path)
	{
		$tpl = 	array(
					
				);
		
		//это массив для результатов
		$res = array();
		$brand = array();

		$a = $path;
		
		//Если путь / or пусто
		if ($a === '/' || $a === '') {
			return array('module' => '/');
		}
		else {
			//удалим дроби по краям, если они там есть, а потом делаем массив с разделением через дробь
			$a = explode('/', trim($a, '/'));
		}
		
		//Если только 1 элемент после дроби, значит это модуль page
		if(count($a) === 1 && !in_array(reset($a), array('blog','cart','order','register','search','simpla','user') ) ){
			return array('module' => 'page', 'url'=> array_shift($a) );
		}
		
		$res['module'] = array_shift($a);
		
		switch($res['module']){
			case 'files':
			$res['dir'] = array_shift($a);
			$res['url'] = array_shift($a);
			break;
			
			case 'products':
			case 'order':
			case 'cart':
			case 'page':
			case 'blog':
			$res['url'] = array_shift($a);
			break;

			case 'login':
			$res['url'] = array_shift($a);
			if(count($a) < 1){
			} else {
				$res['arg'] = array_shift($a);
			}
			
			break;
			
			default:
			$res['url'] = array_shift($a);
			
			//Если больше ничего не осталось, останавливаемся
			if(count($a) < 1){
				break;
			}
			
			$explode = explode('-', $a[0]);
			if(count($explode) === 2){
				list($f, $o) = $explode;
			} else {
				dtimer::log(__METHOD__ . __LINE__ ." error", 2);
				return false;
			}
			
			if( $f === 'brand'){
				$res['brand'] = explode('.', $o);
				//убираем использованный элемент
				array_shift($a);
				//Если больше ничего не осталось, останавливаемся
				if(count($a) < 1){
					break;
				}
				$explode = explode('-', $a[0]);
				if(count($explode) === 2){
					list($f, $o) = $explode;
				} else {
					dtimer::log(__METHOD__ . __LINE__ ." error", 2);
					return false;
				}
			}
			
			if($f === 'sort'){
				$res['sort'] = $o;
				//убираем использованный элемент
				array_shift($a);
			}

			//перебираем оставшуюся часть массива - тут у нас опции
			foreach($a as $o){
				//сначала разделяем название и значения
				$explode = explode('-', $o);
				if(count($explode) === 2){
					list($f, $o) = $explode;
				} else {
					dtimer::log(__METHOD__ . __LINE__ ." error", 2);
					return false;
				}
				$res['filter'][$f] = explode('.', $o);
			}
			break;
		}

		return $res;
	}

}
