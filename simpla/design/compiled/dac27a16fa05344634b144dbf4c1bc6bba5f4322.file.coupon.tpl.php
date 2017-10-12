<?php /* Smarty version Smarty-3.1.18, created on 2017-10-12 02:10:21
         compiled from "simpla\design\html\coupon.tpl" */ ?>
<?php /*%%SmartyHeaderCode:202935732059dea4dd77d185-51615500%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'dac27a16fa05344634b144dbf4c1bc6bba5f4322' => 
    array (
      0 => 'simpla\\design\\html\\coupon.tpl',
      1 => 1492712116,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '202935732059dea4dd77d185-51615500',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'manager' => 0,
    'coupon' => 0,
    'message_success' => 0,
    'message_error' => 0,
    'currency' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.18',
  'unifunc' => 'content_59dea4dd980bf6_77444072',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_59dea4dd980bf6_77444072')) {function content_59dea4dd980bf6_77444072($_smarty_tpl) {?>
<?php $_smarty_tpl->_capture_stack[0][] = array('tabs', null, null); ob_start(); ?>
	<?php if (in_array('users',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?><li><a href="index.php?module=UsersAdmin">Покупатели</a></li><?php }?>
	<?php if (in_array('groups',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?><li><a href="index.php?module=GroupsAdmin">Группы</a></li><?php }?>
	<li class="active"><a href="index.php?module=CouponsAdmin">Купоны</a></li>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>

<?php if ($_smarty_tpl->tpl_vars['coupon']->value->code) {?>
<?php $_smarty_tpl->tpl_vars['meta_title'] = new Smarty_variable($_smarty_tpl->tpl_vars['coupon']->value->code, null, 1);
if ($_smarty_tpl->parent != null) $_smarty_tpl->parent->tpl_vars['meta_title'] = clone $_smarty_tpl->tpl_vars['meta_title'];?>
<?php } else { ?>
<?php $_smarty_tpl->tpl_vars['meta_title'] = new Smarty_variable('Новый купон', null, 1);
if ($_smarty_tpl->parent != null) $_smarty_tpl->parent->tpl_vars['meta_title'] = clone $_smarty_tpl->tpl_vars['meta_title'];?>
<?php }?>


<script src="design/js/jquery/datepicker/jquery.ui.datepicker-ru.js"></script>

<script>
$(function() {

	$('input[name="expire"]').datepicker({
		regional:'ru'
	});
	$('input[name="end"]').datepicker({
		regional:'ru'
	});

	// On change date
	$('input[name="expire"]').focus(function() {
 
    	$('input[name="expires"]').attr('checked', true);

	});

});
</script>


<?php if ($_smarty_tpl->tpl_vars['message_success']->value) {?>
<!-- Системное сообщение -->
<div class="message message_success">
	<span class="text"><?php if ($_smarty_tpl->tpl_vars['message_success']->value=='added') {?>Купон добавлен<?php } elseif ($_smarty_tpl->tpl_vars['message_success']->value=='updated') {?>Купон изменен<?php }?></span>
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
		<?php if ($_smarty_tpl->tpl_vars['message_error']->value=='code_exists') {?>Купон с таким кодом уже существует<?php }?>
		<?php if ($_smarty_tpl->tpl_vars['message_error']->value=='code_empty') {?>Заполните название купона<?php }?>
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
		<input class="name" name="code" type="text" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['coupon']->value->code, ENT_QUOTES, 'UTF-8', true);?>
"/>
		<input name="id" class="name" type="hidden" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['coupon']->value->id, ENT_QUOTES, 'UTF-8', true);?>
"/>		
	</div> 

	<!-- Левая колонка свойств товара -->
	<div id="column_left">
			
		<div class="block layer">
			<ul>
				<li>
					<label class=property>Скидка</label><input name="value" class="coupon_value" type="text" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['coupon']->value->value, ENT_QUOTES, 'UTF-8', true);?>
" />
					<select class="coupon_type" name="type">
						<option value="percentage" <?php if ($_smarty_tpl->tpl_vars['coupon']->value->type=='percentage') {?>selected<?php }?>>%</option>
						<option value="absolute" <?php if ($_smarty_tpl->tpl_vars['coupon']->value->type=='absolute') {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
</option>
					</select>
				</li>
				<li>
					<label class=property>Для заказов от</label>
					<input class="coupon_value" type="text" name="min_order_price" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['coupon']->value->min_order_price, ENT_QUOTES, 'UTF-8', true);?>
"> <?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
		
				</li>
				<li>
					<label class=property for="single"></label>
					<input type="checkbox" name="single" id="single" value="1" <?php if ($_smarty_tpl->tpl_vars['coupon']->value->single==1) {?>checked<?php }?>> <label for="single">одноразовый</label>					
				</li>
			</ul>
		</div>
			
	</div>
	<!-- Левая колонка свойств товара (The End)--> 
	
	<!-- Правая колонка свойств товара -->	
	<div id="column_right">

		<div class="block layer">
			<ul>
				<li><label class=property><input type=checkbox name="expires" value="1" <?php if ($_smarty_tpl->tpl_vars['coupon']->value->expire) {?>checked<?php }?>>Истекает</label><input type=text name=expire value='<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['date'][0][0]->date_modifier($_smarty_tpl->tpl_vars['coupon']->value->expire);?>
'></li>
			</ul>
		</div>
		
	</div>
	<!-- Правая колонка свойств товара (The End)--> 
	
	<input class="button_green button_save" type="submit" name="" value="Сохранить" />
	
</form>
<!-- Основная форма (The End) -->
<?php }} ?>
