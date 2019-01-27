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


    public $version = 'rapida v0.0.21';

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
        // Читаем настройки из дефолтного файла
        if(function_exists("apcu_fetch") &&
            apcu_exists($this->host."config")) {
            $this->vars = apcu_fetch($this->host."config");
        }else {
            $flock = false;
            $retries = 0;
            $max_retries = 20;

            $fp = fopen($this->config_path, 'r');
            while(!$flock && $retries <= $max_retries) {
                $flock = flock($fp, LOCK_SH);
                ++$retries;
                usleep(rand(1, 500));
            }

            // couldn't get the lock, give up
            if ($retries == $max_retries) {
                fclose($fp);
                return false;
            }
            $this->vars = include($this->config_path);
            flock($fp, LOCK_UN);
            fclose($fp);
        }

        // Определяем адрес (требуется для отправки почтовых уведомлений)
        if (isset($_SERVER['HTTP_HOST'])) {
            $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] ? 'https' : 'http';
            //~ print_r($_SERVER);
            $this->root_url = $scheme . '://' . $_SERVER['HTTP_HOST'];

            if (!isset($this->vars['host']) || $_SERVER['HTTP_HOST'] !== $this->vars['host']) {
                $this->__set('host', $_SERVER['HTTP_HOST']);
            }
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
        $this->modified = true;
    }

    public function __destruct()
    {
        if($this->modified) $this->save();
    }

    public function save()
    {
        $flock = false;
        $content = '<?php return ' . var_export($this->vars, true) . ';';
        if(function_exists("apcu_store")) {
            apcu_store($this->host."config", $this->vars);
        }
        $retries = 0;
        $max_retries = 20;

        $fp = fopen($this->config_path, 'w');
        while(!$flock && $retries <= $max_retries) {
            $flock = flock($fp, LOCK_EX);
            ++$retries;
            usleep(rand(1, 500));
        }

        // couldn't get the lock, give up
        if ($retries == $max_retries) {
            fclose($fp);
            return false;
        }


        $res = fwrite($fp, $content);
        flock($fp, LOCK_UN);
        fclose($fp);
        return $res;

    }



}

