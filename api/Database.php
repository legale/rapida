<?php



/**
 * Класс для доступа к базе данных
 *
 * @copyright 	2013 Denis Pikusov
 * @link 		http://simplacms.ru
 * @author 		Denis Pikusov
 *
 */

require_once('Simpla.php');

class Database extends Simpla
{
	private $mysqli;
	private $res;

	/**
	 * В конструкторе подключаем базу
	 */
	public function __construct()
	{
		parent::__construct();
		$this->connect();
	}

	/**
	 * В деструкторе отсоединяемся от базы
	 */
	public function __destruct()
	{
		$this->disconnect();
	}

	/**
	 * Подключение к базе данных
	 */
	public function connect($force_reconnect = null) {
		
		// При повторном вызове возвращаем существующий линк
		if(!empty($this->mysqli) && !$force_reconnect)
			return $this->mysqli;
		// Иначе устанавливаем соединение
		else
			$this->mysqli = new mysqli($this->config->db_server, $this->config->db_user, $this->config->db_password, $this->config->db_name);
	
		// Выводим сообщение, в случае ошибки
		if($this->mysqli->connect_error)
		{
			trigger_error("Could not connect to the database: ".$this->mysqli->connect_error, E_USER_WARNING);
			return false;
		}
		// Или настраиваем соединение
		else
		{
			if($this->config->db_charset)
				$this->mysqli->query('SET NAMES '.$this->config->db_charset);
			if($this->config->db_sql_mode)
				$this->mysqli->query('SET SESSION SQL_MODE = "'.$this->config->db_sql_mode.'"');
			if($this->config->db_timezone)
				$this->mysqli->query('SET time_zone = "'.$this->config->db_timezone.'"');
		}

		// записываем в переменную $started время начала соединения в секундах от времени начала эпохи unix 01.01.1970
		$this->started = time();
		
		return $this->mysqli;
	}


	/**
	 * Закрываем подключение к базе данных
	 */
	public function disconnect()
	{
		if(!@$this->mysqli->close())
			return true;
		else
			return false;
	}
	

	/**
	 * Запрос к базе. Обазятелен первый аргумент - текст запроса.
	 * При указании других аргументов автоматически выполняется placehold() для запроса с подстановкой этих аргументов
	 */
	public function query() {
		
		//получаем переданные аргументы
		$args = func_get_args();
		//считаем аргументы и проверяем тип 1 аргумента (должна быть строка)
		$cnt = count($args);
		if($cnt < 1 || !is_string($args[0]) ){
			$this->error_msg = "Query error - empty query";
			return false;
		} elseif ($cnt > 1){
			$q = call_user_func_array(array($this, 'placehold'), $args);
		}else{
			$q = $args[0];
		}

		
		//для долгих перерывов между запросами обновляем соединение с БД
		if (time() - $this->started > 45 ){
			//trigger_error("Mysql ping сработал", E_USER_WARNING);
			$this->connect(true);
		}
		
		//освобождаем память от предыдущего запроса. 
		if(is_object($this->res)){
			$this->res->free();
		}
		
		$q = $this->placehold($q);
		
		//обновляем время с последнего запроса
		$this->started = time();
		$this->res = $this->mysqli->query($q);
		
		if($this->mysqli->affected_rows == -1){
			$this->error_msg = "Query error $q ";
			dtimer::log(__METHOD__ . " query error: $q ");
			return false;
		}else{
			dtimer::log(__METHOD__ . " query completed: $q ");
			return true;
		}
	}

