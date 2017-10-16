<!DOCTYPE html>
{$site_year = '2012'}					{* Указать в каком году начал работать Ваш сайт. Год cнизу страниц сайта будет автоматически меняться со временем *}
{$contact_skype = 'ManagerSkype'}		{* Указать Контактный Skype для покупателей +!ВАЖНО -->> В настройках скайпа РАЗРЕШИТЬ показывать свой статус в сети*}
{$contact_icq = '000000'}				{* Указать Контактный ICQ для покупателей *}
<html>
<head>
	<base href="{$config->root_url}/"/>
	<title>{$meta_title|escape}</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content="{$meta_description|escape}" />
	<meta name="keywords"    content="{$meta_keywords|escape}" />
	<meta name="viewport" content="width=1040"/>

	{* Стили *}
	<link href="design/{$settings->theme|escape}/css/style.css" rel="stylesheet" type="text/css" media="screen"/>
	<link href="design/{$settings->theme|escape}/images/bg/favicon.ico" rel="icon"          type="image/x-icon"/>
	<link href="design/{$settings->theme|escape}/images/bg/favicon.ico" rel="shortcut icon" type="image/x-icon"/>

	{* JQuery *}
	<script src="js/jquery/jquery.js"  type="text/javascript"></script>

	{if $smarty.session.admin == 'admin'}
	<script src ="js/admintooltip/admintooltip.js" type="text/javascript"></script>
	<link   href="js/admintooltip/css/admintooltip.css" rel="stylesheet" type="text/css" />
	{/if}

	{* Увеличитель картинок *}
	<script type="text/javascript" src="js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
	<link rel="stylesheet" href="js/fancybox/jquery.fancybox-1.3.4.css" type="text/css" media="screen" />

	{* Ctrl-навигация на соседние товары *}
	<script type="text/javascript" src="js/ctrlnavigate.js"></script>

	{* Аяксовая корзина *}
	<script src="design/{$settings->theme}/js/jquery-ui.min.js"></script>
	<script src="design/{$settings->theme}/js/ajax_cart.js"></script>

	{* js-проверка форм *}
	<script src="/js/baloon/js/baloon.js" type="text/javascript"></script>
	<link   href="/js/baloon/css/baloon.css" rel="stylesheet" type="text/css" />
	<!-- www.Simpla-Template.ru / Oформление великолепных интернет магазинов. E-mail:help@simpla-template.ru | Skype:SimplaTemplate /-->
	{literal}
	<script src="js/autocomplete/jquery.autocomplete-min.js" type="text/javascript"></script>
	<script>
	$(function() {
		//  Автозаполнитель поиска
		$(".input_search").autocomplete({
			serviceUrl:'ajax/search_products.php',
			minChars:1,
			noCache: false,
			onSelect:
				function(value, data){
					 $(".input_search").closest('form').submit();
				},
			fnFormatResult:
				function(value, data, currentValue){
					var reEscape = new RegExp('(\\' + ['/', '.', '*', '+', '?', '|', '(', ')', '[', ']', '{', '}', '\\'].join('|\\') + ')', 'g');
					var pattern = '(' + currentValue.replace(reEscape, '\\$1') + ')';
	  				return value.replace(new RegExp(pattern, 'gi'), '<strong>$1<\/strong>');
				}
		});
	});
	</script>
	{/literal}

	{* +++ *}
	<script src="design/{$settings->theme}/js/scrolltopcontrol.js"></script>
	{literal}
	<script>
	$(function() {
		$('select[name=currency_id]').change(function() {
			$(this).closest('form').submit();
		});
	});
	</script>
	{/literal}
