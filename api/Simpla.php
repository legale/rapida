<?php
//функция для конвертации величин измерения информации
function convert($size)
{
	if($size == 0 ){
		return 0;
	}
    $unit=array('b','kb','mb','gb','tb','pb');
    $i=floor(log($size,1024));
    return @round($size/pow(1024,$i) , 1).$unit[$i];
}
//функция для конвертации времени, принимает значения в секундах

function convert_time($time)
{
	if($time == 0 ){
		return 0;
	}
	//допустимые единицы измерения
    $unit=array(-4=>'ps', -3=>'ns',-2=>'mcs',-1=>'ms',0=>'s');
    //логарифм времени в сек по основанию 1000
    //берем значение не больше 0, т.к. секунды у нас последняя изменяемая по тысяче величина, дальше по 60
    $i=min(0,floor(log($time,1000)));

	//тут делим наше время на число соответствующее единицам измерения т.е. на миллион для секунд,
    //на тысячу для миллисекунд
    $t = @round($time/pow(1000,$i) , 1);
    return $t.$unit[$i];
}

//нужная функция empty_ работает иначе чем нативная empty
//нативная empty(0) выдает true, наша функция empty(0) выдает - false
function empty_($var)
{
	if (!empty($var) || $var === 0 || $var === '0') {
		return false;
	}
	else {
		return true;
	}
}

/*
 * Этот обычный транслит
 * Если второй аргумент задан true - производится детранслит
 */
function translit($string, $reverse = false)
{
	if (!is_string($string)) {
		trigger_error(__METHOD__ . 'argument type error');
		return false;
	}
	$converter = array(
		'а' => 'a', 'б' => 'b', 'в' => 'v',
		'г' => 'g', 'д' => 'd', 'е' => 'e',
		'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
		'и' => 'i', 'й' => 'j', 'к' => 'k',
		'л' => 'l', 'м' => 'm', 'н' => 'n',
		'о' => 'o', 'п' => 'p', 'р' => 'r',
		'с' => 's', 'т' => 't', 'у' => 'u',
		'ф' => 'f', 'х' => 'h', 'ц' => 'c',
		'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
		'ь' => '\'', 'ы' => 'y', 'ъ' => '\'\'',
		'э' => 'e', 'ю' => 'yu', 'я' => 'ya'
	);
	if ($reverse === true) {
		uasort($converter, function ($a, $b) {
			return strlen($b) - strlen($a);
		});
		$converter = array_flip($converter);
	}
	$string = mb_strtolower($string);

	return strtr($string, $converter);
}
/*
 * Этот транслит предназначен для формирования пригодного для url текста. Соль в том, что сохраняется возможность полной 
 * детранслитерации, в т.ч. мягкого и твердого знака, пропадает только буква ё. Мягкий знак и твердый знак в целях 
 * совместимости со стандартом RFC 3986 становяться ~ и ~~ соответственно, пробел становится подчеркиванием. Для разделения 
 * в адресной строке параметров товаров и их значений используется . и + Точкой разделены название свойства и значение,
 * плюсом разделены несколько значений у одного свойства
 * Если второй аргумент задан true - производится детранслит.
 */
function translit_url($string, $reverse = false)
{
	if (!is_string($string)) {
		trigger_error(__METHOD__ . 'argument type error');
		return false;
	}
	//тут удаляем все кроме букв, цифр и _ + ~
	$string = preg_replace("/[^\w\d\_\s\~\+]+/u", '', $string);
	

	//самая быстрая функция для замены подстроки в строке strtr пробел меняем на подчеркивание
	$pairs = array(' ' => '_', '-' => '+');
	if ($reverse === true) {
		$pairs = array_flip($pairs);
	}
	$string = strtr($string, $pairs);
	

	$converter = array(
		'а' => 'a', 'б' => 'b', 'в' => 'v',
		'г' => 'g', 'д' => 'd', 'е' => 'e',
		'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
		'и' => 'i', 'й' => 'j', 'к' => 'k',
		'л' => 'l', 'м' => 'm', 'н' => 'n',
		'о' => 'o', 'п' => 'p', 'р' => 'r',
		'с' => 's', 'т' => 't', 'у' => 'u',
		'ф' => 'f', 'х' => 'h', 'ц' => 'c',
		'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
		'ь' => '~', 'ы' => 'y', 'ъ' => '~~',
		'э' => 'eh', 'ю' => 'yu', 'я' => 'ya'
	);
	if ($reverse === true) {
		uasort($converter, function ($a, $b) {
			return strlen($b) - strlen($a);
		});
		$converter = array_flip($converter);
	}
	$string = mb_strtolower($string);

	return strtr($string, $converter);
}
    


// отладчик ошибок
require_once (dirname(__FILE__) . '/Dtimer.php');




//*****************************************************************************


/*
 * Основной класс для доступа к API моделей Rapida
 *
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
		'feedbacks' => 'Feedbacks',
		'notify' => 'Notify',
		'managers' => 'Managers',
		'queue' => 'Queue',
		'sys' => 'System',
		'bender' => 'Bender', //js css joiner and minifier
		'coAdmin' => 'ControllerAdmin',
		'coMaster' => 'ControllerMaster',
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
		if (self::$virgin === true) {
			
			//убираем флаг, чтобы код ниже заводился только 1 раз
			self::$virgin = false;
			
			//запустим сессию
			@session_start();

			//уровень отображения ошибок
			error_reporting($this->config->error_reporting);
			dtimer::log('error_reporting config.ini: ' . $this->config->error_reporting . ' error_reporting() says: ' . error_reporting());
			//выключатель отладчика
			dtimer::log(__METHOD__ . ' debuger');
			dtimer::$enabled = $this->config->debug;
			//локаль
			setlocale(LC_ALL, $this->config->locale);
		}

	}

	/**
	 * Магический метод, создает нужный объект API
	 */
	public function __get($name)
	{
		// Если такой объект уже существует, возвращаем его
		if (isset(self::$objects[$name]))
			{
			return (self::$objects[$name]);
		}
		
		// Если запрошенного API не существует - ошибка
		if (!array_key_exists($name, $this->classes))
			{
			return false;
		}
		
		// Определяем имя нужного класса
		$class = $this->classes[$name];
		
		// Подключаем его
		include_once (dirname(__FILE__) . '/' . $class . '.php');
		
		// Сохраняем для будущих обращений к нему
		self::$objects[$name] = new $class();
		
		// Возвращаем созданный объект
		return self::$objects[$name];
	}
}
