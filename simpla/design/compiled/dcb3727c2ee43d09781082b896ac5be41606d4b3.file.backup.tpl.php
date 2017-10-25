<?php /* Smarty version Smarty-3.1.18, created on 2017-10-25 19:16:44
         compiled from "simpla\design\html\backup.tpl" */ ?>
<?php /*%%SmartyHeaderCode:188554828959f0b8ec13e7b6-19743571%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'dcb3727c2ee43d09781082b896ac5be41606d4b3' => 
    array (
      0 => 'simpla\\design\\html\\backup.tpl',
      1 => 1508698506,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '188554828959f0b8ec13e7b6-19743571',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'manager' => 0,
    'message_error' => 0,
    'message_success' => 0,
    'backup_files_dir' => 0,
    'backups' => 0,
    'backup' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.18',
  'unifunc' => 'content_59f0b8ec296409_45098460',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_59f0b8ec296409_45098460')) {function content_59f0b8ec296409_45098460($_smarty_tpl) {?>
<?php $_smarty_tpl->_capture_stack[0][] = array('tabs', null, null); ob_start(); ?>
	<?php if (in_array('import',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?><li><a href="index.php?module=ImportAdmin">Импорт</a></li><?php }?>
	<?php if (in_array('import',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?><li><a href="index.php?module=ImportYmlAdmin">Импорт YML</a></li><?php }?>
	<?php if (in_array('export',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?><li><a href="index.php?module=ExportAdmin">Экспорт</a></li><?php }?>
	<li class="active"><a href="index.php?module=BackupAdmin">Бекап</a></li>		
	<?php if (in_array('export',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?><li><a href="index.php?module=SystemAdmin">Обслуживание системы</a></li><?php }?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>


<?php $_smarty_tpl->tpl_vars['meta_title'] = new Smarty_variable('Бекап', null, 1);
if ($_smarty_tpl->parent != null) $_smarty_tpl->parent->tpl_vars['meta_title'] = clone $_smarty_tpl->tpl_vars['meta_title'];?>


<div id="header">
	<h1>Бекап</h1>
	<?php if ($_smarty_tpl->tpl_vars['message_error']->value!='no_permission') {?>
	<a class="add" href="">Создать бекап</a>
	<form id="hidden" method="post">
		<input type="hidden" name="session_id" value="<?php echo $_SESSION['id'];?>
">
		<input type="hidden" name="action" value="">
		<input type="hidden" name="name" value="">
	</form>
	<?php }?>
</div>	

<?php if ($_smarty_tpl->tpl_vars['message_success']->value) {?>
<!-- Системное сообщение -->
<div class="message message_success">
	<span class="text"><?php if ($_smarty_tpl->tpl_vars['message_success']->value=='created') {?>Бекап создан<?php } elseif ($_smarty_tpl->tpl_vars['message_success']->value=='restored') {?>Бекап восстановлен<?php }?></span>
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
	<?php if ($_smarty_tpl->tpl_vars['message_error']->value=='no_permission') {?>Установите права на запись в папку <?php echo $_smarty_tpl->tpl_vars['backup_files_dir']->value;?>

	<?php } else { ?><?php echo $_smarty_tpl->tpl_vars['message_error']->value;?>
<?php }?>
	</span>
</div>
<!-- Системное сообщение (The End)-->
<?php }?>

<?php if ($_smarty_tpl->tpl_vars['backups']->value) {?>
<div id="main_list">

	<form id="list_form" method="post">
	<input type="hidden" name="session_id" value="<?php echo $_SESSION['id'];?>
">

		<div id="list">			
			<?php  $_smarty_tpl->tpl_vars['backup'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['backup']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['backups']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['backup']->key => $_smarty_tpl->tpl_vars['backup']->value) {
$_smarty_tpl->tpl_vars['backup']->_loop = true;
?>
			<div class="row">
				<?php if ($_smarty_tpl->tpl_vars['message_error']->value!='no_permission') {?>
		 		<div class="checkbox cell">
					<input type="checkbox" name="check[]" value="<?php echo $_smarty_tpl->tpl_vars['backup']->value->name;?>
"/>				
				</div>
				<?php }?>
				<div class="name cell">
	 				<a href="files/backup/<?php echo $_smarty_tpl->tpl_vars['backup']->value->name;?>
"><?php echo $_smarty_tpl->tpl_vars['backup']->value->name;?>
</a>
					(<?php if ($_smarty_tpl->tpl_vars['backup']->value->size>1024*1024) {?><?php echo round(($_smarty_tpl->tpl_vars['backup']->value->size/1024/1024),2);?>
 МБ<?php } else { ?><?php echo round(($_smarty_tpl->tpl_vars['backup']->value->size/1024),2);?>
 КБ<?php }?>)
				</div>
				<div class="icons cell">
					<?php if ($_smarty_tpl->tpl_vars['message_error']->value!='no_permission') {?>
					<a class="delete" title="Удалить" href="#"></a>
					<?php }?>
		 		</div>
				<div class="icons cell">
					<a class="restore" title="Восстановить этот бекап" href="#"></a>
				</div>
		 		<div class="clear"></div>
			</div>
			<?php } ?>
		</div>
		
		<?php if ($_smarty_tpl->tpl_vars['message_error']->value!='no_permission') {?>
		<div id="action">
		<label id="check_all" class="dash_link">Выбрать все</label>
	
		<span id="select">
		<select name="action">
			<option value="delete">Удалить</option>
		</select>
		</span>
	
		<input id="apply_action" class="button_green" type="submit" value="Применить">
		</div>
		<?php }?>
	
	</form>
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
		$('#list input[type="checkbox"][name*="check"]').attr('checked', $('#list input[type="checkbox"][name*="check"]:not(:checked)').length>0);
	});	

	// Удалить 
	$("a.delete").click(function() {
		$('#list input[type="checkbox"][name*="check"]').attr('checked', false);
		$(this).closest(".row").find('input[type="checkbox"][name*="check"]').attr('checked', true);
		$(this).closest("form").find('select[name="action"] option[value=delete]').attr('selected', true);
		$(this).closest("form").submit();
	});

	// Восстановить 
	$("a.restore").click(function() {
		file = $(this).closest(".row").find('[name*="check"]').val();
		$('form#hidden input[name="action"]').val('restore');
		$('form#hidden input[name="name"]').val(file);
		$('form#hidden').submit();
		return false;
	});

	// Создать бекап 
	$("a.add").click(function() {
		$('form#hidden input[name="action"]').val('create');
		$('form#hidden').submit();
		return false;
	});

	$("form#hidden").submit(function() {
		if($('input[name="action"]').val()=='restore' && !confirm('Текущие данные будут потеряны. Подтвердите восстановление'))
			return false;	
	});
	
	$("form#list_form").submit(function() {
		if($('select[name="action"]').val()=='delete' && !confirm('Подтвердите удаление'))
			return false;	
	});
	

});

</script>

<?php }} ?>
