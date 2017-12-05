<?php

/*
 * Это класс контроллера XHR запросов, для доступа к API. 
 * Здесь выполняется разбор XHR запроса, получение необходимых данных,
 * и передача информации клиенту
 */

require_once ('api/Simpla.php');

class ControllerResize extends Simpla
{

	public function __construct()
	{
		dtimer::$enabled = false; //set true to enable debugger
		
		dtimer::log(__METHOD__ . ' construct');
	}

	public function action()
	{
		
		$dirname = $this->coMaster->uri_arr['path_arr']['dir'];
		$basename = $this->coMaster->uri_arr['path_arr']['url'];
		//Если ссылки нет в пути адресной строки - возьмем id товара из get query
		if(empty($basename) && !empty($this->coMaster->uri_arr['query_arr']['url']) ) {
			$pid = @$this->coMaster->uri_arr['query_arr']['url'];
			$pos = @$this->coMaster->uri_arr['query_arr']['pos'];
			//получим главное изображение товара из таблицы s_images
			if(!$basename = $this->products->get_product_image($pid, $pos)){
				return false;
			} else {
				$w = @$this->coMaster->uri_arr['query_arr']['w'];
				$h = @$this->coMaster->uri_arr['query_arr']['h'];
				$wm = @$this->coMaster->uri_arr['query_arr']['wm'];
				$basename = $this->image->add_resize_params($basename['filename'], $w, $h, $wm);
			}
		} 
		
		dtimer::log(__METHOD__ . " basename: $basename");

		


		if(!empty($basename) && !empty($dirname) ){
			$res = $this->image->resize($basename);
			dtimer::log( __METHOD__ . " basename: $basename");
			dtimer::log( __METHOD__ . " resized: $res");
		}

		if ( isset($res) && is_readable($res)){
			if(isset($_SERVER['HTTP_RANGE']) ){
				$bytes = explode('=', $_SERVER['HTTP_RANGE'], 2);
				$range = explode('-', $bytes[1], 2);
				$length = $range[1] - $range[0];
				$offset = (int)$range[0];
				$f = @fopen($res, 'r');
				@fseek($f, $offset);
				header("Content-length: $length");
				header("Content-type: image");
				print @fread($f,  $length);
			} else {
				$length = filesize($res);
				header("Content-length: $length");
				header("Content-type: image");
				print @file_get_contents($res);
			}

		}
		
		dtimer::show();

		exit();
	}
}
