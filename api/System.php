<?php
require_once ('Simpla.php');

class System extends Simpla
{

	/*
	 * Это метод для синхронизации таблицы features и options
	 * Удаляет лишние и добавляет недостающие столбцы в таблицу options на основе id из таблицы features 
	 */
	public function sync_options()
	{
		$fids = array();
		$cols = array();
		
		//получим нужные нам id свойств из features
		if ($this->db->query("SELECT id FROM __features")) {
			$fids = $this->db->results_array('id');
		}
		else {
			return false;
		}
		//получим столбцы из таблицы options
		if ($this->db->query("SHOW COLUMNS FROM __options")) {
			if ($cols = $this->db->results_array('Field')) {
				$cols = array_combine($cols, $cols);
			}
		}
		else {
			return false;
		}
		
		//Уберем product_id из массива - это поле проверять не нужно
		if (isset($cols['product_id'])) {
			unset($cols['product_id']);
		}
		else {
			return false;
		}
		
		//проверим все свойства на предмет их наличия в таблице options
		if (is_array($fids)) {
			foreach ($fids as $fid) {
				//Если столбца нет - добавим его
				if (!array_key_exists($fid, $cols)) {
					$this->db->query("ALTER TABLE __options ADD `$fid` MEDIUMINT NULL");
				}
				else {
				//если он есть, уберем его из массива $cols
					unset($cols[$fid]);
				}
			}
		}
		//Если у нас что-то осталось в массиве $cols - надо удалить это оттуда
		if (count($cols) > 0) {
			foreach ($cols as $col) {
				$this->db->query("ALTER TABLE __options DROP `$col`");
			}
		}

		return true;

	}

	/*
	 * Данный метод предназначен для очистки таблицы options_uniq от неиспользуемых в товарах свойствах.
	 */

	public function clear_options()
	{
		//сначала надо вытащить id всех уникальных опции из таблицы options_uniq

		if ($this->db->query("SELECT `id` FROM __options_uniq")) {
			
			//так мы получим value ids $vids в виде массива с ключами в виде id значений свойств
			$vids = $this->db->results_array(null, 'id', true);
			
			//Теперь будем проверять каждую ячейку таблицы options на предмет наличия там id из $vids.
			//Если будет совпадение, то мы удалим значение из массива $vids. Оставшиеся в массиве id и будут лишними.

			if ($this->db->query("SELECT * FROM __options")) {
				while ($row = $this->db->result_array(null, 'product_id', true)) {
					foreach ($row as $vid) {
						if (array_key_exists($vid, $vids)) {
							unset($vids[$vid]);
						}
					}
				}
			}
			
			//теперь удалим лишнее
			if ($vids !== false && count($vids) > 0) {
				foreach ($vids as $vid => $v) {
					$this->db->query("DELETE `o` FROM __options_uniq AS `o` WHERE `id` = '$vid'");
				}
			}
			//Если удалось дойти до конца вернем true
			return true;

		}
		else {
			return false;
		}


	}

	/*
	 * Данный метод предназначен для очистки таблицы options_uniq от неиспользуемых в товарах свойствах.
	 */

	public function download_all_images()
	{
		$q = "SELECT DISTINCT `basename` as `f` 
		FROM __img_products 
		WHERE 1
		AND `basename` LIKE 'http:%' 
		OR `basename` LIKE 'https:%'";
		
		//получаем список подходящих файлов 
		if ($this->db->query($q)) {
			$fnames = $this->db->results_array('f');
			//перебираем список и скачиваем эти файлы
			if ($fnames) {
				foreach ($fnames as $f) {
					$this->image->download_image($f);
				}
			}
			return true;
		}
		else {
			return false;
		}
	}


}