</head>
<body>
<div id="top_bg"><div id="wrapper">

	<span class='header_label'></span>
	<div id="header">

		<div id="top_line">
			<a class='top_01 hover_mouse' href="ссылка_на_страницу_с_описаниями_доставки"></a>
			<a class='top_05 hover_mouse' href="/#tab1"></a>
			<a class='top_04 hover_mouse' href="ссылка_на_страницу_с_графиком_работы_и_контактами"></a>
			<a class='top_03 hover_mouse' href="ссылка_на_элемент_в_описании_о_самовывозе_АКЦЕНТ_на_этом"></a>
			<a class='top_02 hover_mouse' href="ссылка_на_страницу_с_описанием_гарантий_или_способов_возврата"></a>
		</div>

		<a href="" class='logo' title='{$settings->site_name}'></a>

		<div id="contacts">
			{if $contact_skype}
			<p><script type="text/javascript" src="http://download.skype.com/share/skypebuttons/js/skypeCheck.js"></script>
			<a href="skype:{$contact_skype}?chat" title='Открыть окно звонка (сообщения) в скайпе' class='link_2'><img src="http://mystatus.skype.com/smallicon/{$contact_skype}"/>{$contact_skype}</a></p>
			{/if}
			{if $contact_icq}<p><img border="0" src="http://icq-rus.com/icq/3/{$contact_icq}.gif"/>{$contact_icq}</p>{/if}
		</div>

		<div id="user_box_top">
			{if $user}
			<a href="user" class='username color'>{$user->name}</a>{if $group->discount>0}<br /><b class='color' style='text-transform:uppercase;'>Ваша скидка - {$group->discount}%</b>{/if}
			&nbsp;|&nbsp;&nbsp;<a id="logout" href="user/logout" class='link_2'>Выход</a>
			{else}<br /><b class='color'>Добро пожаловать,</b>&nbsp;&nbsp;<a id=login href="user/login" class='link_2'>Вход</a>&nbsp;|&nbsp;<a id="register" href="user/register" class='link_2'>Регистрация</a>
			{/if}
		</div>

		{if $currencies|count>1}
			<form name="currency" method="post" id="currencies">
			Валюта:&nbsp;
			<select name="currency_id">
			{foreach from=$currencies item=c}{if $c->enabled}<option value="{$c->id}" {if $c->id==$currency->id}selected{/if}>&nbsp;{$c->name|escape}&nbsp;&nbsp;</option>{/if}{/foreach}
			</select>
			</form>
		{/if}

		<form action="products" id="search">
		<input class="button_search" value="" type="submit" />
		<input class="input_search" type="text" name="keyword" value="{$keyword|escape}" placeholder="Поиск товара по названию"/>
		</form>
		<div id="cart_informer">{include file='cart_informer.tpl'}</div>
		<ul id="section_menu">
		{foreach $pages as $p}
		{if $p->menu_id == 1}<li {if $page && $page->id == $p->id}class="selected"{/if}><a data-page="{$p->id}" href="{$p->url}">{$p->name|escape}</a></li>{/if}
		{/foreach}
		</ul>

	</div>

	<div id="content-container">

		<div id="content_right">
		{if $page && $page->url==''}{include file='slider.tpl'}{/if}
		{$content}
		</div>

		<div id="content_left">

			<div id="nav-container">
			{function name=categories_tree}
			{if $categories}
				<ul id="nav">
				{foreach $categories as $c}
				{if $c->visible}
				<li{if in_array($category->id, $c->children)} class="active"{/if}>
				<a href="catalog/{$c->url}" data-category="{$c->id}">{$c->name}</a>
				{if in_array($category->id, $c->children)}{categories_tree categories=$c->subcategories}{/if}
				</li>
				{/if}
				{/foreach}
				</ul>
			{/if}
			{/function}
			{categories_tree categories=$categories}
			</div>

			{get_browsed_products var=browsed_products limit=12}
			{if $browsed_products}
			<ul id="browsed_products">
				<h2>Вы просматривали:</h2>
				{foreach $browsed_products as $browsed_product}

				<li class="image"><a class='hover_mouse' href="products/{$browsed_product->url}"><img src="{if $browsed_product->image}{$browsed_product->image->filename|resize:30:30}{else}design/{$settings->theme}/images/bg/nofoto.png{/if}" alt="{$browsed_product->name}" title="{$browsed_product->name}"></a></li>

				{/foreach}
			</ul>
			{/if}

			<ul id='info_block'>
				<h2>Заказы онлайн</h2>
				<p>Если Вы не уверены в выборе или сомневаетесь, то наши специалисты бесплатно проконсультируют Вас по любым вопросам, связанным с нашими предложениями</p>
				<p>Вы всегда можете задать вопрос по телефону:</p><br />
				<p>Рабочие дни: 9:00-22:00<br />Выходные дни: 9:00-18:00</p><br />
				<p class='telnumber'>+7 (000) 000-00-00</p>
				<p class='telnumber'>+7 (111) 111-11-11</p>
			</ul>

			{if $page && $page->url==''}
			{else}
				{get_new_products var=new_products limit=5}
				{if $new_products}
				<ul id="last_products">
					<h2>Новые поступления:</h2>
					{foreach $new_products as $product name=product}
					<li{if $smarty.foreach.product.last} style='border:none;'{/if}>
						<div class="image"><a href="products/{$product->url}"><img src="{if $product->image}{$product->image->filename|resize:40:40}{else}design/{$settings->theme}/images/bg/nofoto.png{/if}" alt="{$product->name|escape}"/></a></div>
						<a class='link_2' href="products/{$product->url}">{$product->name|escape|truncate:50:'...'} {$product->brand|escape}</a>
						{if $product->variant->price >0}<div class="price">{$product->variant->price|convert} {$currency->sign|escape}</div>{/if}
					</li>
					{/foreach}
				</ul>
				{/if}
			{/if}

			{get_posts var=last_posts limit=10}
			{if $last_posts}
				<ul id="all_blog">
				<h2>Новости <a href="blog">в блоге</a></h2>
				{foreach $last_posts as $post}
				<li>
				<p class='data' data-post="{$post->id}">{$post->date|date}</p>
				<a href="blog/{$post->url}">{$post->name|escape}</a>
				</li>
				{/foreach}
				</ul>
			{/if}

			{get_brands var=all_brands}
			{if $all_brands}
				<ul id="all_brands">
				<h2>Бренды каталога</h2>
				{foreach $all_brands as $b}
				<a href="brands/{$b->url}" class='hover_mouse' title='Выбрать из каталога все предложения от {$b->name|escape}'>{if $b->image}<img src="{$config->brands_images_dir}{$b->image}" alt="{$b->name|escape}">{else}{$b->name}{/if}</a>
				{/foreach}
				</ul>
			{/if}
		</div>
		<div class="clear_dot"></div>
		<div id="moneyline"><img src="design/{$settings->theme}/images/images_theme/money_line.png" alt="Мы принимаем к оплате"></div>
	</div>
