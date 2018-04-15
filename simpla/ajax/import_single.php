<?php
require_once('../../api/Simpla.php');

if (defined("PHP7")) {
    eval("declare(strict_types=1);");
}


/**
 * Class Import_single
 */
class Import_single extends Simpla
{

    // Соответствие полей в базе и имён колонок в файле
    const DELIM = '|';

    // Соответствие имени колонки и поля в базе
    const SUBDELIM = '/';
    const COL_DELIM = ';'; // Временная папка
    const COL_ENCLOSURE = '"';           // Временный файл
    const COL_ESCAPE = '\\';           // Временный файл
    private $col_names = array(
        'name' => array('product', 'name', 'товар', 'название', 'наименование'),
        'url' => array('url', 'адрес'),
        'visible' => array('visible'),
        'featured' => array('featured', 'рекомендуемый'),
        'category' => array('category', 'категория'),
        'vendor' => array('vendor', 'brand'),
        'variant' => array('variant', 'вариант'),
        'price' => array('price', 'цена'),
        'old_price' => array('compare price', 'старая цена'),
        'sku' => array('sku'),
        'stock' => array('stock', 'склад'),
        'meta_title' => array('meta title', 'заголовок страницы'),
        'meta_keywords' => array('meta keywords', 'ключевые слова'),
        'meta_description' => array('meta description', 'описание страницы'),
        'annotation' => array('annotation', 'аннотация', 'краткое описание'),
        'description' => array('description', 'описание'),
        'images' => array('images', 'изображения')
    );                       // Разделитель значений одной колонки
    private $import_files_dir = '../files/import/';                   //Разделитель колонок
    private $import_file = 'import_single.csv';                   //Контейнер колонки
    private $products_count = 500;
    private $columns = array();
    private $features_array = array();
    private $options_array = array();

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return array|bool
     */
    public function import()
    {
        if (!$this->users->check_access('import')) {
            return false;
        }

        //сюда будем писать результат импорта
        $result = array();

        //получим массив id=>название свойства
        //Чтобы не делать это постоянно на каждом свойстве
        $filter = array('return' => array('key' => 'name', 'col' => 'id'));
        //получим без кеша на первом цикле
        if (empty($_GET['from'])) {
            $filter['force_no_cache'] = true;
        }
        $this->features_array = $this->features->get_features_ids($filter);

        //теперь уникальные значения опций
        //поставим выполнение запроса без кеша только для первой позиции импорта
        $filter = array('return' => array('key' => 'val', 'col' => 'id'));
        dtimer::log(__METHOD__ . " from: " . var_export($this->request->get('from'), true));
        if (empty($_GET['from'])) {
            $filter['force_no_cache'] = true;
        }
        $this->options_array = $this->features->get_options_ids($filter);




        // Определяем колонки из первой строки файла
        $f = fopen($this->import_files_dir . $this->import_file, 'r');

        $str =  fgets($f, 4096);
        $this->columns = str_getcsv($str, self::COL_DELIM, self::COL_ENCLOSURE, self::COL_ESCAPE);

        // Заменяем имена колонок из файла на внутренние имена колонок
        foreach ($this->columns as $i => $col) {
            $internal_name = $this->get_col_name($col);
            if ($internal_name !== false) {
                $this->columns[$i] = $internal_name;
            }
        }

        // Проверяем 2 условия: 1. Есть product_id и число колонок не менее 2
        if (!in_array('product_id', $this->columns) || count($this->columns) < 2) {
            dtimer::log(__METHOD__ . " product_id not found or columns less than 2! Abort. ", 1);
            return false;
        }

        // Переходим на заданную позицию, если импортируем не сначала
        if (isset($_GET['from'])) {
            $from = (int)$_GET['from'];
            fseek($f, $from);
        } else {
            $from = 0;
        }

        // Массив импортированных товаров
        $imported_items = array();

        // Проходимся по строкам, пока не конец файла
        // или пока не импортировано достаточно строк для одного запроса
        for ($k = 0; !feof($f) && $k < $this->products_count; $k++) {
            // Читаем строку
            $str =  fgets($f, 4096);
            $line = str_getcsv($str, self::COL_DELIM, self::COL_ENCLOSURE, self::COL_ESCAPE);

            $product = null;


            if (is_array($line)) {
                // Проходимся по колонкам строки
                foreach ($this->columns as $i => $col) {
                    // Создаем массив item[название_колонки]=значение и сразу обрезаем пробелы по краям

                    if (isset($line[$i])) {
                        $item[$col] = trim($line[$i]);
                    }
                }
            }


            // Импортируем этот товар
            if (isset($item)) {
                $imported_item = $this->import_item($item);
                if (isset($imported_item)) {
                    $imported_items[] = $imported_item;
                } else {
                    print "\n unable to import item! start from offset:$from. Failed item: " . var_export($item, true);
                    return false;
                }
            }
        }

        // Запоминаем на каком месте закончили импорт
        $from = ftell($f);

        // И закончили ли полностью весь файл
        $result['end'] = feof($f);

        fclose($f);
        $size = filesize($this->import_files_dir . $this->import_file);

        // Создаем массив результата
        $result['from'] = $from;          // На каком месте остановились
        $result['total_size'] = $size;     // Размер всего файла
        //$result['items'] = $imported_items;   // Импортированные товары

        return $result;
    }

