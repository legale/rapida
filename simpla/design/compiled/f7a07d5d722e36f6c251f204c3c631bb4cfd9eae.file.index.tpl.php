<?php /* Smarty version Smarty-3.1.18, created on 2017-10-11 14:27:19
         compiled from "simpla\design\html\index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:134016197159ddfea09f1908-44913352%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f7a07d5d722e36f6c251f204c3c631bb4cfd9eae' => 
    array (
      0 => 'simpla\\design\\html\\index.tpl',
      1 => 1507721237,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '134016197159ddfea09f1908-44913352',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.18',
  'unifunc' => 'content_59ddfea0ab8cb6_26380956',
  'variables' => 
  array (
    'meta_title' => 0,
    'config' => 0,
    'manager' => 0,
    'new_orders_counter' => 0,
    'new_comments_counter' => 0,
    'content' => 0,
    'settings' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_59ddfea0ab8cb6_26380956')) {function content_59ddfea0ab8cb6_26380956($_smarty_tpl) {?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="-1">
<title><?php echo $_smarty_tpl->tpl_vars['meta_title']->value;?>
</title>
<link rel="icon" href="design/images/favicon.ico" type="image/x-icon">
<link href="design/css/style.css" rel="stylesheet" type="text/css" />

<script src="design/js/jquery/jquery.js"></script>
<script src="design/js/jquery/jquery.form.js"></script>
<script src="design/js/jquery/jquery-ui.min.js"></script>
<link rel="stylesheet" type="text/css" href="design/js/jquery/jquery-ui.css" media="screen" />

<meta name="viewport" content="width=1024">

</head>
<body>

<a href='<?php echo $_smarty_tpl->tpl_vars['config']->value->root_url;?>
' class='admin_bookmark'></a>

<!-- Вся страница --> 
<div id="main">
	<!-- Главное меню -->
	<ul id="main_menu">
		
	<?php if (in_array('products',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?>
		<li><a href="index.php?module=ProductsAdmin"><img src="design/images/menu/catalog.png"><b>Каталог</b></a></li>
	<?php } elseif (in_array('categories',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?>
		<li><a href="index.php?module=CategoriesAdmin"><img src="design/images/menu/catalog.png"><b>Каталог</b></a></li>
	<?php } elseif (in_array('brands',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?>
		<li><a href="index.php?module=BrandsAdmin"><img src="design/images/menu/catalog.png"><b>Каталог</b></a></li>
	<?php } elseif (in_array('features',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?>
		<li><a href="index.php?module=FeaturesAdmin"><img src="design/images/menu/catalog.png"><b>Каталог</b></a></li>
	<?php }?>
		
	<?php if (in_array('orders',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?>
		<li>
			<a href="index.php?module=OrdersAdmin"><img src="design/images/menu/orders.png"><b>Заказы</b></a>
			<?php if ($_smarty_tpl->tpl_vars['new_orders_counter']->value) {?><div class='counter'><span><?php echo $_smarty_tpl->tpl_vars['new_orders_counter']->value;?>
</span></div><?php }?>
		</li>
	<?php } elseif (in_array('labels',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?>
		<li><a href="index.php?module=OrdersLabelsAdmin"><img src="design/images/menu/orders.png"><b>Заказы</b></a></li>
	<?php }?>
		
	<?php if (in_array('users',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?>
		<li><a href="index.php?module=UsersAdmin"><img src="design/images/menu/users.png"><b>Покупатели</b></a></li>
	<?php } elseif (in_array('groups',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?>
		<li><a href="index.php?module=GroupsAdmin"><img src="design/images/menu/users.png"><b>Покупатели</b></a></li>
	<?php } elseif (in_array('coupons',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?>
		<li><a href="index.php?module=CouponsAdmin"><img src="design/images/menu/users.png"><b>Покупатели</b></a></li>
	<?php }?>
		
	<?php if (in_array('pages',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?>
		<li><a href="index.php?module=PagesAdmin"><img src="design/images/menu/pages.png"><b>Страницы</b></a></li>
	<?php }?>
		
	<?php if (in_array('blog',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?>
		<li><a href="index.php?module=BlogAdmin"><img src="design/images/menu/blog.png"><b>Блог</b></a></li>
	<?php }?>
		
	<?php if (in_array('comments',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?>
		<li><a href="index.php?module=CommentsAdmin"><img src="design/images/menu/comments.png"><b>Комментарии</b></a>
		<?php if ($_smarty_tpl->tpl_vars['new_comments_counter']->value) {?><div class='counter'><span><?php echo $_smarty_tpl->tpl_vars['new_comments_counter']->value;?>
</span></div><?php }?></li>
	<?php } elseif (in_array('feedbacks',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?>
		<li><a href="index.php?module=FeedbacksAdmin"><img src="design/images/menu/comments.png"><b>Комментарии</b></a>
	<?php }?>
		
	<?php if (in_array('import',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?>
		<li><a href="index.php?module=ImportAdmin"><img src="design/images/menu/wizards.png"><b>Автоматизация</b></a></li>
	<?php } elseif (in_array('export',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?>
		<li><a href="index.php?module=ExportAdmin"><img src="design/images/menu/wizards.png"><b>Автоматизация</b></a></li>
	<?php } elseif (in_array('backup',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?>
		<li><a href="index.php?module=BackupAdmin"><img src="design/images/menu/wizards.png"><b>Автоматизация</b></a></li>
	<?php }?>	
		
	<?php if (in_array('stats',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?>
		<li><a href="index.php?module=StatsAdmin"><img src="design/images/menu/statistics.png"><b>Статистика</b></a></li>
	<?php }?>
	
	<?php if (in_array('design',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?>
		<li><a href="index.php?module=ThemeAdmin"><img src="design/images/menu/design.png"><b>Дизайн</b></a></li>
	<?php }?>
	
	<?php if (in_array('settings',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?>
		<li><a href="index.php?module=SettingsAdmin"><img src="design/images/menu/settings.png"><b>Настройки</b></a></li>
	<?php } elseif (in_array('delivery',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?>
		<li><a href="index.php?module=DeliveriesAdmin"><img src="design/images/menu/settings.png"><b>Настройки</b></a></li>
	<?php } elseif (in_array('payment',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?>
		<li><a href="index.php?module=PaymentMethodsAdmin"><img src="design/images/menu/settings.png"><b>Настройки</b></a></li>
	<?php } elseif (in_array('managers',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?>
		<li><a href="index.php?module=ManagersAdmin"><img src="design/images/menu/settings.png"><b>Настройки</b></a></li>
	<?php }?>
		
	</ul>
	<!-- Главное меню (The End)-->
	
	
	<!-- Таб меню -->
	<ul id="tab_menu">
		<?php echo Smarty::$_smarty_vars['capture']['tabs'];?>

	</ul>
	<!-- Таб меню (The End)-->
	
 
	
	<!-- Основная часть страницы -->
	<div id="middle">
		<?php echo $_smarty_tpl->tpl_vars['content']->value;?>

	</div>
	<!-- Основная часть страницы (The End) --> 
	
	<!-- Подвал сайта -->
	<div id="footer">
	&copy; 2017 <a href='#'>Simpla <?php echo $_smarty_tpl->tpl_vars['config']->value->version;?>
</a>

	Вы вошли как <?php echo $_smarty_tpl->tpl_vars['manager']->value->login;?>
.
	<a href='<?php echo $_smarty_tpl->tpl_vars['config']->value->root_url;?>
?logout' id="logout">Выход</a>
	</div>
	<!-- Подвал сайта (The End)--> 
	
</div>
<!-- Вся страница (The End)--> 

</body>
</html>


<?php if ($_smarty_tpl->tpl_vars['settings']->value->pz_server&&$_smarty_tpl->tpl_vars['settings']->value->pz_phones[$_smarty_tpl->tpl_vars['manager']->value->login]) {?>
<script src="design/js/prostiezvonki/prostiezvonki.min.js"></script>
<script>
var pz_type = 'simpla';
var pz_password = '<?php echo $_smarty_tpl->tpl_vars['settings']->value->pz_password;?>
';
var pz_server = '<?php echo $_smarty_tpl->tpl_vars['settings']->value->pz_server;?>
';
var pz_phone = '<?php echo $_smarty_tpl->tpl_vars['settings']->value->pz_phones[$_smarty_tpl->tpl_vars['manager']->value->login];?>
';

function NotificationBar(message)
{
	ttop = $('body').height()-110;
	var HTMLmessage = "<div class='notification-message' style='  text-align:center; line-height: 40px;'> " + message + " </div>";
	if ($('#notification-bar').size() == 0)
	{
		$('body').prepend("<div id='notification-bar' style='-moz-border-radius: 5px 5px 5px 5px; -webkit-border-radius: 5px 5px 5px 5px; display:none;  height: 40px; padding: 20px; background-color: #fff; position: fixed; top:"+ttop+"px; right:30px; z-index: 100; color: #000;border: 1px solid #cccccc;'>" + HTMLmessage + "</div>");
	}
	else
    {
    	$('#notification-bar').html(HTMLmessage);
    }
    $('#notification-bar').slideDown();
}

$(window).on("blur focus", function (e) {
    if ($(this).data('prevType') !== e.type) {
        $(this).data('prevType', e.type);

        switch (e.type) {
        case 'focus':
            if (!pz.isConnected()) {
				pz.connect({
				            client_id: pz_password,
				            client_type: pz_type,
				            host: pz_server
				});
            }
            break;
        }
    }
});

$(function() {
	// Простые звонки
	pz.setUserPhone(pz_phone);
	pz.connect({
                client_id: pz_password,
                client_type: pz_type,
                host: pz_server
	});
    pz.onConnect(function () {
        $(".ip_call").addClass('phone');
    });
    pz.onDisconnect(function () {
        $(".ip_call").removeClass('phone');
    });
	
    $(".ip_call").click( function() {
        var phone = $(this).attr('data-phone').trim();
        pz.call(phone);
        return false;
    });

    pz.onEvent(function (event) {
        if (event.isIncoming()) {
			$.ajax({
				type: "GET",
				url: "ajax/search_orders.php",
				data: { keyword: event.from, limit:"1"},
				dataType: 'json'
			}).success(function(data){
				if(event.to == pz_phone)
				if(data.length>0)
				{
					NotificationBar('<img src="design/images/phone_sound.png" align=absmiddle> Звонит <a href="index.php?module=OrderAdmin&id='+data[0].id+'">'+data[0].name+'</a>');
				}
				else
				{
					NotificationBar('<img src="design/images/phone_sound.png" align=absmiddle> Звонок с '+event.from+'. <a href="index.php?module=OrderAdmin&phone='+event.from+'">Создать заказ</a>');
				}
			});        	     
        }
    });

});
</script>
<?php }?>


<script>
$(function() {

	if($.browser.opera)
		$("#logout").hide();
	
	$("#logout").click( function(event) {
		event.preventDefault();

		if($.browser.msie)
		{
			try{document.execCommand("ClearAuthenticationCache");}
			catch (exception){} 
			window.location.href='/';
		}
		else
		{
			$.ajax({
				url: $(this).attr('href'),
				username: '',
				password: '',
				complete: function () {
					window.location.href='/';
				},
				beforeSend : function(req) {
					req.setRequestHeader('Authorization', 'Basic');
				}
			});
		}
	});


});
</script>
<?php }} ?>
