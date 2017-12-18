<?php
define( 'PCLZIP_TEMPORARY_DIR', './' );

require_once('api/Simpla.php');
require_once('simpla/pclzip/pclzip.lib.php');

$simpla = new Simpla();

//корневая директория системы 
$dir = dirname(__FILE__) . '/';

//имя создаваемого архива
$filename = $dir.'distro/rapida_'.$simpla->config->version.'.zip';

$dbfile = 'rapida.sql';


//функция для фильтрации того, что нужно архивировать, а что нет
function precheck($p_event, &$p_header){
	
	//это мы точно включаем
	$include = array(
		'/^design\/default\/.*/i',
		'/^simpla\/files\/backup\/empty\.zip/i',
		'/^simpla\/files\/import\/example\.csv/i',
		'/^simpla\/files\/export$/i',
		'/^simpla\/files\/watermark\/watermark\.png/i',
		'/\.htaccess$/i',
		'/^rapida\.sql$/i',
	);
	
	//это точно исключаем
	$exclude = array(
		'/\/[\.][^\/]*/i', //файлы начинающиеся с точки
		'/^[\.].*/i', //каталоги с точки
		'/^sandbox\.php$/i',
		'/^create_distro\.php$/i',
		'/^install\.php$/i',
		'/rapida.*?\.zip$/i',
		'/^img\/.*/i',
		'/^compiled\/.*/i',
		'/^simpla\/files\/.*/i',
		'/^cache\/.*/i',
		'/^distro\/.*/i',
		'/^design\/.*/i',
		'/^simpla\/design\/compiled\/.*/i',
		'/^simpla\/files\/.*/i',
		'/^config\/db.ini$/i',
	);
	
	$fname = $p_header['stored_filename'];

	foreach($include as $p){
		if(preg_match($p, $fname) ){
			return true;
		}
	}
	
	foreach($exclude as $p){
		if(preg_match($p, $fname) ){
			return false;
		}
	}
	//если не попало под фильтры, значит включаем в архив
	return true;
}



//***************************************************************************************************
//КОД ПРОЦЕССА СОЗДАНИЯ ДИСТРИБУТИВА




//Делаем дамп базы
$simpla->db->dump($dir.$dbfile);
chmod($dir.$dbfile, 0777);

//Архивируем
$zip = new PclZip('rapida_source.zip');
$v_list = $zip->create(array($dir), PCLZIP_OPT_REMOVE_PATH, $dir, PCLZIP_CB_PRE_ADD, "precheck");
if ($v_list == 0)
{
	trigger_error('Не могу заархивировать '.$zip->errorInfo(true));
}

//Архивируем полученный архив README.md и install.php
$zip = new PclZip($filename);
$v_list = $zip->create(array('README.md','install.php','rapida_source.zip'));
if ($v_list == 0)
{
	trigger_error('Не могу заархивировать '.$zip->errorInfo(true));
}

//удаляет файл архива для дистрибутива
@unlink('rapida_source.zip');

//удаляет файл БД
//~ @unlink($dir.$dbfile);

//Finish
print "\n DONE!";
