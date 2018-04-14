<?PHP

require_once('api/Simpla.php');

############################################
# Этот класс является контроллером для страницы категории в админ. панели
############################################
class CategoryAdmin extends Simpla
{
	
	//это переменная для сохранения и последующей передачи в шаблон статусов
	//выполнения отдельных операций
	private $status = array();
	
	//единственный общедоступный метод в классе, все остальные методы вызываются уже из него
    public function fetch()
    {
        //Всего существует 4 возможных сценария.
        //1. Открытие страницы категории
        //2. Сохранение изменений категории
        //3. Открытие страницы создания новой категории
        //4. Создание новой категории
        // Для каждого сценария сделаем свою функцию, чтобы не мешать все в 1 кучу.

        //1 сценарий - открытие 
        if (empty($_POST) && !empty_(@$_GET['id'] )) {
            $this->open($_GET['id']);
        } //2 сценарий - открытие страницы создания категории
        elseif (empty($_POST) && empty_(@$_GET['id'] )) {
            $this->create();
        //3-4 сценарии отличаются незначительно поэтому их соединим вместе
        } elseif (!empty($_POST)) {
            $this->save();
        }
        return $this->design->fetch('category.tpl');
    }

    //1. сценарий. Простое открытие категории
    private function open($cid)
    {
		dtimer::log(__METHOD__ . " start");

        //Сначала мы все получим из моделей
        $category = $this->categories->get_category((int)$cid);
        $category['images'] = $this->image->get('categories', array('item_id' => $cid) );


		$cats = $this->categories->get_categories();
		
		$this->status[] = array(
		'status'=> 3,
		'message'=> 'Выполнено открытие',
		);
		//теперь передаем в шаблон
        $this->design->assign('status', $this->status);
		
        //Теперь все это запилим в шаблон
        $this->design->assign('category', $category);
        $this->design->assign('cats', $cats);
    }

    //2. сценарий. Открытие страницы для создания нового категории
    private function create()
    {
        //тут нужны только категории
        $cats = $this->categories->get_categories();
		$this->status[] = array(
		'status'=> 3,
		'message'=> 'Создание новой категории',
		);
		//теперь передаем в шаблон
        $this->design->assign('status', $this->status);

        $this->design->assign('cats', $cats);
    }

    //3-4. сценарий. Сохранение категории
    //Это самые сложные сценарии, они будут разложены на составляющие
    private function save()
    {
		dtimer::log(__METHOD__ . " start");
		//сначала получим данные для сохранения
		$save = $this->get_data();
		
		//теперь обновим или создадим категорию
		$cid = $this->save_category($save['category']);
		
		//теперь сохраним/загрузим картинки
		if( $cid !== false ){
			$this->save_images($cid, $save['images']);
			$this->upload_images($cid, $save['new_images']);
		}
		
		$this->status[] = array(
			'status' => 3,
			'message' => 'Изменения сохранены',
		);
		
		//теперь выполним сценарий простого открытия, ведь у нас уже есть $cid
		$this->open($cid);

    }
    
    private function get_data(){
		dtimer::log(__METHOD__ . " start");
		//~ print "<pre>";
        //~ print_r($_POST);
        //~ print "</pre>";
        
        $save = array();
        //Сначала надо получить все аргументы из POST запроса
        //это уже по существующим
        $save['category'] = @$_POST['save']['category'];
        $save['images'] = @$_POST['save']['images'];

		//для загружаемых изображений отдельный массив 
        $save['new_images'] = @$_FILES['new_images'];
        return $save;
	}
	
	private function save_category($c){
		dtimer::log(__METHOD__ . " start");
		
		if( !empty_(@$c['id']) ){
			dtimer::log(__METHOD__ . " update");
			$cid = $this->categories->update_category($c['id'], $c);
			if($cid === false){
				$this->status[] = array(
					'status' => 1,
					'message' => 'Не удалось обновить категорию',
				);
				return false;
			}
			dtimer::log(__METHOD__ . " update ok $cid");
			return $cid;
		}

		dtimer::log(__METHOD__ . " add");
		$cid = $this->categories->add_category($c);

		if($cid === false){
			$this->status[] = array(
				'status' => 1,
				'message' => 'Не удалось создать категорию',
			);
			return false;
		}
		
		dtimer::log(__METHOD__ . " add ok $cid");
		return $cid;
	}


	private function save_images($cid, $raw){
		dtimer::log(__METHOD__ . " start");
		//~ print_r($_POST);
		
		
		
		//тут будут id изображений, которые удалять не нужно
		if(is_array($raw)){
			$keep = array_flip($raw);
		} else {
			$keep = array();
		}
		//тут существующие
		if($saved = $this->image->get('categories', array('item_id'=>$cid) )){
			foreach($saved as $id=>$img){
				if( !isset($keep[$id]) ){
					$this->image->delete('categories', $id);
					$this->status[] = array(
						'status' => 3,
						'message' => "Удалено изображение $id",
					);
				}
			}
		}
		
		//тут поменяем порядок изображений
		for($i = 0, $c = count($keep); $i < $c; $i++){
			$this->image->update('categories', $raw[$i], array('item_id' => $cid, 'pos'=> $i) );
		}
	}


	private function upload_images($cid, $raw){
		dtimer::log(__METHOD__ . " start");
		//~ print_r($raw);
		if(empty($raw['name'])){
			dtimer::log(__METHOD__. " no images to upload");
			return false;
		}
		foreach($raw['name'] as $k=>$name){
			
			if(empty_($raw['name'][$k])){ //если имя не задано, просто пропускаем
				continue;
			} elseif($raw['error'][$k] !== 0){ //если статус ошибки не 0, значит есть проблема
				$this->status[] = array(
					'status' => 2,
					'message' => "Ошибка загрузки изобажения $name",
				);
				continue;
			}
			
			
			if ($img = $this->image->upload('categories', $cid, $raw['tmp_name'][$k], $raw['name'][$k]))
			{
				dtimer::log(__METHOD__ . " image uploaded ".$img['basename']);
				continue;
			} 
			//если пришли сюда, значит изображение добавить не удалось
			$this->status[] = array(
				'status' => 2,
				'message' => "Не удалось добавить изображение $name",
			);
		}
	}

}
