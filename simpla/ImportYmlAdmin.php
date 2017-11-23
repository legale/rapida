<?PHP
require_once('api/Simpla.php');

class ImportYmlAdmin extends Simpla 
{
	
	public $import_files_dir = 'simpla/files/import/';
	public $import_file = 'import.yml';
	public $import_file_csv = 'import.csv';
	public $allowed_extensions = array('yml', 'gz');
	
	private $columns = array(
		'name'=>             'название', 
		'url'=>              'адрес',
		'visible'=>          'видим',
		'featured'=>         'рекомендуемый',
		'category'=>         'категория',
		'brand'=>            'бренд',
		'variant'=>          'вариант',
		'price'=>            'цена',
		'currency'=>         'currency',
		'weight'=>           'weight',
		'compare price'=>    'старая цена',
		'sku'=>              'артикул',
		'stock'=>            'склад',
		'meta title'=>       'заголовок страницы',
		'meta keywords'=>    'ключевые слова',
		'meta description'=> 'описание страницы',
		'annotation'=>       'краткое описание',
		'description'=>      'описание',
		'images'=>           'изображения',
		
	);   
	
	
	private $columns_min = array(
		'name'=>             '###1###', 
		'url'=>              '###2###',
		'visible'=>          '###3###',
		'featured'=>         '###4###',
		'category'=>         '###5###',
		'brand'=>            '###6###',
		'variant'=>          '###7###',
		'price'=>            '###8###',
		'currency'=>         '###9###',
		'weight'=>           '###10###',
		'compare price'=>    '###11###',
		'sku'=>              '###12###',
		'stock'=>            '###13###',
		'meta title'=>       '###14###',
		'meta keywords'=>    '###15###',
		'meta description'=> '###16###',
		'annotation'=>       '###17###',
		'description'=>      '###18###',
		'images'=>           '###19###',
		
	);   
	
