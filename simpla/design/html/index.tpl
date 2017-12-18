<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="-1">
<title>{$meta_title}</title>
<link rel="icon" href="design/images/favicon.ico" type="image/x-icon">
<link href="design/css/style.css" rel="stylesheet" type="text/css" />

<script src="design/js/jquery/jquery.js"></script>
<script src="design/js/jquery/jquery.form.js"></script>
<script src="design/js/jquery/jquery-ui.min.js"></script>

	{* функции для работы с api системы *}
	<script type="text/javascript" src="/js/main.js"></script>   

	{* Тут библиотека для autocomplete *}
	<script type="text/javascript" src="/js/auto-complete/auto-complete.js"></script>   
	<link href="/js/auto-complete/auto-complete.css" rel="stylesheet" type="text/css" />



<link rel="stylesheet" type="text/css" href="design/js/jquery/jquery-ui.css" media="screen" />

<meta name="viewport" content="width=1024">

</head>
<body>

<a href='/' class='admin_bookmark'></a>

<!-- Вся страница --> 
<div id="main">
	<!-- Главное меню -->
	<ul id="main_menu">
		
		
	{if !empty(array_intersect_key( $userperm, array_flip(array('products','categories','brands')) ))}
		<li><a href="?module=ProductsAdmin"><img src="design/images/menu/catalog.png"><b>Каталог</b></a></li>
	{/if}

	{if $userperm['features']}
		<li><a href="?module=Options_groupsAdmin"><img src="design/images/menu/catalog.png"><b>Свойства</b></a></li>
	{/if}
		
	{if !empty(array_intersect_key( $userperm, array_flip(array('orders','labels')) ))}
		<li>
			<a href="?module=OrdersAdmin"><img src="design/images/menu/orders.png"><b>Заказы</b></a>
			{if $new_orders_counter}<div class='counter'><span>{$new_orders_counter}</span></div>{/if}
		</li>
	{/if}
		
	{if !empty(array_intersect_key( $userperm, array_flip(array('users','groups','coupons')) ))}
		<li><a href="?module=UsersAdmin"><img src="design/images/menu/users.png"><b>Покупатели</b></a></li>
	{/if}
		
	{if isset($userperm['pages'])}
		<li><a href="?module=PagesAdmin"><img src="design/images/menu/pages.png"><b>Страницы</b></a></li>
	{/if}
		
	{if isset($userperm['blog'])}
		<li><a href="?module=BlogAdmin"><img src="design/images/menu/blog.png"><b>Блог</b></a></li>
	{/if}
		
	{if !empty(array_intersect_key( $userperm, array_flip(array('comments','feedbacks','coupons')) ))}
		<li>
			<a href="?module=CommentsAdmin"><img src="design/images/menu/comments.png"><b>Комментарии</b></a>
			{if $new_comments_counter}<div class='counter'><span>{$new_comments_counter}</span></div>{/if}
		</li>
	{/if}
	
	{if !empty(array_intersect_key( $userperm, array_flip(array('import','export','backup')) ))}
		<li><a href="?module=BackupAdmin"><img src="design/images/menu/wizards.png"><b>Автоматизация</b></a></li>
	{/if}	
		
	{if isset($userperm['stats'])}
		<li><a href="?module=StatsAdmin"><img src="design/images/menu/statistics.png"><b>Статистика</b></a></li>
	{/if}
	
	{if isset($userperm['design'])}
		<li><a href="?module=ThemeAdmin"><img src="design/images/menu/design.png"><b>Дизайн</b></a></li>
	{/if}
	
	{if !empty(array_intersect_key( $userperm, array_flip(array('settings','delivery','payment')) ))}
		<li><a href="?module=SettingsAdmin"><img src="design/images/menu/settings.png"><b>Настройки</b></a></li>
	{/if}
		
	</ul>
	<!-- Главное меню (The End)-->
	
	
	<!-- Таб меню -->
	<ul id="tab_menu">
		{$smarty.capture.tabs}
	</ul>
	<!-- Таб меню (The End)-->
	
 
	
	<!-- Основная часть страницы -->
	<div id="middle">
		{$content}
	</div>
	<!-- Основная часть страницы (The End) --> 
	
	<!-- Подвал сайта -->
	<div id="footer">
	&copy; 2017 <a href='#'>Rapida {$config->version}</a>

	Вы вошли как {$manager['login']}
	<a href='{$config->root_url}/user/logout' id="logout">Выход</a>
	</div>
	<!-- Подвал сайта (The End)--> 
	
</div>
<!-- Вся страница (The End)--> 

</body>
</html>
