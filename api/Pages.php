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

class Pages extends Simpla
{
    private $tokeep = array(
        'force_no_cache',
        'visible',
        'menu_id',
    );

    public $ttl = 14440; //cache ttl

	/*
	 *
	 * Функция возвращает страницу по ее id или trans (в зависимости от типа)
	 * @param $id id или trans страницы
	 *
	 */
	public function get_page($id)
	{
	    dtimer::log(__METHOD__. " start");
		if (gettype($id) == 'string'){
			$id = trim($id, '/');
			$where = $this->db->placehold(' WHERE trans=? ', $id);
		}else{
			$where = $this->db->placehold(' WHERE id=? ', intval($id));
		}
		$query = "SELECT id, trans, header, name, meta_title, meta_description, meta_keywords, body, menu_id, pos, visible
		          FROM __pages $where LIMIT 1";

		$this->db->query($query);
		return $this->db->result_array();
	}
	
	/*
	 *
	 * Функция возвращает массив страниц, удовлетворяющих фильтру
	 * @param $filter
	 *
	 */
	public function get_pages($filter = array())
	{
        dtimer::log(__METHOD__." start ");


        //сначала уберем из фильтра лишние параметры, которые не влияют на результат, но влияют на хэширование
        dtimer::log(__METHOD__ . " start filter: " . var_export($filter, true));
        $filter = array_intersect_key($filter, array_flip($this->tokeep));
        dtimer::log(__METHOD__ . " filtered filter: " . var_export($filter, true));
        $filter_ = $filter;
        if (!empty($filter_['force_no_cache'])) {
            $force_no_cache = true;
            unset($filter_['force_no_cache']);
        } else{
            $force_no_cache = false;
        }

        //сортируем фильтр, чтобы порядок данных в нем не влиял на хэш
        ksort($filter_);
        $filter_string = var_export($filter_, true);
        $keyhash = md5(__METHOD__ . $filter_string);

        //если запуск был не из очереди - пробуем получить из кеша
        if (!$force_no_cache) {
            dtimer::log("get_pages normal run keyhash: $keyhash");
            $res = $this->cache->redis_get_serial($keyhash);
            //если дата создания записи в кеше больше даты последнего импорта, то не будем добавлять задание в очередь на обновление
            if($res !== null && $this->cache->redis_created($keyhash, $this->ttl) > $this->config->cache_date) {
                return $res;
            }

            //запишем в фильтр параметр force_no_cache, чтобы при записи задания в очередь
            //функция выполнялась полностью
            $filter_['force_no_cache'] = true;
            $filter_string = var_export($filter_, true);
            dtimer::log("get_pages add task force_no_cache keyhash: $keyhash");

            $task = '$this->pages->get_pages(';
            $task .= $filter_string;
            $task .= ');';
            $this->queue->redis_addtask($keyhash, isset($filter['method']) ? $filter['method'] : '', $task);
            return $res;
        }



		$menu_filter = '';
		$visible_filter = '';
		$pages = array();


		if (isset($filter['menu_id']))
			$menu_filter = $this->db->placehold('AND menu_id in (?@)', (array)$filter['menu_id']);

		if (isset($filter['visible']))
			$visible_filter = $this->db->placehold('AND visible = ?', intval($filter['visible']));
		$q = "SELECT * FROM __pages WHERE 1 $menu_filter $visible_filter ORDER BY pos";

		$this->db->query($q);

        if ($res = $this->db->results_array(null, 'id')) {
            dtimer::log(__METHOD__ . " redis set key: $keyhash");
            $this->cache->redis_set_serial($keyhash, $res, $this->ttl);
            return $res;
        } else {
            return false;
        }
	}

	/*
	 *
	 * Создание страницы
	 *
	 */
	public function add_page($page)
	{
        if(!empty($page['name']) && empty($page['trans'])){
            $page['trans'] = translit_ya($page['name']);
        }
		//удалим id, если он сюда закрался, при создании id быть не должно
		if (isset($page['id'])) {
			unset($page['id']);
		}

		foreach ($page as $k => $e) {
			if (empty_($e)) {
				unset($page[$k]);
			}
		}

		$query = $this->db->placehold('INSERT INTO __pages SET ?%', $page);
		if (!$this->db->query($query))
			return false;

		$id = $this->db->insert_id();
		$this->db->query("UPDATE __pages SET pos=id WHERE id=?", $id);
		return $id;
	}
	
	/*
	 *
	 * Обновить страницу
	 *
	 */
	public function update_page($id, $page)
	{
	    if(!empty($page['name']) && !isset($page['trans'])){
	        $page['trans'] = translit_ya($page['name']);
        }
		$query = $this->db->placehold('UPDATE __pages SET ?% WHERE id in (?@)', $page, (array)$id);
		if (!$this->db->query($query))
			return false;
		return $id;
	}
	
	/*
	 *
	 * Удалить страницу
	 *
	 */
	public function delete_page($id)
	{
		if (!empty($id))
			{
			$query = $this->db->placehold("DELETE FROM __pages WHERE id=? LIMIT 1", intval($id));
			if ($this->db->query($query))
				return true;
		}
		return false;
	}	
	
	/*
	 *
	 * Функция возвращает массив меню
	 *
	 */
	public function get_menus()
	{
		$menus = array();
		$query = "SELECT * FROM __menu ORDER BY pos";
		$this->db->query($query);
		$menus = $this->db->results_array(null, 'id');
		return $menus;
	}
	
	/*
	 *
	 * Функция возвращает меню по id
	 * @param $id
	 *
	 */
	public function get_menu($menu_id)
	{
		$query = $this->db->placehold("SELECT * FROM __menu WHERE id=? LIMIT 1", intval($menu_id));
		$this->db->query($query);
		return $this->db->result_array();
	}

}