</div></div>

<div id="footer-container"><div id="footer">

	<ul class='footer_menu'>
		<h2>О нашем каталоге</h2>
		{foreach $pages as $p}{if $p->menu_id == 1}
		<li><a data-page="{$p->id}" href="{$p->url}">{$p->name|escape}</a></li>
		{/if}{/foreach}
	</ul>

	<ul class='footer_menu'>
		<h2>Информация для покупателей</h2>
		<li><a href="">Статья или обзор о товаре 001</a></li>
		<li><a href="">Статья или обзор о товаре 002</a></li>
		<li><a href="">Статья или обзор о товаре 003</a></li>
		<li><a href="">Статья или обзор о товаре 004</a></li>
	</ul>

	<ul class='footer_menu' style='margin-right:0;'>
		<h2>Это может быть интересно</h2>
		<li><a href="">Статья или обзор о товаре 001</a></li>
		<li><a href="">Статья или обзор о товаре 002</a></li>
		<li><a href="">Статья или обзор о товаре 003</a></li>
	</ul>

	<div class="clear_dot"></div>

	<p class="counters right">

		<!-- Заменить два изображения счетчиков снизу кодом своих счетчиков. Если используете код метрики Яндекса - вставлять МЕЖДУ тэгами {literal}код метрики{/literal} /-->
		<!-- это просто пример изображения счетчика /--><img src="design/{$settings->theme}/temp_counters.png" alt=""/>
		<!-- это просто пример изображения счетчика /--><img src="design/{$settings->theme}/temp_counters.png" alt=""/>

	</p>

	<p>Данный информационный ресурс не является публичной офертой. Наличие и стоимость товаров уточняйте по телефону. Производители оставляют за собой право изменять технические характеристики и внешний вид товаров без предварительного уведомления.</p>
	<p><b>{$settings->site_name} © {if $smarty.now|date_format:"%Y"=={$site_year}}{$site_year}{else}{$site_year} - {$smarty.now|date_format:"%Y"}{/if}</b></p>{*Просьба не удалять ссылку на наш сайт*}<p class='copyr'><a title='Оформление для великолепных интернет-магазинов - www.Simpla-Template.ru' target='blank' href='http://Simpla-Template.ru/'>© 2012 SimplaTemplate ™</a></p>
</div></div>
</body></html>