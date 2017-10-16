<?php

//нужная функция empty_ работает иначе чем нативная empty
//нативная empty(0) выдает true, наша функция empty(0) выдает - false
function empty_($var){
	if(!empty($var) || $var === 0){
		return false;
	}else{
		return true;
	}
}

// отладчик ошибок
require_once(dirname(__FILE__) .'/Dtimer.php');




//*****************************************************************************


/**
 * Основной класс Simpla для доступа к API Simpla
 *
 * @copyright 	2014 Denis Pikusov
 * @link 		http://simplacms.ru
 * @author 		Denis Pikusov
 *
 */

class Simpla
{
	// Свойства - Классы API
	private $classes = array(
		'config'     => 'Config',
		'cache'      => 'Cache',
		'request'    => 'Request',
		'db'         => 'Database',
		'db2'        => 'Database',
		'settings'   => 'Settings',
		'design'     => 'Design',
		'products'   => 'Products',
		'variants'   => 'Variants',
		'categories' => 'Categories',
		'brands'     => 'Brands',
		'features'   => 'Features',
		'money'      => 'Money',
		'pages'      => 'Pages',
		'blog'       => 'Blog',
		'cart'       => 'Cart',
		'image'      => 'Image',
		'delivery'   => 'Delivery',
		'payment'    => 'Payment',
		'orders'     => 'Orders',
		'users'      => 'Users',
		'coupons'    => 'Coupons',
		'comments'   => 'Comments',
		'feedbacks'  => 'Feedbacks',
		'notify'     => 'Notify',
		'managers'   => 'Managers',
		'queue'      => 'Queue'
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
		if(self::$virgin === true) {
			
			//убираем флаг, чтобы код ниже заводился только 1 раз
			
			self::$virgin = false;

			//уровень отображения ошибок
			error_reporting($this->config->error_reporting);
			dtimer::log('error_reporting config.ini ' . var_export($this->config->error_reporting, true ) );
			//выключатель отладчика
			dtimer::$disabled = $this->config->dtimer_disabled;
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
		if(isset(self::$objects[$name]))
		{
			return(self::$objects[$name]);
		}
		
		// Если запрошенного API не существует - ошибка
		if(!array_key_exists($name, $this->classes))
		{
			return null;
		}
		
		// Определяем имя нужного класса
		$class = $this->classes[$name];
		
		// Подключаем его
		include_once(dirname(__FILE__).'/'.$class.'.php');
		
		// Сохраняем для будущих обращений к нему
		self::$objects[$name] = new $class();
		
		// Возвращаем созданный объект
		return self::$objects[$name];
	}
}
