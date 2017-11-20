<?PHP

/**
 * Simpla CMS
 *
 * @copyright 	2011 Denis Pikusov
 * @link 		http://simp.la
 * @author 		Denis Pikusov
 *
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
			'products'=> 'ProductView',
			'brands'=> 'ProductsView',
			'contact'=> 'FeedbackView',
			'user'=> 'UserView',
			'login'=> 'LoginView',
			'register'=> 'RegisterView',
			'cart'=> 'CartView',
			'order'=> 'OrderView',
			'page'=> 'PageView',
			'blog'=> 'BlogView',
			);

	public $modules_dir = 'view/';

	public function __construct()
	{
		parent::__construct();
		dtimer::log(__METHOD__ . " construct");
	}

		
	/**
	 *
	 * Отображение
	 *
	 */
	function fetch($url = null)
	{
		dtimer::log(__METHOD__ . " fetch");
		// Содержимое корзины
		$this->design->assign('cart',		$this->cart->get_cart());
	
        // Категории товаров
		$this->design->assign('categories', $this->categories->get_categories_tree());
		
		// Страницы
		$pages = $this->pages->get_pages(array('visible'=>1));
		$this->design->assign('pages', $pages);
		
		if($url !== '404'){
		
			// Текущий модуль (для отображения центрального блока)
			$module = $this->modules[$this->coMaster->uri_arr['path_arr']['module']];
			dtimer::log(__METHOD__ . " module: $module");
			
			// Если не задан - берем из настроек
			if(empty($module))
				return false;

			// Создаем соответствующий класс
			if (is_file($this->modules_dir."$module.php"))
			{
					include_once($this->modules_dir."$module.php");
					if (class_exists($module))
					{
						$this->main = new $module($this);
					} else {
						return false;
					}
			} else {
				return false;
			}
		}

		// Создаем основной блок страницы, в случае ошибки сделаем 404
		if ($url === '404' || !$content = $this->main->fetch())
		{
			$module = 'PageView';
			if(!class_exists($module)){
				include_once($this->modules_dir."$module.php");
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
		if(is_null($wrapper))
			$wrapper = 'index.tpl';
			
		if(!empty($wrapper))
			return $this->body = $this->design->fetch($wrapper);
		else
			return $this->body = $content;

	}
}
