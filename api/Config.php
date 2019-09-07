<?php
if (defined('PHP7')) {
    eval("declare(strict_types=1);");
}

/**
 * Класс-обертка для конфигурационного файла с настройками магазина
 * В отличие от класса Settings, Config оперирует низкоуровневыми настройками, например найстройками базы данных.
 */

require_once('Simpla.php');


/**
 * Class Config
 */
class Config extends Simpla
{


    public $version = 'rapida v0.0.24';

    public $root_dir;
    public $root_url;
    public $cli;

    //слова для формирования соли, которая используется для усиления стойкости шифрования
    public $salt_word = 'sale marino. il sale iodato. il sale e il pepe. solo il sale.';

    // Файл для хранения настроек
    private $config_filename = 'config.php';
    private $config_path;
    private $vars = array();
    private $modified = false;


    public function __construct()
    {
        //определяем был ли запуск из командной строки
        $this->cli = (php_sapi_name() === 'cli') ? true : false;
        // Определяем корневую директорию сайта
        $this->root_dir = dirname(dirname(__FILE__)) . '/';
        $this->config_path = $this->root_dir . 'config/' . $this->config_filename;
        
        $this->vars = $this->read();
        

        // Определяем адрес (требуется для отправки почтовых уведомлений)
        if (isset($_SERVER['HTTP_HOST'])) {
            $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] ? 'https' : 'http';

            $this->root_url = $scheme . '://' . $_SERVER['HTTP_HOST'];
        }

        // Часовой пояс
        if (!empty($this->vars['timezone'])) {
            date_default_timezone_set($this->vars['timezone']);
        } elseif (!ini_get('date.timezone')) {
            date_default_timezone_set('UTC');
        }
    }


    public function max_upload_filesize()
    {

        // Максимальный размер загружаемых файлов
        $max_upload = (int)(ini_get('upload_max_filesize'));
        $max_post = (int)(ini_get('post_max_size'));
        $memory_limit = (int)(ini_get('memory_limit'));
        $max_upload_filesize = min($max_upload, $max_post, $memory_limit) * 1024 * 1024;
        return $max_upload_filesize;

    }

    public function &__get($name)
    {
        if (!array_key_exists($name, $this->vars)) {
            $this->vars[$name] = '';
        }
        return $this->vars[$name];
    }

    public function __set($name, $value)
    {
        // Запишем конфиги
        $this->vars[$name] = $value;
        dtimer::log( __METHOD__ , 2);
        $this->modified = true;
    }


    //для вывода следа в журнал

    /**
     * @param $bt
     * @return bool
     */
    public function debug_backtrace($bt)
    {
        $bt = array_column(array_slice($bt, 0, 2), 'function', 'class');
        if (!empty($bt)) {
            $bt = var_export($bt, true);
            dtimer::log(" backtrace: $bt", 1);
        }
        return false;
    }


    public function __destruct()
    {
        if($this->modified) $this->save();
    }

	public function read(): ?array
	{
		dtimer::log(__METHOD__ . " start");
		//$this->debug_backtrace(debug_backtrace());
        // Читаем настройки из дефолтного файла
        if(function_exists("apcu_fetch")) {
			dtimer::log(__METHOD__ . " apcu check. apcu is installed");
            if(!apcu_exists(__FILE__)){
				dtimer::log(__METHOD__ . " " . __FILE__ . " not in apcu cache");
			} else {
				$res = apcu_fetch(__FILE__);

				dtimer::log(__METHOD__ . " cache data is fetched");	
				return is_array($res) ? $res : null;			
			}
		}	
	
		$flock = false;
		$retries = 0;
		$max_retries = 20;

		$fp = fopen($this->config_path, 'r');
		while(!$flock && $retries <= $max_retries) {
			$flock = flock($fp, LOCK_SH);
			++$retries;
			usleep(rand(1, 500));
		}

		if (!$flock) {
			fclose($fp);
			return null;
		}
		@eval('$res = ' . fread($fp, 99999) . ';');
		dtimer::log(__METHOD__ . " after fread and eval");
		
		flock($fp, LOCK_UN);
		fclose($fp);

        if(function_exists("apcu_store")) {
            apcu_store(__FILE__, $res);
			dtimer::log(__METHOD__ . " apcu cache saved");
        }
	
		dtimer::log(__METHOD__ . " end");
		return is_array($res) ? $res : null; 
	}

    public function save(): bool
    {
        $flock = false;
        $content = var_export($this->vars, true);
        if(function_exists("apcu_store")) {
            apcu_store(__FILE__, $this->vars);
        }
        $retries = 0;
        $max_retries = 20;

        $fp = fopen($this->config_path, 'w');
		if(!$fp) return false;
        while(!$flock && $retries <= $max_retries) {
            $flock = flock($fp, LOCK_EX);
            ++$retries;
            usleep(rand(1, 500));
        }

        // couldn't get the lock, give up
        if (!$flock) {
            fclose($fp);
            return false;
        }

        $res = fwrite($fp, $content);
        flock($fp, LOCK_UN);
        fclose($fp);
        
        $test = $this->read();
        
        return  $res && $content == $test ? $res : false;

    }

}

