<?PHP 

require_once ('api/Simpla.php');

########################################
class DeliveriesAdmin extends Simpla
{


	public function fetch()
	{
  
	   	// Обработка действий
		if ($this->request->method('post'))
			{			
			// Действия с выбранными
			$ids = $this->request->post('check');

			if (is_array($ids))
				switch ($this->request->post('action'))
				{
				case 'disable' :
					{
						$this->delivery->update_delivery($ids, array('enabled' => 0));
						break;
					}
				case 'enable' :
					{
						$this->delivery->update_delivery($ids, array('enabled' => 1));
						break;
					}
				case 'delete' :
					{
						foreach ($ids as $id)
							$this->delivery->delete_delivery($id);
						break;
					}
			}	
				
			// Сортировка
			$poss = $this->request->post('poss');
			$ids = array_keys($poss);
			sort($poss);
			foreach ($poss as $i => $pos)
				$this->delivery->update_delivery($ids[$i], array('pos' => $pos));

		}

  

		// Отображение
		$deliveries = $this->delivery->get_deliveries();
		$this->design->assign('deliveries', $deliveries);
		return $this->design->fetch('deliveries.tpl');
	}
}
