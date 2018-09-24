<?PHP 

require_once ('api/Simpla.php');

########################################
class PaymentMethodsAdmin extends Simpla
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
			foreach ($poss as $i => $pos){
				$this->payment->update_payment_method($ids[$i], array('pos' => $pos)); 
			}
		
		// Действия с выбранными
			$ids = $this->request->post('check');

			if (is_array($ids))
				switch ($this->request->post('action'))
				{
				case 'disable' :
					{
						$this->payment->update_payment_method($ids, array('enabled' => 0));
						break;
					}
				case 'enable' :
					{
						$this->payment->update_payment_method($ids, array('enabled' => 1));
						break;
					}
				case 'delete' :
					{
						foreach ($ids as $id)
							$this->payment->delete_payment_method($id);
						break;
					}
			}

		}

  

	// Отображение
		$payment_methods = $this->payment->get_payment_methods();
		$this->design->assign('payment_methods', $payment_methods);
		return $this->design->fetch('payment_methods.tpl');
	}
}


?>