	/**
	 *  Экранирование
	 */
	public function escape($str)
	{
		return $this->mysqli->real_escape_string($str);
	}

	
	/**
	 * Плейсхолдер для запросов. Пример работы: $query = $db->placehold('SELECT name FROM products WHERE id=?', $id);
	 */
	public function placehold()
	{
		$args = func_get_args();	
		$tmpl = array_shift($args);
		// Заменяем все __ на префикс, но только необрамленные кавычками
		$tmpl = preg_replace('/([^"\'0-9a-z_])__([a-z_]+[^"\'])/i', "\$1".$this->config->db_prefix."\$2", $tmpl);
		if(!empty($args))
		{
			$result = $this->sql_placeholder_ex($tmpl, $args, $error); 
			if ($result === false)
			{ 
				$error = "Placeholder substitution error. Diagnostics: \"$error\""; 
				trigger_error($error, E_USER_WARNING); 
				return false; 
			} 
			return $result;
		}
		else
			return $tmpl;
	}
	

	/**
	 * Возвращает результаты запроса. Необязательный второй аргумент указывает какую колонку возвращать 
	 * вместо всего массива колонок
	 */
	public function results($field = null, $group_field = null) {
		if(empty($this->res) || $this->res->num_rows == 0){
			return false;
		}
		
		if(!is_object($this->res)){
			$this->error_msg = "exec query first!";
		}
		
		$results = array();

		if($field !== null){
			while($row = $this->res->fetch_object()){
				array_push($results, $row->$field);
			}
		}elseif($group_field !== null){
			while($row = $this->res->fetch_object()){
				$results[$row->$group_field] = $row;
			}
		}else{
			while($row = $this->res->fetch_object()){
				array_push($results, $row);
			}
		}
		return $results;
	}


	/**
	 * Возвращает результаты запроса в виде объекта. Если указан 1 аргумент, то будет выведен одномерный объект
	 * с ключами 1,2,3,4 и значения указанного в аргументе столбца. Если указан 2 аргумент, то 
	 * результат будет собран в объект с ключами по указанному в аргументе столбцу
	 */
	public function results_object($field = null, $group_field = null, $unsetkey = false) {
		
		if(empty($this->res) || $this->res->num_rows == 0){
			return false;
		}
		
		if(!is_object($this->res)){
			$this->error_msg = "exec query first!";
		}
		
		$results = new stdClass();

		if($field !== null){
			for($i = 0; $row = $this->res->fetch_object(); $i++){
				$results->{$i} = $row->{$field};
			}
		}
		elseif($group_field !== null){
			while($row = $this->res->fetch_object()){
				$key = $row->$group_field;
				if($unsetkey === true){
					unset($row->$group_field);
				}
				$results->$key = $row;
			}
		}else{
			for($i = 0; $row = $this->res->fetch_object(); $i++){
				$results->{$i} = $row;
			}
		}
		return $results;
	}

	/**
	 * Возвращает результаты запроса ассоциативным массивом
	 * может вывести только 1 конкретное поле одномерным массивом, для этого указывается 1 аргумент (string),
	 * также может вывести массив с ключами из указанного поля БД, для этого указывается 2 аргумент (string)
	 */
	public function results_array($field = null, $group_field = null, $unsetkey = false) {
		if(empty($this->res) || $this->res->num_rows == 0){
			return false;
		}
		
		if(!is_object($this->res)){
			$this->error_msg = "exec query first!";
		}
		
		$results = array();
		
		if($field !== null){
			while($row = $this->res->fetch_array(MYSQLI_ASSOC)){
				array_push($results, $row[$field]);
			}
		}elseif($group_field !== null){
			while($row = $this->res->fetch_array(MYSQLI_ASSOC)){
				$key = $row[$group_field];
				if($unsetkey === true){
					unset($row[$group_field]);
				}
				$results[$key] = $row;
			}
		}else{
			while($row = $this->res->fetch_array(MYSQLI_ASSOC)){
				array_push($results, $row);
			}
		}
		return $results;
	}

	/**
	 * Возвращает первый результат запроса. Необязательный второй аргумент указывает какую колонку возвращать вместо всего массива колонок
	 */
	public function result($field = null)
	{
		$result = array();
		if(!$this->res)
		{
			return false;
		}
		$row = $this->res->fetch_object();
		if(!empty($field) && isset($row->$field))
			return $row->$field;
		elseif(!empty($field) && !isset($row->$field))
			return false;
		else
			return $row;
	}

