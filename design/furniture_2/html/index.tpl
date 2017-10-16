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
	
	<link href="design/{$settings->theme|escape}/images/favicon.ico" rel="icon"          type="image/x-icon"/>
	<link href="design/{$settings->theme|escape}/images/favicon.ico" rel="shortcut icon" type="image/x-icon"/>
	<link href='http://fonts.googleapis.com/css?family=Roboto:400,300,300italic,400italic,500,500italic,700,900,700italic,900italic&subset=latin,cyrillic' rel='stylesheet' type='text/css'>	
		
	{* JQuery *}
	<script type="text/javascript" src="design/{$settings->theme|escape}/js/jquery-1.7.min.js"></script>
	<script type="text/javascript" src="design/{$settings->theme|escape}/js/superfish.js"></script>
	<script type="text/javascript" src="design/{$settings->theme|escape}/js/scripts.js"></script>
	
<![endif]-->
<!--[if lt IE 9]>
<div style=' clear: both; text-align:center; position: relative;'>
 <a href="http://windows.microsoft.com/en-US/internet-explorer/products/ie/home?ocid=ie6_countdown_bannercode"><img src="http://storage.ie6countdown.com/assets/100/images/banners/warning_bar_0000_us.jpg" border="0" height="42" width="820" alt="You are using an outdated browser. For a faster, safer browsing experience, upgrade for free today." /></a>
</div>
<![endif]--> 

<!--[if lt IE 9]>
	<style>
	body {
		min-width: 960px !important;
	}
	</style>
<![endif]--> 
<link rel="stylesheet" type="text/css" href="design/{$settings->theme|escape}/css/font-awesome.css" media="all" />
<link rel="stylesheet" type="text/css" href="design/{$settings->theme|escape}/css/jquery.bxslider.css" media="all" />
<link rel="stylesheet" type="text/css" href="design/{$settings->theme|escape}/css/photoswipe.css" media="all" />
<link rel="stylesheet" type="text/css" href="design/{$settings->theme|escape}/css/bootstrap.css" media="all" />
<link rel="stylesheet" type="text/css" href="design/{$settings->theme|escape}/css/extra_style.css" media="all" />
<link rel="stylesheet" type="text/css" href="design/{$settings->theme|escape}/css/styles.css" media="all" />
<link rel="stylesheet" type="text/css" href="design/{$settings->theme|escape}/css/responsive.css" media="all" />
<link rel="stylesheet" type="text/css" href="design/{$settings->theme|escape}/css/superfish.css" media="all" />
<link rel="stylesheet" type="text/css" href="design/{$settings->theme|escape}/css/camera.css" media="all" />
<link rel="stylesheet" type="text/css" href="design/{$settings->theme|escape}/css/widgets.css" media="all" />
<link rel="stylesheet" type="text/css" href="design/{$settings->theme|escape}/css/ecommerceteam/cloud-zoom.css" media="all" />
<link rel="stylesheet" type="text/css" href="design/{$settings->theme|escape}/css/catalogsale.css" media="all" />
<link rel="stylesheet" type="text/css" href="design/{$settings->theme|escape}/css/print.css" media="print" />
<link href="design/{$settings->theme|escape}/css/style.css" rel="stylesheet" type="text/css" media="screen"/>

<script type="text/javascript" src="design/{$settings->theme|escape}/js/prototype/prototype.js"></script>
{*
<script type="text/javascript" src="design/{$settings->theme|escape}/js/lib/ccard.js"></script>
<script type="text/javascript" src="design/{$settings->theme|escape}/js/prototype/validation.js"></script>
<script type="text/javascript" src="design/{$settings->theme|escape}/js/scriptaculous/builder.js"></script>
<script type="text/javascript" src="design/{$settings->theme|escape}/js/scriptaculous/effects.js"></script>
<script type="text/javascript" src="design/{$settings->theme|escape}/js/scriptaculous/dragdrop.js"></script>
<script type="text/javascript" src="design/{$settings->theme|escape}/js/scriptaculous/controls.js"></script>
*}
<script type="text/javascript" src="design/{$settings->theme|escape}/js/scriptaculous/slider.js"></script>

