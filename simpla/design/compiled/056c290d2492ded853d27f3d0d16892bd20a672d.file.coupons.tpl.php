<?php /* Smarty version Smarty-3.1.18, created on 2017-10-20 04:31:11
         compiled from "simpla\design\html\coupons.tpl" */ ?>
<?php /*%%SmartyHeaderCode:106452706559e951df80f472-24090140%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '056c290d2492ded853d27f3d0d16892bd20a672d' => 
    array (
      0 => 'simpla\\design\\html\\coupons.tpl',
      1 => 1492708202,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '106452706559e951df80f472-24090140',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'manager' => 0,
    'coupons_count' => 0,
    'coupons' => 0,
    'coupon' => 0,
    'currency' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.18',
  'unifunc' => 'content_59e951dfaa3799_22242057',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_59e951dfaa3799_22242057')) {function content_59e951dfaa3799_22242057($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include 'D:\\openserver5.2.7\\OSPanel\\domains\\startup.my\\Smarty\\libs\\plugins\\modifier.date_format.php';
?>
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


<?php $_smarty_tpl->tpl_vars['meta_title'] = new Smarty_variable('Купоны', null, 1);
if ($_smarty_tpl->parent != null) $_smarty_tpl->parent->tpl_vars['meta_title'] = clone $_smarty_tpl->tpl_vars['meta_title'];?>
		

<div id="header">
	<?php if ($_smarty_tpl->tpl_vars['coupons_count']->value) {?>
	<h1><?php echo $_smarty_tpl->tpl_vars['coupons_count']->value;?>
 <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['plural'][0][0]->plural_modifier($_smarty_tpl->tpl_vars['coupons_count']->value,'купон','купонов','купона');?>
</h1>
	<?php } else { ?>
	<h1>Нет купонов</h1>
	<?php }?>
	<a class="add" href="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0][0]->url_modifier(array('module'=>'CouponAdmin','return'=>$_SERVER['REQUEST_URI']),$_smarty_tpl);?>
">Новый купон</a>
</div>	

<?php if ($_smarty_tpl->tpl_vars['coupons']->value) {?>
<div id="main_list">
	
	<!-- Листалка страниц -->
	<?php echo $_smarty_tpl->getSubTemplate ('pagination.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>
	
	<!-- Листалка страниц (The End) -->

	<form id="form_list" method="post">
	<input type="hidden" name="session_id" value="<?php echo $_SESSION['id'];?>
">
	
		<div id="list">
			<?php  $_smarty_tpl->tpl_vars['coupon'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['coupon']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['coupons']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['coupon']->key => $_smarty_tpl->tpl_vars['coupon']->value) {
$_smarty_tpl->tpl_vars['coupon']->_loop = true;
?>
			<div class="<?php if ($_smarty_tpl->tpl_vars['coupon']->value->valid) {?>green<?php }?> row">
		 		<div class="checkbox cell">
					<input type="checkbox" name="check[]" value="<?php echo $_smarty_tpl->tpl_vars['coupon']->value->id;?>
"/>				
				</div>
				<div class="coupon_name cell">			 	
	 				<a href="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0][0]->url_modifier(array('module'=>'CouponAdmin','id'=>$_smarty_tpl->tpl_vars['coupon']->value->id,'return'=>$_SERVER['REQUEST_URI']),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['coupon']->value->code;?>
</a>
				</div>
				<div class="coupon_discount cell">			 	
	 				Скидка <?php echo $_smarty_tpl->tpl_vars['coupon']->value->value*1;?>
 <?php if ($_smarty_tpl->tpl_vars['coupon']->value->type=='absolute') {?><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
<?php } else { ?>%<?php }?><br>
	 				<?php if ($_smarty_tpl->tpl_vars['coupon']->value->min_order_price>0) {?>
	 				<div class="detail">
	 				Для заказов от <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['coupon']->value->min_order_price, ENT_QUOTES, 'UTF-8', true);?>
 <?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>

	 				</div>
	 				<?php }?>
				</div>
				<div class="coupon_details cell">			 	
					<?php if ($_smarty_tpl->tpl_vars['coupon']->value->single) {?>
	 				<div class="detail">
	 				Одноразовый
	 				</div>
	 				<?php }?>
	 				<?php if ($_smarty_tpl->tpl_vars['coupon']->value->usages>0) {?>
	 				<div class="detail">
	 				Использован <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['coupon']->value->usages, ENT_QUOTES, 'UTF-8', true);?>
 <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['plural'][0][0]->plural_modifier($_smarty_tpl->tpl_vars['coupon']->value->usages,'раз','раз','раза');?>

	 				</div>
	 				<?php }?>
	 				<?php if ($_smarty_tpl->tpl_vars['coupon']->value->expire) {?>
	 				<div class="detail">
	 				<?php if (smarty_modifier_date_format(time(),'%Y%m%d')<=smarty_modifier_date_format($_smarty_tpl->tpl_vars['coupon']->value->expire,'%Y%m%d')) {?>
	 				Действует до <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['date'][0][0]->date_modifier($_smarty_tpl->tpl_vars['coupon']->value->expire);?>

	 				<?php } else { ?>
	 				Истёк <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['date'][0][0]->date_modifier($_smarty_tpl->tpl_vars['coupon']->value->expire);?>

	 				<?php }?>
	 				</div>
	 				<?php }?>
				</div>
				<div class="icons cell">
					<a href='#' class=delete></a>
				</div>
				<div class="name cell" style='white-space:nowrap;'>
					
	 				
				</div>
				<div class="clear"></div>
			</div>
			<?php } ?>
		</div>
		
	
		<div id="action">
		<label id="check_all" class="dash_link">Выбрать все</label>
	
		<span id="select">
		<select name="action">
			<option value="delete">Удалить</option>
		</select>
		</span>
	
		<input id="apply_action" class="button_green" type="submit" value="Применить">
		
		</div>
				
	</form>	

	<!-- Листалка страниц -->
	<?php echo $_smarty_tpl->getSubTemplate ('pagination.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>
	
	<!-- Листалка страниц (The End) -->
	
</div>
<?php }?>




<script>
$(function() {

	// Раскраска строк
	function colorize()
	{
		$("#list div.row:even").addClass('even');
		$("#list div.row:odd").removeClass('even');
	}
	// Раскрасить строки сразу
	colorize();

	// Выделить все
	$("#check_all").click(function() {
		$('#list input[type="checkbox"][name*="check"]').attr('checked', 1-$('#list input[type="checkbox"][name*="check"]').attr('checked'));
	});	

	// Удалить 
	$("a.delete").click(function() {
		$('#list input[type="checkbox"][name*="check"]').attr('checked', false);
		$(this).closest(".row").find('input[type="checkbox"][name*="check"]').attr('checked', true);
		$(this).closest("form").find('select[name="action"] option[value=delete]').attr('selected', true);
		$(this).closest("form").submit();
	});
		
	// Подтверждение удаления
	$("form").submit(function() {
		if($('#list input[type="checkbox"][name*="check"]:checked').length>0)
			if($('select[name="action"]').val()=='delete' && !confirm('Подтвердите удаление'))
				return false;	
	});
});

</script>
<?php }} ?>
