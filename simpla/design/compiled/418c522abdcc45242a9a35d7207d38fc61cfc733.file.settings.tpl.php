<?php /* Smarty version Smarty-3.1.18, created on 2017-10-11 14:27:26
         compiled from "simpla\design\html\settings.tpl" */ ?>
<?php /*%%SmartyHeaderCode:33070975859de001e2190c3-37715588%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '418c522abdcc45242a9a35d7207d38fc61cfc733' => 
    array (
      0 => 'simpla\\design\\html\\settings.tpl',
      1 => 1492712116,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '33070975859de001e2190c3-37715588',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'manager' => 0,
    'message_success' => 0,
    'message_error' => 0,
    'config' => 0,
    'settings' => 0,
    'currency' => 0,
    'managers' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.18',
  'unifunc' => 'content_59de001e5b6e10_83883612',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_59de001e5b6e10_83883612')) {function content_59de001e5b6e10_83883612($_smarty_tpl) {?><?php if (!is_callable('smarty_function_math')) include 'D:\\openserver5.2.7\\OSPanel\\domains\\startup.my\\Smarty\\libs\\plugins\\function.math.php';
?><?php $_smarty_tpl->_capture_stack[0][] = array('tabs', null, null); ob_start(); ?>
	<li class="active"><a href="index.php?module=SettingsAdmin">Настройки</a></li>
	<?php if (in_array('currency',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?><li><a href="index.php?module=CurrencyAdmin">Валюты</a></li><?php }?>
	<?php if (in_array('delivery',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?><li><a href="index.php?module=DeliveriesAdmin">Доставка</a></li><?php }?>
	<?php if (in_array('payment',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?><li><a href="index.php?module=PaymentMethodsAdmin">Оплата</a></li><?php }?>
	<?php if (in_array('managers',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?><li><a href="index.php?module=ManagersAdmin">Менеджеры</a></li><?php }?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
 
<?php $_smarty_tpl->tpl_vars['meta_title'] = new Smarty_variable("Настройки", null, 1);
if ($_smarty_tpl->parent != null) $_smarty_tpl->parent->tpl_vars['meta_title'] = clone $_smarty_tpl->tpl_vars['meta_title'];?>

<?php if ($_smarty_tpl->tpl_vars['message_success']->value) {?>
<!-- Системное сообщение -->
<div class="message message_success">
	<span class="text"><?php if ($_smarty_tpl->tpl_vars['message_success']->value=='saved') {?>Настройки сохранены<?php }?></span>
	<?php if ($_GET['return']) {?>
	<a class="button" href="<?php echo $_GET['return'];?>
">Вернуться</a>
	<?php }?>
</div>
<!-- Системное сообщение (The End)-->
<?php }?>

<?php if ($_smarty_tpl->tpl_vars['message_error']->value) {?>
<!-- Системное сообщение -->
<div class="message message_error">
	<span class="text"><?php if ($_smarty_tpl->tpl_vars['message_error']->value=='watermark_is_not_writable') {?>Установите права на запись для файла <?php echo $_smarty_tpl->tpl_vars['config']->value->watermark_file;?>
<?php }?></span>
	<a class="button" href="">Вернуться</a>
</div>
<!-- Системное сообщение (The End)-->
<?php }?>
			

<!-- Основная форма -->
<form method=post id=product enctype="multipart/form-data">
<input type=hidden name="session_id" value="<?php echo $_SESSION['id'];?>
">
			
		<!-- Параметры -->
		<div class="block">
			<h2>Настройки сайта</h2>
			<ul>
				<li><label class=property>Имя сайта</label><input name="site_name" class="simpla_inp" type="text" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['settings']->value->site_name, ENT_QUOTES, 'UTF-8', true);?>
" /></li>
				<li><label class=property>Имя компании</label><input name="company_name" class="simpla_inp" type="text" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['settings']->value->company_name, ENT_QUOTES, 'UTF-8', true);?>
" /></li>
				<li><label class=property>Формат даты</label><input name="date_format" class="simpla_inp" type="text" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['settings']->value->date_format, ENT_QUOTES, 'UTF-8', true);?>
" /></li>
				<li><label class=property>Email для восстановления пароля</label><input name="admin_email" class="simpla_inp" type="text" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['settings']->value->admin_email, ENT_QUOTES, 'UTF-8', true);?>
" /></li>
			</ul>
		</div>
		<div class="block layer">
			<h2>Оповещения</h2>
			<ul>
				<li><label class=property>Оповещение о заказах</label><input name="order_email" class="simpla_inp" type="text" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['settings']->value->order_email, ENT_QUOTES, 'UTF-8', true);?>
" /></li>
				<li><label class=property>Оповещение о комментариях</label><input name="comment_email" class="simpla_inp" type="text" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['settings']->value->comment_email, ENT_QUOTES, 'UTF-8', true);?>
" /></li>
				<li><label class=property>Обратный адрес оповещений</label><input name="notify_from_email" class="simpla_inp" type="text" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['settings']->value->notify_from_email, ENT_QUOTES, 'UTF-8', true);?>
" /></li>
			</ul>
		</div>
		<!-- Параметры (The End)-->

		<!-- Параметры -->
		<div class="block layer">
			<h2>Формат цены</h2>
			<ul>
				<li><label class=property>Разделитель копеек</label>
					<select name="decimals_point" class="simpla_inp">
						<option value='.' <?php if ($_smarty_tpl->tpl_vars['settings']->value->decimals_point=='.') {?>selected<?php }?>>точка: 12.45 <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['currency']->value->sign, ENT_QUOTES, 'UTF-8', true);?>
</option>
						<option value=',' <?php if ($_smarty_tpl->tpl_vars['settings']->value->decimals_point==',') {?>selected<?php }?>>запятая: 12,45 <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['currency']->value->sign, ENT_QUOTES, 'UTF-8', true);?>
</option>
					</select>
				</li>
				<li><label class=property>Разделитель тысяч</label>
					<select name="thousands_separator" class="simpla_inp">
						<option value='' <?php if ($_smarty_tpl->tpl_vars['settings']->value->thousands_separator=='') {?>selected<?php }?>>без разделителя: 1245678 <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['currency']->value->sign, ENT_QUOTES, 'UTF-8', true);?>
</option>
						<option value=' ' <?php if ($_smarty_tpl->tpl_vars['settings']->value->thousands_separator==' ') {?>selected<?php }?>>пробел: 1 245 678 <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['currency']->value->sign, ENT_QUOTES, 'UTF-8', true);?>
</option>
						<option value=',' <?php if ($_smarty_tpl->tpl_vars['settings']->value->thousands_separator==',') {?>selected<?php }?>>запятая: 1,245,678 <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['currency']->value->sign, ENT_QUOTES, 'UTF-8', true);?>
</option>
					</select>
				
				
				</li>
			</ul>
		</div>
		<!-- Параметры (The End)-->
		
		<!-- Параметры -->
		<div class="block layer">
			<h2>Настройки каталога</h2>
			<ul>
				<li><label class=property>Товаров на странице сайта</label><input name="products_num" class="simpla_inp" type="text" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['settings']->value->products_num, ENT_QUOTES, 'UTF-8', true);?>
" /></li>
				<li><label class=property>Товаров на странице админки</label><input name="products_num_admin" class="simpla_inp" type="text" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['settings']->value->products_num_admin, ENT_QUOTES, 'UTF-8', true);?>
" /></li>
				<li><label class=property>Максимум товаров в заказе</label><input name="max_order_amount" class="simpla_inp" type="text" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['settings']->value->max_order_amount, ENT_QUOTES, 'UTF-8', true);?>
" /></li>
				<li><label class=property>Единицы измерения товаров</label><input name="units" class="simpla_inp" type="text" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['settings']->value->units, ENT_QUOTES, 'UTF-8', true);?>
" /></li>
			</ul>
		</div>
		<!-- Параметры (The End)-->
		
		<!-- Параметры -->
		<div class="block layer">
			<h2>Изображения товаров</h2>
			
			<ul>
				<li><label class=property>Водяной знак</label>
				<input name="watermark_file" class="simpla_inp" type="file" />

				<img style='display:block; border:1px solid #d0d0d0; margin:10px 0 10px 0;' src="<?php echo $_smarty_tpl->tpl_vars['config']->value->root_url;?>
/<?php echo $_smarty_tpl->tpl_vars['config']->value->watermark_file;?>
?<?php echo smarty_function_math(array('equation'=>'rand(10,10000)'),$_smarty_tpl);?>
">
				</li>
				<li><label class=property>Горизонтальное положение водяного знака</label><input name="watermark_offset_x" class="simpla_inp" type="text" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['settings']->value->watermark_offset_x, ENT_QUOTES, 'UTF-8', true);?>
" /> %</li>
				<li><label class=property>Вертикальное положение водяного знака</label><input name="watermark_offset_y" class="simpla_inp" type="text" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['settings']->value->watermark_offset_y, ENT_QUOTES, 'UTF-8', true);?>
" /> %</li>
				<li><label class=property>Прозрачность знака (больше &mdash; прозрачней)</label><input name="watermark_transparency" class="simpla_inp" type="text" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['settings']->value->watermark_transparency, ENT_QUOTES, 'UTF-8', true);?>
" /> %</li>
				<li><label class=property>Резкость изображений (рекомендуется 20%)</label><input name="images_sharpen" class="simpla_inp" type="text" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['settings']->value->images_sharpen, ENT_QUOTES, 'UTF-8', true);?>
" /> %</li>
			</ul>
		</div>
		<!-- Параметры (The End)-->

		
		<!-- Параметры -->
		<div class="block layer">
			<h2>Интеграция с <a href="http://prostiezvonki.ru">простыми звонками</a></h2>
			<ul>
				<li><label class=property>Сервер</label><input name="pz_server" class="simpla_inp" type="text" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['settings']->value->pz_server, ENT_QUOTES, 'UTF-8', true);?>
" /></li>
				<li><label class=property>Пароль</label><input name="pz_password" class="simpla_inp" type="text" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['settings']->value->pz_password, ENT_QUOTES, 'UTF-8', true);?>
" /></li>
				<li><label class=property>Телефоны менеджеров:</label></li>
				<?php  $_smarty_tpl->tpl_vars['manager'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['manager']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['managers']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['manager']->key => $_smarty_tpl->tpl_vars['manager']->value) {
$_smarty_tpl->tpl_vars['manager']->_loop = true;
?>
				<li><label class=property><?php echo $_smarty_tpl->tpl_vars['manager']->value->login;?>
</label><input name="pz_phones[<?php echo $_smarty_tpl->tpl_vars['manager']->value->login;?>
]" class="simpla_inp" type="text" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['settings']->value->pz_phones[$_smarty_tpl->tpl_vars['manager']->value->login], ENT_QUOTES, 'UTF-8', true);?>
" /></li>
				<?php } ?>
			</ul>
		</div>
		<!-- Параметры (The End)-->
		
		<input class="button_green button_save" type="submit" name="save" value="Сохранить" />
			
	<!-- Левая колонка свойств товара (The End)--> 
	
</form>
<!-- Основная форма (The End) -->
<?php }} ?>
