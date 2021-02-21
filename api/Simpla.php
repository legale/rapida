<?php


/**
 * benchmark function for scoring function perfomance by cycling it given times
 * @return bool|mixed
 */
function bmark()
{
    $args = func_get_args();
    $len = count($args);

    if ($len < 3) {
        trigger_error("At least 3 args expected. Only $len given.", 256);
        return false;
    }

    $cnt = array_shift($args);
    $fun = array_shift($args);

    $start = microtime(true);
    $i = 0;
    while ($i < $cnt) {
        $i++;
        $res = call_user_func_array($fun, $args);
    }
    $end = microtime(true) - $start;
    return $end;
}


function parse_url_($url)
{
    $res = array();

    //scheme
    $pos = stripos($url, '://');
    if (!$pos) {
        return false;
    }
    $res['scheme'] = substr($url, 0, $pos);
    $url = substr($url, $pos + 3);
    if ($url === '') {
        return false;
    }
    $pos = stripos($url, '@');

    //auth
    if ($pos) {
        $auth = substr($url, 0, $pos);
        $url = substr($url, $pos + 1);

        $pos = stripos($auth, ':');
        if ($pos) {
            $res['user'] = substr($auth, 0, $pos);
            $res['pass'] = substr($auth, $pos + 1);
        } else {
            $res['user'] = $auth;
        }
    }
    if ($url === '') {
        return false;
    }

    //fragment
    $pos = stripos($url, '#');
    if ($pos) {
        $res['fragment'] = substr($url, $pos + 1);
        $url = substr($url, 0, $pos);
        //return $url;
    }
    if ($url === '') {
        return false;
    }
    //query
    $pos = stripos($url, '?');
    if ($pos) {
        $res['query'] = substr($url, $pos + 1);
        $url = substr($url, 0, $pos);
        //return $url;
    }
    if ($url === '') {
        return false;
    }
    //host
    $pos = stripos($url, '/');
    if ($pos) {
        $res['host'] = substr($url, 0, $pos);
        $url = substr($url, $pos);
        //return $url;
        //port
        $pos = stripos($res['host'], ':');
        if ($pos) {
            $res['port'] = substr($res['host'], $pos + 1);
            $res['host'] = substr($res['host'], 0, $pos);
        }
    } else {
        return false;
    }

    //path
    if ($url !== '') {
        $res['path'] = $url;
    }

    return $res;
}


//функция для обратного преобразования массива parse_url() в строку
/**
 * @param $parsed_url
 * @return string
 */
function unparse_url($parsed_url)
{
    $scheme = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
    $host = isset($parsed_url['host']) ? $parsed_url['host'] : '';
    $port = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
    $user = isset($parsed_url['user']) ? $parsed_url['user'] : '';
    $pass = isset($parsed_url['pass']) ? ':' . $parsed_url['pass'] : '';
    $pass = ($user || $pass) ? "$pass@" : '';
    $path = isset($parsed_url['path']) ? $parsed_url['path'] : '';
    $query = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
    $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
    return "$scheme$user$pass$host$port$path$query$fragment";
}

//функция для конвертации величин измерения информации
function convert(int $size, string $unit = null): ?string
{
    $units = ['b', 'kb', 'mb', 'gb', 'tb', 'pb']; 

    if($unit && $i = array_flip($units)[$unit]){}
    else{
		$i = (int)floor(log($size, 1024));
	}
    return round($size / pow(1024, $i), 2) . $units[$i];
}


function return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    switch($last) {
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }

    return $val;
}

//функция для конвертации времени, принимает значения в секундах

/**
 * @param $time
 * @return int|string
 */
function convert_time($time)
{
    if ($time == 0) {
        return 0;
    }
    //допустимые единицы измерения
    $unit = array(-4 => 'ps', -3 => 'ns', -2 => 'mcs', -1 => 'ms', 0 => 's');
    //логарифм времени в сек по основанию 1000
    //берем значение не больше 0, т.к. секунды у нас последняя изменяемая по тысяче величина, дальше по 60
    $i = (int)min(0, floor(log($time, 1000)));

    //тут делим наше время на число соответствующее единицам измерения т.е. на миллион для секунд,
    //на тысячу для миллисекунд
    $t = round($time / pow(1000, $i), 1);
    /** @noinspection PhpIllegalArrayKeyTypeInspection */
    return $t . $unit[$i];
}

//нужная функция empty_ работает иначе чем нативная empty
//нативная empty(0) выдает true, наша функция empty(0) выдает - false
/**
 * @param $var
 * @return bool
 */
function empty_($var)
{
    if (!empty($var) || $var === 0 || $var === '0') {
        return false;
    } else {
        return true;
    }
}

//удаляем неразрывный пробел
function filter_spaces($str)
{
    return trim(preg_replace("/\s+/u", ' ', $str));
}

//меняем местами пробелы и подчеркивания
function convert_spaces($str, $reverse = false)
{
    $pairs = $reverse ? array_flip(array(' ' => '_', '_' => ' ')) : array(' ' => '_', '_' => ' ');
    return strtr($str, $pairs);
}

//фильтруем все непечатаемые символы
function filter_ascii($str)
{
    return preg_replace('/[^[:ascii:]а-яА-ЯёЁ]/u', '', $str);
}

//заменяет в строке дефисы на волну поскольку они используется системой в качестве разделителей
function encode_param($str)
{
    return strtr($str, array('-' => '~', '~' => ''));
}

//меняет обратно волну на дефис
function decode_param($str)
{
    return strtr($str, array('~' => '-'));
}


/**
 * @param $string
 * @param bool $reverse
 * @return bool|string
 */