	/**
	 * Возвращает первый результат запроса в виде массива
	 */
	public function result_array($field = null, $group_field = null, $unsetkey = false)
	{
		if(!is_object($this->res)){
			$this->error_msg = "exec query first!";
			return false;
		}
		
		$row = $this->res->fetch_array(MYSQLI_ASSOC);
		
		if( isset($field) ){
			return $row[$field];
		} else {
			if($group_field !== null && $unsetkey === true){
				unset($row[$group_field]);
			}
			return $row;
		}
	}

	/**
	 * Возвращает последний вставленный id
	 */
	public function insert_id()
	{
		return $this->mysqli->insert_id;
	}

	/**
	 * Возвращает количество выбранных строк
	 */
	public function num_rows()
	{
		return $this->res->num_rows;
	}

	/**
	 * Возвращает количество затронутых строк
	 */
	public function affected_rows()
	{
		return $this->mysqli->affected_rows;
	}
	
	/**
	 * Компиляция плейсхолдера
	 */
	private function sql_compile_placeholder($tmpl)
	{ 
		$compiled = array(); 
		$p = 0;	 // текущая позиция в строке 
		$i = 0;	 // счетчик placeholder-ов 
		$has_named = false; 
		while(false !== ($start = $p = strpos($tmpl, "?", $p)))
		{ 
			// Определяем тип placeholder-а. 
			switch ($c = substr($tmpl, ++$p, 1))
			{ 
				case '!': case '%': case '^': case '&': case '@': case '#': 
					$type = $c; ++$p; break; 
				default: 
					$type = ''; break; 
			} 
			// Проверяем, именованный ли это placeholder: "?keyname" 
			if (preg_match('/^((?:[^\s[:punct:]]|_)+)/', substr($tmpl, $p), $pock))
			{ 
				$key = $pock[1]; 
				if ($type != '#')
					$has_named = true; 
				$p += strlen($key); 
			}
			else
			{ 
				$key = $i; 
				if ($type != '#')
					$i++; 
			} 
			// Сохранить запись о placeholder-е. 
			$compiled[] = array($key, $type, $start, $p - $start); 
		} 
		return array($compiled, $tmpl, $has_named); 
	} 

