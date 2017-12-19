<?php

require_once('Simpla.php');

/**
 * Class Image
 */
class Image extends Simpla
{

    private $tables = array();
    /**
     * @var array
     */
    private $allowed_extentions = array('png', 'gif', 'jpg', 'jpeg', 'ico');

    /**
     * @var array
     */
    private $allowed_types = array('blog', 'products', 'categories', 'brands');

    /**
     * Image constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Универсальный метод для добавления изображений
     * @param $type
     * @param $id
     * @return bool
     */
    public function delete($type, $id)
    {
        dtimer::log(__METHOD__ . " type: $type start id: $id");
        //check if type is allowed
        if (!$this->type_check($type)) {
            return false;
        }

        $table = $this->config->db_prefix . 'img_' . $type;

        //id existence check
        $this->db->query("SELECT * FROM $table WHERE `id` = $id");
        $image = $this->db->result_array();
        if (empty_($image)) {
            dtimer::log(__METHOD__ . " id: $id not exists!", 1);
            return false;
        }

        dtimer::log(__METHOD__ . " id: $id found. Trying to continue");

        //extracting vars
        $id = '';
        $item_id = '';
        $basename = '';
        $pos = '';

        extract($image);

        //check if image has pos == 0
        if ($pos == 0) {
            dtimer::log(__METHOD__ . " pos 0 image detected. Trying to find next image with the same item_id: $item_id");
            if (!$image2 = $this->get($type, array('item_id' => $item_id))) {
                $this->db->query("UPDATE `__$type` SET image = '', image_id = '' ");
                dtimer::log(__METHOD__ . " unable to get next image with item_id: $item_id");
            } else {
                $image2 = reset($image2);
                $id2 = $image['id'];
                $image2['pos'] = 0;
                dtimer::log(__METHOD__ . " next image with item_id: $item_id found! id: $id2. Trying to set pos = 0");
                $this->update($type, $id2, $image2);
            }
        }

        $q = $this->db->placehold("DELETE FROM `$table` WHERE `id`=?", $id);
        if ($this->db->query($q)) {
            dtimer::log(__METHOD__ . " image with id: $id deleted!");
        } else {
            dtimer::log(__METHOD__ . " unable to delete image with id: $id ", 1);
            return false;
        }

        //remove image files
        return $this->remove_files($type, $basename);
    }

