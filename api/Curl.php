<?php

require_once ('Simpla.php');

class Curl extends Simpla 
{

	//curl handle
	public $ch;
	
	//curl data and header
	public $curl_data = '';
	public $curl_content_length = 0;
	public $curl_headers = array();
	
	//other vars
	public $fopen; //file handle
	public $tmp; //tmp filepath
	public $lastbyte = '';
	
	public function download($src){
		dtimer::log(__METHOD__ . " start $src");
		$localbyte = '';
		
		$src = $this->get_link($src);
		$this->curl_open($src);
		$this->curl_exec();
		dtimer::log(__METHOD__. " writing data finished. starting last byte check.");
			
		if (empty($this->curl_headers)){
			dtimer::log(__METHOD__ . " http headers empty! ", 1);
			return false;
			
		}
		
		//if content-length header is recieved and last byte saved 
		if($this->lastbyte !== ''){
			dtimer::log(__METHOD__. " using saved local file last byte");
			$localbyte = $this->lastbyte;
		} else if (is_resource($this->fopen) ){
			//else - lastbyte is not saved so file handle is still opened
			dtimer::log(__METHOD__. " there is no saved local file last byte. trying to get it from opened file handle");
			fseek($this->fopen, -1 , SEEK_END);
			$localbyte = fread($this->fopen, 2);
			fclose($this->fopen);
		} else {
			dtimer::log(__METHOD__ . " nothing to read!", 1);
			return false;
			
		}
		if($localbyte === ''){
			dtimer::log(__METHOD__. " unable to get local file last byte", 1);
			return false;
		}
		
		if(!$remotebyte = $this->read_remote($src, $this->curl_content_length - 1, 1)){
			dtimer::log(__METHOD__. " unable to get remote file last byte", 1);
			return false;
		}
		
		if($localbyte !== $remotebyte){
			dtimer::log(__METHOD__. " remote last byte: $remotebyte is not equal to local: $localbyte", 1);
			return false;
		}
		dtimer::log(__METHOD__. " last byte check completed."); 

		
		//if we get here - download is completed
		dtimer::log(__METHOD__. " done! file saved to: ".$this->tmp); 
		return $this->tmp;
	
	}

	private function callbackheader($ch, $hdr) { 
		dtimer::log(__METHOD__ . " start curl header: $hdr");
		//закрываем файл для записи данных, если он еще открыт
		if(is_resource($this->fopen) ){
			dtimer::log(__METHOD__ . " trying to close file handle ");
			fclose($this->fopen);
		}
		
		if( empty(trim($hdr)) ){
			return strlen($hdr);
		}
		//делим строку по двоеточию на 2 эл. максимум
		$v = explode(':', $hdr, 2);
		if(count($v) < 2){
			//если двоеточия не было, тогда оставим старый ключ и значение
			$key = 0;
			dtimer::log(__METHOD__ . " http version header: $hdr");
			$val = explode(' ', trim($v[0]));
		}else{
			$key = strtolower($v[0]);
			$val = trim($v[1]);
		}
		$this->curl_headers[$key] = $val;
		//~ unset($this->curl_headers['content-length']); //for testing 
			
		return strlen($hdr); 
	} 
	
	private function callbackdata($ch, $data){
		dtimer::log(__METHOD__ . " start written data length: ".$this->curl_content_length);
		$len = strlen($data);
		$this->curl_content_length += $len;
		
		if(!is_resource($this->fopen)){
			$this->open_tmp();
		}
		fwrite( $this->fopen, $data );

		if(isset($this->curl_headers['content-length'])
		 && $this->curl_headers['content-length'] == $this->curl_content_length){
			dtimer::log(__METHOD__ ." content-length reached ". $this->curl_content_length);
			$this->lastbyte = substr($data, -1); //save last byte
			if(is_resource($this->fopen)){
				fclose($this->fopen);
			}
		}


		return $len;
	}
	
	public function filecheck($remote, $local){
		dtimer::log(__METHOD__ . " start remote: $remote local: $local");

		$rfs = $this->remote_filesize($remote);
		
		$lfs = filesize($local);
		
		if($rfs === false || $lfs === false){
			dtimer::log(__METHOD__ . " unable to get filesize remote or local file", 2);
		} else {
			if($rfs !== $lfs){
				dtimer::log(__METHOD__ . " remote filesize: $rfs is not equal to local: $lfs", 2);
				return false;
			}
			
		}
		dtimer::log(__METHOD__ . " last byte check");
		
		$f = fopen($local, 'r');
		fseek($f, -1, SEEK_END);
		$localbyte = fread($f, 2);
		fclose($f);
		
		$remotebyte = $this->read_remote($remote, $rfs -1 , 1);


		dtimer::log(__METHOD__ . " localbyte: $localbyte remotebyte: $remotebyte");
		if($localbyte === $remotebyte){
			return true;
		} else {
			return false;
		}
		
	}