	/**
	 * Выполнение плейсхолдера
	 */
	private function sql_placeholder_ex($tmpl, $args, &$errormsg)
	{ 
		// Запрос уже разобран?.. Если нет, разбираем. 
		if (is_array($tmpl))
			$compiled = $tmpl; 
		else
			$compiled	 = $this->sql_compile_placeholder($tmpl); 
	
		list ($compiled, $tmpl, $has_named) = $compiled; 
	
		// Если есть хотя бы один именованный placeholder, используем 
		// первый аргумент в качестве ассоциативного массива. 
		if ($has_named)
			$args = @$args[0]; 
	
		// Выполняем все замены в цикле. 
		$p	 = 0;		// текущее положение в строке 
		$out = '';		// результирующая строка 
		$error = false; // были ошибки? 
	
		foreach ($compiled as $num=>$e)
		{ 
			list ($key, $type, $start, $length) = $e; 
	
			// Pre-string. 
			$out .= substr($tmpl, $p, $start - $p); 
			$p = $start + $length; 
	
			$repl = '';		// текст для замены текущего placeholder-а 
			$errmsg = ''; // сообщение об ошибке для этого placeholder-а 
			do { 
				// Это placeholder-константа? 
				if ($type === '#')
				{ 
					$repl = @constant($key); 
					if (NULL === $repl)	 
						$error = $errmsg = "UNKNOWN_CONSTANT_$key"; 
					break; 
				} 
				// Обрабатываем ошибку. 
				if (!isset($args[$key]))
				{ 
					$error = $errmsg = "UNKNOWN_PLACEHOLDER_$key"; 
					break; 
				} 
				// Вставляем значение в соответствии с типом placeholder-а. 
				$a = $args[$key]; 
				
				//Если тип не задан, то это должен быть скалярный тип т.е. не массив или объект
				if ($type === '')
				{ 
					// Скалярный placeholder. 
					if (!is_scalar($a))
					{ 
						$error = $errmsg = "NOT_A_SCALAR_PLACEHOLDER_$key"; 
						break; 
					} 
					$repl = is_int($a) || is_float($a) ? str_replace(',', '.', $a) : "'".$this->db->escape($a)."'"; 
					break; 
				}
				
				//Если тип не задан, то это должен быть скалярный тип т.е. не массив или объект
				if ($type === '!')
				{ 
					// Скалярный placeholder. 
					if (!is_scalar($a))
					{ 
						$error = $errmsg = "NOT_A_SCALAR_PLACEHOLDER_$key"; 
						break; 
					} 
					$repl = "`".$this->db->escape($a)."`"; 
					break; 
				}
				 
				// Если это объект - сделаем его массивом
				if(is_object($a)){
					$a = (array)$a;
				}
				//Если в итоге не получился массив - покажем ошибку
				if (!is_array($a))
				{ 
					$error = $errmsg = "NOT_AN_ARRAY/OBJECT_PLACEHOLDER_$key"; 
					break; 
				} 
				// Это список со значениями полей. 
				if ($type === '@')
				{ 
					foreach ($a as $v)
					{
						if(is_null($v))
							$r = "NULL";
						else
							$r = "'".$this->db->escape($v)."'";

						$repl .= ($repl===''? "" : ",").$r; 
					}
				// Это список с названиями столбцов
				}elseif ($type === '^')
				{ 
					foreach ($a as $v)
					{
						if(is_null($v))
							$r = "NULL";
						else
							$r = '`'.$this->db->escape($v).'`';

						$repl .= ($repl===''? "" : ",").$r; 
					}
				}
				// Это набор пар `название поля` = 'значение поля' для конструкции SET
				elseif ($type === '%')
				{ 
					$lerror = array(); 
					foreach ($a as $k=>$v)
					{ 
						if (!is_string($k) && !is_int($k)){
							$lerror[$k] = "NOT_A_STRING_OR_INTEGER_KEY_{$k}_FOR_PLACEHOLDER_$key"; 
						}else{ 
							$k = '`'.$this->db->escape($k).'`'; 
						}
						
						if(is_null($v))
							$r = "=NULL";
						else
							$r = "='".$this->db->escape($v)."'";

						$repl .= ($repl===''? "" : ", ").$k.$r; 
					}  
					// Если была ошибка, составляем сообщение. 
					if (count($lerror) > 0)
					{ 
						$repl = ''; 
						foreach ($a as $k=>$v)
						{ 
							if (isset($lerror[$k]))
							{ 
								$repl .= ($repl===''? "" : ", ").$lerror[$k]; 
							}
							else
							{ 
								$k = $this->db->escape($k); 
								$repl .= ($repl===''? "" : ", ").$k."=?"; 
							} 
						} 
						$error = $errmsg = $repl; 
					} 
				} 
				// Это набор пар `название поля` = 'значение поля' для конструкции WHERE
				elseif ($type === '&')
				{ 
					$lerror = array(); 
					foreach ($a as $k=>$v)
					{ 
						if (!is_string($k) && !is_int($k)){
							$lerror[$k] = "NOT_A_STRING_OR_INTEGER_KEY_{$k}_FOR_PLACEHOLDER_$key"; 
						}else{ 
							$k = '`'.$this->db->escape($k).'`'; 
						}
						
						if(is_null($v))
							$r = "=NULL";
						else
							$r = "='".$this->db->escape($v)."'";

						$repl .= ($repl===''? "" : " AND ").$k.$r; 
					}  
					// Если была ошибка, составляем сообщение. 
					if (count($lerror) > 0)
					{ 
						$repl = ''; 
						foreach ($a as $k=>$v)
						{ 
							if (isset($lerror[$k]))
							{ 
								$repl .= ($repl===''? "" : ", ").$lerror[$k]; 
							}
							else
							{ 
								$k = $this->db->escape($k); 
								$repl .= ($repl===''? "" : ", ").$k."=?"; 
							} 
						} 
						$error = $errmsg = $repl; 
					} 
				} 
			} while (false); 
			if ($errmsg) $compiled[$num]['error'] = $errmsg; 
			if (!$error) $out .= $repl; 
		} 
		$out .= substr($tmpl, $p); 
	
		// Если возникла ошибка, переделываем результирующую строку 
		// в сообщение об ошибке (расставляем диагностические строки 
		// вместо ошибочных placeholder-ов). 
		if ($error)
		{ 
			$out = ''; 
			$p	 = 0; // текущая позиция 
			foreach ($compiled as $num=>$e)
			{ 
				list ($key, $type, $start, $length) = $e; 
				$out .= substr($tmpl, $p, $start - $p); 
				$p = $start + $length; 
				if (isset($e['error']))
				{ 
					$out .= $e['error']; 
				}
				else
				{ 
					$out .= substr($tmpl, $start, $length); 
				} 
			} 
			// Последняя часть строки. 
			$out .= substr($tmpl, $p); 
			$errormsg = $out; 
			return false; 
		}
		else
		{ 
			$errormsg = false; 
			return $out; 
		} 
	} 

