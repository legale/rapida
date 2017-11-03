<?php

/*
 * Это класс контроллера XHR запросов, для доступа к API. 
 * Здесь выполняется разбор XHR запроса, получение необходимых данных,
 * и передача информации клиенту
 */

require_once ('api/Simpla.php');

class ControllerXhr extends Simpla
{

	//разрешенные классы и методы
	private $allowed_classes = array('products', 'brands', 'variants', 'features', 'image', 'cart', 'blog', 'comments');
	private $allowed_methods = array(
		'get_products', 'get_product', 'get_variants', 'get_variant', 'get_features',
		'get_options', 'get_cart', 'get_brands', 'get_brand', 'get_comments', 'get_comment', 'get_images'
	);
	private $request;
	private $is_valid = false;

	public function __construct()
	{
		//проверяем запрос на валидность
		$this->validateRequest();
	}

	private function validateRequest()
	{
		//~ print_r( $_POST);
		if (isset($_GET['xhr']) && isset($_POST['json'])) {
			$json = json_decode($_POST['json'], true);

			if (!empty($json['class'])
				&& !empty($json['method'])
				&& !empty($json['args'])
				&& in_array($json['class'], $this->allowed_classes)
				&& in_array($json['method'], $this->allowed_methods)) {
				$this->request = $json;
				$this->is_valid = true;
			}
		}
		return $this->is_valid;
	}

	public function action()
	{

		header("Content-type: application/json; charset=UTF-8");
		header("Cache-Control: must-revalidate");
		header("Pragma: no-cache");
		header("Expires: -1");	
		//Возьмем запрос в переменной класса
		$r = $this->request;
		//Если запрос не прошел проверку валидности или получем ответ от api false - скажем об этом
		if ($this->is_valid === false || !$res = $this->{$r['class']}->{$r['method']}($r['args'])) {
			$res = 'unable to perform api request';
		}
		//передаем результат клиенту и останавливаем скрипт
		print json_encode($res, JSON_UNESCAPED_UNICODE);
		exit();
	}
}
