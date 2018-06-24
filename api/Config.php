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


    public $version = '0.0.13b1';

    public $root_dir;
    public $root_url;
    public $cli;

    //слова для формирования соли, которая используется для усиления стойкости шифрования
    public $salt_word = 'sale marino. il sale iodato. il sale e il pepe. solo il sale.';

    // Файл для хранения настроек
    private $config_filename = 'config.php';
    private $config_path;
    private $config_length;
    private $vars = array();

    public function __destruct()
    {
        // сохраним конфиг
        $this->save();
    }

    public function __construct()
    {
        //определяем был ли запуск из командной строки
        $this->cli = (php_sapi_name() === 'cli') ? true : false;
        // Определяем корневую директорию сайта
        $this->root_dir = dirname(dirname(__FILE__)) . '/';

        $this->config_path = dirname(dirname(__FILE__)) . '/config/' . $this->config_filename;
        // Читаем настройки из дефолтного файла
        $config_content = file_get_contents($this->config_path);
        $this->config_length = strlen($config_content);
        $this->vars = eval($config_content);
        unset($config_content);


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

        // Максимальный размер загружаемых файлов1
        $max_upload = (int)(ini_get('upload_max_filesize'));
        $max_post = (int)(ini_get('post_max_size'));
        $memory_limit = (int)(ini_get('memory_limit'));
        $max_upload_filesize = min($max_upload, $max_post, $memory_limit) * 1024 * 1024;
        return $max_upload_filesize;

    }

    // Магическим методов возвращаем нужную переменную
    public function &__get($name)
    {
        dtimer::log(__METHOD__ . "get $name");
        if (!array_key_exists($name, $this->vars)) {
            $this->vars[$name] = '';
        }
        return $this->vars[$name];
    }

    // Магическим методов задаём нужную переменную
    public function __set($name, $value)
    {
        // Запишем конфиги
        $this->vars[$name] = $value;
    }

    private function save()
    {
        dtimer::log(__METHOD__ . " start");
        $content = 'return ' . var_export($this->vars, true) . ';';

        if ($this->config_length === strlen($content)) {
            dtimer::log(__METHOD__ . " generated config equals saved. Nothing to save.");
            return true;
        }


        error_log("saving config...");
        return file_put_contents($this->config_path, $content);
    }

}

