<?php /* Smarty version Smarty-3.1.18, created on 2017-10-12 04:52:45
         compiled from "simpla\design\html\comments.tpl" */ ?>
<?php /*%%SmartyHeaderCode:172615260159decaed98dbc3-43840700%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c1e6c381ee6de6324ae7174247395dc2cfb530a8' => 
    array (
      0 => 'simpla\\design\\html\\comments.tpl',
      1 => 1492708202,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '172615260159decaed98dbc3-43840700',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'manager' => 0,
    'comments' => 0,
    'keyword' => 0,
    'comments_count' => 0,
    'type' => 0,
    'comment' => 0,
    'config' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.18',
  'unifunc' => 'content_59decaedc06968_88762924',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_59decaedc06968_88762924')) {function content_59decaedc06968_88762924($_smarty_tpl) {?>
<?php $_smarty_tpl->_capture_stack[0][] = array('tabs', null, null); ob_start(); ?>
	<li class="active"><a href="index.php?module=CommentsAdmin">Комментарии</a></li>
	<?php if (in_array('feedbacks',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?><li><a href="index.php?module=FeedbacksAdmin">Обратная связь</a></li><?php }?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>



<?php $_smarty_tpl->tpl_vars['meta_title'] = new Smarty_variable('Комментарии', null, 1);
if ($_smarty_tpl->parent != null) $_smarty_tpl->parent->tpl_vars['meta_title'] = clone $_smarty_tpl->tpl_vars['meta_title'];?>


<?php if ($_smarty_tpl->tpl_vars['comments']->value||$_smarty_tpl->tpl_vars['keyword']->value) {?>
<form method="get">
<div id="search">
	<input type="hidden" name="module" value='CommentsAdmin'>
	<input class="search" type="text" name="keyword" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['keyword']->value, ENT_QUOTES, 'UTF-8', true);?>
" />
	<input class="search_button" type="submit" value=""/>
</div>
</form>
<?php }?>



<div id="header">
	<?php if ($_smarty_tpl->tpl_vars['keyword']->value&&$_smarty_tpl->tpl_vars['comments_count']->value) {?>
	<h1><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['plural'][0][0]->plural_modifier($_smarty_tpl->tpl_vars['comments_count']->value,'Нашелся','Нашлось','Нашлись');?>
 <?php echo $_smarty_tpl->tpl_vars['comments_count']->value;?>
 <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['plural'][0][0]->plural_modifier($_smarty_tpl->tpl_vars['comments_count']->value,'комментарий','комментариев','комментария');?>
</h1> 
	<?php } elseif (!$_smarty_tpl->tpl_vars['type']->value) {?>
	<h1><?php echo $_smarty_tpl->tpl_vars['comments_count']->value;?>
 <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['plural'][0][0]->plural_modifier($_smarty_tpl->tpl_vars['comments_count']->value,'комментарий','комментариев','комментария');?>
</h1> 
	<?php } elseif ($_smarty_tpl->tpl_vars['type']->value=='product') {?>
	<h1><?php echo $_smarty_tpl->tpl_vars['comments_count']->value;?>
 <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['plural'][0][0]->plural_modifier($_smarty_tpl->tpl_vars['comments_count']->value,'комментарий','комментариев','комментария');?>
 к товарам</h1> 
	<?php } elseif ($_smarty_tpl->tpl_vars['type']->value=='blog') {?>
	<h1><?php echo $_smarty_tpl->tpl_vars['comments_count']->value;?>
 <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['plural'][0][0]->plural_modifier($_smarty_tpl->tpl_vars['comments_count']->value,'комментарий','комментариев','комментария');?>
 к записям в блоге</h1> 
	<?php }?>
</div>	


<?php if ($_smarty_tpl->tpl_vars['comments']->value) {?>
<div id="main_list">
	
	<!-- Листалка страниц -->
	<?php echo $_smarty_tpl->getSubTemplate ('pagination.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>
	
	<!-- Листалка страниц (The End) -->
	
	<form id="list_form" method="post">
	<input type="hidden" name="session_id" value="<?php echo $_SESSION['id'];?>
">
	
		<div id="list" class="sortable">
			<?php  $_smarty_tpl->tpl_vars['comment'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['comment']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['comments']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['comment']->key => $_smarty_tpl->tpl_vars['comment']->value) {
$_smarty_tpl->tpl_vars['comment']->_loop = true;
?>
			<div class="<?php if (!$_smarty_tpl->tpl_vars['comment']->value->approved) {?>unapproved<?php }?> row">
		 		<div class="checkbox cell">
					<input type="checkbox" name="check[]" value="<?php echo $_smarty_tpl->tpl_vars['comment']->value->id;?>
"/>				
				</div>
				<div class="name cell">
					<div class="comment_name">
					<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['comment']->value->name, ENT_QUOTES, 'UTF-8', true);?>

					<a class="approve" href="#">Одобрить</a>
					</div>
					<div class="comment_text">
					<?php echo nl2br(htmlspecialchars($_smarty_tpl->tpl_vars['comment']->value->text, ENT_QUOTES, 'UTF-8', true));?>

					</div>
					<div class="comment_info">
					Комментарий оставлен <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['date'][0][0]->date_modifier($_smarty_tpl->tpl_vars['comment']->value->date);?>
 в <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['time'][0][0]->time_modifier($_smarty_tpl->tpl_vars['comment']->value->date);?>

					<?php if ($_smarty_tpl->tpl_vars['comment']->value->type=='product') {?>
					к товару <a target="_blank" href="<?php echo $_smarty_tpl->tpl_vars['config']->value->root_url;?>
/products/<?php echo $_smarty_tpl->tpl_vars['comment']->value->product->url;?>
#comment_<?php echo $_smarty_tpl->tpl_vars['comment']->value->id;?>
"><?php echo $_smarty_tpl->tpl_vars['comment']->value->product->name;?>
</a>
					<?php } elseif ($_smarty_tpl->tpl_vars['comment']->value->type=='blog') {?>
					к статье <a target="_blank" href="<?php echo $_smarty_tpl->tpl_vars['config']->value->root_url;?>
/blog/<?php echo $_smarty_tpl->tpl_vars['comment']->value->post->url;?>
#comment_<?php echo $_smarty_tpl->tpl_vars['comment']->value->id;?>
"><?php echo $_smarty_tpl->tpl_vars['comment']->value->post->name;?>
</a>
					<?php }?>
					</div>
				</div>
				<div class="icons cell">
					<a class="delete" title="Удалить" href="#"></a>
				</div>
				<div class="clear"></div>
			</div>
			<?php } ?>
		</div>
	
		<div id="action">
		Выбрать <label id="check_all" class="dash_link">все</label> или <label id="check_unapproved" class="dash_link">ожидающие</label>
	
		<span id="select">
		<select name="action">
			<option value="approve">Одобрить</option>
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
<?php } else { ?>
Нет комментариев
<?php }?>

<!-- Меню -->
<div id="right_menu">
	
	<!-- Категории товаров -->
	<ul>
	<li <?php if (!$_smarty_tpl->tpl_vars['type']->value) {?>class="selected"<?php }?>><a href="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0][0]->url_modifier(array('type'=>null),$_smarty_tpl);?>
">Все комментарии</a></li>
	</ul>
	<ul>
		<li <?php if ($_smarty_tpl->tpl_vars['type']->value=='product') {?>class="selected"<?php }?>><a href='<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0][0]->url_modifier(array('keyword'=>null,'type'=>'product'),$_smarty_tpl);?>
'>К товарам</a></li>
		<li <?php if ($_smarty_tpl->tpl_vars['type']->value=='blog') {?>class="selected"<?php }?>><a href='<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0][0]->url_modifier(array('keyword'=>null,'type'=>'blog'),$_smarty_tpl);?>
'>К блогу</a></li>
	</ul>
	<!-- Категории товаров (The End)-->
	
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

	// Выделить ожидающие
	$("#check_unapproved").click(function() {
		$('#list input[type="checkbox"][name*="check"]').attr('checked', false);
		$('#list .unapproved input[type="checkbox"][name*="check"]').attr('checked', true);
	});	

	// Удалить 
	$("a.delete").click(function() {
		$('#list input[type="checkbox"][name*="check"]').attr('checked', false);
		$(this).closest(".row").find('input[type="checkbox"][name*="check"]').attr('checked', true);
		$(this).closest("form").find('select[name="action"] option[value=delete]').attr('selected', true);
		$(this).closest("form").submit();
	});
	
	// Одобрить
	$("a.approve").click(function() {
		var line        = $(this).closest(".row");
		var id          = line.find('input[type="checkbox"][name*="check"]').val();
		$.ajax({
			type: 'POST',
			url: 'ajax/update_object.php',
			data: {'object': 'comment', 'id': id, 'values': {'approved': 1}, 'session_id': '<?php echo $_SESSION['id'];?>
'},
			success: function(data){
				line.removeClass('unapproved');
			},
			dataType: 'json'
		});	
		return false;	
	});
	
	$("form#list_form").submit(function() {
		if($('#list_form select[name="action"]').val()=='delete' && !confirm('Подтвердите удаление'))
			return false;	
	});	
 	
});

</script>

<?php }} ?>
