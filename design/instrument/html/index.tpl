<!DOCTYPE html>
{*
	Общий вид страницы
	Этот шаблон отвечает за общий вид страниц без центрального блока.
*}
<html>
<head>
	<base href="{$config->root_url}/"/>
	<title>{$meta_title|escape}</title>
	
	{* Метатеги *}
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content="{$meta_description|escape}" />
	<meta name="keywords"    content="{$meta_keywords|escape}" />
	<meta name="viewport" content="width=1024"/>
	
	{* Стили *}
	<link href="design/{$settings->theme|escape}/css/style.css" rel="stylesheet" type="text/css" media="screen"/>
	<link href="design/{$settings->theme|escape}/images/favicon.png" rel="icon"          type="image/x-icon"/>
	<link href="design/{$settings->theme|escape}/images/favicon.png" rel="shortcut icon" type="image/x-icon"/>
	
<link rel="stylesheet" type="text/css" href="design/{$settings->theme|escape}/stylesheet/stylesheet.css" />
<link href="design/{$settings->theme|escape}/stylesheet/cloud-zoom.css" rel="stylesheet" type="text/css" />
<link href="design/{$settings->theme|escape}/stylesheet/superfish.css" rel="stylesheet" type="text/css" />
<link href="design/{$settings->theme|escape}/stylesheet/slideshow.css" rel="stylesheet" type="text/css" />
<link href="design/{$settings->theme|escape}/stylesheet/jquery.prettyPhoto.css" rel="stylesheet" type="text/css" />
	{* JQuery *}
	<script src="js/jquery/jquery.js"  type="text/javascript"></script>
<script type="text/javascript" src="design/{$settings->theme|escape}/javascript/jquery/ui/jquery-ui-1.8.16.custom.min.js"></script>
<link rel="stylesheet" type="text/css" href="design/{$settings->theme|escape}/javascript/jquery/ui/themes/ui-lightness/jquery-ui-1.8.16.custom.css" />
<script type="text/javascript" src="design/{$settings->theme|escape}/javascript/jquery/ui/external/jquery.cookie.js"></script>
<script type="text/javascript" src="design/{$settings->theme|escape}/javascript/jquery/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="design/{$settings->theme|escape}/javascript/jquery/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
<script type="text/javascript" src="design/{$settings->theme|escape}/javascript/jquery/colorbox/jquery.colorbox.js"></script>
<script type="text/javascript" src="design/{$settings->theme|escape}/javascript/jquery/jquery.jcarousel.min.js"></script>
<script type="text/javascript" src="design/{$settings->theme|escape}/javascript/jquery/jquery.cycle.js"></script>
<link href="design/{$settings->theme|escape}/stylesheet/skin.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="design/{$settings->theme|escape}/javascript/jquery/colorbox/colorbox.css" media="screen" />

 <link href='http://fonts.googleapis.com/css?family=Roboto:700italic&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
<!--[if IE]>
<script type="text/javascript" src="design/{$settings->theme|escape}/javascript/jquery/fancybox/jquery.fancybox-1.3.4-iefix.js"></script>
<![endif]-->
<!--[if lt IE 8]><div style='clear:both;height:59px;padding:0 15px 0 15px;position:relative;z-index:10000;text-align:center;'><a href="../../www.microsoft.com/windows/internet-explorer/default.aspx@ocid=ie6_countdown_bannercode"><img src="../../storage.ie6countdown.com/assets/100/images/banners/warning_bar_0000_us.jpg" border="0" height="42" width="820" alt="You are using an outdated browser. For a faster, safer browsing experience, upgrade for free today." /></a></div><![endif]-->
<script type="text/javascript" src="design/{$settings->theme|escape}/javascript/jquery/tabs.js"></script>
<script type="text/javascript" src="design/{$settings->theme|escape}/javascript/jquery/easyTooltip.js"></script>

<script type="text/javascript" src="design/{$settings->theme|escape}/javascript/jQuery.equalHeights.js"></script>
<script type="text/JavaScript" src="design/{$settings->theme|escape}/javascript/cloud-zoom.1.0.2.js"></script>
<script type="text/javascript" src="design/{$settings->theme|escape}/javascript/jquery.prettyPhoto.js"></script>
<script type="text/javascript" src="design/{$settings->theme|escape}/javascript/jscript_zjquery.anythingslider.js"></script>
<script type="text/javascript" src="design/{$settings->theme|escape}/javascript/superfish.js"></script>
<script type="text/javascript" src="design/{$settings->theme|escape}/javascript/jquery.bxSlider.min.js"></script>
<script type="text/javascript" src="design/{$settings->theme|escape}/javascript/script.js"></script>
<script type="text/javascript" src="design/{$settings->theme|escape}/javascript/jquery/nivo-slider/jquery.nivo.slider.pack.js"></script>

