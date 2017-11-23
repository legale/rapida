<?PHP

require_once('api/Simpla.php');

############################################
# Этот класс является контроллером для страницы товара в админ. панели
############################################
class ProductAdmin extends Simpla
{
	
	//это переменная для сохранения и последующей передачи в шаблон статусов
	//выполнения отдельных операций
	private $status = array();
	
	//единственный общедоступный метод в классе, все остальные методы вызываются уже из него
    public function fetch()
    {
        //Всего существует 4 возможных сценария.
        //1. Открытие страницы товара
        //2. Сохранение изменений страницы товара
        //3. Открытие страницы нового товара
        //4. Создание нового товара
        // Для каждого сценария сделаем свою функцию, чтобы не мешать все в 1 кучу.

        //1 сценарий - открытие товара
        if (empty($_POST) && !empty_(@$_GET['id'] )) {
            $this->open($_GET['id']);
        } //2 сценарий - открытие страницы создания товара
        elseif (empty($_POST) && empty_(@$_GET['id'] )) {
            $this->create();
        //3-4 сценарии отличаются незначительно поэтому их соединим вместе
        } elseif (!empty($_POST)) {
            $this->save();
        }
        return $this->design->fetch('product.tpl');
    }

    //1. сценарий. Простое открытие существующего товара
    private function open($pid)
    {
		dtimer::log(__METHOD__ . " start");

        //Сначала мы все получим из моделей
        $product = $this->products->get_product((int)$pid);
        $product['variants'] = $this->variants->get_product_variants($pid);
        $product['images'] = $this->products->get_product_images($pid);
        $product['options'] = $this->features->get_product_options($pid);
        $product['cats'] = $this->categories->get_product_categories($pid);
        if ($rel_id = $this->products->get_related_products($pid)) {
            $rel_id = array_keys( $rel_id );
            $product['related'] = $this->products->get_products(array('id'=>$rel_id));
        }
        
        //это будет отдельными переменными
        $features = $this->features->get_features();
        $cats = $this->categories->get_categories();
        $brands = $this->brands->get_brands();

		//статусы теперь пишем иначе, пишем все в 1 переменную с указанием 1 из трех статусов
		//3 - все ок, 2 - не совсем ок, 1 - совсем не ок.
		//статусы пишутся в переменную класса, откуда в этом сценарии будут переданы в шаблон
		$this->status[] = array(
		'status'=> 3,
		'message'=> 'Выполнено открытие',
		);
		
		//теперь передадим это в шаблон
        $this->design->assign('status', $this->status);

		
        //Теперь все это запилим в шаблон
        $this->design->assign('product', $product);
        $this->design->assign('features', $features);
        $this->design->assign('cats', $cats);
        $this->design->assign('brands', $brands);
        //~ print_r($product);
        //~ print_r($rel_id);
    }

    //2. сценарий. Открытие страницы для создания нового товара
    private function create()
    {
        //тут все очень просто, нам нужны только категории и бренды
        $cats = $this->categories->get_categories();
        $brands = $this->brands->get_brands();

        //Теперь все это запилим в шаблон
        $this->design->assign('cats', $cats);
        $this->design->assign('brands', $brands);
    }



    //3-4. сценарий. Сохранение товара
    //Это самые сложные сценарии, они будут разложены на составляющие
    private function save()
    {
		dtimer::log(__METHOD__ . " start");
		//сначала получим данные для сохранения
		$save = $this->get_data();
		
		//теперь обновим или создадим сам товар
		$pid = $this->save_product($save['product']);
		

		//теперь сохраним
		if( $pid !== false ){
			$this->save_cats($pid, $save['cats']);
			$this->save_variants($pid, $save['variants']);
			$this->save_options($pid, $save['options']);
			$this->save_images($pid, $save['images']);
			$this->upload_images($pid, $save['new_images']);
			$this->save_related($pid, $save['related']);
		}
		
		$this->status[] = array(
			'status' => 3,
			'message' => 'Изменения сохранены',
		);
		
		//теперь выполним сценарий простого открытия, ведь у нас уже есть $pid
		$this->open($pid);

    }
    
