<?php /* Smarty version Smarty-3.1.18, created on 2017-10-12 04:52:56
         compiled from "simpla\design\html\feedbacks.tpl" */ ?>
<?php /*%%SmartyHeaderCode:38333611859decaf8c18ab3-57058353%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '69ec84b504bab5845b6b0a52039b7ec944453104' => 
    array (
      0 => 'simpla\\design\\html\\feedbacks.tpl',
      1 => 1492708202,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '38333611859decaf8c18ab3-57058353',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'manager' => 0,
    'feedbacks' => 0,
    'keyword' => 0,
    'feedbacks_count' => 0,
    'feedback' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.18',
  'unifunc' => 'content_59decaf8dc2791_08393153',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_59decaf8dc2791_08393153')) {function content_59decaf8dc2791_08393153($_smarty_tpl) {?>
<?php $_smarty_tpl->_capture_stack[0][] = array('tabs', null, null); ob_start(); ?>
		<?php if (in_array('comments',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?><li><a href="index.php?module=CommentsAdmin">Комментарии</a></li><?php }?>
		<li class="active"><a href="index.php?module=FeedbacksAdmin">Обратная связь</a></li>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>


<?php $_smarty_tpl->tpl_vars['meta_title'] = new Smarty_variable('Обратная связь', null, 1);
if ($_smarty_tpl->parent != null) $_smarty_tpl->parent->tpl_vars['meta_title'] = clone $_smarty_tpl->tpl_vars['meta_title'];?>



<?php if ($_smarty_tpl->tpl_vars['feedbacks']->value||$_smarty_tpl->tpl_vars['keyword']->value) {?>
<form method="get">
<div id="search">
	<input type="hidden" name="module" value='FeedbacksAdmin'>
	<input class="search" type="text" name="keyword" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['keyword']->value, ENT_QUOTES, 'UTF-8', true);?>
" />
	<input class="search_button" type="submit" value=""/>
</div>
</form>
<?php }?>


<div id="header">
	<?php if ($_smarty_tpl->tpl_vars['feedbacks_count']->value) {?>
	<h1><?php echo $_smarty_tpl->tpl_vars['feedbacks_count']->value;?>
 <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['plural'][0][0]->plural_modifier($_smarty_tpl->tpl_vars['feedbacks_count']->value,'сообщение','сообщений','сообщения');?>
</h1> 
	<?php } else { ?>
	<h1>Нет сообщений</h1> 
	<?php }?>
</div>	

<div id="main_list">
	
	<!-- Листалка страниц -->
	<?php echo $_smarty_tpl->getSubTemplate ('pagination.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>
	
	<!-- Листалка страниц (The End) -->
		
	<?php if ($_smarty_tpl->tpl_vars['feedbacks']->value) {?>
		<form id="list_form" method="post">
		<input type="hidden" name="session_id" value="<?php echo $_SESSION['id'];?>
">
		
			<div id="list" style="width:100%;">
				
				<?php  $_smarty_tpl->tpl_vars['feedback'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['feedback']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['feedbacks']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['feedback']->key => $_smarty_tpl->tpl_vars['feedback']->value) {
$_smarty_tpl->tpl_vars['feedback']->_loop = true;
?>
				<div class="row">
			 		<div class="checkbox cell">
						<input type="checkbox" name="check[]" value="<?php echo $_smarty_tpl->tpl_vars['feedback']->value->id;?>
" />				
					</div>
					<div class="name cell">
						<div class='comment_name'>
						<a href="mailto:<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['feedback']->value->name, ENT_QUOTES, 'UTF-8', true);?>
<<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['feedback']->value->email, ENT_QUOTES, 'UTF-8', true);?>
>?subject=Вопрос от пользователя <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['feedback']->value->name, ENT_QUOTES, 'UTF-8', true);?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['feedback']->value->name, ENT_QUOTES, 'UTF-8', true);?>
</a>
						</div>
						<div class='comment_text'>
						<?php echo nl2br(htmlspecialchars($_smarty_tpl->tpl_vars['feedback']->value->message, ENT_QUOTES, 'UTF-8', true));?>

						</div>
						<div class='comment_info'>
						Сообщение отправлено <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['date'][0][0]->date_modifier($_smarty_tpl->tpl_vars['feedback']->value->date);?>
 в <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['time'][0][0]->time_modifier($_smarty_tpl->tpl_vars['feedback']->value->date);?>

						</div>
					</div>
					<div class="icons cell">
						<a href='#' title='Удалить' class="delete"></a>
					</div>
					<div class="clear"></div>
				</div>
				<?php } ?>
			</div>
		
			<div id="action">
			<label id='check_all' class='dash_link'>Выбрать все</label>
		
			<span id=select>
			<select name="action">
				<option value="delete">Удалить</option>
			</select>
			</span>
		
			<input id='apply_action' class="button_green" type=submit value="Применить">
		
			
		</div>
		</form>
		
	<?php } else { ?>
	Нет сообщений
	<?php }?>
		
	<!-- Листалка страниц -->
	<?php echo $_smarty_tpl->getSubTemplate ('pagination.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>
	
	<!-- Листалка страниц (The End) -->
			
</div>

<!-- Меню -->
<div id="right_menu">
	
</div>
<!-- Меню  (The End) -->


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
		$('#list input[type="checkbox"][name*="check"]').attr('checked', $('#list input[type="checkbox"][name*="check"]:not(:checked)').length>0);
	});	

	// Удалить 
	$("a.delete").click(function() {
		$('#list input[type="checkbox"][name*="check"]').attr('checked', false);
		$(this).closest(".row").find('input[type="checkbox"][name*="check"]').attr('checked', true);
		$(this).closest("form").find('select[name="action"] option[value=delete]').attr('selected', true);
		$(this).closest("form").submit();
	});
	
	// Скрыт/Видим
	$("a.enable").click(function() {
		var icon        = $(this);
		var line        = icon.closest(".row");
		var id          = line.find('input[type="checkbox"][name*="check"]').val();
		var state       = line.hasClass('invisible')?1:0;
		icon.addClass('loading_icon');
		$.ajax({
			type: 'POST',
			url: 'ajax/update_object.php',
			data: {'object': 'blog', 'id': id, 'values': {'visible': state}, 'session_id': '<?php echo $_SESSION['id'];?>
'},
			success: function(data){
				icon.removeClass('loading_icon');
				if(state)
					line.removeClass('invisible');
				else
					line.addClass('invisible');				
			},
			dataType: 'json'
		});	
		return false;	
	});
	
	// Подтверждение удаления
	$("form#list_form").submit(function() {
		if($('select[name="action"]').val()=='delete' && !confirm('Подтвердите удаление'))
			return false;	
	});

});

</script>

<?php }} ?>