	private $columns_compared = array(
		'offer_id' => 'sku',
		'offer_available' => 'visible',
		'price' => 'price',
		'oldprice' => 'compare price',
		'categoryId' => 'category',
		'picture' => 'images',
		'stock' => 'stock',
		'vendor' => 'brand',
		'model' => 'name',
		'description' => 'description',
	);
		
	
	public function fetch() {
		$this->design->assign('import_files_dir', $this->import_files_dir);
		if(!is_writable($this->import_files_dir)) {
			$this->design->assign('message_error', 'no_permission');
		}
		
		// Проверяем локаль
		if(setlocale(LC_ALL, 0) != $this->config->locale)
		{
			$this->design->assign('message_error', 'locale_error');
			$this->design->assign('locale', $this->config->locale);			
		}

		
		//проверяем загрузился ли файл
		if($this->request->method('post') && ($this->request->files("file") || $this->request->post("file_url"))) {
			
			//если не указан удаленный файл, берем загруженный файл из post запроса
			$temp = tempnam($this->import_files_dir, 'temp_');
			
			if(!$this->request->post("file_url")){
				$uploaded_name = $this->request->files("file", "tmp_name");
				if(!$file_ok = move_uploaded_file($uploaded_name, $temp)) {
					$this->design->assign('message_error', 'upload_error');
				}
			}else{
				$file_url = $this->request->post("file_url");
				if(!$file_ok = @copy($file_url, $temp)){
					$this->design->assign('message_error', 'download_error');
				}
			}
			//print "$file_ok";
			
			if($file_ok && $is_gzip = $this->is_gzip($temp)){
				$temp2 = $temp; 
				$temp = tempnam($this->import_files_dir, 'temp_');
				$gzopen = gzopen($temp2, "r");
				$fopen = fopen($temp, "w");
				//разжимаем куски по 2мб и пишем
				while (!feof($gzopen)) {
				$data = gzread($gzopen, 2097152);
				fwrite($fopen, $data);
				}
				fclose($fopen);
				gzclose($gzopen);
				unlink($temp2);
			}
			
			
			//проверяем XML ли это файл у нас получился
			$is_xml = $this->is_xml($temp);
			if($file_ok && !$is_xml){
				$content = file_get_contents($temp, null, null, null, 50);
				$this->design->assign('message_error', "XML ERROR. WRONG HEADER '$content'");
			}else{
				if(!$this->convert_file($temp, $this->import_files_dir.$this->import_file)) {
					$this->design->assign('message_error', 'convert_error');
				} 
			}
			unlink($temp);
		}elseif($this->request->method('post') && $this->request->post("file_fields")){
			//выбираем из YML валюты 
			$yml_currencies = $this->get_yml_currencies($this->import_files_dir.$this->request->post("file_fields"));
			//сохраним валюты для дальнейшей работы
			if(!empty($yml_currencies)){
				$_SESSION['yml_currencies'] = $yml_currencies;
			}
			
			//выбираем из YML названия полей
			$yml_params = $this->get_yml_offers_params($this->import_files_dir.$this->request->post("file_fields"));
			//print_r($yml_params);
			
			//выбираем имеющиеся параметры из базы
			$this->db->query("SELECT name FROM __features ORDER BY position");
			$features = $this->db->results('name');
			
			//добавляем переменные для доступа к ним из шаблона tpl
			$this->design->assign('features',  $features);
			$this->design->assign('yml_params',  $yml_params);
			$this->design->assign('yml_currencies',  $yml_currencies);
			$this->design->assign('columns',  $this->columns);
			$this->design->assign('columns_compared',  $this->columns_compared);
		}

		//проверяем открыта страница с post запросом и у нас валидный XML
		if($this->request->method('post') && $this->is_xml($this->import_files_dir.$this->import_file)){
				$filename_yml_size = $this->human_filesize(filesize($this->import_files_dir.$this->import_file));
				//$filename_yml_size = $this->import_files_dir.$this->import_file;
				$this->design->assign('filename_yml',  $this->import_file);
				$this->design->assign('filename_yml_size',  $filename_yml_size);
		}

		
		if($this->request->method('post') && $this->request->post("start_import_yml")){
			//var_export($_POST);
			
			//берем названия полей из POST запроса
			$yml_params = $_POST['yml_params'];
			//убираем прочие служебные значения из массива (остаются только сами параметры)
			//unset($yml_params['session_id'], $yml_params['start_import_yml'], $yml_params['convert_only'], $yml_params['yml_import_currency']); 
			
			//print_r($yml_params);

			foreach($yml_params as $k=>$p){
				if(mb_substr($p, 0, 6) == 'param_'){
					$yml_params[$k] = mb_substr($p, 6);
				}
			}
			//print_r($yml_params);
			
			
			if($_POST['convert_only'] == 1){
				$this->design->assign('convert_only',  1);
			}
			
			if(isset($_POST['yml_import_currency'])){
				$yml_currencies = $_SESSION['yml_currencies'];
				$yml_currency['id'] = $_POST['yml_import_currency'];
				$yml_currency['rate'] =  $yml_currencies[$yml_currency['id']]['rate'];
				//создаем валюту, если у нас такой еще нет (стандартный метод get_currency, работает криво, 
				//изменять стандартный метод не стал, поэтому пришлось городить такой огород)

				$db_currency = $this->money->get_currency($yml_currency['id']);
				if(!isset($db_currency['code']) || $db_currency['code'] != $yml_currency['id']){
					$this->money->add_currency(array(
					'name' => $yml_currency['id'], 'sign' => $yml_currency['id'],
					 'code' => $yml_currency['id'], 
					 'rate_from' => '1.00', 'rate_to' => '1.00'));
					if( $yml_currency['dbid'] = $this->money->get_currency($yml_currency['id']) ) {
						$yml_currency['dbid'] = $yml_currency['dbid']['id'];
					}
				} else {
					$yml_currency['dbid'] = $db_currency['id'];
				}
		
				
			}

			$yml_params = $this->add_minimum_params($yml_params, $this->columns_min);
			$this->convert_yml_to_csv($this->import_files_dir.$this->import_file, $this->import_files_dir.$this->import_file_csv, $yml_params, $yml_currency , $yml_currencies );

			$filename_csv_size = $this->human_filesize(filesize($this->import_files_dir.$this->import_file_csv));
			$this->design->assign('filename_csv',  $this->import_file_csv);
			$this->design->assign('filename_csv_size',  $filename_csv_size);
		}
		

		
		return $this->design->fetch('import_yml.tpl');
	}
	
	
	private function is_gzip($realpath) {
		$mystery_string = file_get_contents($realpath, null, null, null, 50);
		
		if(mb_strpos($mystery_string , "\x1f" . "\x8b" . "\x08") !== false){;
			return true;
		}
	} 
	
