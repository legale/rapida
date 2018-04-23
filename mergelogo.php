<?php

function is_url( $text )  
{  
	//273 - FILTER_VALIDATE_URL 131072 - FILTER_FLAG_HOST_REQUIRED
    return filter_var( $text, 273, 131072) !== false;  
}



function url_exists($url){
    $ch = curl_init($url);    
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	
	$code = (int)substr($code, 0, 1);

	
    if(in_array($code, array(2,3))){
       $status = true;
    }else{
      $status = false;
    }
    curl_close($ch);
   return $status;
}


function LoadImg($src, $logo, $text = null)
{
	if( !file_exists($logo) ){
		header('HTTP/1.0 404 Not Found');
		return false;
	}

	$src = trim($src,'/');		
	if(!is_url($src) && !file_exists($src) ){
		$src = 'http://' . $_SERVER['SERVER_NAME'] . '/' . $src;
		if(url_exists($src)){
			$tmp = tempnam( sys_get_temp_dir() , 'tmp_');
			copy($src, $tmp);
			$src = $tmp;
		}else{
			header('HTTP/1.0 404 Not Found');
			return false;	
		}
	}

	
	// логотип
	$logo = imagecreatefrompng($logo);
	// ширина логотипа
	$logo_width = imagesx($logo);

	// высота логотипа
	$logo_height = imagesy($logo);

	// изм. размера в процентах
	$percent = '';
	// тип итогового изображения
	$res_type = 'png';

	if(!list($w, $h) = getimagesize($src)) return "Unsupported picture type!";

	// получаем изображение из исходного файла в зависимости от типа файла
	$type = image_type_to_mime_type(exif_imagetype($src));

	switch($type){
		case 'image/bmp': $image = imagecreatefromwbmp($src); break;
		case 'image/gif': $image = imagecreatefromgif($src); break;
		case 'image/jpeg': $image = imagecreatefromjpeg($src); break;
		case 'image/png': $image = imagecreatefrompng($src); break;
		default : return "Unsupported picture type!";
	}

	// тип содержимого
	header("Content-Type: image/".$res_type);
	// получение новых размеров
	list($width, $height) = getimagesize($src);

	$percent = 1;


	$new_width = $width * $percent;
	$new_height = $height * $percent;

	$dst_x = ($new_width - $width) / 2 ;
	$dst_y = ($new_height - $height) / 2 ;

	// создаем черный квадрат
	$square = imagecreatetruecolor($new_width, $new_height);
	//устанавливаем цвет RGB
	$color = imagecolorallocate($square, 255, 90, 35);
	// создаем итоговый квадрат с заданным цветом
	imagefilledrectangle($square, 0, 0, $new_width, $new_height, $color);


	// ресэмплирование imagecopyresampled(итоговое изображение, накладываемое изображение, коорд. точки на итог. изобр.,
	// коорд. точки на накл. изобр, шир. и выс. на итог. изобр., шир. и выс. исх. изобр)
	imagecopyresampled($square, $image, $dst_x, $dst_y, 0, 0, $width, $height, $width, $height);

	//накладываем изображение логотипа на изображение
	$newlogo_width = $width * 0.3;
	$ratio = $logo_width / $newlogo_width;
	$newlogo_height = $logo_height / $ratio;

	imagecopyresampled($square, $logo, $width -  ($width * 0.02 + $newlogo_width), $height - ($height * 0.02 + $newlogo_height), 0, 0, $newlogo_width, $newlogo_height, $logo_width, $logo_height);
	
	if(isset($text)) {
		$white = imagecolorallocate ($square, 255, 255, 255);
		imagettftext ($square, 60, 0, $width * 0.02, $height - $height * 0.03, $white, "captcha/verdana.ttf", $text);
	}
	
	
	// вывод
switch($res_type){
    case 'bmp': return imagewbmp($square, null, 100); break;
    case 'gif': return imagegif($square, null, 100); break;
    case 'jpg': return imagejpeg($square, null, 100); break;
    case 'png': return imagepng($square, null, 9, PNG_NO_FILTER); break;
    default : return "Unsupported picture type!";
	}

}


$img = LoadImg($_GET['p'], $_GET['l'], @$_GET['text']);

