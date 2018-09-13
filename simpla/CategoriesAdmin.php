<?PHP
require_once ('api/Simpla.php');


class CategoriesAdmin extends Simpla
{
	function fetch()
	{
		if ($this->request->method('post'))
			{
			// Действия с выбранными
			$ids = $this->request->post('check');
			if (is_array($ids))
				switch ($this->request->post('action'))
				{
				case 'disable' :
					{
						foreach ($ids as $id)
							$this->categories->update_category($id, array('visible' => 0));
						break;
					}
				case 'enable' :
					{
						foreach ($ids as $id)
							$this->categories->update_category($id, array('visible' => 1));
						break;
					}
				case 'delete' :
					{
						$this->categories->delete_category($ids);
						break;
					}
			}		
	  	
			// Сортировка
			$poss = $this->request->post('poss');
			$ids = array_keys($poss);
			sort($poss);
			foreach ($poss as $i => $pos){
				$this->categories->update_category($ids[$i], array('pos' => $pos));
			}

		}

        $this->categories->init_categories(true); //reinit cats
		$categories = $this->categories->categories_tree; //do reinitialize categories tree
        //print_r($categories);

		$this->design->assign('categories', $categories);
		return $this->design->fetch('categories.tpl');
	}
}
