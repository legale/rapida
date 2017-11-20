<?php
require_once ('Simpla.php');
/* Класс для работы с кешем
 */
class Cache extends Simpla
{


	private $tmp = array();

	static private $config = array();
	
	

	// Конструктор
	public function __construct()
	{
		// загружаем настройки кеша, если они еще не были загружены
		$config = array(
			"default_chmod" => '777', // 777 , 666, 644
			"securityKey" => "", // directory to store cache
			"htaccess" => true, // create htaccess file
			"path" => "auto", // cache root path
			"JSON_UNESCAPED_UNICODE" => true, //parameter to json_encode cache->encode method
			"codepage" => "cp1251", //codepage to store cache data on disk
			"method" => "var_export", //method to save data (json, serialize, var_export)
		);

		$ini_config = $this->config->vars_sections['cache'];

		self::$config = array_merge($config, $ini_config);
		
		//меняем систему счисления, чтобы chmod и mkdir правильно обрабатывали права, заданные в виде строки
		self::$config['default_chmod'] = octdec(self::$config['default_chmod']);
	}



	/**
	 * @param $filename
	 * @return mixed
	 */
	private function cleanFileName($filename)
	{
		$regex = array(
			'/[^\.\w\d\_]/',
		);
		$replace = array('');
		return preg_replace($regex, $replace, $filename);
	}


	/**
	 * @return bool
	 */
	public function checkdriver()
	{
		if (is_writable($this->getPath())) {
			return true;
		}/* else {

		}*/
		return false;
	}

	/**
	 * @param $keyword
	 * @return string
	 */
	private function encodeFilename($keyword)
	{
		return trim(trim(preg_replace("/[^a-zA-Z0-9]+/", "_", $keyword), "_"));
		// return rtrim(base64_encode($keyword), '=');
	}

	/**
	 * @param $filename
	 * @return mixed
	 */
	private function decodeFilename($filename)
	{
		return $filename;
		// return base64_decode($filename);
	}



	/**
	 * @param bool $skip_create_path
	 * @param $config
	 * @return string
	 * @throws \Exception
	 */
	public function getPath($skip_create_path = false)
	{

		if (!isset(self::$config['path']) || self::$config['path'] == '' || self::$config['path'] == 'auto') {
			$tmp_dir = ini_get('upload_tmp_dir') ? ini_get('upload_tmp_dir') : sys_get_temp_dir();
			$path = $tmp_dir;
		} else {
			$path = dirname(__FILE__) . '/../' . self::$config['path'];
		}

		$securityKey = array_key_exists('securityKey', self::$config) ? self::$config['securityKey'] : "";
		if ($securityKey == "" || $securityKey == "auto") {
			$securityKey = self::$config['securityKey'];
			if ($securityKey == "auto" || $securityKey == "") {
				$securityKey = isset($_SERVER['HTTP_HOST']) ? preg_replace(
					'/^www./',
					'',
					strtolower($_SERVER['HTTP_HOST'])
				) : "default";
			}
		}
		if ($securityKey != "") {
			$securityKey .= "/";
		}

		$securityKey = $this->cleanFileName($securityKey);

		$full_path = $path . "/" . $securityKey;
		
		$full_pathx = hash('md4', $full_path);
		dtimer::log(__METHOD__ . " '$path' '$securityKey'");
		dtimer::log(__METHOD__ . " full_path: '$full_path'");

		if ($skip_create_path !== true && !isset($this->tmp[$full_pathx])) {
			if (!@file_exists($full_path)) {
				@mkdir($full_path, self::$config['default_chmod'], true);
			}

			$perms = substr(decoct(fileperms($full_path)), 2);
			$def_chmod = decoct(self::$config['default_chmod']);
			dtimer::log(__METHOD__ . " fileperms '$perms' $full_path");
			
			if ($perms !== $def_chmod) {
				dtimer::log(__METHOD__ . " fileperms '$perms' not equals to default_chmod '$def_chmod' trying to chmod $full_path");
				chmod($full_path, self::$config['default_chmod']);
			}

			if (!@file_exists($full_path) || !@is_writable($full_path)) {
				dtimer::log("mkdir($full_path) error or chmod('$full_path', " . self::$config['default_chmod'] . ") error");
				dtimer::log("PLEASE CREATE OR CHMOD " . $full_path . " - 0755 OR ANY WRITABLE PERMISSION!", 92);
				return false;
			}

			$this->tmp[$full_pathx] = true;
			$this->htaccessGen($full_path, array_key_exists('htaccess', self::$config) ? self::$config['htaccess'] : false);
		}

		return realpath($full_path);
	}