<script type='text/javascript' src='design/{$settings->theme|escape}/javascript/sl/camera.min.js'></script>
<script type="text/javascript" src="design/{$settings->theme|escape}/javascript/jquery/jquery.total-storage.min.js"></script> 
<!--[if  IE 8]>
	<style>
		.success, #header #cart .content  { border:1px solid #e7e7e7;}
	</style>
<![endif]-->

<!--[if  IE 7]>
<link rel="stylesheet" type="text/css" href="design/{$settings->theme|escape}/stylesheet/ie7.css" />
<![endif]-->
<!--[if lt IE 7]>
<link rel="stylesheet" type="text/css" href="design/{$settings->theme|escape}/stylesheet/ie6.css" />
<script type="text/javascript" src="design/{$settings->theme|escape}/javascript/DD_belatedPNG_0.0.8a-min.js"></script>
<script type="text/javascript">
DD_belatedPNG.fix('#logo img');
</script>
<![endif]-->	
	

	
	{* Всплывающие подсказки для администратора *}
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
	<script src="js/baloon/js/baloon.js" type="text/javascript"></script>
	<link   href="js/baloon/css/baloon.css" rel="stylesheet" type="text/css" /> 
	
	{* Автозаполнитель поиска *}
	{literal}
	<script src="js/autocomplete/jquery.autocomplete-min.js" type="text/javascript"></script>
	<style>
	.autocomplete-w1 { position:absolute; top:0px; left:0px; margin:6px 0 0 6px; /* IE6 fix: */ _background:none; _margin:1px 0 0 0; }
	.autocomplete { border:1px solid #999; background:#FFF; cursor:default; text-align:left; overflow-x:auto;  overflow-y: auto; margin:-6px 6px 6px -6px; /* IE6 specific: */ _height:350px;  _margin:0; _overflow-x:hidden; }
	.autocomplete .selected { background:#F0F0F0; }
	.autocomplete div { padding:2px 5px; white-space:nowrap; }
	.autocomplete strong { font-weight:normal; color:#3399FF; }
	</style>	
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
	  				return (data.image?"<img align=absmiddle src='"+data.image+"'> ":'') + value.replace(new RegExp(pattern, 'gi'), '<strong>$1<\/strong>');
				}	
		});
	});
	</script>
	{/literal}
		
			
</head>
<body class="common-home">
<div class="bg-1">
<div class="main-shining">
<div class="row-1">
<div id="header">

	<div class="outer">
	
		<div id="welcome">
						Здравствуйте, 
			{if $user}
				<span id="username">
					<a href="user">{$user->name}</a>{if $group->discount>0},
					ваша скидка &mdash; {$group->discount}%{/if}
				</span>, 
				<a id="logout" href="user/logout">выйти</a>
			{else}
			гость, вы можете 
			 <a id="login" href="user/login">войти</a>	
				 или <a id="register" href="user/register">зарегистрироваться</a>
			{/if}						
		</div>
		<div class="clear"></div>
		<div class="cart-position">
			<div class="cart-inner">
			<div id="cart">
			{* Обновляемая аяксом корзина должна быть в отдельном файле *}
			{include file='cart_informer.tpl'}
</div>
</div>
		</div>
		<div class="clear"></div>
	<div class="header-top1"> 
                   <div id="logo"><a href="/"><img alt="{$settings->site_name|escape}" title="{$settings->site_name|escape}" src="design/{$settings->theme|escape}/image/logo.png"></a></div>
				<div id="search">
				<form action="products">
					<input class="input_search" type="text" name="keyword" value="{$keyword|escape}" placeholder="Поиск товара"/>
					<input class="button-search" value="" type="submit" />
				</form>				
		</div>
		<ul class="links">
			{foreach $pages as $p}
				{* Выводим только страницы из первого меню *}
				{if $p->menu_id == 1}
				<li {if $page && $page->id == $p->id}class="selected"{/if}>
					<a data-page="{$p->id}" href="{$p->url}">{$p->name|escape}</a>
				</li>
				{/if}
			{/foreach}
		</ul>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>
	</div>
	<div class="clear"></div>
</div>
<div class="clear"></div>
</div>
<div class="clear"></div>
{if $page && $page->url == ''}
{include file='slider.tpl'}
 {/if}
<div class="clear"></div>
<div class="main-container">
<p id="back-top"> <a href="#top"><span></span></a> </p>
<div id="container">
<div id="notification"> </div>
<div id="column-left">
    <script type="text/javascript">
