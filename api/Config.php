<?php
if(defined('PHP7')) {
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
class Config
{

    public $version = '0.0.13';

    //слова для формирования соли, которая используется для усиления стойкости шифрования
    public $salt_word = 'sale marino. il sale iodato. il sale e il pepe. solo il sale.';

    // Файл для хранения настроек
    private $config_filename = 'config.php';
    protected $config_path;
    protected $vars = array();

    public function __construct()
    {
        $this->config_path = dirname(dirname(__FILE__)) . '/config/' . $this->config_filename;
        // Читаем настройки из дефолтного файла
        $this->vars = include($this->config_path);


        //определяем был ли запуск из командной строки
        $this->vars['cli']= (php_sapi_name() === 'cli') ? true : false;
        // Определяем корневую директорию сайта
        $this->vars['root_dir'] = dirname(dirname(__FILE__)) . '/';
        // Определяем адрес (требуется для отправки почтовых уведомлений)
        if (isset($_SERVER['HTTP_HOST'])) {
            $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] ? 'https' : 'http';
            //~ print_r($_SERVER);
            $this->vars['root_url'] = $scheme . '://' . $_SERVER['HTTP_HOST'];
            if (!isset($this->vars['host']) || $_SERVER['HTTP_HOST'] !== $this->vars['host']) {
                $this->__set('host', $_SERVER['HTTP_HOST']);
            }

        }
        // Максимальный размер загружаемых файлов
        $max_upload = (int)(ini_get('upload_max_filesize'));
        $max_post = (int)(ini_get('post_max_size'));
        $memory_limit = (int)(ini_get('memory_limit'));
        $this->vars['max_upload_filesize'] = min($max_upload, $max_post, $memory_limit) * 1024 * 1024;

        // Часовой пояс
        if (!empty($this->vars['timezone'])) {
            date_default_timezone_set($this->vars['timezone']);
        }elseif (!ini_get('date.timezone')) {
            date_default_timezone_set('UTC');
        }
    }

    // Магическим методов возвращаем нужную переменную
    public function &__get($name)
    {
        if(!array_key_exists($name, $this->vars)){
            $this->vars[$name] = null;
        }
        return $this->vars[$name];
    }

    // Магическим методов задаём нужную переменную
    public function __set($name, $value)
    {
        // Запишем конфиги
        $this->vars[$name] = $value;
        // сохраним
        $this->save();
    }

    private function save(){
        $content = '<?php return ' . var_export($this->vars, true) . ';';
        $h = fopen($this->config_path, 'w');
        if(flock($h, LOCK_EX)){
            fwrite($h, $content);
            return fclose($h);
        }else{
            return false;
        }
    }

}

