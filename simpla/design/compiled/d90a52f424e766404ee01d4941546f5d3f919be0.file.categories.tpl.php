<?php /* Smarty version Smarty-3.1.18, created on 2017-10-11 14:45:33
         compiled from "simpla\design\html\categories.tpl" */ ?>
<?php /*%%SmartyHeaderCode:69377936559de045defac92-84056811%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd90a52f424e766404ee01d4941546f5d3f919be0' => 
    array (
      0 => 'simpla\\design\\html\\categories.tpl',
      1 => 1492708202,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '69377936559de045defac92-84056811',
  'function' => 
  array (
    'categories_tree' => 
    array (
      'parameter' => 
      array (
        'level' => 0,
      ),
      'compiled' => '',
    ),
  ),
  'variables' => 
  array (
    'manager' => 0,
    'categories' => 0,
    'category' => 0,
    'level' => 0,
  ),
  'has_nocache_code' => 0,
  'version' => 'Smarty-3.1.18',
  'unifunc' => 'content_59de045e1f6c92_32224512',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_59de045e1f6c92_32224512')) {function content_59de045e1f6c92_32224512($_smarty_tpl) {?>
<?php $_smarty_tpl->_capture_stack[0][] = array('tabs', null, null); ob_start(); ?>
	<?php if (in_array('products',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?><li><a href="index.php?module=ProductsAdmin">Товары</a></li><?php }?>
	<li class="active"><a href="index.php?module=CategoriesAdmin">Категории</a></li>
	<?php if (in_array('brands',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?><li><a href="index.php?module=BrandsAdmin">Бренды</a></li><?php }?>
	<?php if (in_array('features',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?><li><a href="index.php?module=FeaturesAdmin">Свойства</a></li><?php }?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>


<?php $_smarty_tpl->tpl_vars['meta_title'] = new Smarty_variable('Категории', null, 1);
if ($_smarty_tpl->parent != null) $_smarty_tpl->parent->tpl_vars['meta_title'] = clone $_smarty_tpl->tpl_vars['meta_title'];?>


<div id="header">
	<h1>Категории товаров</h1>
	<a class="add" href="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0][0]->url_modifier(array('module'=>'CategoryAdmin','return'=>$_SERVER['REQUEST_URI']),$_smarty_tpl);?>
">Добавить категорию</a>
</div>	
<!-- Заголовок (The End) -->

<?php if ($_smarty_tpl->tpl_vars['categories']->value) {?>
<div id="main_list" class="categories">

	<form id="list_form" method="post">
	<input type="hidden" name="session_id" value="<?php echo $_SESSION['id'];?>
">
		
		<?php if (!function_exists('smarty_template_function_categories_tree')) {
    function smarty_template_function_categories_tree($_smarty_tpl,$params) {
    $saved_tpl_vars = $_smarty_tpl->tpl_vars;
    foreach ($_smarty_tpl->smarty->template_functions['categories_tree']['parameter'] as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);};
    foreach ($params as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);}?>
		<?php if ($_smarty_tpl->tpl_vars['categories']->value) {?>
		<div id="list" class="sortable">
		
			<?php  $_smarty_tpl->tpl_vars['category'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['category']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['categories']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['category']->key => $_smarty_tpl->tpl_vars['category']->value) {
$_smarty_tpl->tpl_vars['category']->_loop = true;
?>
			<div class="<?php if (!$_smarty_tpl->tpl_vars['category']->value->visible) {?>invisible<?php }?> row">		
				<div class="tree_row">
					<input type="hidden" name="positions[<?php echo $_smarty_tpl->tpl_vars['category']->value->id;?>
]" value="<?php echo $_smarty_tpl->tpl_vars['category']->value->position;?>
">
					<div class="move cell" style="margin-left:<?php echo $_smarty_tpl->tpl_vars['level']->value*20;?>
px"><div class="move_zone"></div></div>
			 		<div class="checkbox cell">
						<input type="checkbox" name="check[]" value="<?php echo $_smarty_tpl->tpl_vars['category']->value->id;?>
" />				
					</div>
					<div class="cell">
						<a href="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0][0]->url_modifier(array('module'=>'CategoryAdmin','id'=>$_smarty_tpl->tpl_vars['category']->value->id,'return'=>$_SERVER['REQUEST_URI']),$_smarty_tpl);?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['category']->value->name, ENT_QUOTES, 'UTF-8', true);?>
</a> 	 			
					</div>
					<div class="icons cell">
						<a class="preview" title="Предпросмотр в новом окне" href="../catalog/<?php echo $_smarty_tpl->tpl_vars['category']->value->url;?>
" target="_blank"></a>				
						<a class="enable" title="Активна" href="#"></a>
						<a class="delete" title="Удалить" href="#"></a>
					</div>
					<div class="clear"></div>
				</div>
				<?php smarty_template_function_categories_tree($_smarty_tpl,array('categories'=>$_smarty_tpl->tpl_vars['category']->value->subcategories,'level'=>$_smarty_tpl->tpl_vars['level']->value+1));?>

			</div>
			<?php } ?>
	
		</div>
		<?php }?>
		<?php $_smarty_tpl->tpl_vars = $saved_tpl_vars;
foreach (Smarty::$global_tpl_vars as $key => $value) if(!isset($_smarty_tpl->tpl_vars[$key])) $_smarty_tpl->tpl_vars[$key] = $value;}}?>

		<?php smarty_template_function_categories_tree($_smarty_tpl,array('categories'=>$_smarty_tpl->tpl_vars['categories']->value));?>

		
		<div id="action">
		<label id="check_all" class="dash_link">Выбрать все</label>
		
		<span id="select">
		<select name="action">
			<option value="enable">Сделать видимыми</option>
			<option value="disable">Сделать невидимыми</option>
			<option value="delete">Удалить</option>
		</select>
		</span>
		
		<input id="apply_action" class="button_green" type="submit" value="Применить">
		
		</div>
	
	</form>
</div>
<?php } else { ?>
Нет категорий
<?php }?>


<script>
$(function() {

	// Сортировка списка
	$(".sortable").sortable({
		items:".row",
		handle: ".move_zone",
		tolerance:"pointer",
		scrollSensitivity:40,
		opacity:0.7, 
		axis: "y",
		update:function()
		{
			$("#list_form input[name*='check']").attr('checked', false);
			$("#list_form").ajaxSubmit();
		}
	});
 
	// Выделить все
	$("#check_all").click(function() {
		$('#list input[type="checkbox"][name*="check"]:not(:disabled)').attr('checked', $('#list input[type="checkbox"][name*="check"]:not(:disabled):not(:checked)').length>0);
	});	

	// Показать категорию
	$("a.enable").click(function() {
		var icon        = $(this);
		var line        = icon.closest(".row");
		var id          = line.find('input[type="checkbox"][name*="check"]').val();
		var state       = line.hasClass('invisible')?1:0;
		icon.addClass('loading_icon');
		$.ajax({
			type: 'POST',
			url: 'ajax/update_object.php',
			data: {'object': 'category', 'id': id, 'values': {'visible': state}, 'session_id': '<?php echo $_SESSION['id'];?>
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

	// Удалить 
	$("a.delete").click(function() {
		$('#list input[type="checkbox"][name*="check"]').attr('checked', false);
		$(this).closest("div.row").find('input[type="checkbox"][name*="check"]:first').attr('checked', true);
		$(this).closest("form").find('select[name="action"] option[value=delete]').attr('selected', true);
		$(this).closest("form").submit();
	});

	
	// Подтвердить удаление
	$("form").submit(function() {
		if($('select[name="action"]').val()=='delete' && !confirm('Подтвердите удаление'))
			return false;	
	});

});
</script>
<?php }} ?>
