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
    private function product_save()
    {
        print "<pre>";
        print_r($_POST);
        print "</pre>";
        //Сначала надо получить все аргументы из POST запроса
        $product = $_POST['product'];
        $product['variants'] = $_POST['product']['variants'];
        $product['images'] = $_POST['product']['images'];
        $product['options'] = $_POST['product']['options'];
        $product['cats'] = $_POST['product']['cats'];
        $product['related'] = $_POST['product']['related'];


        $product['new_variants'] = $_POST['product']['new_variants'];
        $product['new_images'] = $_POST['product']['images'];
        $product['new_options'] = $_POST['product']['new_options'];
        $product['new_cats'] = $_POST['product']['new_cats'];

        //тут все очень просто, нам нужны только категории и бренды
        $cats = $this->categories->get_categories();
        $brands = $this->brands->get_brands();

        //Теперь все это запилим в шаблон
        $this->design->assign('product', $product);
        $this->design->assign('cats', $cats);
        $this->design->assign('brands', $brands);
    }
}
