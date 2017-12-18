<?php

require_once('../../api/Simpla.php');


class ImportAjax extends Simpla{

    // Соответствие полей в базе и имён колонок в файле
    private $columns_names = array(
            'name'=>             array('product', 'name', 'товар', 'название', 'наименование'),
            'url'=>              array('url', 'адрес'),
            'visible'=>          array('visible', 'published', 'видим'),
            'featured'=>         array('featured', 'hit', 'хит', 'рекомендуемый'),
            'category'=>         array('category', 'категория'),
            'brand'=>            array('brand', 'бренд'),
            'variant'=>          array('variant', 'вариант'),
            'price'=>            array('price', 'цена'),
            'old_price'=>        array('compare price', 'старая цена'),
            'sku'=>              array('sku', 'артикул'),
            'stock'=>            array('stock', 'склад', 'на складе'),
            'meta_title'=>       array('meta title', 'заголовок страницы'),
            'meta_keywords'=>    array('meta keywords', 'ключевые слова'),
            'meta_description'=> array('meta description', 'описание страницы'),
            'annotation'=>       array('annotation', 'аннотация', 'краткое описание'),
            'description'=>      array('description', 'описание'),
            'images'=>           array('images', 'изображения')
            );
    
    // Соответствие имени колонки и поля в базе
    private $internal_columns_names = array();

    private $import_files_dir      = '../files/import/'; // Временная папка
    private $import_file           = 'import.csv';           // Временный файл
    private $category_delimiter = ',';                       // Разделитель каегорий в файле
    private $subcategory_delimiter = '/';                    // Разделитель подкаегорий в файле
    private $column_delimiter      = ';';
    private $products_count        = 150;
    private $columns               = array();

    public function import()
    {
        if (!$this->users->check_access('import')) {
            return false;
        }

        //сюда будем писать результат импорта
        $result = array();
        
        //получим массив id=>название свойства
        //Чтобы не делать это постоянно на каждом свойстве
        $filter = array('return'=> array('key' => 'name' , 'col' => 'id'));
        //получим без кеша на первом цикле
        if(empty($_GET['from'])){
			$filter['force_no_cache'] = true;
		}
        $GLOBALS['features'] = $this->features->get_features_ids( $filter );
        
        // Сначала получим уникальные значения свойств товаров, чтобы, не искать их постоянно
        // должно значительное ускорить импорт
        
        //поставим выполнение запроса без кеша только для первой позиции импорта
        $filter = array('return'=> array('key' => 'val' , 'col' => 'id'));
        dtimer::log(__METHOD__ . " from: " . var_export($this->request->get('from'), true) );
        if (empty($_GET['from'])) {
            $filter['force_no_cache'] = true;
        }
        $GLOBALS['options_uniq'] = $this->features->get_options_ids($filter);
        
        if (!is_array($GLOBALS['options_uniq'])) {
            $GLOBALS['options_uniq'] = array();
        }


        // Определяем колонки из первой строки файла
        $f = fopen($this->import_files_dir.$this->import_file, 'r');
        $this->columns = fgetcsv($f, null, $this->column_delimiter);

        // Заменяем имена колонок из файла на внутренние имена колонок
        foreach ($this->columns as &$column) {
            if ($internal_name = $this->internal_column_name($column)) {
                $this->internal_columns_names[$column] = $internal_name;
                $column = $internal_name;
            }
        }

        // Если нет названия товара - не будем импортировать
        if (!in_array('name', $this->columns) && !in_array('sku', $this->columns)) {
            return false;
        }
        
        // Переходим на заданную позицию, если импортируем не сначала
        if ($from = $this->request->get('from')) {
            fseek($f, $from);
        }
        
        // Массив импортированных товаров
        $imported_items = array();
        
        // Проходимся по строкам, пока не конец файла
        // или пока не импортировано достаточно строк для одного запроса
        for ($k=0; !feof($f) && $k<$this->products_count; $k++) {
            // Читаем строку
            $line = fgetcsv($f, 0, $this->column_delimiter);

            $product = null;

            if (is_array($line)) {
                // Проходимся по колонкам строки
                foreach ($this->columns as $i => $col) {
                    // Создаем массив item[название_колонки]=значение и сразу обрезаем пробелы по краям
                    if (isset($line[$i]) && !empty_(@$line) && !empty_(@$col)) {
                        $product[$col] = trim($line[$i]);
                    }
                }
            }
            
            // Импортируем этот товар
            if ($imported_item = $this->import_item($product)) {
                $imported_items[] = $imported_item;
            }
        }
        // Запоминаем на каком месте закончили импорт
        $from = ftell($f);
        
        // И закончили ли полностью весь файл
        $result['end'] = feof($f);

        fclose($f);
        $size = filesize($this->import_files_dir.$this->import_file);
        
        // Создаем объект результата
        $result['from'] = $from;          // На каком месте остановились
        $result['totalsize'] = $size;     // Размер всего файла
        $result['items'] = $imported_items;   // Импортированные товары
        
        return $result;
    }
    