	private function is_xml($realpath) {
		$mystery_string = trim(@file_get_contents($realpath, null, null, null, 50));
			
		if (mb_strpos($mystery_string , '<?xml') !== false){
			return true;
		}
	} 


	private function human_filesize($bytes, $decimals = 2) {
		$sz = 'BKMGTP';
		$factor = floor((strlen($bytes) - 1) / 3);
		return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
	}   
	 
	private function convert_file($source, $dest) {
		// Узнаем какая кодировка у файла
		$teststring = file_get_contents($source, null, null, null, 1000000);
		
		// Кодировка - UTF8 
		if (mb_detect_encoding($teststring, 'UTF-8')) {
			// Просто копируем файл
			return copy($source, $dest);
			//если кодировка CP1251
		} elseif(mb_detect_encoding($teststring, 'CP1251')) {
			// Конвертируем в UFT8
			if(!$src = fopen($source, "r")) {
				return false;
			}
			
			if(!$dst = fopen($dest, "w")) {
				return false;
			}
			
			while (($line = fgets($src, 4096)) !== false) {
				$line = $this->win_to_utf($line);
				fwrite($dst, $line);
			}
			fclose($src);
			fclose($dst);
			return true;
		}
	}
	
	private function win_to_utf($text) {
		if(function_exists('iconv')) {
			return @iconv('windows-1251', 'UTF-8', $text);
		} else {
			$t = '';
			for($i=0, $m=strlen($text); $i<$m; $i++) {
				$c=ord($text[$i]);
				if ($c<=127) {$t.=chr($c); continue; }
				if ($c>=192 && $c<=207)    {$t.=chr(208).chr($c-48); continue; }
				if ($c>=208 && $c<=239) {$t.=chr(208).chr($c-48); continue; }
				if ($c>=240 && $c<=255) {$t.=chr(209).chr($c-112); continue; }
				//				if ($c==184) { $t.=chr(209).chr(209); continue; };
				//				if ($c==168) { $t.=chr(208).chr(129);  continue; };
				if ($c==184) { $t.=chr(209).chr(145); continue; }; #ё
				if ($c==168) { $t.=chr(208).chr(129); continue; }; #Ё
				if ($c==179) { $t.=chr(209).chr(150); continue; }; #і
				if ($c==178) { $t.=chr(208).chr(134); continue; }; #І
				if ($c==191) { $t.=chr(209).chr(151); continue; }; #ї
				if ($c==175) { $t.=chr(208).chr(135); continue; }; #ї
				if ($c==186) { $t.=chr(209).chr(148); continue; }; #є
				if ($c==170) { $t.=chr(208).chr(132); continue; }; #Є
				if ($c==180) { $t.=chr(210).chr(145); continue; }; #ґ
				if ($c==165) { $t.=chr(210).chr(144); continue; }; #Ґ
				if ($c==184) { $t.=chr(209).chr(145); continue; }; #Ґ
			}
			return $t;
		}
	}
	
	

	private function get_yml_offers_params($xmlfile, $pics_concat = true) {
		$key = '';
		
		//открываем XML
		$xml = new XMLReader;
		$xml->open($xmlfile);
		
		while($xml->read() && $xml->name !== 'offer'){
		}
		
		
		
		while($xml->name === 'offer'){
			$node = new SimpleXMLElement($xml->readOuterXML());
			
				$names = array();
				
				//эти поля создаем вручную, т.к. они являются аттрибутами самого $node, а не внутри его элементов
				$fields['offer_id'] = '';
				$fields['offer_available'] = '';
					
				foreach($node as $elem) {
					$elem_name = (string)$elem->getName();
					//изменим имя тега url, чтобы не было совпадения с полем url в okayCMS
					if($elem_name == 'url'){
						$elem_name = '#url#';
					}
								
					//отдельная запись повторяющихся элементов или склеивание элементов picture
					if($pics_concat == true && in_array($key, array('picture', 'image'))){
						$key = $elem_name;
					}else{
						if(array_key_exists($elem_name,$names)){
							$key = $elem_name.count($names[$elem_name]);
						}else{
							$key = $elem_name;
						}
					}

					//этот массив для подсчета количества повторяющихся элементов
					$names[$elem_name][] = '';
					
					if ($elem->attributes()) {
						$attribs = array();
						foreach ( $elem->attributes() as $nnn){
							$attribs[] = $nnn;
						}
						$attribs = $elem_name.'_'.implode(', ', $attribs);
						$fields[$attribs] = ''  ;
					} else {
						$fields[$key] = '' ;
					}
				}
		
			$xml->next('offer');
		}
		
		return $fields;
		
	}