function translit_ya($string, $reverse = false)
{
    if (!is_string($string)) {
        trigger_error(__METHOD__ . 'argument type error');
        return false;
    }

    $converter = array(
        'а' => 'a',
        'б' => 'b',
        'в' => 'v',
        'г' => 'g',
        'д' => 'd',
        'е' => 'e',
        'ё' => 'yo',
        'ж' => 'zh',
        'з' => 'z',
        'и' => 'i',
        'й' => 'j',
        'к' => 'k',
        'л' => 'l',
        'м' => 'm',
        'н' => 'n',
        'о' => 'o',
        'п' => 'p',
        'р' => 'r',
        'с' => 's',
        'т' => 't',
        'у' => 'u',
        'ф' => 'f',
        'х' => 'kh',
        'ц' => 'c',
        'ч' => 'ch',
        'ш' => 'sh',
        'щ' => 'shh',
        'ь' => '\'',
        'ы' => 'yi',
        'ъ' => '\'\'',
        'э' => 'eh',
        'ю' => 'yu',
        'я' => 'ya',
        ' ' => '_',
        '/' => '__',
        '%' => '_pct_',
    );
    if ($reverse === true) {
        $converter = array_flip($converter);
    }
    $string = mb_strtolower($string);
    $string = filter_spaces(filter_ascii($string));

    return strtr($string, $converter);
}

//каталог настоящего файла
define('API_DIR', dirname(__FILE__) . '/');

//корень сайта
define('ROOT_DIR', dirname(API_DIR) . '/');


// отладчик ошибок
require_once(API_DIR . 'Dtimer.php');

//*****************************************************************************
/*
 * Основной класс для доступа к API моделей Rapida
 *
 */

/**
 * @property bool|mixed config
 * @property bool|mixed cache
 */
class Simpla
{
    // Свойства - Классы API
    private $classes = array(
        'config' => 'Config',
        'cache' => 'Cache',
        'request' => 'Request',
        'db' => 'Database',
        'db2' => 'Database',
        'db3' => 'Db',
        'settings' => 'Settings',
        'design' => 'Design',
        'products' => 'Products',
        'variants' => 'Variants',
        'categories' => 'Categories',
        'brands' => 'Brands',
        'features' => 'Features',
        'money' => 'Money',
        'pages' => 'Pages',
        'blog' => 'Blog',
        'cart' => 'Cart',
        'image' => 'Image',
        'delivery' => 'Delivery',
        'payment' => 'Payment',
        'orders' => 'Orders',
        'users' => 'Users',
        'coupons' => 'Coupons',
        'comments' => 'Comments',
        'feedback' => 'Feedback',
        'notify' => 'Notify',
        'slider' => 'Slider',
        'managers' => 'Managers',
        'queue' => 'Queue',
        'sys' => 'System',
        'bender' => 'Bender', //js css joiner and minifier
        'coAdmin' => 'ControllerAdmin',
        'root' => 'ControllerMaster',
        'coSimpla' => 'ControllerSimpla', /* Контроллер Симплы, которые запускает view/indexView.php */
        'coResize' => 'ControllerResize',
        'coXhr' => 'ControllerXhr',
        'curl' => 'Curl', //curl library helper
    );

    //первое обращение к классу будет хранится тут
    private static $virgin = true;

    // Созданные объекты
    private static $objects = array();

    /**
     * Конструктор оставим пустым, но определим его на случай обращения parent::__construct() в классах API
     */
    public function __construct()
    {
        if (self::$virgin !== true) {
            return null;
        }

        //убираем флаг, чтобы код ниже заводился только 1 раз
        self::$virgin = false;

        //запустим сессию, если запуск не из командной строки
        if (session_status() === PHP_SESSION_NONE && !$this->config->cli) {
            session_start();
        }

        //log ошибок
        ini_set('error_log', ROOT_DIR . $this->config->php['logfile']);
        //user-agent
        ini_set('user_agent', $this->config->user_agent);
        //уровень отображения ошибок
        error_reporting($this->config->php['error_reporting']);
        dtimer::log("error_reporting config.ini: " . $this->config->php['error_reporting'] . " error_reporting() says: " . error_reporting());
        //выключатель отладчика
        dtimer::log(__METHOD__ . " debuger");
        dtimer::$enabled = isset($_SESSION['admin']) && $this->config->debug ? true : false;
        //локаль
        setlocale(LC_ALL, $this->config->php['locale']);
        //кеш включается через статичекую переменную класса
        /** @var cache $enabled */
        $cache_class = &$this->cache;
        $cache_class::$enabled = $this->config->cache['enabled'];
        dtimer::log(__METHOD__ . " cache enabled: " . $cache_class::$enabled);

        //настраиваем очередь заданий. Из таблицы заданий queue_full задания не удаляются. Для отладки может быть полезно
        if ($this->config->cache['skip_queue_full'] === true) {
            $queue_class = &$this->queue;
            $queue_class::$skip_queue_full = true;
        }

    }

    /**
     * Магический метод, создает нужный объект API
     * @param $name
     * @return bool|mixed
     */
    public function __get($name)
    {
        // Если такой объект уже существует, возвращаем его
        if (isset(self::$objects[$name])) {
            return (self::$objects[$name]);
        }

        // Если запрошенного API не существует - ошибка
        if (!array_key_exists($name, $this->classes)) {
            return false;
        } else {
            // Определяем имя нужного класса
            $class = $this->classes[$name];
        }
        // Подключаем его
        /** @noinspection PhpIncludeInspection */
        include_once(API_DIR . $class . ".php");

        // Сохраняем для будущих обращений к нему
        self::$objects[$name] = new $class();

        // Возвращаем созданный объект
        return self::$objects[$name];
    }
}
