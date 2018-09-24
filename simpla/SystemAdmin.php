<?PHP 

require_once ('api/Simpla.php');

########################################
class SystemAdmin extends Simpla
{

	public function fetch()
	{


   	// Обработка действий
		if ($this->request->method('post')) {

			switch ($this->request->post('action'))
				{
				case 'clear_options' :
					{
						$this->sys->clear_options();
						break;
					}
				case 'download_all_images' :
					{
						$this->sys->download_all_images();
						break;
					}
			}

		}


		return $this->design->fetch('system.tpl');
	}
}


?>
