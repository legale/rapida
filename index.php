<?PHP

/**
 * Simpla CMS
 *
 * @copyright 	2011 Denis Pikusov
 * @link 		http://simp.la
 * @author 		Denis Pikusov
 *
 */

// Засекаем время
$time_start = microtime(true);
session_start();

//тут мы будем проверять xhr запросы, чтобы не грузить view
require_once('api/Simpla.php');
$simpla = new Simpla();

if (isset($_GET['xhr']) ){
	if( isset($_POST['json']) ){
		$json = json_decode($_POST['json'], true);
		
		//разрешенные классы и методы
		$allowed = array();
		$allowed['classes'] = array('products', 'brands', 'variants', 'features', 'image', 'cart', 'blog', 'comments');
		$allowed['methods'] = array('get_products', 'get_product', 'get_variants', 'get_variant', 'get_features', 
		'get_options', 'get_cart', 'get_brands', 'get_brand', 'get_comments', 'get_comment', 'get_images');
		if( 
			!empty($json['class'])
			&&  !empty($json['method'])
			&&  !empty($json['args'])
			&&  in_array($json['class'], $allowed['classes'])
			&&  in_array($json['method'], $allowed['methods'])
		){
		}else{
			print 'empty method/class/arguments or method/class not allowed';
			die;
		}
			
		if( $res = $simpla->{$json['class']}->{$json['method']}($json['args']) ) {
			header("Content-type: application/json; charset=UTF-8");
			header("Cache-Control: must-revalidate");
			header("Pragma: no-cache");
			header("Expires: -1");	
			print json_encode($res, JSON_UNESCAPED_UNICODE);
		} else {
			print 'unable to perform api request';
		}
	}
	die;
}

require_once('view/IndexView.php');


$view = new IndexView();


if(isset($_GET['logout']))
{
	unset($_SESSION['admin']);
	header("Location: ");
	die;
}

// Если все хорошо
if(($res = $view->fetch()) !== false)
{
	// Выводим результат
	header("Content-type: text/html; charset=UTF-8");	
	print $res;

	// Сохраняем последнюю просмотренную страницу в переменной $_SESSION['last_visited_page']
	if(empty($_SESSION['last_visited_page']) || empty($_SESSION['current_page']) || $_SERVER['REQUEST_URI'] !== $_SESSION['current_page'])
	{
		if(!empty($_SESSION['current_page']) && !empty($_SESSION['last_visited_page']) && $_SESSION['last_visited_page'] !== $_SESSION['current_page'])
			$_SESSION['last_visited_page'] = $_SESSION['current_page'];
		$_SESSION['current_page'] = $_SERVER['REQUEST_URI'];
	}		
}
else 
{ 
	// Иначе страница об ошибке
	header("http/1.0 404 not found");
	
	// Подменим переменную GET, чтобы вывести страницу 404
	$_GET['page_url'] = '404';
	$_GET['module'] = 'PageView';
	print $view->fetch();   
}



// Отладочная информация
if(1)
{
	//показываем отладочную информацию из dtimer
	if($simpla->config->dtimer_disabled === false){
		dtimer::show();
	}
	print "<!--\r\n";
	$time_end = microtime(true);
	$exec_time = $time_end-$time_start;
  
  	if(function_exists('memory_get_peak_usage'))
		print "memory peak usage: ".memory_get_peak_usage()." bytes\r\n";  
	print "page generation time: ".$exec_time." seconds\r\n";  
	print "-->";
}
