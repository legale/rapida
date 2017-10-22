<?php /* Smarty version Smarty-3.1.18, created on 2017-10-23 02:08:23
         compiled from "simpla\design\html\import_yml.tpl" */ ?>
<?php /*%%SmartyHeaderCode:188831303659ec99607ba967-55235638%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2524666a7ce7598bfb55a36e898fbbde274dbb58' => 
    array (
      0 => 'simpla\\design\\html\\import_yml.tpl',
      1 => 1508698506,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '188831303659ec99607ba967-55235638',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.18',
  'unifunc' => 'content_59ec9960a52b01_72135504',
  'variables' => 
  array (
    'manager' => 0,
    'config' => 0,
    'filename_csv' => 0,
    'convert_only' => 0,
    'message_error' => 0,
    'import_files_dir' => 0,
    'locale' => 0,
    'filename_yml' => 0,
    'filename_yml_size' => 0,
    'filename_csv_size' => 0,
    'filename' => 0,
    'yml_params' => 0,
    'pkey' => 0,
    'columns' => 0,
    'k' => 0,
    'columns_compared' => 0,
    'f' => 0,
    'features' => 0,
    'yml_currencies' => 0,
    'c' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_59ec9960a52b01_72135504')) {function content_59ec9960a52b01_72135504($_smarty_tpl) {?><?php $_smarty_tpl->_capture_stack[0][] = array('tabs', null, null); ob_start(); ?>
	<?php if (in_array('import',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?><li><a href="index.php?module=ImportAdmin">Импорт</a></li><?php }?>
	<li class="active"><a href="index.php?module=ImportYmlAdmin">Импорт YML</a></li>
	<?php if (in_array('export',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?><li><a href="index.php?module=ExportAdmin">Экспорт</a></li><?php }?>
	<?php if (in_array('backup',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?><li><a href="index.php?module=BackupAdmin">Бекап</a></li><?php }?>
	<?php if (in_array('export',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?><li><a href="index.php?module=SystemAdmin">Обслуживание системы</a></li><?php }?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
<?php $_smarty_tpl->tpl_vars['meta_title'] = new Smarty_variable('Импорт товаров', null, 1);
if ($_smarty_tpl->parent != null) $_smarty_tpl->parent->tpl_vars['meta_title'] = clone $_smarty_tpl->tpl_vars['meta_title'];?>

<script src="<?php echo $_smarty_tpl->tpl_vars['config']->value->root_url;?>
/simpla/design/js/piecon/piecon.js"></script>
<script>
<?php if ($_smarty_tpl->tpl_vars['filename_csv']->value&&!$_smarty_tpl->tpl_vars['convert_only']->value) {?>

	
	var in_process=false;
	var count=1;

	// On document load
	$(function(){
 		Piecon.setOptions({fallback: 'force'});
 		Piecon.setProgress(0);
    	$("#progressbar").progressbar({ value: 1 });
		in_process=true;
		do_import();	    
	});
  
	function do_import(from)
	{
		from = typeof(from) != 'undefined' ? from : 0;
		$.ajax({
 			 url: "ajax/import.php",
 			 	data: {from:from},
 			 	dataType: 'json',
  				success: function(data){
  					//~ for(var key in data.items)
  					//~ {
    					//~ $('ul#import_result').prepend('<li><span class=count>'+count+'</span> <span title='+data.items[key].status+' class="status '+data.items[key].status+'"></span> <a target=_blank href="index.php?module=ProductAdmin&id='+data.items[key].product.id+'">'+data.items[key].product.name+'</a> '+data.items[key].variant.name+'</li>');
    					//~ count++;
    				//~ }

    				Piecon.setProgress(Math.round(100*data.from/data.totalsize * 100) / 100);
   					$("#progressbar").progressbar({ value: 100*data.from/data.totalsize });
   					$("ul#import_result").text('progress: ' + Math.round(data.from / 1024) + ' of ' + Math.round(data.totalsize / 1024) + ' kb');
  				
    				if(data != false && !data.end)
    				{
    					do_import(data.from);
    				}
    				else
    				{
    					Piecon.setProgress(100);
    					//~ $("#progressbar").hide('fast');
    					$("ul#import_result").append(' Done!');
    					in_process = false;
    				}
  				},
				error: function(xhr, status, errorThrown) {
					alert(errorThrown+'\n'+xhr.responseText);
        		}  				
		});
	
	} 

<?php }?>
</script>

<style>
	.ui-progressbar-value { background-color:#b4defc; background-image: url(design/images/progress.gif); background-position:left; border-color: #009ae2;}
	#progressbar{ clear: both; height:29px;}
	#result{ clear: both; width:100%;}
</style>

<?php if ($_smarty_tpl->tpl_vars['message_error']->value) {?>
<!-- Системное сообщение -->
<div class="message message_error">
	<span class="text">
	<?php if ($_smarty_tpl->tpl_vars['message_error']->value=='no_permission') {?>Установите права на запись в папку <?php echo $_smarty_tpl->tpl_vars['import_files_dir']->value;?>

	<?php } elseif ($_smarty_tpl->tpl_vars['message_error']->value=='convert_error') {?>Не получилось сконвертировать файл в кодировку UTF8
	<?php } elseif ($_smarty_tpl->tpl_vars['message_error']->value=='locale_error') {?>На сервере не установлена локаль <?php echo $_smarty_tpl->tpl_vars['locale']->value;?>
, импорт может работать некорректно
	<?php } else { ?><?php echo $_smarty_tpl->tpl_vars['message_error']->value;?>
<?php }?>
	</span>
</div>
<!-- Системное сообщение (The End)-->
<?php }?>

<?php if ($_smarty_tpl->tpl_vars['message_error']->value!='no_permission') {?>
    <?php if ($_smarty_tpl->tpl_vars['filename_yml']->value) {?>
	<div class="block layer">
					<?php if ($_smarty_tpl->tpl_vars['filename_yml']->value) {?>Имя файла YML: <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['filename_yml']->value, ENT_QUOTES, 'UTF-8', true);?>
<?php }?>
					<?php if ($_smarty_tpl->tpl_vars['filename_yml_size']->value) {?>Размер файла YML: <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['filename_yml_size']->value, ENT_QUOTES, 'UTF-8', true);?>
<?php }?>
					<?php if ($_smarty_tpl->tpl_vars['filename_csv']->value) {?>Имя файла CSV: <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['filename_csv']->value, ENT_QUOTES, 'UTF-8', true);?>
<?php }?>
					<?php if ($_smarty_tpl->tpl_vars['filename_csv_size']->value) {?>Размер файла CSV: <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['filename_csv_size']->value, ENT_QUOTES, 'UTF-8', true);?>
<?php }?>
	</div>            
            <?php if ($_smarty_tpl->tpl_vars['filename_csv']->value) {?>
				<div class="block layer">
					<h1>Импорт <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['filename']->value, ENT_QUOTES, 'UTF-8', true);?>
</h1>
					<div id='progressbar'></div>
					<ul id='import_result'></ul>
				</div>        
            <?php }?>
            
		<?php if ($_smarty_tpl->tpl_vars['yml_params']->value) {?>
		<form method=post enctype="multipart/form-data">
			<input type=hidden name="session_id" value="<?php echo $_SESSION['id'];?>
">
			<input type=hidden name="start_import_yml" value="1">

            
				<div id="list">
					<div class="row">
						<div style="width:50%" class="cell">Название параметра YML</div>
						<div style="width:50%" class="cell">Название параметра Simpla</div>
						<div class="clear"></div>
					</div>
					<?php  $_smarty_tpl->tpl_vars['pvalue'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['pvalue']->_loop = false;
 $_smarty_tpl->tpl_vars['pkey'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['yml_params']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['pvalue']->key => $_smarty_tpl->tpl_vars['pvalue']->value) {
$_smarty_tpl->tpl_vars['pvalue']->_loop = true;
 $_smarty_tpl->tpl_vars['pkey']->value = $_smarty_tpl->tpl_vars['pvalue']->key;
?>
						<div class="row">
							<div style="width:50%" class="cell"><?php echo $_smarty_tpl->tpl_vars['pkey']->value;?>
</div>
							<div style="width:50%" class="cell">
								<select name="yml_params[<?php echo $_smarty_tpl->tpl_vars['pkey']->value;?>
]">
									<optgroup label="Добавить новый или пропустить параметр">
										<option value="<?php echo $_smarty_tpl->tpl_vars['pkey']->value;?>
">Добавить как новый параметр</option>
										<option value="">Не добавлять</option>
									</optgroup>
									<optgroup label="Основные параметры Simpla">
										<?php  $_smarty_tpl->tpl_vars['f'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['f']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['columns']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['f']->key => $_smarty_tpl->tpl_vars['f']->value) {
$_smarty_tpl->tpl_vars['f']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['f']->key;
?>
										<option value="<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['columns_compared']->value[$_smarty_tpl->tpl_vars['pkey']->value]==$_smarty_tpl->tpl_vars['k']->value) {?> selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['f']->value;?>
</option>
										<?php } ?>
									</optgroup>
									<optgroup label="Уже имеющиеся параметры товаров">
										<?php  $_smarty_tpl->tpl_vars['f'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['f']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['features']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['f']->key => $_smarty_tpl->tpl_vars['f']->value) {
$_smarty_tpl->tpl_vars['f']->_loop = true;
?>
										<option value="<?php echo $_smarty_tpl->tpl_vars['f']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['pkey']->value==$_smarty_tpl->tpl_vars['f']->value) {?>selected<?php } elseif ($_smarty_tpl->tpl_vars['pkey']->value=="param_".((string)$_smarty_tpl->tpl_vars['f']->value)) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['f']->value;?>
</option>
										<?php } ?>
									</optgroup>
									</select>
							</div>  
							<div class="clear"></div>
						</div>
					<?php } ?>
				</div>
			<?php if ($_smarty_tpl->tpl_vars['yml_currencies']->value) {?>
				<div class="block layer">
					<label>Валюты из YML файла (в скобках указан rate валюты)</label>
					<select name="yml_import_currency">
						<?php  $_smarty_tpl->tpl_vars['c'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['c']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['yml_currencies']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['c']->key => $_smarty_tpl->tpl_vars['c']->value) {
$_smarty_tpl->tpl_vars['c']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['c']->key;
?>
						<option value="<?php echo $_smarty_tpl->tpl_vars['c']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['c']->value['id'];?>
 (<?php echo $_smarty_tpl->tpl_vars['c']->value['rate'];?>
)</option>
						<?php } ?>
					</select>
				</div>
			<?php }?>
				<div class="block layer">
					<label>Параметры выполнения</label>
					<select name="convert_only">
					<option value="0">Импортировать YML</option>
					<option value="1">Только сконвертировать YML файл в CSV</option>
					</select>
					<button type="submit" class="button_green">Выполнить</button>
				</div>
		</form>
		<?php }?>
    <?php }?>
    
	<?php if (!$_smarty_tpl->tpl_vars['filename_yml_size']->value) {?>
		<div class="block">
		<h1>Импорт товаров из файла Яндекс Маркета формат YML</h1>
		</div>
		<form method=post id=product enctype="multipart/form-data">
			<input type=hidden name="session_id" value="<?php echo $_SESSION['id'];?>
">
			<div class="block layer">	
				<h2>Загрузка файла с локального компьютера</h2>
				<input name="file" class="import_file" type="file" value="" />
			</div>
			<div class="block layer">
				<h2>Загрузка файла с удаленного сервера</h2>	
				<label>Введите URL для загрузки из Интернета</label>
				<input name="file_url" type="text" value=""/>
				<input type="submit" class="button_green" name="" value="Загрузить"/>
				<p>
					Поддерживаемые форматы: xml, xml.gz
					(максимальный размер файла &mdash; <?php if ($_smarty_tpl->tpl_vars['config']->value->max_upload_filesize>1024*1024) {?><?php echo $_smarty_tpl->tpl_vars['config']->value->max_upload_filesize/1024/round(1024,'2');?>
 МБ<?php } else { ?><?php echo $_smarty_tpl->tpl_vars['config']->value->max_upload_filesize/round(1024,'2');?>
 КБ<?php }?>)
				</p>
			</div>
		</form>

	<?php } elseif ($_smarty_tpl->tpl_vars['filename_yml_size']->value&&!$_smarty_tpl->tpl_vars['filename_csv_size']->value&&!$_smarty_tpl->tpl_vars['yml_params']->value) {?>
		<div class="block layer">
			<div class="row">
                <form method="post" enctype="multipart/form-data">
					<input type=hidden name="session_id" value="<?php echo $_SESSION['id'];?>
">
					<input type=hidden name="file_fields" value="<?php echo $_smarty_tpl->tpl_vars['filename_yml']->value;?>
" />
					<button type="submit" class="button_green">Считать названия полей</button>
                </form>
			</div>
		</div>
	<?php }?>

<?php }?>

<script>

	function colorize()
	{
		$("#list div.row:even").addClass('even');
		$("#list div.row:odd").removeClass('even');
	}
	// Раскрасить строки сразу
	colorize();

</script>
<?php }} ?>
