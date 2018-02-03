<?PHP

require_once('api/Simpla.php');

############################################
# Этот класс является контроллером для страницы категории в админ. панели
############################################
class PostAdmin extends Simpla
{
	
	//это переменная для сохранения и последующей передачи в шаблон статусов
	//выполнения отдельных операций
	private $status = array();
	
	//единственный общедоступный метод в классе, все остальные методы вызываются уже из него
    public function fetch()
    {
        //Всего существует 4 возможных сценария.
        //1. Открытие страницы статьи
        //2. Создание
        //3. Сохранение уже созданного
        //4. Сохранение нового
        // Для каждого сценария сделаем свою функцию, чтобы не мешать все в 1 кучу.

        //1 сценарий - открытие 
        if (empty($_POST) && !empty_(@$_GET['id'] )) {
            $this->open($_GET['id']);
        } //2 сценарий - создание
        elseif (empty($_POST) && empty_(@$_GET['id'] )) {
            $this->create();
        //3-4 сохранение
        } elseif (!empty($_POST)) {
            $this->save();
        }
        return $this->design->fetch('post.tpl');
    }

    //1. сценарий. 
    private function open($id){
		dtimer::log(__METHOD__ . " start");

        //Сначала мы все получим из моделей
        $post = $this->blog->get_post((int)$_GET['id']);
        $images = $this->image->get('blog', array('item_id' => $id) );
		
		$this->status[] = array(
		'status'=> 3,
		'message'=> 'Выполнено открытие',
		);
		//теперь передаем в шаблон
        $this->design->assign('status', $this->status);
		
        //Теперь все это запилим в шаблон
        $this->design->assign('post', $post);
        $this->design->assign('images', $images);
    }

    //2. сценарий. Открытие страницы для создания нового категории
    private function create(){
        //тут вообще пустая страница
		$this->status[] = array(
		'status'=> 3,
		'message'=> 'Создание новой категории',
		);
		//теперь передаем в шаблон
        $this->design->assign('status', $this->status);

    }

    //3-4. сценарий. Сохранение 
    private function save(){
		dtimer::log(__METHOD__ . " start");
		//сначала получим данные для сохранения
		$save = $this->get_data();
		//~ print_r($save);
		
		//теперь обновим или создадим категорию
		$id = $this->save_item($save['post']);
		
		//теперь сохраним/загрузим картинки
		if( $id !== false ){
			$this->save_images($id, $save['images']);
			$this->upload_images($id, $save['new_images']);
		}
		
		$this->status[] = array(
			'status' => 3,
			'message' => 'Изменения сохранены',
		);
		
		//теперь выполним сценарий простого открытия, ведь у нас уже есть $id
		$this->open($id);
    }
    
    private function get_data(){
		dtimer::log(__METHOD__ . " start");
		//~ print "<pre>";
        //~ print_r($_POST);
        //~ print "</pre>";
        
        $save = array();
        //Сначала надо получить все аргументы из POST запроса
        //это уже по существующим
        $save['post'] = @$_POST['post'];
        $save['post']['id'] = @$_GET['id'];
        $save['post']['date'] = date('Y-m-d', strtotime($save['post']['date']));
        

		//для загружаемых изображений отдельный массив 
        $save['images'] = @$_POST['images'];
        $save['new_images'] = @$_FILES['new_images'];
        return $save;
	}
	
	private function save_item($a){
		dtimer::log(__METHOD__ . " start");
		
		if( !empty_(@$a['id']) ){
			dtimer::log(__METHOD__ . " update");
			$id = $this->blog->update_post($a['id'], $a);
			if($id === false){
				$this->status[] = array(
					'status' => 1,
					'message' => 'Не удалось сохранить изменения',
				);
				return false;
			}
			dtimer::log(__METHOD__ . " update ok $id");
			return $id;
		}

		dtimer::log(__METHOD__ . " add");
		$id = $this->blog->add_post($a);

		if($id === false){
			$this->status[] = array(
				'status' => 1,
				'message' => 'Не удалось создать',
			);
			return false;
		}
		
		dtimer::log(__METHOD__ . " add ok $id");
		return $id;
	}


	private function save_images($id, $raw){
		dtimer::log(__METHOD__ . " start");
		//~ print_r($_POST);
		
		//тут будут id изображений, которые удалять не нужно
		if(is_array($raw)){
			$keep = array_flip($raw);
		} else {
			$keep = array();
		}
		//тут существующие
		if($saved = $this->image->get('blog', array('item_id'=>$id) )){
			foreach($saved as $id=>$img){
				if( !isset($keep[$id]) ){
					$this->image->delete('blog', $id);
					$this->status[] = array(
						'status' => 3,
						'message' => "Удалено изображение $id",
					);
				}
			}
		}
		
		//тут поменяем порядок изображений
		for($i = 0, $c = count($raw); $i < $c; $i++){
			$this->image->update('categories', $raw[$i], array('item_id' => $id, 'pos'=> $i) );
		}
	}


	private function upload_images($id, $raw){
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
			
			
			if ($img = $this->image->upload('blog', $id, $raw['tmp_name'][$k], $raw['name'][$k]))
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