	/**
	 * Auto Create .htaccess to protect cache folder
	 * @param string $path
	 * @throws \phpfastcacheCoreException
	 */
	protected function htaccessGen($path = "")
	{
		if (self::$config["htaccess"] == true) {
			if (!file_exists($path . "/.htaccess")) {
				//   echo "write me";
				$html = "order deny, allow \r\n
deny from all \r\n
allow from 127.0.0.1";

				$f = @fopen($path . "/.htaccess", "w+");
				if (!$f) {
					dtimer::log("Can't create .htaccess", 97);
				}
				@fwrite($f, $html);
				@fclose($f);
			} /*else {
				//   echo "got me";
			}*/
		}
	}

	private function getFilePath($keyword, $skip = false)
	{
		$path = $this->getPath();
		dtimer::log(__METHOD__ . " Path: '$path'");
		if (empty($path)) {
			dtimer::log(__METHOD__ . "getPath empty!");
		}

		$filename = $this->encodeFilename($keyword);
		//filename string length
		$len = strlen($filename);
		$short_len = $len * 0.05;
		$folder = substr($filename, 0, $short_len + 1); /* my method */
		
		//$folder = substr($filename, 0, 2); /* original method */
		$path = rtrim($path, "/") . "/" . $folder;
		/*
		 * Skip Create Sub Folders;
		 */
		if ($skip == false) {
			if (!file_exists($path)) {
				if (!mkdir($path, self::$config['default_chmod'])) {
					dtimer::log("mkdir($path) error");
					dtimer::log("PLEASE CHMOD " . $this->getPath() . " - 0755 OR ANY WRITABLE PERMISSION!", 92);
				}
			}
		}

		$file_path = $path . "/" . $filename . ".txt";
		return $file_path;
	}


	private function var_export($data) {
	   //это позволит сохранить объект, а не только массив
	   //$data = str_replace('stdClass::__set_state', '(object)', $val);
		$res = var_export($data, true).";";
		return $res;
	}

	/**
	 * Encode data to JSON format
	 * @param $data
	 * @return string
	 */
	private function serialize($data)
	{
		dtimer::log(__METHOD__ . " start");
		if (empty_($data)) {
			dtimer::log(__METHOD__ . " empty value! skip encode", 2);
			return false;
		}
		
		if (is_object($data) || is_array($data)) {
		} else {
			dtimer::log(__METHOD__ . ' wrong argument type ' . gettype($data), 1);
			return false;
		}

		if ($res = serialize($data)) {
			return $res;
		} else {
			dtimer::log(__METHOD__ . " serialize error", 1);
			return false;
		}
	}
	
	
	private function encode($data, $unescaped = true)
	{
		dtimer::log(__METHOD__ . " start");
		if (empty_($data)) {
			dtimer::log(__METHOD__ . " empty value! skip encode", 2);
			return false;
		}
		
		if ((is_object($data) || is_array($data)) && is_bool($unescaped)) {
		} else {
			dtimer::log(__METHOD__ . ' wrong argument type ' . gettype($data) . ' ' . gettype($unescaped), 1);
			return false;
		}
		
		if ($unescaped === true) {
			$param = JSON_UNESCAPED_UNICODE;
		} else {
			$param = '';
		}
		if ($res = json_encode($data, $param)) {
			return $res;
		} else {
			dtimer::log(__METHOD__ . " json encode error", 1);
			return false;
		}
	}