    // Импорт одного товара $item[column_name] = value;
    private function import_item($item)
    {
        
        $imported_item = array();
        
        
        
        // Проверим не пустое ли название и артинкул (должно быть хоть что-то из них)
        if (empty_(@$item['name']) && empty_(@$item['sku'])) {
            return false;
        }

        // Подготовим товар для добавления в базу
        $product = array();
        
        if (isset($item['name'])) {
            $product['name'] = $item['name'];
        }

        if (isset($item['meta_title'])) {
            $product['meta_title'] = $item['meta_title'];
        }

        if (isset($item['meta_keywords'])) {
            $product['meta_keywords'] = $item['meta_keywords'];
        }

        if (isset($item['meta_description'])) {
            $product['meta_description'] = $item['meta_description'];
        }

        if (isset($item['annotation'])) {
            $product['annotation'] = $item['annotation'];
        }

        if (isset($item['description'])) {
            $product['body'] = $item['description'];
        }
    
        if (isset($item['visible'])) {
            $product['visible'] = intval($item['visible']);
        }

        if (isset($item['featured'])) {
            $product['featured'] = intval($item['featured']);
        }
    
        if (!empty_(@$item['url'])) {
            $product['url'] = $item['url'];
        } elseif (!empty_(@$item['name'])) {
            $product['url'] = translit_url($item['name']);
        }
    
        // Если задан бренд
        if (!empty_(@$item['brand'])) {
            $item['brand'] = $item['brand'];
            // Найдем его по имени
            if ($brand = $this->brands->get_brand($item['brand'])) {
                $product['brand_id'] = $brand['id'];
            } else {
                // Создадим, если не найден
                if (($product['brand_id'] = $this->brands->add_brand(array('name'=>$item['brand']))) === false) {
                    dtimer::log(__METHOD__. " failed on add_brand", 1);
                    return false;
                }
            }
        }

        
        // Если задана категория
        $category_id = null;
        $categories_ids = array();
        if (!empty_(@$item['category'])) {
            foreach (explode($this->category_delimiter, $item['category']) as $c) {
                $categories_ids[] = $this->import_category($c);
            }
            $category_id = reset($categories_ids);
        }
    
        // Подготовим вариант товара
        $variant = array();
        
        if (isset($item['variant'])) {
            $variant['name'] = $item['variant'];
        }

        if (isset($item['price'])) {
            $variant['price'] = str_replace(',', '.', str_replace(' ', '', $item['price']));
        }
        if (isset($item['old_price'])) {
            $variant['old_price'] = $item['old_price'];
        }
        if (isset($item['stock'])) {
            if ($item['stock'] == '') {
                $variant['stock'] = null;
            } else {
                $variant['stock'] = $item['stock'];
            }
        }

        //если нет артикула, значит мы его сгенерируем из имени
        if (isset($item['sku'])) {
            $variant['sku'] = $item['sku'];
        } else {
            $variant['sku'] = md5($item['name']);
        }
        
        // Если задан артикул варианта, найдем этот вариант и id товара

        $this->db->query('SELECT * FROM __variants WHERE sku = ?', $variant['sku']);
        
        if ($res = $this->db->result_array()) {
            //запишем себе pid и varid
            $pid = $res['product_id'];
            $varid = $res['id'];

            // и обновим товар
            if (!empty_(@$product)) {
                $product['id'] = $pid;
                $this->products->update_product($product);
            }
            // и вариант
            if (!empty_(@$variant)) {
                $variant['id'] = $varid;
                $this->variants->update_variant($variant);
            }
            

            // Обновлен
            $imported_item['status'] = 'updated';
        } else {
        //Если ничего не удалось найти, значит такого товара у нас нет
            if (false !== ($pid = $this->products->add_product($product))) { //сначала добавим товар
                $variant['product_id'] = $pid;
                if (false !== ($varid = $this->variants->add_variant($variant))) {
                    $imported_item['status'] = 'added';
                } else {
                    //вернем false
                    return false;
                }
            }
        }
        
        if (!empty_(@$varid) && !empty_(@$pid)) {
            // Нужно вернуть обновленный товар
            $imported_item['variant'] = $this->variants->get_variant(intval($varid));
            $imported_item['product'] = $this->products->get_product(intval($pid));
    
            // Добавляем категории к товару
            if (!empty_(@$categories_ids)) {
                foreach ($categories_ids as $c_id) {
                    $this->categories->add_product_category($pid, $c_id);
                }
            }
    
            // Изображения товаров
            if (isset($item['images'])) {
                // Изображений может быть несколько, через запятую
                $images = explode(',', $item['images']);
                foreach ($images as $image) {
                    $image = $image;
                    if (!empty_(@$image)) {
                        // Имя файла
                        $image_filename = pathinfo($image, PATHINFO_BASENAME);
                        
                        // Добавляем изображение только если такого еще нет в этом товаре
                        $this->db->query('SELECT filename FROM __images WHERE product_id=? AND (filename=? OR filename=?) LIMIT 1', $pid, $image_filename, $image);
                        if (!$this->db->result_array('filename')) {
                            $this->image->add('products', $pid, $image, true, true);
                        }
                    }
                }
            }
            // Характеристики товаров
            $features = array(); //массив для записи пар id свойства и id значения свойства
            foreach ($item as $feature_name => $feature_value) {
                // Если нет такого названия колонки, значит это название свойства
                if (!in_array($feature_name, $this->internal_columns_names)) {
                    // Свойство добавляем только если для товара указана категория и непустое значение свойства
                    if ($feature_value!=='') {
                        //если у нас уже есть id свойства, просто берем это значение
                        if (isset($GLOBALS['features'][$feature_name])) {
                            $fid = $GLOBALS['features'][$feature_name];
                        } else {
                            //иначе добавляем свойство в базу и пишем в наш глобальный массив
                            $fid = $this->features->add_feature(array('name'=>$feature_name));
                            $GLOBALS['features'][$feature_name] = $fid;
                        }
                            
                        
                        //Если у нас уже есть id значения опции,
                        //пользуемся быстрым методом features->update_options_direct(), но сразу по всем найденным опциям
                        if (isset($GLOBALS['options_uniq'][$feature_value])) {
                            $vid = $GLOBALS['options_uniq'][$feature_value];
                            $features[$fid] = $vid;
                        } else {
                            //иначе пользуемся обычной функцией, а результат записываем в наш суперглобальный массив
                            if (false !== ( $vid = $this->features->update_option($pid, $fid, $feature_value))) {
                                $GLOBALS['options_uniq'][$feature_value] =  $vid ;
                            }
                        }
                    }
                }
            }
            
            //тут пишем разом все свойства, чьи id удалось найти
            if ($this->features->update_options_direct(array('product_id' => $pid, 'features' => $features )) !== false) {
                return $imported_item;
            } else {
                dtimer::log(__METHOD__ . "unable to add options", 1);
                return false;
            }
        }
    }
    
    
    // Отдельная функция для импорта категории
    private function import_category($category)
    {
        // Поле "категория" может состоять из нескольких имен, разделенных subcategory_delimiter-ом
        // Только неэкранированный subcategory_delimiter может разделять категории
        $delimiter = $this->subcategory_delimiter;
        $regex = "/\\DELIMITER((?:[^\\\\\DELIMITER]|\\\\.)*)/";
        $regex = str_replace('DELIMITER', $delimiter, $regex);
        $names = preg_split($regex, $category, 0, PREG_SPLIT_DELIM_CAPTURE);
        $id = null;
        $parent = 0;
        
        // Для каждой категории
        foreach ($names as $name) {
            // Заменяем \/ на /
            $name = str_replace("\\$delimiter", $delimiter, $name);
            if (!empty_(@$name)) {
                // Найдем категорию по имени
                $this->db->query('SELECT id FROM __categories WHERE name=? AND parent_id=?', $name, $parent);
                $id = $this->db->result_array('id');
                
                // Если не найдена - добавим ее
                if (empty_(@$id)) {
                    $id = $this->categories->add_category(array('name'=>$name, 'parent_id'=>$parent, 'meta_title'=>$name,  'meta_keywords'=>$name,  'meta_description'=>$name, 'url'=>translit_url($name)));
                }

                $parent = $id;
            }
        }
        return $id;
    }


    
    // Фозвращает внутреннее название колонки по названию колонки в файле
    private function internal_column_name($name)
    {
        $name = $name;
        $name = str_replace('/', '', $name);
        $name = str_replace('\/', '', $name);
        foreach ($this->columns_names as $i => $names) {
            foreach ($names as $n) {
                if (!empty_(@$name) && preg_match("/^".preg_quote($name)."$/ui", $n)) {
                    return $i;
                }
            }
        }
        return false;
    }
}

$import_ajax = new ImportAjax();

        
$json = json_encode($import_ajax->import());
//~ dtimer::show();


header("Content-type: application/json; charset=UTF-8");
header("Cache-Control: must-revalidate");
header("Pragma: no-cache");
header("Expires: -1");	
print $json;
