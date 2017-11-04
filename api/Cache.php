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
			"default_chmod" => '0666', // 0777 , 0666, 0644
			"securityKey" => "", // directory to store cache
			"htaccess" => true, // create htaccess file
			"path" => "auto", // cache root path
			"JSON_UNESCAPED_UNICODE" => true, //parameter to json_encode cache->encode method 
			"codepage" => "cp1251" //codepage to store cache data on disk
		);

		$ini_config = $this->config->vars_sections['cache'];

		self::$config = array_merge($config, $ini_config);

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

		}
		else {
			$path = dirname(__FILE__) . '/../' . self::$config['path'];
		}

		$securityKey = array_key_exists('securityKey', self::$config) ? self::$config['securityKey'] : "";
		dtimer::log('$config: ' . var_export(self::$config, true));
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
		dtimer::log(__METHOD__ . "getPath: '$path' '$securityKey'");
		dtimer::log(__METHOD__ . "getPath full_path: '$full_path'");
		dtimer::log(__METHOD__ . "getPath realpath: " . var_export(realpath($full_path), true));

		if ($skip_create_path !== true && !isset($this->tmp[$full_pathx])) {

			if (!@file_exists($full_path) || !@is_writable($full_path)) {
				if (!@file_exists($full_path)) {
					@mkdir($full_path, (int)self::$config['default_chmod'], true);
				}
				if (!@is_writable($full_path)) {
					@chmod($full_path, (int)self::$config['default_chmod']);
				}
				if (!@file_exists($full_path) || !@is_writable($full_path)) {
					dtimer::log("mkdir($full_path) error or chmod('$full_path', " . (int)self::$config['default_chmod'] . ") error");
					dtimer::log("PLEASE CREATE OR CHMOD " . $full_path . " - 0755 OR ANY WRITABLE PERMISSION!", 92);
				}
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
		dtimer::log(__METHOD__ . "getFilePath: '$path'");
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
				if (!mkdir($path, (int)self::$config['default_chmod'])) {
					print_r($path);
					dtimer::log("mkdir($path) error");
					dtimer::log("PLEASE CHMOD " . $this->getPath() . " - 0755 OR ANY WRITABLE PERMISSION!", 92);
				}
			}
		}

		$file_path = $path . "/" . $filename . ".txt";
		return $file_path;
	}



	/**
	 * Encode data to JSON format
	 * @param $data
	 * @return string
	 */
	private function encode($data, $unescaped = true)
	{
		if ( (is_object($data) || is_array($data) || is_null($data)) && is_bool($unescaped)) {
		}
		else {
			trigger_error(__METHOD__ . ' wrong argument type ' . gettype($data) . ' ' . gettype($unescaped));
			return false;
		}

		if ($unescaped === true) {
			return json_encode($data, JSON_UNESCAPED_UNICODE);
		}
		else {
			return json_encode($data);
		}
	}

	/**
	 * Decode JSON to array
	 * @param $value
	 * @return mixed
	 */
	private function decode($value, $as_array = null)
	{
		if (is_null($as_array)) {
			$as_array = true;
		}

		$x = @json_decode($value, $as_array);
		if ($x === null) {
			return false;
		}
		else {
			return $x;
		}
	}




	/**
	 * @param $keyword
	 * @param string $value
	 * @param int $time
	 * @param array $option
	 * @return bool
	 * @throws \Exception
	 */
	public function set_cache_nosql($keyword, $value = '')
	{
		dtimer::log(__METHOD__ . ' driver_set start ');
		$file_path = $this->getFilePath($keyword);
		$tmp_path = $file_path . ".tmp";
		$data = $this->encode($value, self::$config['JSON_UNESCAPED_UNICODE']);

		$toWrite = true;


		/*
		 * write to intent file to prevent race during read; race during write is ok
		 * because first-to-lock wins and the file will exist before the writer attempts
		 * to write.
		 */
		dtimer::log(__METHOD__ . ' write if condition ');
		dtimer::log(__METHOD__ . ' $tmp_path ' . $tmp_path);
		dtimer::log(__METHOD__ . ' $file_path ' . $file_path);
		if ($toWrite == true) {
			try {
				dtimer::log(__METHOD__ . ' write to tmp ' . $tmp_path);
				$f = @fopen($tmp_path, "w");
				if (!empty(self::$config['codepage'])) {
					$data = iconv("utf-8", self::$config['codepage'] . "//IGNORE", $data);
				}

				if (@fwrite($f, $data)) {
					$written = true;
				}
				else {
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
				dtimer::log("write FALSE error: " . print_r($e));
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
	public function get_cache_nosql($keyword, $as_array = true)
	{
		//проверка типов аргументов
		if (is_string($keyword) && is_bool($as_array)) {
		}
		else {
			trigger_error(__METHOD__ . ' wrong argument type ' . gettype($keyword) . ' ' . gettype($as_array));
			return false;
		}

		dtimer::log(__METHOD__ . ' driver_get start ');
		dtimer::log(__METHOD__ . ' keyword ' . $keyword);
		$file_path = $this->getFilePath($keyword);
		dtimer::log(__METHOD__ . ' file_path ' . $file_path);
		if (!file_exists($file_path)) {
			dtimer::log(__METHOD__ . ' file_exists check. not found ' . $file_path);
			return null;
		}

		$content = file_get_contents($file_path);
		dtimer::log(__METHOD__ . " before iconv $file_path first 5 sym ' " . mb_substr($content, 0, 5) . " '");
		// check if codepage isset transcode from codepage to utf8
		if (!empty(self::$config['codepage'])) {
			$content = iconv(self::$config['codepage'], "utf-8", $content);
		}

		dtimer::log(__METHOD__ . " after iconv $file_path first 5 sym ' " . mb_substr($content, 0, 5) . " '");
		$array = $this->decode($content, $as_array);

		dtimer::log(__METHOD__ . " before driver_get return");
		return $array;
	}

	/**
	 * @param $keyword
	 * @param array $option
	 * @return bool
	 * @throws \Exception
	 */
	public function driver_delete($keyword, $option = array())
	{
		$file_path = $this->getFilePath($keyword, true);
		if (file_exists($file_path) && @unlink($file_path)) {
			return true;
		}
		else {
			return false;
		}
	}


	/* Функция для добавления в кеш для цифровых значений
	 * аргумент в виде массива array(['keyhash' => $key], ['value' => $value]);
	 */

	public function set_cache_integer($keyhash, $value)
	{
		
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
		}
		else {
			return false;
		}
	}
	
	/* Функция для получение данных из кеша для цифровых значений
	 * аргумент в виде массива ['keyhash' => $key];
	 */

	public function get_cache_integer($keyhash)
	{

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
		}
		else {
			return false;
		}
	}

}
