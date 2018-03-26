<?php


require_once('Simpla.php');

class Slider extends Simpla
{
	/*
	*
	* Функция возвращает массив слайдов
	*
	*/
	public function get()
	{
		$q = $this->db->placehold("SELECT * FROM __slider ORDER BY pos");
		$this->db->query($q);

		return $this->db->results_array(null, 'id');
	}

	/*
	*
	* Функция возвращает слайд по его id или url
	*
	*/
	public function get_slide($id)
	{
		$filter = is_int($id) ? "AND id = $id" : "AND url = '$id'";
		
		$query = "SELECT * FROM __slides WHERE 1 $filter ORDER BY pos LIMIT 1";
		$this->db->query($query);
		return $this->db->result_array();
	}

	/*
	*
	* Добавление слайда
	*
	*/
	public function add_slide($slide)
	{
		$query = $this->db->placehold("INSERT INTO __slides SET ?%", $slide);
		$this->db->query($query);
		$id = $this->db->insert_id();
		$query = $this->db->placehold("UPDATE __slides SET position=id WHERE id=? LIMIT 1", $id);
		$this->db->query($query);
		return $id;
	}

	/*
	*
	* Обновление слайда(ов)
	*
	*/		
	public function update_slide($id, $slide)
	{
		$query = $this->db->placehold("UPDATE __slides SET ?% WHERE id in(?@) LIMIT ?", (array)$slide, (array)$id, count((array)$id));
		$this->db->query($query);
		return $id;
	}
	
	/*
	*
	* Удаление слайда
	*
	*/	
	public function delete_slide($id)
	{
		if(!empty($id))
		{
			$this->delete_image($id);	
			$query = $this->db->placehold("DELETE FROM __slides WHERE id=? LIMIT 1", $id);
			$this->db->query($query);		
		}
	}
	
	/*
	*
	* Удаление изображения слайда
	*
	*/
	public function delete_image($slide_id)
	{
		$query = $this->db->placehold("SELECT image FROM __slides WHERE id=?", intval($slide_id));
		$this->db->query($query);
		$filename = $this->db->result_array('image');
		if(!empty($filename))
		{
			$query = $this->db->placehold("UPDATE __slides SET image=NULL WHERE id=?", $slide_id);
			$this->db->query($query);
			$query = $this->db->placehold("SELECT count(*) as count FROM __slides WHERE image=? LIMIT 1", $filename);
			$this->db->query($query);
			$count = $this->db->result_array('count');
			if($count == 0)
			{			
				@unlink($this->config->root_dir.$filename);
			}
		}
	}

}