<script type="text/javascript" src="design/{$settings->theme|escape}/js/varien/js.js"></script>
<script type="text/javascript" src="design/{$settings->theme|escape}/js/varien/form.js"></script>
<script type="text/javascript" src="design/{$settings->theme|escape}/js/mage/translate.js"></script>
<script type="text/javascript" src="design/{$settings->theme|escape}/js/mage/cookies.js"></script>
<script type="text/javascript" src="design/{$settings->theme|escape}/js/ecommerceteam/cloud-zoom.1.0.2.js"></script>
<script type="text/javascript" src="design/{$settings->theme|escape}/js/jquery.easing.1.3.js"></script>
<script type="text/javascript" src="design/{$settings->theme|escape}/js/jquery.mobile.customized.min.js"></script>
<script type="text/javascript" src="design/{$settings->theme|escape}/js/bootstrap.js"></script>
<script type="text/javascript" src="design/{$settings->theme|escape}/js/jquery.carouFredSel-6.2.1.js"></script>
<script type="text/javascript" src="design/{$settings->theme|escape}/js/jquery.touchSwipe.js"></script>
<script type="text/javascript" src="design/{$settings->theme|escape}/js/jquery.bxslider.min.js"></script>
<script type="text/javascript" src="design/{$settings->theme|escape}/js/carousel.js"></script>
<!--[if lt IE 8]>
<link rel="stylesheet" type="text/css" href="design/{$settings->theme|escape}/css/styles-ie.css" media="all" />
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
	(function($){

	})(jQuery)
	</script>
	{/literal}
		


		
</head>
<body class="ps-static cms-index-index cms-home">
<div class="wrapper ps-static">
        <noscript>
        <div class="global-site-notice noscript">
            <div class="notice-inner">
                <p>
                    <strong>JavaScript seems to be disabled in your browser.</strong><br />
                    You must have JavaScript enabled in your browser to utilize the functionality of this website.                </p>
            </div>
        </div>
    </noscript>
    <div class="page">
 <div class="top-icon-menu">

    <div class="top-search"><i class="icon-search"></i></div>
    <span class="clear"></span>
</div>   
        <div class="header-container">
        <div class="container">
		<div class="row">
			<div class="col-xs-12">        
    <div class="header">
    
<div class="right_head">        	
  <div class="header-buttons">													 
             
        
  </div>         
  <div class="quick-access">														               
    <ul class="links">                        
      <li class="first" >
      {if $user}
      	<a href="user/logout" title="Выйти">Выйти</a>
      {else}
        <a href="user/login" title="Войти">Войти</a>
       {/if} 
      </li>                                
      <li class=" last" >
        <a href="cart" title="Оформить заказ" class="top-link-checkout">Оформить заказ</a>
      </li>            
    </ul>         
  </div>        
</div>

                <h1 class="logo"><strong>{$settings->site_name|escape}</strong><a href="./" title="{$settings->site_name|escape}" class="logo"><img src="design/{$settings->theme|escape}/images/logo.gif" alt="{$settings->site_name|escape}" /></a></h1>
                
<div class="header_center">        	
  <div class="header_phone">Звоните нам: 
    <span>+7 (812) 971-57-75 
    </span>
  </div>         
	            <ul class="links">
 			{if $user}
				<li  class="first"><span id="username">
					<a href="user">{$user->name}</a>{if $group->discount>0},
					ваша скидка &mdash; {$group->discount}%{/if}
				</span></li>
				<li  class=" last" ><a id="logout" href="user/logout">Выйти</a></li>
			{else}
				<li  class="first"><a id="register" href="user/register">Регистрация</a></li>
				<li class=" last"  ><a id="login" href="user/login">Вход</a></li>
			{/if}                               
            </ul>       
</div>        
<div class="clear"></div>                

<div class="top_links_duplicate">
  <ul class="links">                        
 			{if $user}
				<li ><span id="username">
					<a href="user">{$user->name}</a>{if $group->discount>0},
					ваша скидка &mdash; {$group->discount}%{/if}
				</span></li>
				<li  class=" last" ><a id="logout" href="user/logout">Выйти</a></li>
			{else}
				<li ><a id="register" href="user/register">Регистрация</a></li>
				<li class=" last"  ><a id="login" href="user/login">Вход</a></li>
			{/if}           
  </ul>
</div>                


            </div>
    </div>
		</div>
		<div class="clear"></div>
	</div>           
</div>
			{* Рекурсивная функция вывода дерева категорий *}
			{function name=categories_tree}
			{if $categories}
			{foreach $categories as $c}
				{* Показываем только видимые категории *}
				{if $c->visible}
					<li class="{if $c->subcategories}parent{/if}{if in_array($category->id, $c->children)} active{/if}{if $c@last} last{/if}">
						<a {if $category->id == $c->id}class="selected"{/if} href="catalog/{$c->url}" data-category="{$c->id}">{$c->name}</a>
						{if $c->subcategories}
						<ul class="level0">
						{categories_tree categories=$c->subcategories}
						</ul>
						{/if}
					</li>
				{/if}
			{/foreach}
			
			{/if}
			{/function}
			        

<div class="nav-container">	
  <div class="container">		
    <div class="row">            
      <div class="col-xs-12">                
        <ul id="nav" class="sf-menu">
        {categories_tree categories=$categories}                                   
        </ul>                
        <div class="sf-menu-block">                    
          <div id="menu-icon">Категории
          </div>                    
          <ul class="sf-menu-phone">
          {categories_tree categories=$categories}                                           
          </ul>                
        </div>            
      </div>        
    </div>		
    <div class="clear">
    </div>	
  </div>
