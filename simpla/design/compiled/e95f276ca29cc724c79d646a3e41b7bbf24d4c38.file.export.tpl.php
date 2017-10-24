<?php /* Smarty version Smarty-3.1.18, created on 2017-10-24 23:58:25
         compiled from "simpla\design\html\export.tpl" */ ?>
<?php /*%%SmartyHeaderCode:79783691059ec9967e5b8f9-14211689%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e95f276ca29cc724c79d646a3e41b7bbf24d4c38' => 
    array (
      0 => 'simpla\\design\\html\\export.tpl',
      1 => 1508698506,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '79783691059ec9967e5b8f9-14211689',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.18',
  'unifunc' => 'content_59ec996802ac34_74568522',
  'variables' => 
  array (
    'manager' => 0,
    'config' => 0,
    'message_error' => 0,
    'export_files_dir' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_59ec996802ac34_74568522')) {function content_59ec996802ac34_74568522($_smarty_tpl) {?><?php $_smarty_tpl->_capture_stack[0][] = array('tabs', null, null); ob_start(); ?>
	<?php if (in_array('import',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?><li><a href="index.php?module=ImportAdmin">Импорт</a></li><?php }?>
	<?php if (in_array('import',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?><li><a href="index.php?module=ImportYmlAdmin">Импорт YML</a></li><?php }?>
	<li class="active"><a href="index.php?module=ExportAdmin">Экспорт</a></li>
	<?php if (in_array('backup',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?><li><a href="index.php?module=BackupAdmin">Бекап</a></li><?php }?>
	<?php if (in_array('export',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?><li><a href="index.php?module=SystemAdmin">Обслуживание системы</a></li><?php }?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
<?php $_smarty_tpl->tpl_vars['meta_title'] = new Smarty_variable('Экспорт товаров', null, 1);
if ($_smarty_tpl->parent != null) $_smarty_tpl->parent->tpl_vars['meta_title'] = clone $_smarty_tpl->tpl_vars['meta_title'];?>

<script src="<?php echo $_smarty_tpl->tpl_vars['config']->value->root_url;?>
/simpla/design/js/piecon/piecon.js"></script>
<script>

	
var in_process=false;

$(function() {

	// On document load
	$('input#start').click(function() {
 
 		Piecon.setOptions({fallback: 'force'});
 		Piecon.setProgress(0);
    	$("#progressbar").progressbar({ value: 0 });

    	$("#start").hide('fast');
		do_export();
    
	});
  
	function do_export(page)
	{
		page = typeof(page) != 'undefined' ? page : 1;

		$.ajax({
 			 url: "ajax/export.php",
 			 	data: {page:page},
 			 	dataType: 'json',
  				success: function(data){
  				
    				if(data && !data.end)
    				{
    					Piecon.setProgress(Math.round(100*data.page/data.totalpages));
    					$("#progressbar").progressbar({ value: 100*data.page/data.totalpages });
    					do_export(data.page*1+1);
    				}
    				else
    				{	
	    				if(data && data.end)
	    				{
	    					Piecon.setProgress(100);
	    					$("#progressbar").hide('fast');
	    					window.location.href = 'files/export/export.csv';
    					}
    				}
  				},
				error:function(xhr, status, errorThrown) {
					alert(errorThrown+'\n'+xhr.responseText);
        		}  				
  				
		});
	
	} 
	
});

</script>

<style>
	.ui-progressbar-value { background-image: url(design/images/progress.gif); background-position:left; border-color: #009ae2;}
	#progressbar{ clear: both; height:29px; }
	#result{ clear: both; width:100%;}
	#download{ display:none;  clear: both; }
</style>


<?php if ($_smarty_tpl->tpl_vars['message_error']->value) {?>
<!-- Системное сообщение -->
<div class="message message_error">
	<span class="text">
	<?php if ($_smarty_tpl->tpl_vars['message_error']->value=='no_permission') {?>Установите права на запись в папку <?php echo $_smarty_tpl->tpl_vars['export_files_dir']->value;?>

	<?php } else { ?><?php echo $_smarty_tpl->tpl_vars['message_error']->value;?>
<?php }?>
	</span>
</div>
<!-- Системное сообщение (The End)-->
<?php }?>


<div>
	<h1>Экспорт товаров</h1>
	<?php if ($_smarty_tpl->tpl_vars['message_error']->value!='no_permission') {?>
	<div id='progressbar'></div>
	<input class="button_green" id="start" type="button" name="" value="Экспортировать" />	
	<?php }?>
</div>
 
<?php }} ?>
