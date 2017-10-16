<?php /* Smarty version Smarty-3.1.18, created on 2017-10-15 15:31:46
         compiled from "simpla\design\html\theme.tpl" */ ?>
<?php /*%%SmartyHeaderCode:175794985759e35532017171-40238872%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '473cb5278dd8353e0cdfebb3043f44b14aa3f01e' => 
    array (
      0 => 'simpla\\design\\html\\theme.tpl',
      1 => 1492708202,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '175794985759e35532017171-40238872',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'theme' => 0,
    'settings' => 0,
    'message_error' => 0,
    'themes_dir' => 0,
    'themes' => 0,
    't' => 0,
    'root_dir' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.18',
  'unifunc' => 'content_59e35532245b70_06642921',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_59e35532245b70_06642921')) {function content_59e35532245b70_06642921($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_truncate')) include 'D:\\openserver5.2.7\\OSPanel\\domains\\startup.my\\Smarty\\libs\\plugins\\modifier.truncate.php';
?><?php $_smarty_tpl->_capture_stack[0][] = array('tabs', null, null); ob_start(); ?>
	<li class="active"><a href="index.php?module=ThemeAdmin">Тема</a></li>
	<li><a href="index.php?module=TemplatesAdmin">Шаблоны</a></li>		
	<li><a href="index.php?module=StylesAdmin">Стили</a></li>		
	<li><a href="index.php?module=ImagesAdmin">Изображения</a></li>		
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>

<?php if ($_smarty_tpl->tpl_vars['theme']->value->name) {?>
<?php $_smarty_tpl->tpl_vars['meta_title'] = new Smarty_variable("Тема ".((string)$_smarty_tpl->tpl_vars['theme']->value->name), null, 1);
if ($_smarty_tpl->parent != null) $_smarty_tpl->parent->tpl_vars['meta_title'] = clone $_smarty_tpl->tpl_vars['meta_title'];?>
<?php }?>

<script>

	
$(function() {

	// Выбрать тему
	$('.set_main_theme').click(function() {
     	$("form input[name=action]").val('set_main_theme');
    	$("form input[name=theme]").val($(this).closest('li').attr('theme'));
    	$("form").submit();
	});	
	
	// Клонировать текущую тему
	$('#header .add').click(function() {
     	$("form input[name=action]").val('clone_theme');
    	$("form").submit();
	});	
	
	// Редактировать название
	$("a.edit").click(function() {
		name = $(this).closest('li').attr('theme');
		inp1 = $('<input type=hidden name="old_name[]">').val(name);
		inp2 = $('<input type=text name="new_name[]">').val(name);
		$(this).closest('li').find("p.name").html('').append(inp1).append(inp2);
		inp2.focus().select();
		return false;
	});
	
	// Удалить тему
	$('.delete').click(function() {
     	$("form input[name=action]").val('delete_theme');
     	$("form input[name=theme]").val($(this).closest('li').attr('theme'));
   		$("form").submit();
	});	

	$("form").submit(function() {
		if($("form input[name=action]").val()=='delete_theme' && !confirm('Подтвердите удаление'))
			return false;	
	});
	
});

</script>

<div id="header">
<h1 class="<?php if ($_smarty_tpl->tpl_vars['theme']->value->locked) {?>locked<?php }?>">Текущая тема &mdash; <?php echo $_smarty_tpl->tpl_vars['theme']->value->name;?>
</h1>
<a class="add" href="#">Создать копию темы <?php echo $_smarty_tpl->tpl_vars['settings']->value->theme;?>
</a>
</div>

<?php if ($_smarty_tpl->tpl_vars['message_error']->value) {?>
<!-- Системное сообщение -->
<div class="message message_error">
	<span class="text"><?php if ($_smarty_tpl->tpl_vars['message_error']->value=='permissions') {?>Установите права на запись для папки <?php echo $_smarty_tpl->tpl_vars['themes_dir']->value;?>

	<?php } elseif ($_smarty_tpl->tpl_vars['message_error']->value=='name_exists') {?>Тема с таким именем уже существует
	<?php } else { ?><?php echo $_smarty_tpl->tpl_vars['message_error']->value;?>
<?php }?></span>
</div>
<!-- Системное сообщение (The End)-->
<?php }?>

<div class="block layer">

<form method="post" enctype="multipart/form-data">
<input type="hidden" name="session_id" value="<?php echo $_SESSION['id'];?>
">
<input type=hidden name="action">
<input type=hidden name="theme">

<ul class="themes">
<?php  $_smarty_tpl->tpl_vars['t'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['t']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['themes']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['t']->key => $_smarty_tpl->tpl_vars['t']->value) {
$_smarty_tpl->tpl_vars['t']->_loop = true;
?>
	<li theme='<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['t']->value->name, ENT_QUOTES, 'UTF-8', true);?>
'>
		<?php if ($_smarty_tpl->tpl_vars['theme']->value->name==$_smarty_tpl->tpl_vars['t']->value->name) {?><img class="tick" src='design/images/tick.png'> <?php }?>
		<?php if ($_smarty_tpl->tpl_vars['t']->value->locked) {?><img class="tick" src='design/images/lock_small.png'> <?php }?>
		<?php if ($_smarty_tpl->tpl_vars['theme']->value->name!=$_smarty_tpl->tpl_vars['t']->value->name&&!$_smarty_tpl->tpl_vars['t']->value->locked) {?>
		<a href='#' title="Удалить" class='delete'><img src='design/images/delete.png'></a>
		<a href='#' title="Переименовать" class='edit'><img src='design/images/pencil.png'></a>
		<?php } elseif ($_smarty_tpl->tpl_vars['theme']->value->name!=$_smarty_tpl->tpl_vars['t']->value->name) {?>
		
		<?php } elseif (!$_smarty_tpl->tpl_vars['t']->value->locked) {?>
		<a href='#' title="Удалить" class='delete'><img src='design/images/delete.png'></a>
		<a href='#' title="Изменить название" class='edit'><img src='design/images/pencil.png'></a>
		<?php }?>
		<?php if ($_smarty_tpl->tpl_vars['theme']->value->name==$_smarty_tpl->tpl_vars['t']->value->name) {?>
		<p class=name><?php echo smarty_modifier_truncate(htmlspecialchars($_smarty_tpl->tpl_vars['t']->value->name, ENT_QUOTES, 'UTF-8', true),16,'...');?>
</p>
		<?php } else { ?>
		<p class=name><a href='#' class='set_main_theme'><?php echo smarty_modifier_truncate(htmlspecialchars($_smarty_tpl->tpl_vars['t']->value->name, ENT_QUOTES, 'UTF-8', true),16,'...');?>
</a></p>
		<?php }?>
		<a href="index.php?module=TemplatesAdmin"><img class="preview" src='<?php echo $_smarty_tpl->tpl_vars['root_dir']->value;?>
../design/<?php echo $_smarty_tpl->tpl_vars['t']->value->name;?>
/preview.png'></a>
	</li>
<?php } ?>
</ul>

<div class="block">
<input class="button_green button_save" type="submit" name="save" value="Сохранить" />
</div>
</form>

</div><?php }} ?>