    // Импорт одного товара $item[column_name] = value;

    /**
     * @param $name
     * @return bool|int|string
     */
    private function get_col_name($name)
    {
        $name = mb_strtolower($name);
        foreach ($this->col_names as $i => $names) {
            if (in_array($name, $names)) {
                return $i;
            }
        }
        return false;
    }

    /**
     * @param $category
     * @return null
     */
    private function import_category($category)
    {
        // Поле "категория" может состоять из нескольких имен
        //~ print_r($category);
        //~ die;
        $names = array_filter(explode(self::SUBDELIM, $category));

        $id = null;
        $parent = 0;

        // Для каждой категории
        foreach ($names as $name) {
            if (isset($this->cats[$name])) {
                $id = $parent = $this->cats[$name]['id'];
                continue;
            }

            // Найдем категорию по имени
            $this->db->query('SELECT id FROM __categories WHERE name=? AND parent_id=?', $name, $parent);
            $id = $this->db->result_array('id');

            // Если не найдена - добавим ее
            if (isset($id)) {
                $id = $this->categories->add_category(array('name' => $name, 'parent_id' => $parent, 'meta_title' => $name, 'meta_keywords' => $name, 'meta_description' => $name, ));
            }

            $parent = $id;
        }

        return $id;
    }

    /**
     * @param $item
     * @return array|bool
     */
    private function import_item($item)
    {
        dtimer::log(__METHOD__ . " start: " . var_export($item, true));

        $imported_item = array();


        // Проверим 2 условия: наличие product_id и количество колонок не менее 2
        if (!isset($item['product_id']) && count($item) < 2) {
            dtimer::log(__METHOD__ . " product_id is not set or columns is less than 2! Abort! ", 1);
            return false;
        }

        // Массив для товара
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


        $product['id'] = $pid = $item['product_id'];
        unset($item['product_id']);

        // Если задан бренд
        if (isset($item['vendor'])) {
            // Найдем его по имени
            $brand = $this->brands->get_brand(translit_ya($item['vendor']));
            if ($brand !== false) {
                $product['brand_id'] = $brand['id'];
            } else {
                // Создадим, если не найден
                $product['brand_id'] = $this->brands->add_brand(array('name' => $item['vendor']));
                if ($product['brand_id'] === false) {
                    dtimer::log(__METHOD__ . " failed on add_brand", 1);
                    return false;
                }
            }
            unset($item['vendor']);
        }


        // Если задана категория
        if (isset($item['category'])) {
            foreach (explode(self::DELIM, $item['category']) as $c) {
                $cat_ids[] = $this->import_category($c);
            }
            // Добавляем категории к товару
            if (isset($cat_ids)) {
                foreach ($cat_ids as $cid) {
                    $this->categories->add_product_category($pid, $cid);
                }
            }
            unset($item['category']);
        }

        //Обновляем товар
        $this->products->update_product($product);



        // Характеристики товаров
        $features = array(); //массив для записи пар id свойства и id значения свойства
        foreach ($item as $f_name => $f_value) {
            // Если нет такого названия колонки, значит это название свойства
            if (!in_array($f_name, $this->col_names)) {
                // Свойство добавляем только если значение свойства непустое
                if ($f_value !== '') {
                    //если у нас уже есть id свойства, просто берем это значение
                    if (isset($this->features_array[$f_name])) {
                        $fid = $this->features_array[$f_name];
                    } else {
                        //иначе добавляем свойство в базу и пишем в наш глобальный массив
                        $fid = $this->features->add_feature(array('name' => $f_name));
                        if(!isset($fid)){
                            dtimer::log(__METHOD__." unable to add feature $f_name Abort! ",1);
                            return false;
                        }
                        $this->features_array[$f_name] = $fid;
                    }


                    //Если у нас уже есть id значения опции,
                    //пользуемся быстрым методом features->update_options_direct(), но сразу по всем найденным опциям
                    if (isset($this->options_array[$f_value])) {
                        $vid = $this->options_array[$f_value];
                        $features[$fid] = $vid;
                    } else {
                        //иначе пользуемся обычной функцией, а результат записываем в наш суперглобальный массив
                        $vid = $this->features->update_option($pid, $fid, $f_value);
                        if (false !== $vid) {
                            $this->options_array[$f_value] = $vid;
                        } else {
                            dtimer::log(__METHOD__ . " unable to update option pid:$pid fid:$fid val:$f_value", 1);
                            return false;
                        }
                    }
                }
            }
        }


        //тут пишем разом все свойства, чьи id удалось найти
        if (empty($features)) {
        } else if (false !== $this->features->update_options_direct(array('product_id' => $pid, 'features' => $features))) {
        } else {
            dtimer::log(__METHOD__ . " unable to add options", 1);
            return false;
        }


        return $imported_item;
    }


}

$import = new Import_single();


$json = json_encode($import->import());

//dtimer::show();
//die;

header("Content-type: application/json; charset=UTF-8");
header("Cache-Control: must-revalidate");
header("Pragma: no-cache");
header("Expires: -1");


print $json;