    /**
     * Проверяет является ли тип допустимым
     * @param $type
     * @return bool
     */
    private function type_check($type)
    {
        dtimer::log(__METHOD__ . " start");
        if (!in_array($type, $this->allowed_types)) {
            dtimer::log(__METHOD__ . " type: $type not allowed", 1);
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param $type
     * @param $filter
     * @return bool
     */
    public function get($type, $filter)
    {
        dtimer::log(__METHOD__ . " start type: $type var_export filter " . var_export($filter, true));
        //check if type is allowed
        if (!$this->type_check($type)) {
            return false;
        }
        $table = $this->config->db_prefix . 'img_' . $type;

        if ($this->db->query("SELECT * FROM `$table` WHERE ?& ORDER BY `pos` ASC", $filter)) {
            return $this->db->results_array(null, 'id');
        } else {
            return false;
        }
    }

    /**
     * @param $type
     * @param $id
     * @param $image
     * @return bool
     */
    public function update($type, $id, $image)
    {
        dtimer::log(__METHOD__ . " start type: $type id: $id var_export image: " . var_export($image, true));
        //check if type is allowed
        if (!$this->type_check($type)) {
            return false;
        }
        $id = (int)$id;
        // allowed fields
        $f = array_flip(array('id', 'item_id', 'basename', 'pos'));
        if (!is_array($image)) {
            dtimer::log(__METHOD__ . " arg image is not an array!", 1);
            return false;
        }
        foreach ($image as $k => $v) {
            if (!isset($f[$k])) {
                dtimer::log(__METHOD__ . " $k is unknown field!", 1);
                return false;
            }
        }

        $table = $this->config->db_prefix . 'img_' . $type;


        //update image
        if (!$this->db->query("UPDATE `$table` SET ?% WHERE id=?", $image, $id)) {
            dtimer::log(__METHOD__ . " unable to update image with id: $id", 1);
            return false;
        }

        //id existence check
        $this->db->query("SELECT * FROM `$table` WHERE `id` = $id");
        $image = $this->db->result_array();
        if (empty_($image)) {
            dtimer::log(__METHOD__ . " unable to get image with id: $id ", 1);
            return false;
        } else {
            $pos = (int)$image['pos'];
            $item_id = $image['item_id'];
        }

        //if pos === 0 update specified type table
        if (isset($pos) && $pos === 0) {
            dtimer::log(__METHOD__ . " new pos 0 image detected. Trying to update table __$type");
            $basename = $image['basename'];
            if ($this->db->query("UPDATE `__$type` SET `image` = '$basename', image_id = $id  WHERE `id`=$item_id")) {
            } else {
                dtimer::log(__METHOD__ . " unable to update image table: __$type id: $item_id", 1);
                return false;
            }
        }

        return true;
    }

    /**
     * Method for delete image files
     * @param $type
     * @param $basename
     * @return bool
     */
    private function remove_files($type, $basename)
    {
        dtimer::log(__METHOD__ . " start type: $type basename: $basename");
        //check if type is allowed
        if (!$this->type_check($type)) {
            return false;
        }

        $table = $this->config->db_prefix . 'img_' . $type;
        $this->db->query("SELECT count(*) as count FROM $table WHERE `basename`=? LIMIT 1", $basename);
        $count = $this->db->result_array('count');
        if ($count > 0) {
            return true;
        } else if ($count == 0) {
            //looking for dirs with images
            $path_res = $this->config->root_dir . 'img/' . $type . '_resized';
            $path_orig = $this->config->root_dir . 'img/' . $type;

            //delete original file first
            dtimer::log(__METHOD__ . " trying to delete original file: " . $path_orig . '/' . $basename);
            @unlink($path_orig . '/' . $basename);

            if (false !== ($path_array = $this->scandir($path_res, 'dir'))) {
                foreach ($path_array as $dir) {
                    dtimer::log(__METHOD__ . " trying to delete: " . $dir . '/' . $basename);
                    @unlink($dir . '/' . $basename);
                }
                return true;
            }
            return false;
        }

    }

    /**
     * @param $path
     * @param string $flag
     * @return array|bool
     */
    public function scandir($path, $flag = 'all')
    {
        dtimer::log(__METHOD__ . " start path: $path flag: $flag");
        if (is_dir($path)) {
            $path = realpath($path);
            $dh = opendir($path);
        } else {
            dtimer::log(__METHOD__ . " $path is not a directory or not exists", 2);
            return array();
        }

        $res = array();
        while (false !== ($e = readdir($dh))) {
            if ($e === '.' || $e === '..') {
                continue;
            }

            $filepath = $path . '/' . $e;
            if ($this->is_file_dir_link($filepath, $flag)) {
                $res[] = $filepath;
            }
        }
        closedir($dh);

        return $res;
    }

    /**
     * @param $path
     * @param $flag
     * @return bool
     */
    private function is_file_dir_link($path, $flag)
    {
        switch ($flag) {
            case 'all':
                return true;
            case 'file':
                return is_file($path);
            case 'dir':
                return is_dir($path);
            case 'link':
                return is_link($path);
            default:
                return false;
        }
    }

    /**
     * @param $src
     * @param $w
     * @param $h
     * @return bool|string
     */
    public function resize($src, $w, $h)
    {
        dtimer::log(__METHOD__ . " start src: $src w: $w h: $h");
//        return false;
        //generate absolute path
        $src_absolute = $this->config->root_dir . $src;
        if (!file_exists($src_absolute)) {
            dtimer::log(__METHOD__ . " $src_absolute file not exists!", 1);
            return false;
        }
        //create dst filepath
        $pi = pathinfo($src);
        $root = $this->config->root_dir;
        $dirname = $pi['dirname'];
        $dirname_dst = $this->gen_resize_dirname($dirname, $w, $h);
        $filename = $pi['filename'];
        $ext = $pi['extension'];
        $dst = $dirname_dst . '/' . $filename . '.' . strtolower($ext);
        $dst_absolute = $root . $dst;

//        return false;
        if (class_exists('Imagick') && $this->config->use_imagick) {
            $res = $this->image_constrain_imagick($src_absolute, $dst_absolute, $w, $h);
        } else {
            $res = $this->image_constrain_gd($src_absolute, $dst_absolute, $w, $h);
        }
//        print $dst_absolute;
//        return false;
        return $res ? $dst : false ;
    }

    /**
     * Принимает на входе путь к файлу и размеры, путь задается относительно корневой директории
     * Например, /img/originals, или img/originals/
     * @param $dirname
     * @param $w
     * @param $h
     * @return bool|string
     */
    private function gen_resize_dirname($dirname, $w, $h)
    {
        dtimer::log(__METHOD__ . " start $dirname");
        $root = $this->config->root_dir;
        $dirname_array = explode('/', trim($dirname, '/'));
        //reverse array
        //expected element 1 = 'img' and element 0 is originals directory
        $dirname_array = array_reverse($dirname_array);
        if ($dirname_array[1] !== 'img') {
            dtimer::log(__METHOD__ . " path error! '$dirname_array[1]' only /img/* is allowed", 1);
            return false;
        }
        $res = $dirname_array;
        $res[0] .= '_resized/' . $w . 'x' . $h;
        $res = array_reverse($res);

        $res = implode('/', $res);
        dtimer::log(__METHOD__ . " res: $res");
        //create dst dir if not exists

        $this->mkdir($root . $res);

        return $res;
    }

    /**
     * @param $path
     * @return bool
     */
    private function mkdir($path)
    {
        //create dst dir if not exists
        if (file_exists($path)) {
            return true;
        }

        dtimer::log(__METHOD__ . " $path is not exists! trying to mkdir");
        //octdec() because the function mkdir() takes second parameter in decimal format
        //so chmod 755 octal needs to be converted in decimal first
        if (mkdir($path, octdec((int)$this->config->resize_chmod), true)) {
            dtimer::log(__METHOD__ . " created: $path ");
        } else {
            dtimer::log(__METHOD__ . " failed to create: $path ", 1);
            return false;
        }

    }

    /*
     * Принимает на входе путь к директории оригинала изображения и его размеры, а отдает путь для ресайза
     */

    /**
     * Создание превью средствами imagick
     * @param $src_file исходный файл
     * @return bool
     */
    private function image_constrain_imagick($src_file, $dst_file, $max_w, $max_h, $watermark = null, $watermark_offet_x = 0, $watermark_offet_y = 0, $watermark_opacity = 1, $sharpen = 0.2)
    {
        $thumb = new Imagick();

        // Читаем изображение
        if (!$thumb->readImage($src_file))
            return false;

        // Размеры исходного изображения
        $src_w = $thumb->getImageWidth();
        $src_h = $thumb->getImageHeight();

        // Нужно ли обрезать?
        if (!$watermark && ($src_w <= $max_w) && ($src_h <= $max_h)) {
            // Нет - просто скопируем файл
            if (!copy($src_file, $dst_file))
                return false;
            return true;
        }

        // Размеры превью при пропорциональном уменьшении
        list($dst_w, $dst_h) = $this->calc_contrain_size($src_w, $src_h, $max_w, $max_h);

        // Уменьшаем
        $thumb->thumbnailImage($dst_w, $dst_h);

        // Устанавливаем водяной знак
        if ($watermark && is_readable($watermark)) {
            $overlay = new Imagick($watermark);
            //$overlay->setImageOpacity($watermark_opacity);
            //$overlay_compose = $overlay->getImageCompose();
            $overlay->evaluateImage(Imagick::EVALUATE_MULTIPLY, $watermark_opacity, Imagick::CHANNEL_ALPHA);

            // Get the size of overlay
            $owidth = $overlay->getImageWidth();
            $oheight = $overlay->getImageHeight();

            $watermark_x = min(($dst_w - $owidth) * $watermark_offet_x / 100, $dst_w);
            $watermark_y = min(($dst_h - $oheight) * $watermark_offet_y / 100, $dst_h);

        }


        // Анимированные gif требуют прохода по фреймам
        foreach ($thumb as $frame) {
            // Уменьшаем
            $frame->thumbnailImage($dst_w, $dst_h);

            /* Set the virtual canvas to correct size */
            $frame->setImagePage($dst_w, $dst_h, 0, 0);

            // Наводим резкость
            if ($sharpen > 0)
                $thumb->adaptiveSharpenImage($sharpen, $sharpen);

            if (isset($overlay) && is_object($overlay)) {
                // $frame->compositeImage($overlay, $overlay_compose, $watermark_x, $watermark_y, imagick::COLOR_ALPHA);
                $frame->compositeImage($overlay, imagick::COMPOSITE_OVER, $watermark_x, $watermark_y, imagick::COLOR_ALPHA);
            }

        }

        // Убираем комменты и т.п. из картинки
        $thumb->stripImage();

        //		$thumb->setImageCompressionQuality(100);

        // Записываем картинку
        if (!$thumb->writeImages($dst_file, true))
            return false;

        // Уборка
        $thumb->destroy();
        if (isset($overlay) && is_object($overlay))
            $overlay->destroy();

        return true;
    }

    /**
     * Вычисляет размеры изображения, до которых нужно его пропорционально уменьшить, чтобы вписать в квадрат $max_w x $max_h
     * @param src_w ширина исходного изображения
     * @param src_h высота исходного изображения
     * @param max_w максимальная ширина
     * @param max_h максимальная высота
     * @return array(w, h)
     */
    function calc_contrain_size($src_w, $src_h, $max_w = 0, $max_h = 0)
    {
        dtimer::log(__METHOD__ . " start");

        if ($src_w == 0 || $src_h == 0)
            return false;

        $dst_w = $src_w;
        $dst_h = $src_h;

        if ($src_w > $max_w && $max_w > 0) {
            $dst_h = $src_h * ($max_w / $src_w);
            $dst_w = $max_w;
        }
        if ($dst_h > $max_h && $max_h > 0) {
            $dst_w = $dst_w * ($max_h / $dst_h);
            $dst_h = $max_h;
        }
        return array($dst_w, $dst_h);
    }

    /**
     * Создание превью средствами gd
     * @param $src_file
     * @param $dst_file
     * @param $max_w
     * @param $max_h
     * @param null $watermark
     * @param int $watermark_offet_x
     * @param int $watermark_offet_y
     * @param int $watermark_opacity
     * @return bool
     */
    private function image_constrain_gd($src_file, $dst_file, $max_w, $max_h, $watermark = null, $watermark_offet_x = 0, $watermark_offet_y = 0, $watermark_opacity = 1)
    {
        dtimer::log(__METHOD__ . " start $src_file");
        $quality = 100;
        if (!file_exists($src_file)) {
            dtimer::log(__METHOD__ . " file not found: $src_file", 1);
            return false;
        }

        // Параметры исходного изображения
        $src_size = array_values(getimagesize($src_file));
        dtimer::log(__METHOD__ . " src_size: " . var_export($src_size, true));

        @list($src_w, $src_h, $src_type) = $src_size;


        $src_type = image_type_to_mime_type($src_type);
        dtimer::log(__METHOD__ . " src_type: $src_type");


        if (empty($src_w) || empty($src_h) || empty($src_type)) {
            return false;
        }

        // Нужно ли обрезать?
        if (!$watermark && ($src_w <= $max_w) && ($src_h <= $max_h)) {
            // Нет - просто скопируем файл
            if (!copy($src_file, $dst_file)) {
                return false;
            }
            return true;
        }
        // Размеры превью при пропорциональном уменьшении
        @list($dst_w, $dst_h) = $this->calc_contrain_size($src_w, $src_h, $max_w, $max_h);

        // Читаем изображение
        switch ($src_type) {
            case 'image/jpeg' :
                $src_img = imageCreateFromJpeg($src_file);
                break;
            case 'image/gif' :
                $src_img = imageCreateFromGif($src_file);
                break;
            case 'image/png' :
                $src_img = imageCreateFromPng($src_file);
                imagealphablending($src_img, true);
                break;
            default :
                return false;
        }

        if (empty($src_img))
            return false;

        $src_colors = imagecolorstotal($src_img);

        // create destination image (indexed, if possible)
        if ($src_colors > 0 && $src_colors <= 256)
            $dst_img = imagecreate($dst_w, $dst_h);
        else
            $dst_img = imagecreatetruecolor($dst_w, $dst_h);

        if (empty($dst_img))
            return false;

        $transparent_index = imagecolortransparent($src_img);
        if ($transparent_index >= 0 && $transparent_index <= 128) {
            $t_c = imagecolorsforindex($src_img, $transparent_index);
            $transparent_index = imagecolorallocate($dst_img, $t_c['red'], $t_c['green'], $t_c['blue']);
            if ($transparent_index === false)
                return false;
            if (!imagefill($dst_img, 0, 0, $transparent_index))
                return false;
            imagecolortransparent($dst_img, $transparent_index);
        } // or preserve alpha transparency for png
        elseif ($src_type === 'image/png') {
            if (!imagealphablending($dst_img, false))
                return false;
            $transparency = imagecolorallocatealpha($dst_img, 0, 0, 0, 127);
            if (false === $transparency)
                return false;
            if (!imagefill($dst_img, 0, 0, $transparency))
                return false;
            if (!imagesavealpha($dst_img, true))
                return false;
        }

        // resample the image with new sizes
        if (!imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $dst_w, $dst_h, $src_w, $src_h))
            return false;

        // Watermark
        if (!empty($watermark) && is_readable($watermark)) {
            $overlay = imagecreatefrompng($watermark);

            // Get the size of overlay
            $owidth = imagesx($overlay);
            $oheight = imagesy($overlay);

            $watermark_x = min(($dst_w - $owidth) * $watermark_offet_x / 100, $dst_w);
            $watermark_y = min(($dst_h - $oheight) * $watermark_offet_y / 100, $dst_h);

            imagecopy($dst_img, $overlay, $watermark_x, $watermark_y, 0, 0, $owidth, $oheight);
            //imagecopymerge($dst_img, $overlay, $watermark_x, $watermark_y, 0, 0, $owidth, $oheight, $watermark_opacity*100);

        }


        // recalculate quality value for png image
        if ('image/png' === $src_type) {
            $quality = round(($quality / 100) * 10);
            if ($quality < 1)
                $quality = 1;
            elseif ($quality > 10)
                $quality = 10;
            $quality = 10 - $quality;
        }

        // Сохраняем изображение
        switch ($src_type) {
            case 'image/jpeg' :
                return imageJpeg($dst_img, $dst_file, $quality);
            case 'image/gif' :
                return imageGif($dst_img, $dst_file, $quality);
            case 'image/png' :
                imagesavealpha($dst_img, true);
                return imagePng($dst_img, $dst_file, $quality);
            default :
                return false;
        }
    }

    /**
     * @param $type
     * @param $item_id
     * @param $url
     * @return bool|string
     */
    public function download_new($type, $item_id, $url)
    {
        dtimer::log(__METHOD__ . " 'type: $type item_id: $item_id url: $url");
        //check args types
        if (!is_int($item_id) || $item_id < 0 || !in_array($type, $this->allowed_types)) {
            dtimer::log(__METHOD__ . " args error");
            return false;
        }
        //item_id existence check
        if (!$this->item_exists($type, $item_id)) {
            return false;
        }

        // Имя оригинального файла
        $pi = pathinfo($url);
        dtimer::log(__METHOD__ . "pathinfo url: " . var_export($pi, true));
        if (isset($pi['extension'])) {
            $ext = $pi['extension'];
        } else {
            dtimer::log(__METHOD__ . " extension not found", 2);
            return false;
        }


        if (!$tmp = $this->curl->download($url)) {
            dtimer::log(__METHOD__ . " download failed");
            return false;
        }

        $dir = $this->gen_original_dirname($type);
        $new_basename = md5_file($tmp) . '.' . $ext;
        $root = $this->config->root_dir;
        dtimer::log(__METHOD__ . " basename: $new_basename");
        $filepath = $dir . $new_basename;
        $filepath_absolute = $root . $filepath;

        if (!file_exists($root . $dir . $new_basename)) {
            if (!rename($tmp, $filepath_absolute)) {
                dtimer::log(__METHOD__ . " rename failed $filepath_absolute", 1);
            }
        }

        if (file_exists($filepath_absolute)) {
            if (false !== ($id = $this->add($type, $item_id, $new_basename))) {
                return array('id' => $id, 'item_id' => $item_id, 'filepath_absolute' => $filepath_absolute, 'filepath' => $filepath, 'basename' => $new_basename);
            }
            @unlink($filepath_absolute);
        }
        return false;
    }

    /**
     * @param $type
     * @param $item_id
     * @return bool
     */
    private function item_exists($type, $item_id)
    {
        $this->db->query("SELECT `id` FROM `__$type` WHERE `id` = $item_id");
        if (empty($this->db->result_array('id'))) {
            return false;
        }
        return true;
    }

    /**
     * @param $type
     * @return bool|string
     */
    public function gen_original_dirname($type)
    {
        dtimer::log(__METHOD__ . " start $type");
        $root = $this->config->root_dir;
        if (!in_array($type, $this->allowed_types)) {
            dtimer::log(__METHOD__ . " $type is not allowed type");
            return false;
        }
        $res = "img/$type/";

        //create dst dir if not exists
        $this->mkdir($root . $res);

        return $res;
    }

    /**
     * Метод предназначен для создания таблицы с изображениями какой-то сущности
     * @param $type
     * @param $item_id
     * @param $basename
     * @return bool
     */
    public function add($type, $item_id, $basename, $skip_item_check = false, $skip_table_check = false)
    {
        dtimer::log(__METHOD__ . " start type: $type item_id: $item_id basename: $basename");
        //check if type is allowed
        if (!$this->type_check($type)) {
            return false;
        }

        //item_id existence check
        dtimer::log(__METHOD__ . " item_id existence check");
        if (!$skip_item_check && !$this->item_exists($type, $item_id)) {
            return false;
        }else{
            dtimer::log(__METHOD__ . " item_id check skipped");
        }

        $table = $this->config->db_prefix . 'img_' . $type;

        //table existence check
        dtimer::log(__METHOD__ . " table $table existence check");
        if (!$skip_table_check && !$this->db->query("SELECT `id` FROM `$table` LIMIT 1")) {
            dtimer::log(__METHOD__ . " table $table not exists! Trying to create.");
            if (false === $this->create_table($type)) {
                dtimer::log(__METHOD__ . " unable to create table $table", 1);
                return false;
            }
        } else {
            dtimer::log(__METHOD__ . " table check skipped!");
        }

        $item_id = (int)$item_id;
        $this->db->query("SELECT MAX(pos) as pos 
		FROM `$table` WHERE `item_id` = ? ", $item_id);
        $pos = $this->db->result_array('pos');
        if (!empty_($pos)) {
            $pos = $pos + 1;
        } else {
            $pos = 0;
        }

        $q = $this->db->placehold("INSERT INTO `$table` SET `item_id` = ?, `basename` = ?, pos = ?", $item_id, $basename, $pos);
        if ($this->db->query($q)) {
            $id = $this->db->insert_id();
        } else {
            return false;
        }

        //если удалось добавить изображение с позицией 0, запишем его в соответствующую типу таблицу
        if ($pos === 0 && isset($id)) {
            $this->db->query("UPDATE `__$type` SET image = ? , image_id = ? WHERE id = ?", $basename, $id, $item_id);
        }

        dtimer::log(__METHOD__ . " adding image completed! new image id: $id");
        return ($id);
    }

    /**
     * Универсальный метод для получения изображения по id или item_id
     * @param $type
     * @return bool
     */
    private function create_table($type)
    {
        dtimer::log(__METHOD__ . " start");
        //check if type is allowed
        if (!$this->type_check($type)) {
            return false;
        }
        return $this->db->query("CREATE TABLE `s_img_$type` (
			 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			 `item_id` int(10) unsigned NOT NULL,
			 `basename` varchar(255) NOT NULL,
			 `pos` smallint(5) unsigned NOT NULL DEFAULT '0',
			 PRIMARY KEY (`id`, `item_id`),
			 KEY `position` (`pos`),
			 KEY `item_id` (`item_id`) USING BTREE,
			 KEY `basename` (`basename`) USING BTREE
			) ENGINE=InnoDB DEFAULT CHARSET=utf8
			");
    }

    /**
     * @param $type
     * @param $item_id
     * @param $url
     * @return bool|string
     */
    public function download($type, $id)
    {
        dtimer::log(__METHOD__ . " start type: $type id: $id");
        //check args types
        if (!is_int($id) || $id < 0 || !in_array($type, $this->allowed_types)) {
            dtimer::log(__METHOD__ . " args error");
            return false;
        }

        //get image
        if ( false === ($image = $this->get($type, array('id' => $id)))) {
            dtimer::log(__METHOD__ . " unable to get image with id: $id. aborting!", 1);
            return false;
        }

        $image = reset($image);
        $url = $image['basename'];
        $id = $image['id'];
        $item_id = $image['item_id'];

        //url check
        dtimer::log(__METHOD__ . " url check");
        if(!$this->is_url($url)){
            dtimer::log(__METHOD__ . " is not an url basename: $url", 1);
            return false;
        }

        // Имя оригинального файла
        $pi = pathinfo($image['basename']);
        dtimer::log(__METHOD__ . "pathinfo url: " . var_export($pi, true));
        if (isset($pi['extension'])) {
            $ext = $pi['extension'];
        } else {
            dtimer::log(__METHOD__ . " extension not found", 2);
            return false;
        }

        if (!$tmp = $this->curl->download($url)) {
            dtimer::log(__METHOD__ . " download failed");
            return false;
        }

        $dir = $this->gen_original_dirname($type);
        $new_basename = md5_file($tmp) . '.' . $ext;
        $root = $this->config->root_dir;
        dtimer::log(__METHOD__ . " basename: $new_basename");
        $filepath = $dir . $new_basename;
        $filepath_absolute = $root . $filepath;

        if (file_exists($filepath_absolute)) {
			dtimer::log(__METHOD__." $filepath_absolute found! deleting ");
			unlink($filepath_absolute);
		}
		
		if (!rename($tmp, $filepath_absolute)) {
			dtimer::log(__METHOD__ . " rename $filepath_absolute failed. aborting!", 1);
			return false;
		}  

        if (file_exists($filepath_absolute)) {
            dtimer::log(__METHOD__ . " downloaded file moved to: $filepath_absolute Updating db.");
            if (false !== ($id = $this->update($type, $id, array('basename' => $new_basename)))) {
                dtimer::log(__METHOD__ . " update image with id: $id completed! new basename: $new_basename");
                return array('id' => $id, 'item_id' => $item_id, 'filepath_absolute' => $filepath_absolute, 'filepath' => $filepath, 'basename' => $new_basename);
            }
            dtimer::log(__METHOD__ . " deleting: $filepath_absolute", 2);
            @unlink($filepath_absolute);
        }
        return false;
    }

    /**
     * @param $type
     * @param $item_id
     * @param $url
     */
    public function upload($type, $item_id, $tmp_name, $basename)
    {
        dtimer::log(__METHOD__ . " start type: $type item_id: $item_id tmp_name: $tmp_name basename: $basename");
        if (!file_exists($tmp_name)) {
            dtimer::log(__METHOD__ . " $tmp_name not exists!", 1);
            return false;
        }

        //item_id existence check
        if (!$this->item_exists($type, $item_id)) {
            return false;
        }

        if (!in_array($type, $this->allowed_types)) {
            return false;
        }

        // Имя оригинального файла
        $pi = pathinfo($basename);
        dtimer::log(__METHOD__ . "pathinfo url: " . var_export($pi, true));
        if (isset($pi['extension'])) {
            $ext = $pi['extension'];
        } else {
            dtimer::log(__METHOD__ . " extension not found", 2);
            return false;
        }
        $root = $this->config->root_dir;
        $new_basename = md5_file($tmp_name) . '.' . $ext;
        $filepath = $this->gen_original_dirname($type) . $new_basename;
        $filepath_absolute = $root . $filepath;
        dtimer::log(__METHOD__ . " filepath_absolute: $filepath_absolute");

        if (!file_exists($filepath_absolute)) {
            if (!rename($tmp_name, $filepath_absolute)) {
                dtimer::log(__METHOD__ . " rename failed! $filepath_absolute", 1);
            }
        }

        if (file_exists($filepath_absolute)) {
            if (false !== ($id = $this->add($type, $item_id, $new_basename))) {
                return array('id' => $id, 'item_id' => $item_id, 'filepath_absolute' => $filepath_absolute, 'filepath' => $filepath, 'basename' => $new_basename);
            }
            @unlink($filepath_absolute);
        }
        return false;
    }

    public function is_url($url)
    {
        dtimer::log(__METHOD__ . " start url: $url");
        $s = strtolower(substr($url, 0, 4));
        if ($s === 'http') {
            $s = strtolower(substr($url, 0, 8));
            if ($s === 'https://' || substr($s, 0, 7) === 'http://') {
                return true;
            }
        }
        return false;
    }


}