	private function convert_yml_to_csv($xmlfile, $csvfile, $fields, $currency = array(), $currencies = array(), $pics_concat = true,  $codepage = null) {
		if(!is_string($xmlfile) || !is_string($csvfile) || !is_array($fields)){
			error_log("required arguments missing!", 0);
			return false;
		}
		
		
		//удаляем массив $currency и $currencies, если любое из условий сработало
		if(empty($currencies) || !isset($currencies[$currency['id']]) || !is_string($currency['id']) || !is_numeric($currency['rate'])){
			unset($currency, $currencies);
		}
		
		
		//удалим пустые элементы из массива
		$fields = array_filter($fields);		
		
		//кодировка файла
		$codepages = array('UTF-8', 'CP1251');
		$codepage = strtoupper($codepage);
		
		//Если кодировка задана, будет выполняться конвертация
		if(isset($codepage) && in_array($codepage, $codepages)){
			$convert_flag = true;
		}

		//удаляем csvfile, если он уже существует
		if (file_exists($csvfile)) {
			@unlink($csvfile);
		}
		//открываем csvfile на запись
		
		//если не удается открыть файл на запись, останавливаем функцию
		if(!$fcsv = fopen($csvfile,'w')){
			return false;
		}
		
		//делаем шаблон строки для записи в файл
		$flip = array_flip($fields);
		foreach($fields as $k=>$v){
			$row_tpl[$k] = '';
		}
		//print_r($row_tpl);
		
		
		//пишем 1 строку с заголовками
		fputcsv ( $fcsv , $fields , ";"); 
		
		//открываем XML
		$xml = new XMLReader;
		$xml->open($xmlfile);
	
		//выбираем категории из XML файла
		$yml_categories = $this->get_yml_categories($xmlfile);
		
		
		$opened = array();
		while($xml->read() && $xml->name !== 'offer'){
		}
		
		while($xml->name === 'offer'){
			//делаем заготовку строки из шаблона $row_tpl
			$row = $row_tpl;
			//print_r($row);
			//преобразуем узел offer из XML в SimpleXMLElement объект
			$node = new SimpleXMLElement($xml->readOuterXML());
			
				//массив для названий элементов, повторяющиеся названия элементов будут записываться с разными именами
				$names = array();
				
				
				//пишем аттрибуты самого $node
				$row['offer_id'] = (string)$node->attributes()['id'];
				$av = (string)$node->attributes()['available'];
				if($av == 'true'){
					$row['offer_available'] = 1;
				}else{
					$row['offer_available'] = 0;
				}
				
				foreach($node as $elem) {
					$elem_name = (string)$elem->getName();
					//изменим имя тега url, чтобы не было совпадения с полем url в okayCMS
					if($elem_name == 'url'){
						$elem_name = '#url#';
					}								
								
					//отдельная запись повторяющихся элементов или склеивание элементов picture
					if($pics_concat == true && $elem_name == 'picture'){
						$key = $elem_name;
					}else{
						if(array_key_exists($elem_name,$names)){
							$key = $elem_name.count($names[$elem_name]);
						}else{
							$key = $elem_name;
						}
					}

					//этот массив для подсчета количества повторяющихся элементов
					$names[$elem_name][] = '';
					
					
					if ($elem->attributes()) {
						$attribs = array();
						foreach ( $elem->attributes() as $nnn){
							$attribs[] = $nnn;
						}
						$attribs = $elem_name.'_'.implode(', ', $attribs);
						//пишем, только если такой заголовок есть у нас в $row
						if(isset($row[$attribs])){
							$row[$attribs] = (string)$elem ;
							$p = $row[$attribs];
						}
					} else {
						//пишем, только если такой заголовок есть у нас в $row
						if(isset($row[$key])){
							if($pics_concat == true && in_array($key, array('picture', 'image'))){
								if(!is_array($row[$key])){
									$row[$key] = array();
								}
								$row[$key][] = (string)$elem ;
							}else{
								$row[$key] = (string)$elem ;
							}
						}
					}
				}
				
				//соединяем картинки через запятую
				if($pics_concat == true && is_array($row['picture'])){
					$row['picture'] = implode(',' , $row['picture']);
				}
				
				
			//меняем цену на выбранную из файла валют
			//если массив $currency не задан, не выполняем никаких преобразований
			
			
			//print_r($currencies);
			if(isset($currencies) && isset($row['currencyId'])){
				if(is_numeric($currencies[$row['currencyId']]['rate'])){
					if(isset($row['price'])){
						$row['price'] = $row['price'] * $currencies[$row['currencyId']]['rate'] / $currency['rate'];
					}
					
					if(isset($row['oldprice'])){
						$row['oldprice'] = floatval($row['oldprice']) * $currencies[$row['currencyId']]['rate'] / $currency['rate'];
					}
					if(isset($row['currencyId'])){
						$row['currencyId'] = $currency['id'];
					}
				}
			}
			//добавим значение id валюты, по умолчанию - 0
			if(isset($currency['dbid']) && isset($row[$flip['currency']])){
				$row[$flip['currency']] = $currency['dbid'];
			} else {
				$row[$flip['currency']] = 0;
			}
			

			
			if(isset($row['categoryId'])){
				$row['categoryId'] = $yml_categories[$row['categoryId']]['path'];
			}

			//print_r($row);
			// пишем строку в csvfile
			//print "";
			fputcsv ( $fcsv , $row , ";"); 
				
			//переходим к следующему узлу <offer>
			$xml->next('offer');
		}
		//закрываем файл
		fclose($fcsv);
		
		if(file_exists($csvfile)){
			return true;
		}else{
			return false;
		}
		
	}