	/**
	 * Decode JSON to array
	 * @param $value
	 * @return mixed
	 */
	private function decode($value)
	{
		if (empty_($value)) {
			dtimer::log(__METHOD__ . " empty value! skip decode", 2);
			return false;
		}

		$x = @json_decode($value, true);
		if ($x === null) {
			dtimer::log(__METHOD__ . " json_decode error", 1);
			return false;
		} else {
			return $x;
		}
	}

	private function unserialize($value)
	{
		dtimer::log(__METHOD__ . " start");

		if (empty_($value)) {
			dtimer::log(__METHOD__ . " empty value! skip decode", 2);
			return false;
		}

		$x = @unserialize($value);
		if ($x === false) {
			dtimer::log(__METHOD__ . " unserialize error", 1);
			return false;
		} else {
			return $x;
		}
	}






	public function set_cache_nosql($keyword, $value = '', $method = null)
	{

		//Если кеш отключен - останавливаем
		if (self::$config['cache'] !== true) {
			return false;
		}
		
		//проверка типов аргументов
		if (!is_string($keyword)) {
			dtimer::log(__METHOD__ . ' wrong argument type ' . gettype($keyword), 1);
			return false;
		}

		
		//если способ сохранения не задан, берем настройку из конфига
		if (!isset($method)) {
			$method = self::$config['method'];
		}
		

		dtimer::log(__METHOD__ . ' driver_set start ');
		$file_path = $this->getFilePath($keyword);
		$tmp_path = $file_path . ".tmp";
		switch($method) {
			case 'json':
			$data = $this->encode($value, self::$config['JSON_UNESCAPED_UNICODE']);
			break;
			
			case 'serialize':
			$data = $this->serialize($value);
			break;
			
			case 'var_export':
			default:
			$data = $this->var_export($value);
		}

		$toWrite = true;

		dtimer::log(__METHOD__ . ' write if condition ');
		dtimer::log(__METHOD__ . ' $tmp_path ' . $tmp_path);
		dtimer::log(__METHOD__ . ' $file_path ' . $file_path);
		if ($toWrite == true) {
			try {
				dtimer::log(__METHOD__ . ' write to tmp ' . $tmp_path);
				$f = @fopen($tmp_path, "w");
				if (!empty(self::$config['codepage']) && $method === 'json') {
					$data = @iconv("utf-8", self::$config['codepage'] . "//IGNORE", $data);
				}

				if (@fwrite($f, $data)) {
					$written = true;
				} else {
					$written = false;
				}
				@fclose($f);
					// delete cache if exists
				@unlink($file_path);
					// rename tmp tp cache
				dtimer::log(__METHOD__ . ' rename: ' . $tmp_path . ' to: ' . $file_path);
				if (file_exists($tmp_path)) {
					rename($tmp_path, $file_path);
				}
			} catch (Exception $e) {
				// miss cache
				dtimer::log("write FALSE error: " . print_r($e), 2);
				$written = false;
			}
		}
		dtimer::log("written " . $file_path);
		return $written;
	}