    private function get_data(){
		dtimer::log(__METHOD__ . " start");
		//~ print "<pre>";
        //~ print_r($_POST);
        //~ print "</pre>";
        
        $save = array();
        //Сначала надо получить все аргументы из POST запроса
        //это уже по существующим
        $save['product'] = @$_POST['save']['product'];
        $save['variants'] = @$_POST['save']['variants'];
        $save['images'] = @$_POST['save']['images'];
        $save['options'] = @$_POST['save']['options'];
        $save['cats'] = @$_POST['save']['cats'];
        $save['related'] = @$_POST['save']['related'];

		//для загружаемых изображений отдельный массив 
        $save['new_images'] = @$_FILES['new_images'];
        return $save;
	}
	
	private function save_product($p){
		dtimer::log(__METHOD__ . " start");
		
		if( !empty_(@$p['id']) ){
			dtimer::log(__METHOD__ . " update");
			$pid = $this->products->update_product($p);
			if($pid === false){
				$this->status[] = array(
					'status' => 1,
					'message' => 'Не удалось обновить товар',
				);
				return false;
			}
			dtimer::log(__METHOD__ . " update ok $pid");
			return $pid;
		}

		dtimer::log(__METHOD__ . " add");
		$pid = $this->products->add_product($p);

		if($pid === false){
			$this->status[] = array(
				'status' => 1,
				'message' => 'Не удалось создать товар',
			);
			return false;
		}
		
		dtimer::log(__METHOD__ . " add ok $pid");
		return $pid;
	}


	private function save_related($pid, $raw){
		dtimer::log(__METHOD__ . " start");
		if(!is_array($raw)){
			return false;
		}
		
		
		//тут будут id связанных товаров, которые удалять не нужно
		$keep = array_flip($raw);
		//тут существующие
		$saved = $this->products->get_related_products($pid);
		if(is_array(@$saved)){
			foreach($saved as $id=>$k){
				if( !isset($keep[$id]) ){
					$this->products->delete_related_product($pid, $id);
					$this->status[] = array(
						'status' => 3,
						'message' => "Удален связанный товар $id ".var_export($saved,true),
					);
				}
			}
		}
		
		//тут поменяем порядок связанных товаров и добавим новые
		for($i = 0, $c = count($raw); $i < $c; $i++){
			if(empty_(@$raw[$i])){
				continue;
			}
			$this->products->add_related_product( $pid, $raw[$i], $i  );
		}
	}
	
	
	private function save_cats($pid, $raw){
		dtimer::log(__METHOD__ . " start");
		//~ print_r($raw);
		if(!is_array($raw)){
			return false;
		}
		
		
		//тут будут id категорий, которые удалять не нужно
		$keep = array_flip($raw);
		//тут существующие
		$saved = $this->categories->get_product_categories($pid);
		if(is_array(@$saved)){
			foreach($saved as $id=>$k){
				if( !isset($keep[$id]) ){
					$this->categories->delete_product_category($pid, $id);
					$this->status[] = array(
						'status' => 3,
						'message' => "Удалена категория $id",
					);
				}
			}
		}
		
		//тут поменяем порядок связанных товаров и добавим новые
		for($i = 0, $c = count($raw); $i < $c; $i++){
			if(empty_(@$raw[$i])){
				continue;
			}
			$this->categories->add_product_category($pid, $raw[$i], $i  );
		}
	}

	private function save_images($pid, $raw){
		dtimer::log(__METHOD__ . " start");
		if(!is_array($raw)){
			return false;
		}
		
		
		//тут будут id изображений, которые удалять не нужно
		$keep = array_flip($raw);
		//тут существующие
		$saved = $this->products->get_product_images($pid);
		foreach($saved as $id=>$img){
			if( !isset($keep[$id]) ){
				$this->products->delete_image($id);
				$this->status[] = array(
					'status' => 3,
					'message' => "Удалено изображение $id",
				);
			}
		}
		
		//тут поменяем порядок изображений
		for($i = 0, $c = count($raw); $i < $c; $i++){
			$this->products->update_image($raw[$i], array('position'=> $i) );
		}
	}