	private function add_minimum_params($params1, $params2) {
		if(!is_array($params1) || !is_array($params2) || empty($params1) || empty($params1)){
			return false;
		}
		$params2 = array_diff(array_flip($params2), $params1);
		
		$merged = array_merge($params1, $params2);
		return $merged;
	}

	private function get_yml_currencies($xmlfile) {
		$xml = new XMLReader;
		$xml->open($xmlfile);
		
		$opened = array();
		while($xml->read() && $xml->name !== 'currencies'){
		}
		
		$node = new SimpleXMLElement($xml->readOuterXML());
		//print_r($node);
		foreach($node as $k=>$n){
			$id = (string)$n->attributes()['id'];
			$rate = (string)$n->attributes()['rate'];
			
			
			$cats[$id]['id'] = $id;
			$cats[$id]['rate'] = $rate;


		}
		
		return $cats;
		
	}

	function get_yml_categories($xmlfile) {
		$xml = new XMLReader;
		$xml->open($xmlfile);
		
		$opened = array();
		while($xml->read() && $xml->name !== 'categories'){
		}
		
		$node = new SimpleXMLElement($xml->readOuterXML());
		//print_r($node);
		foreach($node as $k=>$n){
			$id = (string)$n->attributes()['id'];
			$parent_id = (string)$n->attributes()['parentId'];
			
			
			$cats[$id]['id'] = $id;
			$cats[$id]['name'] = (string)$n;
			
			
			if(!empty($parent_id)){
				$cats[$id]['parent_id'] = $parent_id;
			} else {
				$cats[$id]['parent_id'] = 0;
			}
			

		}

		// Дерево категорий
		$tree = array();
		$tree['subcategories'] = array();
		
		// Указатели на узлы дерева
		$pointers = array();
		$pointers[0] = &$tree;
		$pointers[0]['path'] = array();
		$pointers[0]['level'] = 0;
		

		$finish = false;
		while(!empty($cats)  && $finish === false) {
			$flag = false;
			// Проходим все выбранные категории
			foreach($cats as $k=>$category) {
				if(isset($pointers[$category['parent_id']])) {
					// В дерево категорий (через указатель) добавляем текущую категорию
					$pointers[$category['id']] = $pointers[$category['parent_id']]['subcategories'][] = $category;
					
					// Путь к текущей категории
					$curr[0] = $category['name'];
					$pointers[$category['id']]['path'] = implode("/", array_merge((array)$pointers[$category['parent_id']]['path'], $curr));
					
					// Уровень вложенности категории
					$pointers[$category['id']]['level'] = 1+$pointers[$category['parent_id']]['level'];
					
					// Убираем использованную категорию из массива категорий
					unset($cats[$k]);
					$flag = true;
				}
			}
			if($flag === true){
				$finish = true;
			}
		}

		
	
		
		
		return $pointers;
		
	}



}