$(document).ready(function() {
	$('li.cat-header ul').each(function(index) {
 $(this).prev().addClass('idCatSubcat')});
 $('li.cat-header a').after('<span></span>'); 
 $('li.cat-header ul').css('display','none');
 $('li.cat-header ul.active').css('display','block');
 $('li.cat-header ul').each(function(index) {
   $(this).prev().addClass('close').click(function() {
  if (
   $(this).next().css('display') == 'none') {
   $(this).next().slideDown(400, function () {
   $(this).prev().removeClass('collapsed').addClass('expanded');
    });
  }else {
    $(this).next().slideUp(400, function () {
   $(this).prev().removeClass('expanded').addClass('collapsed');
   $(this).find('ul').each(function() {
    $(this).hide().prev().removeClass('expanded').addClass('collapsed');
   });
    });
  }
  return false;
   });
});
 });
</script>
<div class="box category">
	<div class="box-heading"><span>Категории</span></div>
  <div class="box-content">
  
    <div class="box-category">
			{* Рекурсивная функция вывода дерева категорий *}
			{$level=0}
			{function name=categories_tree}
			{if $categories}

			<ul {if $level != 0}{if in_array($category->id, $c->children)}class="active" style="display: block;"{/if}{/if}>
			{foreach $categories as $c}
				{* Показываем только видимые категории *}
				{if $c->visible}
					<li class="{if $level == 0}cat-header{/if} {if in_array($category->id, $c->children)}active{/if}">
						<a {if in_array($category->id, $c->children)}class="active"{/if} href="catalog/{$c->url}" data-category="{$c->id}">{$c->name}</a>
						{categories_tree categories=$c->subcategories level=$level+1}
					</li>
				{/if}
			{/foreach}
			</ul>
			{/if}
			{/function}
			{categories_tree categories=$categories}    
    </div>
  </div>
</div>

			<!-- Все бренды -->
			{* Выбираем в переменную $all_brands все бренды *}
			{get_brands var=all_brands}
			{if $all_brands}
    <div class="box manufacturers">
  <div class="box-heading">Бренды</div>
  <div class="box-content">
        <select onchange="location = this.value;">
        <option>Выберите бренд</option>
        {foreach $all_brands as $b}
		<option value="brands/{$b->url}">{$b->name}</option>
		{/foreach}
      </select>

      </div>
</div>
			{/if}
			<!-- Все бренды (The End)-->
			
			<!-- Просмотренные товары -->
			{get_browsed_products var=browsed_products limit=20}
			{if $browsed_products}
   <div class="box">
  <div class="box-heading">Вы смотрели</div>
  <div class="box-content">			

				<ul id="browsed_products">
				{foreach $browsed_products as $browsed_product}
					<li>
					<a href="products/{$browsed_product->url}"><img src="{$browsed_product->image->filename|resize:50:50}" alt="{$browsed_product->name}" title="{$browsed_product->name}"></a>
					</li>
				{/foreach}
				</ul>
      </div>
</div>				
			{/if}
			<!-- Просмотренные товары (The End)-->			


  </div>
 
<div id="content">
{$content}
</div>
</div>
<div class="clear"></div>
</div>
</div>
<div class="footer-wrap">
<div id="footer">
<div class="outer">
	<div class="wrapper">
      <div class="column col-1">
        <h3>Информация</h3>
        <ul>
			{foreach $pages as $p}
				{* Выводим только страницы из первого меню *}
				{if $p->menu_id == 1}
				<li {if $page && $page->id == $p->id}class="selected"{/if}>
					<a data-page="{$p->id}" href="{$p->url}">{$p->name|escape}</a>
				</li>
				{/if}
			{/foreach}
                  </ul>
      </div>
      <div class="column col-2">
        <h3>Меню2</h3>
        <ul>
         {foreach $pages as $p}
				{* Выводим только страницы из первого меню *}
				{if $p->menu_id == 2}
				<li {if $page && $page->id == $p->id}class="selected"{/if}>
					<a data-page="{$p->id}" href="{$p->url}">{$p->name|escape}</a>
				</li>
				{/if}
			{/foreach}
        </ul>
      </div>
      <div class="column col-3">
        <h3>Меню3</h3>
        <ul>
          <li><a href="#">Ссылка</a></li>
		  <li><a href="#">Ссылка</a></li>
		  <li><a href="#">Ссылка</a></li>
		  <li><a href="#">Ссылка</a></li>
         
        </ul>
      </div>
      <div class="column col-4">
        <h3>Меню4</h3>
        <ul>
         <li><a href="#">Ссылка</a></li>
		  <li><a href="#">Ссылка</a></li>
		  <li><a href="#">Ссылка</a></li>
		  <li><a href="#">Ссылка</a></li>
        </ul>
      </div>
      
  </div>
  <div id="powered"> {$settings->site_name|escape} &copy; 2013</div>
</div>
</div>
</div>

	
</body>
</html>