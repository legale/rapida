<?PHP 

require_once ('api/Simpla.php');

########################################
class OrdersLabelsAdmin extends Simpla
{

	public function fetch()
	{
   	// Обработка действий
		if ($this->request->method('post'))
			{
		// Сортировка
			$poss = $this->request->post('poss');
			$ids = array_keys($poss);
			sort($poss);
			foreach ($poss as $i => $pos)
				$this->orders->update_label($ids[$i], array('pos' => $pos)); 

		
		// Действия с выбранными
			$ids = $this->request->post('check');
			if (is_array($ids))
				switch ($this->request->post('action'))
				{
				case 'delete' :
					{
						foreach ($ids as $id)
							$this->orders->delete_label($id);
						break;
					}
			}
		}

	// Отображение
		$labels = $this->orders->get_labels();

		$this->design->assign('labels', $labels);
		return $this->design->fetch('orders_labels.tpl');
	}
}


?>
