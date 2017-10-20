<?php /* Smarty version Smarty-3.1.18, created on 2017-10-19 20:56:54
         compiled from "simpla\design\html\orders.tpl" */ ?>
<?php /*%%SmartyHeaderCode:112187288359e8e7664cb409-22899339%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '975eec735d92815c1ee66d8220ea9da33ed5b952' => 
    array (
      0 => 'simpla\\design\\html\\orders.tpl',
      1 => 1492708202,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '112187288359e8e7664cb409-22899339',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'manager' => 0,
    'status' => 0,
    'keyword' => 0,
    'orders_count' => 0,
    'message_error' => 0,
    'orders' => 0,
    'order' => 0,
    'l' => 0,
    'currency' => 0,
    'labels' => 0,
    'label' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.18',
  'unifunc' => 'content_59e8e766715384_48134550',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_59e8e766715384_48134550')) {function content_59e8e766715384_48134550($_smarty_tpl) {?>
<?php $_smarty_tpl->_capture_stack[0][] = array('tabs', null, null); ob_start(); ?>
	<?php if (in_array('orders',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?>
	<li <?php if ($_smarty_tpl->tpl_vars['status']->value===0) {?>class="active"<?php }?>><a href="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0][0]->url_modifier(array('module'=>'OrdersAdmin','status'=>0,'keyword'=>null,'id'=>null,'page'=>null,'label'=>null),$_smarty_tpl);?>
">Новые</a></li>
	<li <?php if ($_smarty_tpl->tpl_vars['status']->value==1) {?>class="active"<?php }?>><a href="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0][0]->url_modifier(array('module'=>'OrdersAdmin','status'=>1,'keyword'=>null,'id'=>null,'page'=>null,'label'=>null),$_smarty_tpl);?>
">Приняты</a></li>
	<li <?php if ($_smarty_tpl->tpl_vars['status']->value==2) {?>class="active"<?php }?>><a href="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0][0]->url_modifier(array('module'=>'OrdersAdmin','status'=>2,'keyword'=>null,'id'=>null,'page'=>null,'label'=>null),$_smarty_tpl);?>
">Выполнены</a></li>
	<li <?php if ($_smarty_tpl->tpl_vars['status']->value==3) {?>class="active"<?php }?>><a href="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0][0]->url_modifier(array('module'=>'OrdersAdmin','status'=>3,'keyword'=>null,'id'=>null,'page'=>null,'label'=>null),$_smarty_tpl);?>
">Удалены</a></li>
	<?php if ($_smarty_tpl->tpl_vars['keyword']->value) {?>
	<li class="active"><a href="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0][0]->url_modifier(array('module'=>'OrdersAdmin','keyword'=>$_smarty_tpl->tpl_vars['keyword']->value,'id'=>null,'label'=>null),$_smarty_tpl);?>
">Поиск</a></li>
	<?php }?>
	<?php }?>
	<?php if (in_array('labels',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?>
	<li><a href="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0][0]->url_modifier(array('module'=>'OrdersLabelsAdmin','keyword'=>null,'id'=>null,'page'=>null,'label'=>null),$_smarty_tpl);?>
">Метки</a></li>
	<?php }?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>


<?php $_smarty_tpl->tpl_vars['meta_title'] = new Smarty_variable('Заказы', null, 1);
if ($_smarty_tpl->parent != null) $_smarty_tpl->parent->tpl_vars['meta_title'] = clone $_smarty_tpl->tpl_vars['meta_title'];?>


<form method="get">
<div id="search">
	<input type="hidden" name="module" value="OrdersAdmin">
	<input class="search" type="text" name="keyword" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['keyword']->value, ENT_QUOTES, 'UTF-8', true);?>
"/>
	<input class="search_button" type="submit" value=""/>
</div>
</form>
	

<div id="header">
	<h1><?php if ($_smarty_tpl->tpl_vars['orders_count']->value) {?><?php echo $_smarty_tpl->tpl_vars['orders_count']->value;?>
<?php } else { ?>Нет<?php }?> заказ<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['plural'][0][0]->plural_modifier($_smarty_tpl->tpl_vars['orders_count']->value,'','ов','а');?>
</h1>		
	<a class="add" href="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0][0]->url_modifier(array('module'=>'OrderAdmin'),$_smarty_tpl);?>
">Добавить заказ</a>
</div>	

<?php if ($_smarty_tpl->tpl_vars['message_error']->value) {?>
<!-- Системное сообщение -->
<div class="message message_error">
	<span class="text"><?php if ($_smarty_tpl->tpl_vars['message_error']->value=='error_closing') {?>Нехватка некоторых товаров на складе<?php } else { ?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['message_error']->value, ENT_QUOTES, 'UTF-8', true);?>
<?php }?></span>
	<?php if ($_GET['return']) {?>
	<a class="button" href="<?php echo $_GET['return'];?>
">Вернуться</a>
	<?php }?>
</div>
<?php }?>

<?php if ($_smarty_tpl->tpl_vars['orders']->value) {?>
<div id="main_list">
	
	<!-- Листалка страниц -->
	<?php echo $_smarty_tpl->getSubTemplate ('pagination.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>
	
	<!-- Листалка страниц (The End) -->
	
	<form id="form_list" method="post">
	<input type="hidden" name="session_id" value="<?php echo $_SESSION['id'];?>
">

		<div id="list">		
			<?php  $_smarty_tpl->tpl_vars['order'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['order']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['orders']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['order']->key => $_smarty_tpl->tpl_vars['order']->value) {
$_smarty_tpl->tpl_vars['order']->_loop = true;
?>
			<div class="<?php if ($_smarty_tpl->tpl_vars['order']->value->paid) {?>green<?php }?> row">
		 		<div class="checkbox cell">
					<input type="checkbox" name="check[]" value="<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
"/>				
				</div>
				<div class="order_date cell">				 	
	 				<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['date'][0][0]->date_modifier($_smarty_tpl->tpl_vars['order']->value->date);?>
 в <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['time'][0][0]->time_modifier($_smarty_tpl->tpl_vars['order']->value->date);?>

				</div>
				<div class="order_name cell">
					<?php  $_smarty_tpl->tpl_vars['l'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['l']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['order']->value->labels; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['l']->key => $_smarty_tpl->tpl_vars['l']->value) {
$_smarty_tpl->tpl_vars['l']->_loop = true;
?>
					<span class="order_label" style="background-color:#<?php echo $_smarty_tpl->tpl_vars['l']->value->color;?>
;" title="<?php echo $_smarty_tpl->tpl_vars['l']->value->name;?>
"></span>
					<?php } ?>
	 				<a href="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0][0]->url_modifier(array('module'=>'OrderAdmin','id'=>$_smarty_tpl->tpl_vars['order']->value->id,'return'=>$_SERVER['REQUEST_URI']),$_smarty_tpl);?>
">Заказ №<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
</a> <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->name, ENT_QUOTES, 'UTF-8', true);?>

	 				<?php if ($_smarty_tpl->tpl_vars['order']->value->note) {?>
	 				<div class="note"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->note, ENT_QUOTES, 'UTF-8', true);?>
</div>
	 				<?php }?> 	 			
				</div>
				<div class="icons cell">
					<a href='<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0][0]->url_modifier(array('module'=>'OrderAdmin','id'=>$_smarty_tpl->tpl_vars['order']->value->id,'view'=>'print'),$_smarty_tpl);?>
'  target="_blank" class="print" title="Печать заказа"></a>
					<a href='#' class=delete title="Удалить"></a>
				</div>
				<div class="name cell" style='white-space:nowrap;'>
	 				<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->total_price, ENT_QUOTES, 'UTF-8', true);?>
 <?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>

				</div>
				<div class="icons cell">
					<?php if ($_smarty_tpl->tpl_vars['order']->value->paid) {?>
						<img src='design/images/cash_stack.png' alt='Оплачен' title='Оплачен'>
					<?php } else { ?>
						<img src='design/images/cash_stack_gray.png' alt='Не оплачен' title='Не оплачен'>				
					<?php }?>			 	
				</div>
				<?php if ($_smarty_tpl->tpl_vars['keyword']->value) {?>
				<div class="icons cell">
						<?php if ($_smarty_tpl->tpl_vars['order']->value->status==0) {?>
						<img src='design/images/new.png' alt='Новый' title='Новый'>
						<?php }?>
						<?php if ($_smarty_tpl->tpl_vars['order']->value->status==1) {?>
						<img src='design/images/time.png' alt='Принят' title='Принят'>
						<?php }?>
						<?php if ($_smarty_tpl->tpl_vars['order']->value->status==2) {?>
						<img src='design/images/tick.png' alt='Выполнен' title='Выполнен'>
						<?php }?>
						<?php if ($_smarty_tpl->tpl_vars['order']->value->status==3) {?>
						<img src='design/images/cross.png' alt='Удалён' title='Удалён'>
						<?php }?>
				</div>
				<?php }?>
				<div class="clear"></div>
			</div>
			<?php } ?>
		</div>
	
		<div id="action">
		<label id='check_all' class="dash_link">Выбрать все</label>
	
		<span id="select">
		<select name="action">
			<?php if ($_smarty_tpl->tpl_vars['status']->value!==0) {?><option value="set_status_0">В новые</option><?php }?>
			<?php if ($_smarty_tpl->tpl_vars['status']->value!==1) {?><option value="set_status_1">В принятые</option><?php }?>
			<?php if ($_smarty_tpl->tpl_vars['status']->value!==2) {?><option value="set_status_2">В выполненные</option><?php }?>
			<?php  $_smarty_tpl->tpl_vars['l'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['l']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['labels']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['l']->key => $_smarty_tpl->tpl_vars['l']->value) {
$_smarty_tpl->tpl_vars['l']->_loop = true;
?>
			<option value="set_label_<?php echo $_smarty_tpl->tpl_vars['l']->value->id;?>
">Отметить &laquo;<?php echo $_smarty_tpl->tpl_vars['l']->value->name;?>
&raquo;</option>
			<?php } ?>
			<?php  $_smarty_tpl->tpl_vars['l'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['l']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['labels']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['l']->key => $_smarty_tpl->tpl_vars['l']->value) {
$_smarty_tpl->tpl_vars['l']->_loop = true;
?>
			<option value="unset_label_<?php echo $_smarty_tpl->tpl_vars['l']->value->id;?>
">Снять &laquo;<?php echo $_smarty_tpl->tpl_vars['l']->value->name;?>
&raquo;</option>
			<?php } ?>
			<option value="delete">Удалить выбранные заказы</option>
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

<!-- Меню -->
<div id="right_menu">
	
	<?php if ($_smarty_tpl->tpl_vars['labels']->value) {?>
	<!-- Метки -->
	<ul id="labels">
		<li <?php if (!$_smarty_tpl->tpl_vars['label']->value) {?>class="selected"<?php }?>><span class="label"></span> <a href="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0][0]->url_modifier(array('label'=>null),$_smarty_tpl);?>
">Все заказы</a></li>
		<?php  $_smarty_tpl->tpl_vars['l'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['l']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['labels']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['l']->key => $_smarty_tpl->tpl_vars['l']->value) {
$_smarty_tpl->tpl_vars['l']->_loop = true;
?>
		<li data-label-id="<?php echo $_smarty_tpl->tpl_vars['l']->value->id;?>
" <?php if ($_smarty_tpl->tpl_vars['label']->value->id==$_smarty_tpl->tpl_vars['l']->value->id) {?>class="selected"<?php }?>>
		<span style="background-color:#<?php echo $_smarty_tpl->tpl_vars['l']->value->color;?>
;" class="order_label"></span>
		<a href="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0][0]->url_modifier(array('label'=>$_smarty_tpl->tpl_vars['l']->value->id),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['l']->value->name;?>
</a></li>
		<?php } ?>
	</ul>
	<!-- Метки -->
	<?php }?>
	
</div>
<!-- Меню  (The End) -->





<script>

$(function() {

	// Сортировка списка
	$("#labels").sortable({
		items:             "li",
		tolerance:         "pointer",
		scrollSensitivity: 40,
		opacity:           0.7
	});
	

	$("#main_list #list .row").droppable({
		activeClass: "drop_active",
		hoverClass: "drop_hover",
		tolerance: "pointer",
		drop: function(event, ui){
			label_id = $(ui.helper).attr('data-label-id');
			$(this).find('input[type="checkbox"][name*="check"]').attr('checked', true);
			$(this).closest("form").find('select[name="action"] option[value=set_label_'+label_id+']').attr("selected", "selected");		
			$(this).closest("form").submit();
			return false;	
		}		
	});
	
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
		$('#list input[type="checkbox"][name*="check"]').attr('checked', $('#list input[type="checkbox"][name*="check"]:not(:checked)').length>0);
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
