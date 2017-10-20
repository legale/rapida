<?php /* Smarty version Smarty-3.1.18, created on 2017-10-20 20:35:22
         compiled from "simpla\design\html\payment_method.tpl" */ ?>
<?php /*%%SmartyHeaderCode:151219560559ea33da5f3bf0-69594773%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '60423fdaa04c70cf556d6c06f217d28b4f1af802' => 
    array (
      0 => 'simpla\\design\\html\\payment_method.tpl',
      1 => 1492712116,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '151219560559ea33da5f3bf0-69594773',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'manager' => 0,
    'payment_method' => 0,
    'message_success' => 0,
    'message_error' => 0,
    'payment_modules' => 0,
    'payment_module' => 0,
    'currencies' => 0,
    'currency' => 0,
    'setting' => 0,
    'option' => 0,
    'payment_settings' => 0,
    'deliveries' => 0,
    'delivery' => 0,
    'payment_deliveries' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.18',
  'unifunc' => 'content_59ea33da870802_59440016',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_59ea33da870802_59440016')) {function content_59ea33da870802_59440016($_smarty_tpl) {?>
<?php $_smarty_tpl->_capture_stack[0][] = array('tabs', null, null); ob_start(); ?>
	<?php if (in_array('settings',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?><li><a href="index.php?module=SettingsAdmin">Настройки</a></li><?php }?>
	<?php if (in_array('currency',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?><li><a href="index.php?module=CurrencyAdmin">Валюты</a></li><?php }?>
	<?php if (in_array('delivery',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?><li><a href="index.php?module=DeliveriesAdmin">Доставка</a></li><?php }?>
	<li class="active"><a href="index.php?module=PaymentMethodsAdmin">Оплата</a></li>
	<?php if (in_array('managers',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?><li><a href="index.php?module=ManagersAdmin">Менеджеры</a></li><?php }?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>

<?php if ($_smarty_tpl->tpl_vars['payment_method']->value->id) {?>
<?php $_smarty_tpl->tpl_vars['meta_title'] = new Smarty_variable($_smarty_tpl->tpl_vars['payment_method']->value->name, null, 1);
if ($_smarty_tpl->parent != null) $_smarty_tpl->parent->tpl_vars['meta_title'] = clone $_smarty_tpl->tpl_vars['meta_title'];?>
<?php } else { ?>
<?php $_smarty_tpl->tpl_vars['meta_title'] = new Smarty_variable('Новый способ оплаты', null, 1);
if ($_smarty_tpl->parent != null) $_smarty_tpl->parent->tpl_vars['meta_title'] = clone $_smarty_tpl->tpl_vars['meta_title'];?>
<?php }?>


<?php echo $_smarty_tpl->getSubTemplate ('tinymce_init.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>




<script>
$(function() {
	$('div#module_settings').filter(':hidden').find("input, select, textarea").attr("disabled", true);

	$('select[name=module]').change(function(){
		$('div#module_settings').hide().find("input, select, textarea").attr("disabled", true);
		$('div#module_settings[module='+$(this).val()+']').show().find("input, select, textarea").attr("disabled", false);
	});
});


</script>






<?php if ($_smarty_tpl->tpl_vars['message_success']->value) {?>
<!-- Системное сообщение -->
<div class="message message_success">
	<span class="text"><?php if ($_smarty_tpl->tpl_vars['message_success']->value=='added') {?>Способ оплаты добавлен<?php } elseif ($_smarty_tpl->tpl_vars['message_success']->value=='updated') {?>Способ оплаты изменен<?php }?></span>
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
	<span class="text"><?php if ($_smarty_tpl->tpl_vars['message_error']->value=='empty_name') {?>Укажите название способа оплаты<?php }?></span>
	<a class="button" href="">Вернуться</a>
</div>
<!-- Системное сообщение (The End)-->
<?php }?>


<!-- Основная форма -->
<form method=post id=product enctype="multipart/form-data">
<input type=hidden name="session_id" value="<?php echo $_SESSION['id'];?>
">
	<div id="name">
		<input class="name" name=name type="text" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['payment_method']->value->name, ENT_QUOTES, 'UTF-8', true);?>
"/> 
		<input name=id type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['payment_method']->value->id;?>
"/> 
		<div class="checkbox">
			<input name=enabled value='1' type="checkbox" id="active_checkbox" <?php if ($_smarty_tpl->tpl_vars['payment_method']->value->enabled) {?>checked<?php }?>/> <label for="active_checkbox">Активен</label>
		</div>
	</div> 

	<div id="product_categories">
		<select name="module">
            <option value='null'>Ручная обработка</option>
       		<?php  $_smarty_tpl->tpl_vars['payment_module'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['payment_module']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['payment_modules']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['payment_module']->key => $_smarty_tpl->tpl_vars['payment_module']->value) {
$_smarty_tpl->tpl_vars['payment_module']->_loop = true;
?>
            	<option value='<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['payment_module']->key, ENT_QUOTES, 'UTF-8', true);?>
' <?php if ($_smarty_tpl->tpl_vars['payment_method']->value->module==$_smarty_tpl->tpl_vars['payment_module']->key) {?>selected<?php }?> ><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['payment_module']->value->name, ENT_QUOTES, 'UTF-8', true);?>
</option>
        	<?php } ?>
		</select>
	</div>
	
	<div id="product_brand">
		<label>Валюта</label>
		<div>
		<select name="currency_id">
			<?php  $_smarty_tpl->tpl_vars['currency'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['currency']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['currencies']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['currency']->key => $_smarty_tpl->tpl_vars['currency']->value) {
$_smarty_tpl->tpl_vars['currency']->_loop = true;
?>
            <option value='<?php echo $_smarty_tpl->tpl_vars['currency']->value->id;?>
' <?php if ($_smarty_tpl->tpl_vars['currency']->value->id==$_smarty_tpl->tpl_vars['payment_method']->value->currency_id) {?>selected<?php }?>><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['currency']->value->name, ENT_QUOTES, 'UTF-8', true);?>
</option>
            <?php } ?>
		</select>
		</div>
	</div>
	
	<!-- Левая колонка свойств товара -->
	<div id="column_left">
	
   		<?php  $_smarty_tpl->tpl_vars['payment_module'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['payment_module']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['payment_modules']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['payment_module']->key => $_smarty_tpl->tpl_vars['payment_module']->value) {
$_smarty_tpl->tpl_vars['payment_module']->_loop = true;
?>
        	<div class="block layer" <?php if ($_smarty_tpl->tpl_vars['payment_module']->key!=$_smarty_tpl->tpl_vars['payment_method']->value->module) {?>style='display:none;'<?php }?> id=module_settings module='<?php echo $_smarty_tpl->tpl_vars['payment_module']->key;?>
'>
			<h2><?php echo $_smarty_tpl->tpl_vars['payment_module']->value->name;?>
</h2>
			
			<ul>
			<?php  $_smarty_tpl->tpl_vars['setting'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['setting']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['payment_module']->value->settings; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['setting']->key => $_smarty_tpl->tpl_vars['setting']->value) {
$_smarty_tpl->tpl_vars['setting']->_loop = true;
?>
				<?php $_smarty_tpl->tpl_vars['variable_name'] = new Smarty_variable($_smarty_tpl->tpl_vars['setting']->value->variable, null, 0);?>
				<?php if (count($_smarty_tpl->tpl_vars['setting']->value->options)>1) {?>
				<li><label class=property><?php echo $_smarty_tpl->tpl_vars['setting']->value->name;?>
</label>
				<select name="payment_settings[<?php echo $_smarty_tpl->tpl_vars['setting']->value->variable;?>
]">
					<?php  $_smarty_tpl->tpl_vars['option'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['option']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['setting']->value->options; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['option']->key => $_smarty_tpl->tpl_vars['option']->value) {
$_smarty_tpl->tpl_vars['option']->_loop = true;
?>
					<option value='<?php echo $_smarty_tpl->tpl_vars['option']->value->value;?>
' <?php if ($_smarty_tpl->tpl_vars['option']->value->value==$_smarty_tpl->tpl_vars['payment_settings']->value[$_smarty_tpl->tpl_vars['setting']->value->variable]) {?>selected<?php }?>><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['option']->value->name, ENT_QUOTES, 'UTF-8', true);?>
</option>
					<?php } ?>
				</select>
				</li>
				<?php } elseif (count($_smarty_tpl->tpl_vars['setting']->value->options)==1) {?>
				<?php $_smarty_tpl->tpl_vars['option'] = new Smarty_variable($_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['first'][0][0]->first_modifier($_smarty_tpl->tpl_vars['setting']->value->options), null, 0);?>
				<li><label class="property" for="<?php echo $_smarty_tpl->tpl_vars['setting']->value->variable;?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['setting']->value->name, ENT_QUOTES, 'UTF-8', true);?>
</label><input name="payment_settings[<?php echo $_smarty_tpl->tpl_vars['setting']->value->variable;?>
]" class="simpla_inp" type="checkbox" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['option']->value->value, ENT_QUOTES, 'UTF-8', true);?>
" <?php if ($_smarty_tpl->tpl_vars['option']->value->value==$_smarty_tpl->tpl_vars['payment_settings']->value[$_smarty_tpl->tpl_vars['setting']->value->variable]) {?>checked<?php }?> id="<?php echo $_smarty_tpl->tpl_vars['setting']->value->variable;?>
" /> <label for="<?php echo $_smarty_tpl->tpl_vars['setting']->value->variable;?>
"><?php echo $_smarty_tpl->tpl_vars['option']->value->name;?>
</label></li>
				<?php } else { ?>
				<li><label class="property" for="<?php echo $_smarty_tpl->tpl_vars['setting']->value->variable;?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['setting']->value->name, ENT_QUOTES, 'UTF-8', true);?>
</label><input name="payment_settings[<?php echo $_smarty_tpl->tpl_vars['setting']->value->variable;?>
]" class="simpla_inp" type="text" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['payment_settings']->value[$_smarty_tpl->tpl_vars['setting']->value->variable], ENT_QUOTES, 'UTF-8', true);?>
" id="<?php echo $_smarty_tpl->tpl_vars['setting']->value->variable;?>
"/></li>
				<?php }?>
			<?php } ?>
			</ul>
			
        	
        	</div>
    	<?php } ?>
    	<div class="block layer" <?php if ($_smarty_tpl->tpl_vars['payment_method']->value->module!='') {?>style='display:none;'<?php }?> id=module_settings module='null'></div>

	</div>
	<!-- Левая колонка свойств товара (The End)--> 
	
	<!-- Правая колонка -->
	<div id="column_right">
		<div class="block layer">
		<h2>Возможные способы доставки</h2>
		<ul>
		<?php  $_smarty_tpl->tpl_vars['delivery'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['delivery']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['deliveries']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['delivery']->key => $_smarty_tpl->tpl_vars['delivery']->value) {
$_smarty_tpl->tpl_vars['delivery']->_loop = true;
?>
			<li>
			<input type=checkbox name="payment_deliveries[]" id="delivery_<?php echo $_smarty_tpl->tpl_vars['delivery']->value->id;?>
" value='<?php echo $_smarty_tpl->tpl_vars['delivery']->value->id;?>
' <?php if (in_array($_smarty_tpl->tpl_vars['delivery']->value->id,$_smarty_tpl->tpl_vars['payment_deliveries']->value)) {?>checked<?php }?>> <label for="delivery_<?php echo $_smarty_tpl->tpl_vars['delivery']->value->id;?>
"><?php echo $_smarty_tpl->tpl_vars['delivery']->value->name;?>
</label><br>
			</li>
		<?php } ?>
		</ul>		
		</div>
	</div>
	<!-- Правая колонка (The End)--> 
	
	<!-- Описагние товара -->
	<div class="block layer">
		<h2>Описание</h2>
		<textarea name="description" class="editor_small"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['payment_method']->value->description, ENT_QUOTES, 'UTF-8', true);?>
</textarea>
	</div>
	<!-- Описание товара (The End)-->
	<input class="button_green button_save" type="submit" name="" value="Сохранить" />
	
</form>
<!-- Основная форма (The End) -->

<?php }} ?>
