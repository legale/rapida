<?PHP
chdir('..');

// Засекаем время
$time_start = microtime(true);
session_start();
$_SESSION['id'] = session_id();

@ini_set('session.gc_maxlifetime', 86400); // 86400 = 24 часа
@ini_set('session.cookie_lifetime', 0); // 0 - пока браузер не закрыт

require_once ('simpla/IndexAdmin.php');

// Кеширование в админке нам не нужно
Header("Cache-Control: no-cache, must-revalidate");
header("Expires: -1");
Header("Pragma: no-cache");


// Установим переменную сессии, чтоб фронтенд нас узнал как админа
$_SESSION['admin'] = 'admin';

$backend = new IndexAdmin();
$simpla = new Simpla();

// Проверка сессии для защиты от xss
if (!$backend->request->check_session())
	{
	unset($_POST);
	trigger_error('Session expired', E_USER_WARNING);
}


print $backend->fetch();

// Отладочная информация
if (1)
	{
	//показываем отладочную информацию из dtimer
	if ($simpla->config->dtimer_disabled === false) {
		dtimer::show();
	}

	print "<!--\r\n";

	$time_end = microtime(true);
	$exec_time = $time_end - $time_start;

	if (function_exists('memory_get_peak_usage'))
		print "memory peak usage: " . memory_get_peak_usage() . " bytes\r\n";
	print "page generation time: " . $exec_time . " seconds\r\n";
	print "-->";
}
