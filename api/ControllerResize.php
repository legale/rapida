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
		//Если ссылки нет в пути адресной строки - возьмем ее из get query
		if(empty($basename) && !empty($this->coMaster->uri_arr['query_arr']['url']) ) {
			$basename = $this->coMaster->uri_arr['query_arr']['url'];
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
