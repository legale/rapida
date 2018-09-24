<?PHP
require_once('api/Simpla.php');

class SettingsAdmin extends Simpla
{
    private $allowed_image_extentions = array('png', 'gif', 'jpg', 'jpeg', 'ico');

    public function fetch()
    {
        $this->passwd_file = $this->config->root_dir . '/simpla/.passwd';
        $this->htaccess_file = $this->config->root_dir . '/simpla/.htaccess';


        $managers = $this->managers->get_managers();
        $this->design->assign('managers', $managers);

        if ($this->request->method('POST')) {
            $this->settings->yandex_metric = $this->request->post('yandex_metric');

            $this->settings->site_name = $this->request->post('site_name');
            $this->settings->company_name = $this->request->post('company_name');
            $this->settings->address = $this->request->post('address');
            $this->settings->phone = $this->request->post('phone');

            $this->settings->date_format = $this->request->post('date_format');
            $this->settings->admin_email = $this->request->post('admin_email');

            $this->settings->order_email = $this->request->post('order_email');
            $this->settings->comment_email = $this->request->post('comment_email');
            $this->settings->notify_from_email = $this->request->post('notify_from_email');

            $this->settings->decimals_point = $this->request->post('decimals_point');
            $this->settings->thousands_separator = $this->request->post('thousands_separator');

            $this->settings->products_num = $this->request->post('products_num');
            $this->settings->products_num_admin = $this->request->post('products_num_admin');
            $this->settings->max_order_amount = $this->request->post('max_order_amount');
            $this->settings->units = $this->request->post('units');

            //пропускать добавление во вторую таблицу из которой задания не удаляются (включать для отладки)
            $this->config->cache['skip_queue_full'] = (bool)$this->request->post('skip_queue_full');
            //кеш
            $this->config->cache['enabled'] = (bool)$this->request->post('cache');
            //Способ сохранения кеша на диск
            $this->config->cache['method'] = $this->request->post('method');
            //отладчик
            $this->config->debug = (bool)$this->request->post('debug');
            //капча
            $this->config->captcha = (bool)$this->request->post('captcha');


            // Водяной знак
            $clear_image_cache = false;
            $overlay = $this->request->files('overlay_file', 'tmp_name');
            if (!empty($overlay) && in_array(pathinfo($this->request->files('overlay_file', 'name'), PATHINFO_EXTENSION), $this->allowed_image_extentions)) {
                if (@move_uploaded_file($overlay, $this->config->root_dir . $this->config->images['overlay_file']))
                    $clear_image_cache = true;
                else
                    $this->design->assign('message_error', 'overlay_is_not_writable');
            }

            if ($this->settings->overlay_ratio != $this->request->post('overlay_ratio')) {
                $this->settings->overlay_ratio = $this->request->post('overlay_ratio');
                $clear_image_cache = true;
            }
            if ($this->settings->overlay_offset_x != $this->request->post('overlay_offset_x')) {
                $this->settings->overlay_offset_x = $this->request->post('overlay_offset_x');
                $clear_image_cache = true;
            }
            if ($this->settings->overlay_offset_y != $this->request->post('overlay_offset_y')) {
                $this->settings->overlay_offset_y = $this->request->post('overlay_offset_y');
                $clear_image_cache = true;
            }
            if ($this->settings->overlay_opacity != $this->request->post('overlay_opacity')) {
                $this->settings->overlay_opacity = $this->request->post('overlay_opacity');
                $clear_image_cache = true;
            }
            if ($this->settings->images_sharpness != $this->request->post('images_sharpness')) {
                $this->settings->images_sharpness = $this->request->post('images_sharpness');
                $clear_image_cache = true;
            }

            if ($this->config->images['crop_factor'] != $this->request->post('crop_factor')) {
                $this->config->images['crop_factor'] = $this->request->post('crop_factor');
                $clear_image_cache = true;
            }
            if ($this->config->images['bg_color'] != $this->request->post('bg_color')) {
                $this->config->images['bg_color'] = $this->request->post('bg_color');
                $clear_image_cache = true;
            }
            if ($this->config->images['imagick'] != $this->request->post('imagick')) {
                $this->config->images['imagick'] = (bool)$this->request->post('imagick');
                $clear_image_cache = true;
            }


//            // Удаление заресайзеных изображений
//            if ($clear_image_cache) {
//                $dir = $this->config->images['resized_images_dir'];
//                if ($handle = opendir($dir)) {
//                    while (false !== ($file = readdir($handle))) {
//                        if ($file != "." && $file != "..") {
//                            @unlink($dir . "/" . $file);
//                        }
//                    }
//                    closedir($handle);
//                }
//            }
            $this->design->assign('message_success', 'saved');
        }
        return $this->design->fetch('settings.tpl');
    }

}

