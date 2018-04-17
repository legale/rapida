<?PHP

/**
 * Rapida CMS
 *
 * @link 		http://github.com/legale/rapida
 * @author 		@Rumilan
 *
 */

// Засекаем время
$time_start = microtime(true);

//запускаем наш главный контроллер, он выберет, что делать дальше
require_once('api/Simpla.php');
$simpla = new Simpla();

$simpla->root->action();

	//Выход из админки
	if(isset($_GET['logout']))
	{
		unset($_SESSION['admin']);
		header("Location: ");
		die;
	}
// Отладочная информация

	//показываем отладочную информацию из dtimer
	if(dtimer::$enabled){
		dtimer::show();
	}
	
	if( isset($_SESSION['admin']) ){
		$time_end = microtime(true);
		$exec_time = $time_end-$time_start;
		$out = "<!--\r\n";
		if(function_exists('memory_get_peak_usage')){
			$out .= "memory peak usage: ".convert(memory_get_peak_usage(true))."\r\n";
		}
			$out .= "page generation time: ".convert_time($exec_time)."\r\n";
			$out .= "-->";
			print $out;
	}