	public function dump($filename, $skip_create = false)
	{
		$h = fopen($filename, 'w');
		$q = $this->placehold("SHOW FULL TABLES LIKE '__%';");		
		$result = $this->mysqli->query($q);
		while($row = $result->fetch_row()){
			if($row[1] == 'BASE TABLE'){
				$this->dump_table($row[0], $h, $skip_create);
			}
		}
	    fclose($h);
	}
	
	function restore($filename)
	{
		$templine = '';
		$h = fopen($filename, 'r');
	
		// Loop through each line
		if($h)
		{
			while(!feof($h))
			{
				$line = fgets($h);
				// Only continue if it's not a comment
				if (substr($line, 0, 2) != '--' && $line != '')
				{
					// Add this line to the current segment
					$templine .= $line;
					// If it has a semicolon at the end, it's the end of the query
					if (substr(trim($line), -1, 1) == ';')
					{
						// Perform the query
						$this->mysqli->query($templine) or print('Error performing query \'<b>'.$templine.'</b>\': '.$this->mysqli->error.'<br/><br/>');
						// Reset temp variable to empty
						$templine = '';
					}
				}
			}
		}
		fclose($h);
	}
	
	
	private function dump_table($table, $h, $skip_create = true){
		//массив для типов полей
		$types = array();
		//Числовые поля
		$types['num'] = array(
			'int',
			'tinyint',
			'smallint',
			'mediumint',
			'bigint',
			'decimal',
			'double',
			'float',
			'real',
			'bit',
			'boolean',
			'serial',
		);
		//символьные поля (кроме bin)
		$types['sym'] = array(
			'char',
			'varchar',
			'varchar',
			'text',
			'tinytext',
			'mediumtext',
			'longtext',
			'blob',
			'tinyblob',
			'mediumblob',
			'longblob',
			'enum',
			'set',
		);
		//двоичные
		$types['bin'] = array(
			'binary',
			'varbinary',
		);
		//получаем поля таблицы, чтобы узнать типы полей
		$q = "SHOW FULL FIELDS FROM `$table`;";
		$this->db->query($q);
		//уложим из в массив с ключом по названию поля
		$cols = $this->db->results_array(null, 'Field');
		//теперь для каждого поля запишем способ последущей записи его значений
		foreach($cols as &$col){
			//отрежем все после скобок, если они есть
			if ( $pos = stripos($col['Type'], '(') ) {
				$col['Type'] = substr($col['Type'], 0, $pos);
			}
			
			//в зависимости от типа, запишем группу типа
			if( in_array($col['Type'], $types['num']) ) {
				$col['type_group'] = 'num';
			} elseif ( in_array($col['Type'], $types['sym']) ) {
				$col['type_group'] = 'sym';
			} elseif ( in_array($col['Type'], $types['bin']) ) {
				$col['type_group'] = 'bin';
			} else {
				$col['type_group'] = 'else';
			} 
			
		}
		unset($col);
		
		//Если параметр $skip_create = false - пропускаем создание таблицы
		if ( $skip_create === false ) {
			//удаляем таблицу, если она есть
			fwrite($h, "/* Drop for table $table */\n");
			fwrite($h, "DROP TABLE IF EXISTS `$table`;\n");
			
			//получаем и записываем выражение для создания таблицы
			$this->db->query("SHOW CREATE TABLE `$table`;");
			$create = $this->db->result_array('Create Table');
			fwrite($h, "/* Create table $table */\n");
			fwrite($h, "$create;\n");
		} else {
			fwrite($h, "/* \$skip_create set true Truncate table $table */\n");
			fwrite($h, "TRUNCATE TABLE `$table`;\n");
		}
		
		//Здесь идут данные для таблицы
		fwrite($h, "/* Data for table $table */\n");
		
		//запрос на получение всех данных таблицы
		$this->db->query("SELECT * FROM `$table`");

		if($this->db->num_rows() > 0){
			//Запишем начало запроса и названия полей, если у нас есть что-то в таблице
			$fields = $this->db->placehold('?^', array_keys($cols) ); 
			fwrite($h,  "INSERT INTO `$table` ($fields) VALUES\n");
		}

		//обрабатываем построчно
		
		//флаг для первой строки
		$flag = true;
		while( $row = $this->db->result_array() ){
			
			/* 
			 * Теперь пишем сами значения, каждая строка пишется в круглых скобках через запятую.
			 * Будем писать каждое значение в зависимости от его типа данных. 
			 * Символьные (кроме bin) char, varchar, text, blob, set и др. будем писать через 
			 * экранирование спецсимволов в одинарных кавычках. 
			 * bin будем писать в виде hex строки. 0xcc23865436abc431007759e15a11991a
			 * Числа int будем писать без кавычек.
			 * Все остальное в одинарных кавычках
			 */
			 
			//В зависимости от группы типа поля запишем каждое значение для строки соответствующим образом
			
			foreach($cols as $name=>$col){
				//Если null - ставим null и переходим к следующему циклу
				if( is_null($row[$name]) ) {
					$row[$name] = 'NULL';
					continue;
				}elseif($row[$name] === ''){
					$row[$name] ="''";
					continue;
				}
				switch ($col['type_group']) {
					case 'int':
						break;
					case 'sym':
						$row[$name] = "'" . $this->db->escape($row[$name]) . "'";
						break;
					case 'bin':
						$row[$name] = '0x' . bin2hex($row[$name]);
						break;
					case 'else':
						$row[$name] = "'" . $row[$name] . "'";
						break;
				}
			}
			unset($name, $col);

			//А теперь запишем в файл через запятую и в круглых скобках
			$row = implode(', ', $row);
			if($flag === true){
				//это первая строка
				$vals = "(". $row . ")";
				$flag = false;
			} else {
				//это для записи всех строк, кроме первой
				$vals = ",\n(". $row . ")";
			}
			
			fwrite($h,  $vals);
		}
		
		//запишем точку с запятой и перенос с последней строки
		fwrite($h,  ";\n");
	
	return true;
	}
	
}

