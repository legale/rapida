<?PHP

/**
 * Этот класс использует шаблон index.tpl,
 * который содержит всю страницу кроме центрального блока
 * По get-параметру module мы определяем что будет содержаться в центральном блоке
 *
 */

require_once('View.php');

class IndexView extends View
{

    //здесь массив соответствия контроллеров из view, они включаются через IndexView
    private $modules = array(
        '/' => 'MainView',
        'catalog' => 'ProductsView',
        'search' => 'ProductsView',
        'products' => 'ProductView',
        'brands' => 'BrandsView',
        'contact' => 'FeedbackView',
        'user' => 'UserView',
        'login' => 'LoginView',
        'register' => 'RegisterView',
        'cart' => 'CartView',
        'order' => 'OrderView',
        'page' => 'PageView',
        'blog' => 'BlogView',
        'wishlist' => 'WishlistView',
    );

    private $modules_dir = 'view/';
    private $cats = [];

    public function __construct()
    {
        parent::__construct();
        dtimer::log(__METHOD__ . " construct");
        //~ print_r($this->root->uri_arr);
    }


    /**
     *
     * Отображение
     *
     */
    function fetch($url = null)
    {
        dtimer::log(__METHOD__ . " start");
        // Содержимое корзины
        dtimer::log(__METHOD__ . " fetch ");
        $this->design->assign('cart', $this->cart->get());

        // Категории товаров
        $this->design->assign('categories',  $this->categories->categories_tree);
        $this->design->assign('all_cats', $this->categories->all_categories);

        // Страницы
        $pages = $this->pages->get_pages(array('visible' => 1));
        $this->design->assign('pages', $pages);

        if ($url !== '404') {

            // Текущий модуль (для отображения центрального блока)
            $module = $this->modules[$this->root->uri_arr['path']['module']];
            dtimer::log(__METHOD__ . " module: $module");

            // Если не задан - берем из настроек
            if (empty($module))
                return false;

            // Создаем соответствующий класс
            if (is_file($this->modules_dir . "$module.php")) {
                include_once($this->modules_dir . "$module.php");
                if (class_exists($module)) {
                    $this->main = new $module($this);
                } else {
                    return false;
                }
            } else {
                dtimer::log(__METHOD__ . " module '$module' not found. abort!", 1);
				return false;
			}
        }

        // Создаем основной блок страницы, в случае ошибки сделаем 404
        if ($url === '404' || !$content = $this->main->fetch()) {
            $module = 'PageView';
            if (!class_exists($module)) {
                include_once($this->modules_dir . "$module.php");
            }
            $this->main = new $module($this);
            $content = $this->main->fetch('404');
        }

        // Передаем основной блок в шаблон
        $this->design->assign('content', $content);

        // Передаем название модуля в шаблон, это может пригодиться
        $this->design->assign('module', $module);

        // Создаем текущую обертку сайта (обычно index.tpl)
        $wrapper = $this->design->get_var('wrapper');
        if (is_null($wrapper)) {
            $wrapper = 'index.tpl';
        }

        if (!empty($wrapper)) {
            return $this->body = $this->design->fetch($wrapper);
        } else {
            return $this->body = $content;
        }

    }
}
