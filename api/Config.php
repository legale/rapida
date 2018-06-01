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
    /**
     * @var string
     */
    public $version = '0.0.12';

    //слова для формирования соли
    /**
     * @var string
     */
    public $salt_word = 'sale marino. il sale iodato. il sale e il pepe. solo il sale.';

    // Файлы для хранения настроек
    /**
     * @var string
     */
    public $config_file = '';
    /**
     * @var string
     */
    public $db_config_file = '';

    /**
     * @var array
     */
    private $vars = array();
    /**
     * @var array
     */
    public $vars_sections = array();

    // В конструкторе записываем настройки файла в переменные этого класса
    // для удобного доступа к ним. Например: $simpla->config->db_user
    /**
     * Config constructor.
     */
    public function __construct()
    {
        dtimer::log(__METHOD__ . " config construct ");
        $this->config_file = dirname(__FILE__) . '/../config/config.ini';
        $this->db_config_file = dirname(__FILE__) . '/../config/db.ini';


        // Читаем настройки из файлов с секциями
        $configs = array(
            $this->config_file => parse_ini_file($this->config_file, true),
            $this->db_config_file => parse_ini_file($this->db_config_file, true)
        );

        // Записываем настройки как переменную класса

        foreach ($configs as $file => $ini) {
            if (is_array($ini)) {
                foreach ($ini as $section => $content) {
                    foreach ($content as $name => $value) {
                        if ($value < 2 && $value == strval((int)$value)) {
                            $value = (bool)$value;
                        }
                        $this->vars_sections[$section][$name] = $value;
                        $this->vars[$name] = array('value' => $value, 'section' => $section, 'file' => $file);
                    }
                }
            }
        }
        //~ print "<PRE>";
        //~ var_dump($this->vars);
        //~ print "</PRE>";


        //определяем был ли запуск из командной строки
        $this->vars['cli']['value'] = (php_sapi_name() === 'cli') ? true : false;

        // Определяем корневую директорию сайта
        $this->vars['root_dir']['value'] = dirname(dirname(__FILE__)) . '/';

        // Определяем адрес (требуется для отправки почтовых уведомлений)
        if (isset($_SERVER['HTTP_HOST'])) {
            $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] ? 'https' : 'http';
            //~ print_r($_SERVER);
            $this->vars['root_url']['value'] = $scheme . '://' . $_SERVER['HTTP_HOST'];

            if (!isset($this->vars['host']) || $_SERVER['HTTP_HOST'] !== $this->vars['host']['value']) {
                $this->__set('host', $_SERVER['HTTP_HOST']);
            }

            //отправляем заголовки из конфига
            //header($this->vars['http_headers']['value']);
        }


        // Максимальный размер загружаемых файлов
        $max_upload = (int)(ini_get('upload_max_filesize'));
        $max_post = (int)(ini_get('post_max_size'));
        $memory_limit = (int)(ini_get('memory_limit'));
        $this->vars['max_upload_filesize'] = min($max_upload, $max_post, $memory_limit) * 1024 * 1024;

        // Соль для повышения надежности хеширования
        $this->vars['salt'] = md5($this->salt_word);

        // Часовой пояс
        if (!empty($this->vars['php_timezone']['value']))
            date_default_timezone_set($this->vars['php_timezone']['value']);
        elseif (!ini_get('date.timezone'))
            date_default_timezone_set('UTC');
    }

    // Магическим методов возвращаем нужную переменную

    /**
     * @param $name
     * @return null
     */
    public function __get($name)
    {
        if (isset($this->vars[$name]['value'])) {
            return $this->vars[$name]['value'];
        } else {
            return null;
        }
    }

    // Магическим методов задаём нужную переменную

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        # Запишем конфиги
        if (isset($this->vars[$name])) {
            $conf = file_get_contents($this->vars[$name]['file']);
            $conf = preg_replace("/" . $name . "\s*=.*\n/i", $name . ' = ' . $value . ";\r\n", $conf);
            $cf = fopen($this->vars[$name]['file'], 'w');
            fwrite($cf, $conf);
            fclose($cf);
            $this->vars[$name]['value'] = $value;

        }
    }

    /**
     * @param $text
     * @return string
     */
    public function token($text)
    {
        return md5($text . $this->salt);
    }

    /**
     * @param $text
     * @param $token
     * @return bool
     */
    public function check_token($text, $token)
    {
        if (!empty($token) && $token === $this->token($text))
            return true;
        return false;
    }
}
