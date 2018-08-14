return array (
  'http_headers' => 'Access-Control-Allow-Origin: *',
  'user_agent' => 'Mozilla/5.0 (Windows NT 10.0, Win64, x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36',
  'host' => 'sale-buy.it',
  'db' => 
  array (
    'db_prefix' => 's_',
    'db_charset' => 'UTF8',
    'db_timezone' => '+03:00',
    'db_sql_mode' => '',
  ),
  'php' => 
  array (
    'error_reporting' => 32767,
    'locale' => 'en_US.utf8',
    'logfile' => 'simpla/log.txt',
    'timezone' => 'Etc/GMT+3',
  ),
  'smarty' => 
  array (
    'smarty_compile_check' => true,
    'smarty_caching' => false,
    'smarty_cache_lifetime' => 0,
    'smarty_debugging' => false,
    'smarty_html_minify' => false,
  ),
  'images' => 
  array (
    'imagick' => true,
    'resize_chmod' => '775',
    'overlay' => true,
    'original_images_dir' => 'files/originals/',
    'categories_images_dir' => 'files/categories/',
    'brands_images_dir' => 'files/brands/',
    'overlay_file' => 'simpla/files/watermark/watermark.png',
    'bg_color' => '255,255,255',
    'crop_factor' => '1',
  ),
  'cache' => 
  array (
    'enabled' => true,
    'method' => 'msgpack',
    'default_chmod' => '775',
    'securityKey' => 'mysite',
    'htaccess' => true,
    'path' => './cache',
    'JSON_UNESCAPED_UNICODE' => true,
    'codepage' => 'cp1251',
    'skip_queue_full' => true,
  ),
  'debug' => true,
  'captcha' => false,
  'max_upload_filesize' => '',
);