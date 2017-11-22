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
		dtimer::log(__METHOD__ . ' construct');

	}

	public function action()
	{
		
		$dirname = $this->coMaster->uri_arr['path_arr']['dir'];
		$basename = $this->coMaster->uri_arr['path_arr']['url'];
		//Если ссылки нет в пути адресной строки - возьмем id товара из get query
		if(empty($basename) && !empty($this->coMaster->uri_arr['query_arr']['url']) ) {
			$pid = $this->coMaster->uri_arr['query_arr']['url'];
			//получим главное изображение товара из таблицы s_images
			if(!$basename = $this->products->get_product_image($pid)){
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
			$resized_filename = $this->image->resize($basename);
			dtimer::log( __METHOD__ . " basename: $basename");
			dtimer::log( __METHOD__ . " resized: $resized_filename");
		}

		if ( isset($resized_filename) && is_readable($resized_filename))
			{
			header('Content-type: image');
			print file_get_contents($resized_filename);
		}
		
		//~ dtimer::show();

		exit();
	}
}