	/**
	 * @param $keyword
	 * @param array $option
	 * @return mixed|null
	 * @throws \Exception
	 */
	public function get_cache_nosql($keyword, $method = null)
	{
		//Если кеш отключен - останавливаем
		if (self::$config['cache'] !== true) {
			return false;
		}

		//проверка типов аргументов
		if (is_string($keyword) ) {
		} else {
			dtimer::log(__METHOD__ . ' wrong argument type' , 2);
			return false;
		}

		//если json кодирование не отключено принудительно, берем настройку из конфига
		if (!isset($method)) {
			$method = self::$config['method'];
		}

		dtimer::log(__METHOD__ . ' driver_get start keyword ' . $keyword);
		$file_path = $this->getFilePath($keyword);
		dtimer::log(__METHOD__ . ' file_path ' . $file_path);
		if (!file_exists($file_path)) {
			dtimer::log(__METHOD__ . ' file_exists - not found ');
			return null;
		}


		$value = file_get_contents($file_path);
		
		switch($method) {
			case 'json':
			dtimer::log(__METHOD__ . " method json ");
			dtimer::log(__METHOD__ . " before iconv $file_path ");
			// check if codepage isset transcode from codepage to utf8
			if (!empty(self::$config['codepage'])) {
				$value = @iconv(self::$config['codepage'], "utf-8", $value);
			}
			dtimer::log(__METHOD__ . " after iconv $file_path ");
			$data = $this->decode($value);
			break;
			
			case 'serialize':
			dtimer::log(__METHOD__ . " method serialize ");
			$data = $this->unserialize($value);
			break;
			
			case 'var_export':
			default:
			dtimer::log(__METHOD__ . " method var_export ");
			@eval('$data = '.$value);
		}
		
		
		if( isset($data) ){
			dtimer::log(__METHOD__ . " return data ");
			return $data;
		} else {
			dtimer::log(__METHOD__ . " return false error on decoding cache data", 1);
			return false;
		}
	}

	/**
	 * @param $keyword
	 * @param array $option
	 * @return bool
	 * @throws \Exception
	 */
	public function driver_delete_nosql($keyword, $option = array())
	{
		$file_path = $this->getFilePath($keyword, true);
		if (file_exists($file_path) && @unlink($file_path)) {
			return true;
		} else {
			return false;
		}
	}


	/* Функция для добавления в кеш для цифровых значений
	 * аргумент в виде массива array(['keyhash' => $key], ['value' => $value]);
	 */

	public function set_cache_integer($keyhash, $value)
	{
		//Если кеш отключен - останавливаем
		if (self::$config['cache'] !== true) {
			return false;
		}
		
		//останавливаем если у нас пустое значение или длина хеша не равна 32 символам, или $value не строка
		if ($value === '' || @strlen($keyhash) != 32 || !is_string($value)) {
			dtimer::log('invalid keyhash length (32) ' . strlen($keyhash) . ' or value is not a string: ' . var_export(is_string($value), true) . ' value:' . print_r($value, true));
			return false;
		}
		
		
		// 1 элемент массива содержит данные в виде hex строки, поэтому placehold type ! ключ = 0x{значение},
		// 2 элемент просто строка поэтому тут просто % ключ = значение

		$query = "INSERT __cache_integer SET `keyhash` = 0x$keyhash , `value` = '$value' , `updated` = NOW()
			ON DUPLICATE KEY UPDATE `value` = '$value', `updated` = NOW()";
		$query = $this->db->placehold($query);
		dtimer::log("set_cache_integer query: $query");
		$this->db->query($query);
		$id = $this->db->insert_id();

		if ($id !== false) {
			return $id;
		} else {
			return false;
		}
	}
	
	/* Функция для получение данных из кеша для цифровых значений
	 * аргумент в виде массива ['keyhash' => $key];
	 */

	public function get_cache_integer($keyhash)
	{
		//Если кеш отключен - останавливаем
		if (self::$config['cache'] !== true) {
			return false;
		}
		
		if (@strlen($keyhash) != 32) {
			return false;
		}
		
		
		//элемент массива содержит данные в виде hex строки, поэтому placehold type ! ключ = 0x{значение},
		$query = "SELECT value FROM __cache_integer WHERE `keyhash` = 0x$keyhash ";
		$query = $this->db->placehold($query);
		dtimer::log("get_cache_integer $query");
		$this->db->query($query);
		$res = $this->db->result('value');

		if ($res !== false) {
			return $res;
		} else {
			return false;
		}
	}
}
