<?PHP
require_once ('api/Simpla.php');

// Этот класс выбирает модуль в зависимости от параметра Section и выводит его на экран
class IndexAdmin extends Simpla
{
	// Соответсвие модулей и названий соответствующих прав
	private $modules_permissions = array(
		'ProductsAdmin' => 'products',
		'ProductAdmin' => 'products',
		'CategoriesAdmin' => 'categories',
		'CategoryAdmin' => 'categories',
		'BrandsAdmin' => 'brands',
		'BrandAdmin' => 'brands',
		'FeaturesAdmin' => 'features',
		'FeatureAdmin' => 'features',
		'OrdersAdmin' => 'orders',
		'OrderAdmin' => 'orders',
		'OrdersLabelsAdmin' => 'labels',
		'OrdersLabelAdmin' => 'labels',
		'UsersAdmin' => 'users',
		'UserAdmin' => 'users',
		'ExportUsersAdmin' => 'users',
		'GroupsAdmin' => 'groups',
		'GroupAdmin' => 'groups',
		'CouponsAdmin' => 'coupons',
		'CouponAdmin' => 'coupons',
		'PagesAdmin' => 'pages',
		'PageAdmin' => 'pages',
		'BlogAdmin' => 'blog',
		'PostAdmin' => 'blog',
		'CommentsAdmin' => 'comments',
		'FeedbacksAdmin' => 'feedbacks',
		'ImportAdmin' => 'import',
		'ImportYmlAdmin' => 'import',
		'ExportAdmin' => 'export',
		'BackupAdmin' => 'backup',
		'SystemAdmin' => 'settings',
		'StatsAdmin' => 'stats',
		'ThemeAdmin' => 'design',
		'StylesAdmin' => 'design',
		'TemplatesAdmin' => 'design',
		'ImagesAdmin' => 'design',
		'SettingsAdmin' => 'settings',
		'CurrencyAdmin' => 'currency',
		'DeliveriesAdmin' => 'delivery',
		'DeliveryAdmin' => 'delivery',
		'PaymentMethodAdmin' => 'payment',
		'PaymentMethodsAdmin' => 'payment',
		'ManagersAdmin' => 'managers',
		'ManagerAdmin' => 'managers',
	);
	//сюда будем писать разрешен ли доступ к модулю
	public $access_granted;

	//сюда будем писать все разрешения пользователя
	public $userperm = array();

	// Конструктор
	public function __construct()
	{
	    // Вызываем конструктор базового класса
		parent::__construct();

		$this->design->set_templates_dir('simpla/design/html');
		$this->design->set_compiled_dir('simpla/design/compiled');

		$this->design->assign('settings', $this->settings);
		$this->design->assign('config', $this->config);
		
		// Администратор
		$user = $this->users->get_user($_SESSION['user_id']);
		//~ print_r($user);
		$this->design->assign('user', $user);

 		// Берем название модуля из get-запроса
		$module = $this->request->get('module', 'string');
		
		
		if (empty($module)){
			$module = 'ProductsAdmin';
		}

		// Проверка прав доступа к модулю
		//это id требуемого разрешения
		$req_perm_id = array_flip($this->users->perm_list)[$this->modules_permissions[$module]];
		
		//проверяем у нашего пользователя
		if(isset($user['perm'][$req_perm_id])){
			
			//запишем доступные пользователю права для шаблонов
			if(isset($user['perm']) && is_array($user['perm'])){
				$this->userperm = array_flip(array_intersect_key($this->users->perm_list, $user['perm']));
				$this->access_granted = true;
			}
		}
		
		// Подключаем файл с необходимым модулем
		require_once ('simpla/' . $module . '.php');  
		
		// Создаем соответствующий модуль
		if (class_exists($module))
			$this->module = new $module();
		else
			die("Error creating $module class");

	}

	function fetch()
	{
		$currency = $this->money->get_currency();
		$this->design->assign('currency', $currency);
		$this->design->assign('userperm', $this->userperm);

		if($this->access_granted === true){
			//~ print_r($this->userperm);
			$content = $this->module->fetch();
			$this->design->assign('content', $content);
		}
		else
		{
			$this->design->assign('content', 'Permission denied');
		}

		// Счетчики для верхнего меню
		$new_orders_counter = $this->orders->count_orders(array('status' => 0));
		$this->design->assign('new_orders_counter', $new_orders_counter);

		$new_comments_counter = $this->comments->count_comments(array('approved' => 0));
		$this->design->assign('new_comments_counter', $new_comments_counter);
		
		// Создаем текущую обертку сайта (обычно index.tpl)
		$wrapper = $this->design->smarty->getTemplateVars('wrapper');
		if (is_null($wrapper))
			$wrapper = 'index.tpl';

		if (!empty($wrapper))
			return $this->body = $this->design->fetch($wrapper);
		else
			return $this->body = $content;
	}
}
