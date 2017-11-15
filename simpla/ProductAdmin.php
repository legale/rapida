<?PHP

require_once('api/Simpla.php');

############################################
# Этот класс является контроллером для страницы товара в админ. панели
############################################
class ProductAdmin extends Simpla
{
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
            $this->product_open();
        } //2 сценарий - открытие страницы создания товара
        elseif (empty($_POST) && empty_(@$_GET['id'] )) {
            $this->product_new();
        //3-4 сценарии отличаются незначительно поэтому их соединим вместе
        } elseif (!empty($_POST)) {
            $this->product_save();
        }
        return $this->design->fetch('product.tpl');
    }

    //1. сценарий. Простое открытие существующего товара
    private function product_open()
    {

        //Сначала мы все получим из моделей
        $pid = $_GET['id'];
        $product = $this->products->get_product((int)$pid);
        $product['variants'] = $this->variants->get_product_variants($pid);
        $product['images'] = $this->products->get_product_images($pid);
        $product['options'] = $this->features->get_product_options($pid);
        $product['cats'] = $this->categories->get_product_categories($pid);
        if ($rel_id = $this->products->get_related_products($pid)) {
            $product['related'] = $this->products->get_products(array('product_id'=>$rel_id));
        }
        //это будет отдельными переменными
        $features = $this->features->get_features();
        $cats = $this->categories->get_categories();
        $brands = $this->brands->get_brands();

        //Теперь все это запилим в шаблон
        $this->design->assign('message_success', ' ');
        $this->design->assign('product', $product);
        $this->design->assign('features', $features);
        $this->design->assign('cats', $cats);
        $this->design->assign('brands', $brands);
        // print_r($product);
        // print_r($rel_id);
    }

    //2. сценарий. Открытие страницы для создания нового товара
    private function product_new()
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
    private function product_save()
    {
		//сначала получим данные для сохранения
		$save = $this->get_data();
		
		
		//сначала обновим или создадим сам товар
		if(isset($save['product']['id']) && !empty_($save['product']['id']) ){
			if(!$pid = $this->products->update_product($save['product'])){
				$this->design->assign('message_error', 'update_product_failed');
				return false;
			}
		} else {
			if(!$pid = $this->products->add_product($save['product'])){
				$this->design->assign('message_error', 'add_product_failed');
				return false;
			}
		}
		
		//теперь получим сохраненный товар назад
		$product = $this->products->get_product($pid);
		
		//теперь обновляем варианты, если они есть
		if(isset($save['variants']) && is_array($save['variants'])){
			foreach($save['variants'] as $v){
				$this->variant->update_variant($v);
			}
		}
		
		//теперь обновляем варианты, если они есть
		if(isset($save['variants']) && is_array($save['variants'])){
			foreach($save['variants'] as $v){
				$this->variant->update_variant($v);
			}
		}
		
		$product['variants'] = $this->variants->get_variants($pid);

        //берем категории и бренды
        $cats = $this->categories->get_categories();
        $brands = $this->brands->get_brands();

        //Теперь все это запилим в шаблон
        $this->design->assign('product', $product);
        $this->design->assign('cats', $cats);
        $this->design->assign('brands', $brands);
    }
    
    private function get_data(){
		print "<pre>";
        print_r($_POST);
        print "</pre>";
        
        $save = array();
        //Сначала надо получить все аргументы из POST запроса
        //это уже по существующим
        $save['product'] = @$_POST['save']['product'];
        $save['variants'] = @$_POST['save']['variants'];
        $save['images'] = @$_POST['save']['images'];
        $save['options'] = @$_POST['save']['options'];
        $save['cats'] = @$_POST['save']['cats'];
        $save['related'] = @$_POST['save']['related'];

		//это по вновь добавляемым 
        $save['new_variants'] = @$_POST['save']['new_variants'];
        $save['new_images'] = @$_POST['save']['new_images'];
        $save['new_options'] = @$_POST['save']['new_options'];
        $save['new_cats'] = @$_POST['save']['new_cats'];
        return $save;
	}
}
