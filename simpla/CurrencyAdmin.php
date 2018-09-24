<?PHP 

require_once ('api/Simpla.php');

########################################
class CurrencyAdmin extends Simpla
{


	public function fetch()
	{
  
	   	// Обработка действий
		if ($this->request->method('post'))
		{
			foreach ($this->request->post('currency') as $name => $el){
				foreach ($el as $id => $c){
					$currencies[$id][$name] = $c;
				}
			}

			// Удалить непереданные валюты
			$todelete = array_diff_key($this->money->get_currencies(), $currencies );
			
			foreach($todelete as $c){
				$this->money->delete_currency($c['id']);
			}
			
			$pos = 0;
			//~ print_r($currencies);
			foreach ($currencies as $c)
			{
				if (!empty_($c['id'])) {
					$c['pos'] = $pos;
					$this->money->update_currency($c['id'], $c);
					$pos++;
				}else {
					$new_currencies[] = $c;
				}
			}
			
			//добавим новые
			if(!empty($new_currencies)){
				foreach($new_currencies as $c){
					$this->money->add_currency($c);
				}
			}

			// Действия с выбранными
			$action = $this->request->post('action');
			$id = $this->request->post('action_id');

			if (!empty($action) && !empty($id)){
				switch ($action){
				case 'disable' :
					{
						$this->money->update_currency($id, array('enabled' => 0));
						break;
					}
				case 'enable' :
					{
						$this->money->update_currency($id, array('enabled' => 1));
						break;
					}
				}
			}
		}

  

		// Отображение
		$currencies = $this->money->get_currencies();
		//~ print_r($currencies);
		$currency = $this->money->get_currency();
		$this->design->assign('currency', $currency);
		$this->design->assign('currencies', $currencies);
		return $this->design->fetch('currency.tpl');
	}
}