	public function curl_open($src, $options = array() ){
		dtimer::log(__METHOD__ . " start $src");
		
		if(empty($this->ch)){
			$this->ch = curl_init();
		}
		//reset previous headers and data
		$this->curl_headers = array();
		$this->curl_data = '';
		$this->curl_content_length = 0;
		$this->lastbyte = '';
		
		
		$callback = array(
			'header' => array($this,'callbackheader'),
			'data'=> array($this,'callbackdata')
		);
		
		$opt = array(
			CURLOPT_URL => $src, // устанавливаем URL
			CURLOPT_PIPEWAIT => 1, //1 to wait for pipelining/multiplexing
			CURLOPT_FOLLOWLOCATION => 0, //redirect to location header
			CURLOPT_MAXREDIRS => 10, //max redirects
			CURLOPT_HTTP_VERSION => 3, // пробуем использовать http/2
			CURLOPT_SSL_VERIFYHOST => 2, //отключаем проверку соответсвия имени на сертификате хосту
			CURLOPT_RETURNTRANSFER => 1, //вернуть результат через curl_exec($ch), а не на экран.
			CURLOPT_HEADERFUNCTION => $callback['header'],
			CURLOPT_WRITEFUNCTION => $callback['data'],
			CURLOPT_HTTPHEADER => array(), //reset headers
			CURLOPT_NOBODY => 0, //reset nobody
		);
		
		if(!empty($options)){
			foreach($options as $k=>$o){
				if($o === null){
					unset($opt[$k]);
				}else{
					$opt[$k] = $o;
				}
			}
		}
		
		curl_setopt_array($this->ch, $opt);
		
		return $this->ch;
	}

	public function curl_exec(){
		
		if($this->ch === null){
			dtimer::log(__METHOD__ . " please open curl session first", 1);
			return false;
		}
		
		return curl_exec($this->ch);
	}
	
	public function curl_close(){
		
		if($this->ch === null){
			dtimer::log(__METHOD__ . " please open curl session first", 1);
			return false;
		}
		curl_close($this->ch);
		
		return true;
	}

	public function open_tmp(){
		dtimer::log(__METHOD__ . ' start');
		if(!is_resource($this->fopen)){
			$this->tmp = tempnam(sys_get_temp_dir() , 'curl_tmp');
			$this->fopen = fopen($this->tmp, 'w+');
		return true;
		}else{
			dtimer::log(__METHOD__ . ' unable to create new fopen, close current first!');
			return false;
		}
	}
	
	public function close_tmp(){
		dtimer::log(__METHOD__ . ' start');
		if(is_resource($this->fopen)){
			fclose($this->fopen);
			return true;
		}else{
			dtimer::log(__METHOD__ . ' tmp file is not opened yet');
			return false;
		}
	}



	public function remote_filesize($src){
		dtimer::log(__METHOD__ . " start $src");
		$src = $this->get_link($src);
		
		$options = array (CURLOPT_NOBODY => 1);
		$this->curl_open($src, $options);
		$this->curl_exec();
		if( isset($this->curl_headers['content-length']) ){
			return (int)$this->curl_headers['content-length'];
		}else{
			return false;
		}
	}
	
	
	public function get_link($src){
		dtimer::log(__METHOD__ . " start $src");
		$opt = array(
			CURLOPT_URL => $src, // устанавливаем URL
			CURLOPT_FOLLOWLOCATION => 0, //redirect to location header
			CURLOPT_SSL_VERIFYHOST => 0, //отключаем проверку соответсвия имени на сертификате хосту
			CURLOPT_RETURNTRANSFER => 1, //вернуть результат через curl_exec($ch), а не на экран.
			CURLOPT_HEADERFUNCTION => array($this, 'callbackheader'),
			CURLOPT_NOBODY => 1, //skip content, headers only
		);		
		
		$this->curl_open($src, $opt);
		$this->curl_exec();
		$res = $this->curl_headers;
		
		//get code first sym
		if(!isset($res[0][1])){
			return false;
		}
		$code = substr($res[0][1], 0, 1);
		switch($code){
			//code 2xx ok
			case '2': return $src;
			//code 3xx moved
			case '3': return $this->get_link($res['location']);
			default : return false;
		}
	}

	
	

	public function read_remote($src, $offset, $length) {

		$hdr = array("Range: bytes=".$offset."-".($offset + $length));
		dtimer::log(__METHOD__ . " range header: '$hdr[0]'" );
		
		$opt = array(
			CURLOPT_URL => $src,
			CURLOPT_HTTPHEADER => $hdr,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_NOBODY => 0,
			CURLOPT_WRITEFUNCTION => null,
		);
		$this->curl_open($src, $opt);
		$res = $this->curl_exec();
		$len = strlen($res);
		if($len !== $length){
			dtimer::log(__METHOD__ . " wrong length '$len', expected: $length ", 1);
			return false;
		}
		
		return $res;

	}

}