</div>

<div class="top-container">	
  <div class="container">		
    <div class="row">			
      <div class="col-xs-12">    
        <div class="top_block">        
          <div class="block-cart-header" id="cart_informer" >
			{* Обновляемая аяксом корзина должна быть в отдельном файле *}
			{include file='cart_informer.tpl'}

          </div>         
          								
          <form id="search_mini_form" action="products" method="get">    
            <div class="form-search">        
              <label for="search">Поиск:
              </label>        
              <span class="icon_search">
              </span>        
              <input id="search" type="text"  name="keyword" value="{$keyword}" placeholder="Поиск товара" class="input_search input-text" maxlength="128" />        
              <button type="submit" title="Найти" class="button">
                <span>
                  <span>Найти
                  </span>
                </span>
              </button>        
  
            </div>
          </form>        
            
        </div>   
      </div>		
    </div>		
    <div class="clear"></div>	
  </div>
</div>
 
       <div class="main-container col2-left-layout">
			<div class="container">
				<div class="row">
					<div class="col-xs-12">       
            <div class="main">
            <div class="row">
                                <div class="col-main col-xs-12 col-sm-9">
                                <div class="padding-s">
 {if $module == 'MainView'}                               
  {include file='slider.tpl'}
 {/if}                                       
 
                    <div class="home-products">
{$content}
        </div>

                </div> 
                </div>
                <div class="col-left sidebar col-xs-12 col-sm-3">                
                
                <div class="block block-side-nav">

  <div class="block-title-cat"><strong>
      <span>Категории
      </span></strong>
  </div>  
  <div class="block-content-cat">   
    <ul class="sf-menu-phone2"> 			
			{categories_tree categories=$categories}
			</ul>                
</div>
</div>

 {include file='sidebar.tpl'}

</div>
            </div>
</div>            
					</div>
				</div>
            </div>            
            
        </div>
        <div class="footer-container">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">        
    <div class="footer">
       <p id="back-top"><a href="#top"><span></span></a> </p> 
	   	<div class="footer-cols-wrapper">
        <div class="footer-col">			
         <div class="f_block">
          <h4>Информация</h4>
          <div class="footer-col-content">
           <ul>
     			{foreach name=page from=$pages item=p}
				{* Выводим только страницы из первого меню *}
				{if $p->menu_id == 1}
				<li {if $page && $page->id == $p->id}class="selected"{/if}>
					<a data-page="{$p->id}" href="{$p->url}">{$p->name|escape}</a>
				</li>
				{/if}
			{/foreach}
            </ul>
          </div>
         </div>
        </div>		    
<div class="footer-col footer-col-ex">
	<div class="f_block">
  <h4>Почему стоит купить<span class="toggle"></span></h4>
  <div class="footer-col-content">
   <ul>
    <li><a href="#">Доставка и возврат</a></li>
    <li><a href="#">Безопасные покупки</a></li>
    <li><a href="#">Международные перевозки</a></li>
    <li><a href="#">Партнеры</a></li>
    <li><a href="#">Группа продаж</a></li>
   </ul>
  </div>
 </div>
</div>
<div class="footer-col footer-col-ex">
	<div class="f_block">
  <h4>Мой акаунт<span class="toggle"></span></h4>
  <div class="footer-col-content">
   <ul>
    <li><a href="user/login/">Войти</a></li>
    <li><a href="cart/">Просмотр корзины</a></li>
    <li><a href="#">Мои предпочтения</a></li>
    <li><a href="#">Мой заказ</a></li>
    <li><a href="#">Помощь</a></li>
   </ul>
  </div>
 </div>
</div>

<div class="footer-col wide-col footer-col-ex">
	<div class="f_block">
  <h4>Follow us<span class="toggle"></span></h4>
  <div class="footer-col-content">
   <ul>
    <li><a href="#">Facebook</a></li>
    <li><a href="#">Twitter</a></li>
    <li><a href="#">RSS</a></li>
   </ul>
  </div>
 </div>
</div>

<div class="footer-col wide-col last footer-col-ex">

         <div class="f_block">
 <h4>Звоните нам:<span class="toggle"></span></h4>
 <div class="footer-col-content">
  <div class="footer_info">+7 (812) 971-57-75  </div>
 </div>
</div>         <address>© <script type="text/javascript">var mdate = new Date(); document.write(mdate.getFullYear());</script>  {$settings->site_name|escape}. Все права защищены.</address>
        </div>

        </div>
    </div>
    
			</div>
		</div>
	</div>    
    
                
      </div>            </div>
</div>



	       

	
</body>
</html>