	private function upload_images($pid, $raw){
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
			
			
			if ($img = $this->image->upload_image($raw['tmp_name'][$k], $raw['name'][$k]))
			{
				dtimer::log(__METHOD__ . " image uploaded $img");
				//если изображение добавлено - переходим на сл. итерацию
				if(false !== $this->products->add_image($pid, $img)){
					continue;
				}
			} 
			//если пришли сюда, значит изображение добавить не удалось
			$this->status[] = array(
				'status' => 2,
				'message' => "Не удалось добавить изображение $name",
			);
		}
	}
	
	private function delete_variants($pid, $raw){
		dtimer::log(__METHOD__ . " start");
		//тут id, переданных вариантов
		$keep = array();
		if(isset($raw['id']) && is_array($raw['id'])){
			$keep = array_flip($raw['id']);
		}
		//тут у нас уже имеющиеся в базе варианты
		if($saved = $this->variants->get_product_variants($pid)){
			//удалим те, что не переданы
			foreach($saved as $id=>$v){
				if( !isset($keep[$id]) ){
					$this->variants->delete_variant($id);
					$this->status[] = array(
						'status' => 3,
						'message' => "Удален вариант с артикулом " . $v['sku'],
					);
				}
			}
		}
		return true;
	}


	
	private function save_variants($pid, $raw){
		dtimer::log(__METHOD__ . " start");
		//удалим варианты
		$this->delete_variants($pid, $raw);

		//теперь подготовим варианты для записи
		$vs = array();
		foreach($raw as $param=>$a){
			foreach($a as $k=>$v){
				$vs[$k][$param] = $v; 
			}
		}
		
		
		//стартовый номер позиции варианта
		$pos = 0;
		foreach($vs as $k=>$v){
			//уберем пустые варианты, которые могут попадать из незаполненных ячеек
			//самый важный параметр - артикул
			if(empty_(@$v['sku'])){
				unset($vs[$k]);
				continue;
			}
			//установим позиции в том же порядке, в котором варианты перчислены в массиве
			$v['position'] = $pos;
			$pos++;
			
			if(!empty_(@$v['id'])){
				if($this->variants->update_variant($v) === false){
					$this->status[] = array(
						'status' => 2,
						'message' => 'Не удалось обновить вариант'. $v['sku'],
					);
				}
			} else {
				if($this->variants->add_variant($v) === false){
					$this->status[] = array(
						'status' => 2,
						'message' => 'Не удалось добавить вариант '. $v['sku'],
					);
				}
			}
		}
		
		//если дошли сюда, значит все в порядке
		return true;
	}
	
	private function save_options($pid, $raw){
		dtimer::log(__METHOD__ . " start");
		
		if(!is_array($raw)){
			return false;
		}
		//получим имена свойств и их id
		$fnames = $this->features->get_features_ids( array('return' => array('key' => 'name', 'col' => 'id')) );
		
		
		//сюда будем записывать готовые для записи
		$opts = array();
		foreach($raw as $param=>$o){
			foreach($o as $k=>$v){
				$opts[$k][$param] = $v; 
			}
		}
		
		
		foreach($opts as $o){
			//логика такая, если есть fid, значит свойство уже существует, поэтому просто обновляем
			//значение через features->update_option()
			//если нет значения, поставим пустое
			if( empty_(@$o['val']) ){
				$o['val'] = '';
			}
			
			if( !empty_(@$o['fid']) ){
				$fid = $o['fid'];
			}elseif( !empty_(@$o['fname']) && isset($fnames[trim($o['fname'])]) ){
				$fid = $fnames[trim($o['fname'])];
			} elseif ( !empty_(@$o['fname']) ) {
				$fname = $o['fname'];
				$fid = $this->features->add_feature(array('name' => $fname));
				if($fid === false){
					$this->status[] = array(
						'status' => 2,
						'message' => 'Не удалось создать опцию',
					);
					continue;
				} else {
					$this->status[] = array(
						'status' => 3,
						'message' => "Создана новая опция id: $fid, название: $fname",
					);
				}
			} else {
				//если $fid получить не удалось, значит нечего и менять переходим к следующей итерации
				continue;
			}
			
			if($this->features->update_option($pid, $fid, $o['val']) === false){
				$this->status[] = array(
					'status' => 2,
					'message' => 'Не удалось обновить опцию',
				);
			}
		}
		//если дошли сюда, значит все в порядке
		return true;
	}
}
