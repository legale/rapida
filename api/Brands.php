<?php

/**
 * Simpla CMS
 *
 * @copyright	2011 Denis Pikusov
 * @link		http://simplacms.ru
 * @author		Denis Pikusov
 *
 */

require_once ('Simpla.php');

class Brands extends Simpla
{
	public $brands_ids;

	/*
	 *
	 * Функция возвращает массив названий брендов с ключами в виде id этих брендов
	 * @param $ids array
	 *
	 */
	public function get_brands_ids($ids = null)
	{
		dtimer::log(__METHOD__ . " start");
		//переменные
		$id_filter = '';

		//фильтр

		if (is_null($ids)) {
			if(isset($this->brands_ids)){
				dtimer::log(__METHOD__ . " end");
				return $this->brands_ids;
			}
		}
		elseif (!is_null($ids) && is_array($ids) && count($ids) > 0) {
			$id_filter = $this->db->placehold("AND id in (?@)", $ids);
		}
		else {
			return false;
		}

		$q = $this->db->query("SELECT id, url FROM __brands WHERE 1 $id_filter");
		$res = $this->db->results_array('id' , 'url');
		//Если у нас был запуск без параметров, сохраним результат в переменную класса.
		if(is_null($ids)){
			$this->brands_ids = $res;
		}
		dtimer::log(__METHOD__ . " end");
		return $res;
	}

	/*
	 *
	 * Функция возвращает массив брендов, удовлетворяющих фильтру
	 * @param $filter
	 *
	 */
	public function get_brands($filter = array())
	{

		$category_id_filter = '';
		$visible_filter = '';
		$in_stock_filter = '';

		if (isset($filter['in_stock']))
			$in_stock_filter = $this->db->placehold('AND (SELECT count(*)>0 FROM __variants pv WHERE pv.product_id=p.id AND pv.price>0 AND (pv.stock IS NULL OR pv.stock>0) LIMIT 1) = ?', intval($filter['in_stock']));

		if (isset($filter['visible']))
			$visible_filter = $this->db->placehold('AND p.visible=?', intval($filter['visible']));

		if (!empty($filter['category_id']))
			$category_id_filter = $this->db->placehold("LEFT JOIN __products p ON p.brand_id=b.id LEFT JOIN __products_categories pc ON p.id = pc.product_id WHERE pc.category_id in(?@) $visible_filter $in_stock_filter", (array)$filter['category_id']);

		// Выбираем все бренды
		$query = $this->db->placehold("SELECT DISTINCT b.id, b.name, b.url, b.meta_title, b.meta_keywords, b.meta_description, b.description, b.image
								 		FROM __brands b $category_id_filter ORDER BY b.name");
		$this->db->query($query);

		return $this->db->results();
	}

	/*
	 *
	 * Функция возвращает бренд по его id или url
	 * (в зависимости от типа аргумента, int - id, string - url)
	 * @param $id id или url поста
	 *
	 */
	public function get_brand($id)
	{
		if (is_int($id)) {
			$id = "b.id = '$id'";
		}
		elseif (is_string($id)) {
			$id = "b.url = '$id'";
		}
		else {
			dtimer::log(__METHOD__ . " argument url/id is not set or wrong type! ");
			return false;
		}


		$query = "SELECT b.id, b.name, b.url, b.meta_title, b.meta_keywords, b.meta_description, b.description, b.image
								 FROM __brands b WHERE $id LIMIT 1";
		$this->db->query($query);
		return $this->db->result();
	}

	/*
	 *
	 * Добавление бренда
	 * @param $brand
	 *
	 */
	public function add_brand($brand)
	{

		if (is_object($brand)) {
			$brand = (array)$brand;
		}
		//удалим id, если он сюда закрался, при создании id быть не должно
		if (isset($brand['id'])) {
			unset($brand['id']);
		}

		foreach ($brand as $k => $e) {
			if (empty_($e)) {
				unset($brand[$k]);
			}
		}

		if (!isset($brand['url']) || empty_($brand['url'])) {
			$brand['url'] = preg_replace("/[\s]+/ui", '_', $brand['name']);
			$brand['url'] = strtolower(preg_replace("/[^0-9a-zа-я_]+/ui", '', $brand['url']));
		}


		$this->db->query("INSERT INTO __brands SET ?%", $brand);
		return $this->db->insert_id();
	}

	/*
	 *
	 * Обновление бренда(ов)
	 * @param $brand
	 *
	 */
	public function update_brand($id, $brand)
	{
		$query = $this->db->placehold("UPDATE __brands SET ?% WHERE id=? LIMIT 1", $brand, intval($id));
		$this->db->query($query);
		return $id;
	}
	
	/*
	 *
	 * Удаление бренда
	 * @param $id
	 *
	 */
	public function delete_brand($id)
	{
		if (!empty($id))
			{
			$this->delete_image($id);
			$query = $this->db->placehold("DELETE FROM __brands WHERE id=? LIMIT 1", $id);
			$this->db->query($query);
			$query = $this->db->placehold("UPDATE __products SET brand_id=NULL WHERE brand_id=?", $id);
			$this->db->query($query);
		}
	}
	
	/*
	 *
	 * Удаление изображения бренда
	 * @param $id
	 *
	 */
	public function delete_image($brand_id)
	{
		$query = $this->db->placehold("SELECT image FROM __brands WHERE id=?", intval($brand_id));
		$this->db->query($query);
		$filename = $this->db->result('image');
		if (!empty($filename))
			{
			$query = $this->db->placehold("UPDATE __brands SET image=NULL WHERE id=?", $brand_id);
			$this->db->query($query);
			$query = $this->db->placehold("SELECT count(*) as count FROM __brands WHERE image=? LIMIT 1", $filename);
			$this->db->query($query);
			$count = $this->db->result('count');
			if ($count == 0)
				{
				@unlink($this->config->root_dir . $this->config->brands_images_dir . $filename);
			}
		}
	}

}
