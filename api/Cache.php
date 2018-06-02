<?php
require_once('Simpla.php');

/* Класс для работы с кешем
 */

class Cache extends Simpla
{


    private $tmp = array();
    public static $config = array();
    public static $enabled = false;
    private static $shmop_enabled;


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
            "method" => "serialize", //method to save data (json, serialize, var_export)
        );

        $ini_config = $this->config->vars_sections['cache'];

        self::$config = array_merge($config, $ini_config);

        //меняем систему счисления, чтобы chmod и mkdir правильно обрабатывали права, заданные в виде строки
        self::$config['default_chmod'] = octdec((int)self::$config['default_chmod']);

        //check if shmop enabled
        self::$shmop_enabled = function_exists('shmop_open') ? true : false;
    }

    /**
     * @param $key
     * @param $data
     */
    public function shmop_set($key, $data)
    {
        dtimer::log(__METHOD__ . " start key: $key");
        if (!self::$shmop_enabled) {
            dtimer::log(__METHOD__ . " shmop_open function not found. Please install shmop first. Abort.");
            return false;
        }
        if (empty($data)) {
            dtimer::log(__METHOD__ . " data is empty!. Abort.");
            return false;
        }
        $size = strlen($data);
        $shmid = shmop_open($key, 'c', 0775, $size);
        $res = shmop_write($shmid, $data, 0);
        shmop_close($shmid);
        return $res;
    }

    public function shmop_get($key)
    {
        dtimer::log(__METHOD__ . " start key: $key");
        if (!self::$shmop_enabled) {
            dtimer::log(__METHOD__ . " shmop_open function not found. Please install shmop first. Abort.");
            return false;
        }
        $shmid = shmop_open($key, 'a', 0, 0);
        $res = shmop_read($shmid, 0, 0);
        shmop_close($shmid);
        return $res;
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

        $full_pathx = md5($full_path);
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
            if (self::$config["htaccess"] == true) {
                $this->htaccessGen($full_path);
            }
        }

        return realpath($full_path);
    }


    /**
     * Create .htaccess to deny access to folder
     */
    public function htaccessGen($path = "")
    {
        dtimer::log(__METHOD__ . " start");
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

        $file_path = $path . "/" . $filename;
        return $file_path;
    }


    private function var_export($data)
    {
        //это позволит сохранить объект, а не только массив
        //$data = str_replace('stdClass::__set_state', '(object)', $val);
        $res = var_export($data, true);
        return $res;
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



    public function set_cache_nosql($keyword, $value, $method = null)
    {
        //Если кеш отключен - останавливаем
        if (self::$enabled !== true) {
            return false;
        }

        //проверка типов аргументов
        if (!is_string($keyword)) {
            dtimer::log(__METHOD__ . ' wrong argument type ' . gettype($keyword), 1);
            return false;
        }

        $file_path = $this->getFilePath($keyword);
        $tmp_path = $file_path . ".tmp";

        dtimer::log(__METHOD__ . ' trying to open and lock tmp file ');
        $f = @fopen($tmp_path, "c");

        if (!$f) {
            dtimer::log(__METHOD__ . " unable to open file: $tmp_path ", 1);
            return false;
        }
        if (!flock($f, LOCK_EX)) {
            dtimer::log(__METHOD__ . " unable to lock file: $tmp_path ", 1);
            fclose($f);
            return false;
        }


        //если способ сохранения не задан, берем настройку из конфига
        if (!isset($method)) {
            $method = self::$config['method'];
        }
        dtimer::log(__METHOD__ . " selected serialization method: $method");
        switch ($method) {
            case 'json':
                $data = $this->encode($value, self::$config['JSON_UNESCAPED_UNICODE']);
                break;

            case 'serialize':
            default:
                $data = serialize($value);
                break;

            case 'var_export':
                $data = $this->var_export($value, true);
                break;

            case 'msgpack':
                $data = msgpack_pack($value);
                break;
        }

        if ($method !== 'msgpack' && !empty(self::$config['codepage'])) {
            $data = @iconv("utf-8", self::$config['codepage'] . "//IGNORE", $data);
        }

        if (!ftruncate($f, 0)) {
            dtimer::log(__METHOD__ . " unable to clear file: $tmp_path ", 1);
            fclose($f);
            return false;
        }

        dtimer::log(__METHOD__ . ' write to tmp ' . $tmp_path);
        $written = fwrite($f, $data);
        fclose($f);

        if ($written) {
            dtimer::log("written");
        } else {
            dtimer::log(" unable to write data: " . var_export($data, true), 1);
            return false;
        }

        // delete cache if exists
        @unlink($file_path);

        // rename tmp tp cache
        dtimer::log(__METHOD__ . ' rename: ' . $tmp_path . ' to: ' . $file_path);
        if (file_exists($tmp_path)) {
            rename($tmp_path, $file_path);
        }

        return true;
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
        if (self::$enabled !== true) {
            return false;
        }

        //проверка типов аргументов
        if (is_string($keyword)) {
        } else {
            dtimer::log(__METHOD__ . ' wrong argument type', 2);
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
        if ($method !== 'msgpack' && !empty(self::$config['codepage'])) {
            $codepage = self::$config['codepage'];
            dtimer::log(__METHOD__ . " converting codepage $codepage to utf-8 ");
            $value = @iconv($codepage, "utf-8", $value);
        }
        switch ($method) {
            case 'json':
                $data = $this->decode($value);
                break;

            case 'serialize':
            default:
                $data = unserialize($value);
                break;

            case 'var_export':
                @eval('$data = ' . $value . ';');
                break;

            case 'msgpack':
                $data = msgpack_unpack($value);
                break;
        }


        if (isset($data)) {
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
        if (self::$enabled !== true) {
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
        if (self::$enabled !== true) {
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
        $res = $this->db->result_array('value');

        if ($res !== false) {
            return $res;
        } else {
            return false;
        }
    }
}
