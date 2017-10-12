<?php /* Smarty version Smarty-3.1.18, created on 2017-10-12 01:25:12
         compiled from "simpla\design\html\manager.tpl" */ ?>
<?php /*%%SmartyHeaderCode:138881752959de9a48bf5e78-03596315%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '576af78ac11f98646fd9f2cad9c5bd840a894b7d' => 
    array (
      0 => 'simpla\\design\\html\\manager.tpl',
      1 => 1492712116,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '138881752959de9a48bf5e78-03596315',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'manager' => 0,
    'm' => 0,
    'message_success' => 0,
    'message_error' => 0,
    'perms' => 0,
    'p' => 0,
    'name' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.18',
  'unifunc' => 'content_59de9a48da7853_51580989',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_59de9a48da7853_51580989')) {function content_59de9a48da7853_51580989($_smarty_tpl) {?>
<?php $_smarty_tpl->_capture_stack[0][] = array('tabs', null, null); ob_start(); ?>
	<?php if (in_array('settings',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?><li><a href="index.php?module=SettingsAdmin">Настройки</a></li><?php }?>
	<?php if (in_array('currency',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?><li><a href="index.php?module=CurrencyAdmin">Валюты</a></li><?php }?>
	<?php if (in_array('delivery',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?><li><a href="index.php?module=DeliveriesAdmin">Доставка</a></li><?php }?>
	<?php if (in_array('payment',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?><li><a href="index.php?module=PaymentMethodsAdmin">Оплата</a></li><?php }?>
	<li class="active"><a href="index.php?module=ManagersAdmin">Менеджеры</a></li>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>

<?php if ($_smarty_tpl->tpl_vars['m']->value->login) {?>
<?php $_smarty_tpl->tpl_vars['meta_title'] = new Smarty_variable($_smarty_tpl->tpl_vars['m']->value->login, null, 1);
if ($_smarty_tpl->parent != null) $_smarty_tpl->parent->tpl_vars['meta_title'] = clone $_smarty_tpl->tpl_vars['meta_title'];?>
<?php } else { ?>
<?php $_smarty_tpl->tpl_vars['meta_title'] = new Smarty_variable('Новый менеджер', null, 1);
if ($_smarty_tpl->parent != null) $_smarty_tpl->parent->tpl_vars['meta_title'] = clone $_smarty_tpl->tpl_vars['meta_title'];?>
<?php }?>


<script>

$(function() {
	// Выделить все
	$("#check_all").click(function() {
		$('input[type="checkbox"][name*="permissions"]:not(:disabled)').attr('checked', $('input[type="checkbox"][name*="permissions"]:not(:disabled):not(:checked)').length>0);
	});

	<?php if ($_smarty_tpl->tpl_vars['m']->value->login) {?>$('#password_input').hide();<?php }?>
	$('#change_password').click(function() {
		$('#password_input').show();
	});
		
});

</script>


<?php if ($_smarty_tpl->tpl_vars['message_success']->value) {?>
<!-- Системное сообщение -->
<div class="message message_success">
	<span class="text"><?php if ($_smarty_tpl->tpl_vars['message_success']->value=='added') {?>Менеджер добавлен<?php } elseif ($_smarty_tpl->tpl_vars['message_success']->value=='updated') {?>Менеджер обновлен<?php } else { ?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['message_success']->value, ENT_QUOTES, 'UTF-8', true);?>
<?php }?></span>
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
	<span class="text">
	<?php if ($_smarty_tpl->tpl_vars['message_error']->value=='login_exists') {?>Менеджер с таким логином уже существует
	<?php } elseif ($_smarty_tpl->tpl_vars['message_error']->value=='empty_login') {?>Введите логин
	<?php } elseif ($_smarty_tpl->tpl_vars['message_error']->value=='not_writable') {?>Установите права на запись для файла /simpla/.passwd
	<?php } else { ?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['message_error']->value, ENT_QUOTES, 'UTF-8', true);?>
<?php }?>
	</span>
	<a class="button" href="">Вернуться</a>
</div>
<!-- Системное сообщение (The End)-->
<?php }?>


<!-- Основная форма -->
<form method=post id=product enctype="multipart/form-data">
<input type=hidden name="session_id" value="<?php echo $_SESSION['id'];?>
">
	<div id="name">
		Логин:
		<input class="name" name="login" type="text" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['m']->value->login, ENT_QUOTES, 'UTF-8', true);?>
" maxlength="32"/> 
		<input name="old_login" type="hidden" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['m']->value->login, ENT_QUOTES, 'UTF-8', true);?>
"/>
		Пароль:
		<?php if ($_smarty_tpl->tpl_vars['m']->value->login) {?><a class="dash_link"id="change_password">изменить</a><?php }?>
		<input id="password_input" class="name" name="password" type="password" value=""/> 
	</div> 

	<!-- Левая колонка -->
	<div id="column_left">
		
		<h2>Права доступа: </h2>
		<div class="block"><label id="check_all" class="dash_link">Выбрать все</label></div>

		<!-- Параметры  -->
		<div class="block">
			<ul>
			
				<?php $_smarty_tpl->tpl_vars['perms'] = new Smarty_variable(array('products'=>'Товары','categories'=>'Категории','brands'=>'Бренды','features'=>'Свойства товаров','orders'=>'Заказы','labels'=>'Метки заказов','users'=>'Покупатели','groups'=>'Группы покупателей','coupons'=>'Купоны','pages'=>'Страницы','blog'=>'Блог','comments'=>'Комментарии','feedbacks'=>'Обратная связь','import'=>'Импорт','export'=>'Экспорт','backup'=>'Бекап','stats'=>'Статистика','design'=>'Дизайн','settings'=>'Настройки','currency'=>'Валюты','delivery'=>'Способы доставки','payment'=>'Способы оплаты','managers'=>'Менеджеры','license'=>'Управление лицензией'), null, 0);?>
				
				<?php  $_smarty_tpl->tpl_vars['name'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['name']->_loop = false;
 $_smarty_tpl->tpl_vars['p'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['perms']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['name']->key => $_smarty_tpl->tpl_vars['name']->value) {
$_smarty_tpl->tpl_vars['name']->_loop = true;
 $_smarty_tpl->tpl_vars['p']->value = $_smarty_tpl->tpl_vars['name']->key;
?>
				<li><label class=property for="<?php echo $_smarty_tpl->tpl_vars['p']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['name']->value;?>
</label>
				<input id="<?php echo $_smarty_tpl->tpl_vars['p']->value;?>
" name="permissions[]" class="simpla_inp" type="checkbox" value="<?php echo $_smarty_tpl->tpl_vars['p']->value;?>
"
				<?php if ($_smarty_tpl->tpl_vars['m']->value->permissions&&in_array($_smarty_tpl->tpl_vars['p']->value,$_smarty_tpl->tpl_vars['m']->value->permissions)) {?>checked<?php }?> <?php if ($_smarty_tpl->tpl_vars['m']->value->login==$_smarty_tpl->tpl_vars['manager']->value->login) {?>disabled<?php }?>/></li>
				<?php } ?>
				
			</ul>
		</div>
		<!-- Параметры (The End)-->
	</div>
	<!-- Левая колонка (The End)--> 
	
	<input class="button_green button_save" type="submit" name="" value="Сохранить" />
</form>
<!-- Основная форма (The End) -->
<?php }} ?>
