{capture name=tabs}
	{if isset($userperm['import'])}<li><a href="?module=ImportAdmin">Импорт</a></li>{/if}
	<li class="active"><a href="?module=ImportYmlAdmin">Импорт YML</a></li>
	{if isset($userperm['export'])}<li><a href="?module=ExportAdmin">Экспорт</a></li>{/if}
	{if isset($userperm['backup'])}<li><a href="?module=BackupAdmin">Бекап</a></li>{/if}
	{if isset($userperm['export'])}<li><a href="?module=SystemAdmin">Обслуживание системы</a></li>{/if}
{/capture}
{$meta_title='Импорт товаров' scope=parent}

<script src="{$config->root_url}/simpla/design/js/piecon/piecon.js"></script>
<script>
{if $filename_csv && !$convert_only}
{literal}
	
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
    					//~ $('ul#import_result').prepend('<li><span class=count>'+count+'</span> <span title='+data.items[key].status+' class="status '+data.items[key].status+'"></span> <a target=_blank href="?module=ProductAdmin&id='+data.items[key].product.id+'">'+data.items[key].product.name+'</a> '+data.items[key].variant.name+'</li>');
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
{/literal}
{/if}
</script>

<style>
	.ui-progressbar-value { background-color:#b4defc; background-image: url(design/images/progress.gif); background-position:left; border-color: #009ae2;}
	#progressbar{ clear: both; height:29px;}
	#result{ clear: both; width:100%;}
</style>

{if $message_error}
<!-- Системное сообщение -->
<div class="message message_error">
	<span class="text">
	{if $message_error == 'no_permission'}Установите права на запись в папку {$import_files_dir}
	{elseif $message_error == 'convert_error'}Не получилось сконвертировать файл в кодировку UTF8
	{elseif $message_error == 'locale_error'}На сервере не установлена локаль {$locale}, импорт может работать некорректно
	{else}{$message_error}{/if}
	</span>
</div>
<!-- Системное сообщение (The End)-->
{/if}

{if $message_error != 'no_permission'}
    {if $filename_yml}
	<div class="block layer">
					{if $filename_yml}Имя файла YML: {$filename_yml|escape}{/if}
					{if $filename_yml_size}Размер файла YML: {$filename_yml_size|escape}{/if}
					{if $filename_csv}Имя файла CSV: {$filename_csv|escape}{/if}
					{if $filename_csv_size}Размер файла CSV: {$filename_csv_size|escape}{/if}
	</div>            
            {if $filename_csv}
				<div class="block layer">
					<h1>Импорт {$filename|escape}</h1>
					<div id='progressbar'></div>
					<ul id='import_result'></ul>
				</div>        
            {/if}
            
		{if $yml_params}
		<form method=post enctype="multipart/form-data">
			<input type=hidden name="session_id" value="{$smarty.session.id}">
			<input type=hidden name="start_import_yml" value="1">

            {*блок для отображения полей файла*}
				<div id="list">
					<div class="row">
						<div style="width:50%" class="cell">Название параметра YML</div>
						<div style="width:50%" class="cell">Название параметра Simpla</div>
						<div class="clear"></div>
					</div>
					{foreach $yml_params as $pkey=>$pvalue}
						<div class="row">
							<div style="width:50%" class="cell">{$pkey}</div>
							<div style="width:50%" class="cell">
								<select name="yml_params[{$pkey}]">
									<optgroup label="Добавить новый или пропустить параметр">
										<option value="{$pkey}">Добавить как новый параметр</option>
										<option value="">Не добавлять</option>
									</optgroup>
									<optgroup label="Основные параметры Simpla">
										{foreach $columns as $k=>$f}
										<option value="{$k}" {if $columns_compared[$pkey] == $k} selected{/if}>{$f}</option>
										{/foreach}
									</optgroup>
									<optgroup label="Уже имеющиеся параметры товаров">
										{foreach $features as $f}
										<option value="{$f}" {if $pkey == $f}selected{elseif $pkey == "param_`$f`"}selected{/if}>{$f}</option>
										{/foreach}
									</optgroup>
									</select>
							</div>  
							<div class="clear"></div>
						</div>
					{/foreach}
				</div>
			{if $yml_currencies}
				<div class="block layer">
					<label>Валюты из YML файла (в скобках указан rate валюты)</label>
					<select name="yml_import_currency">
						{foreach $yml_currencies as $k=>$c}
						<option value="{$c['id']}">{$c['id']} ({$c['rate']})</option>
						{/foreach}
					</select>
				</div>
			{/if}
				<div class="block layer">
					<label>Параметры выполнения</label>
					<select name="convert_only">
					<option value="0">Импортировать YML</option>
					<option value="1">Только сконвертировать YML файл в CSV</option>
					</select>
					<button type="submit" class="button_green">Выполнить</button>
				</div>
		</form>
		{/if}
    {/if}
    
	{if !$filename_yml_size}
		<div class="block">
		<h1>Импорт товаров из файла Яндекс Маркета формат YML</h1>
		</div>
		<form method=post id=product enctype="multipart/form-data">
			<input type=hidden name="session_id" value="{$smarty.session.id}">
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
					(максимальный размер файла &mdash; {if $config->max_upload_filesize>1024*1024}{$config->max_upload_filesize/1024/1024|round:'2'} МБ{else}{$config->max_upload_filesize/1024|round:'2'} КБ{/if})
				</p>
			</div>
		</form>

	{elseif $filename_yml_size && !$filename_csv_size && !$yml_params}
		<div class="block layer">
			<div class="row">
                <form method="post" enctype="multipart/form-data">
					<input type=hidden name="session_id" value="{$smarty.session.id}">
					<input type=hidden name="file_fields" value="{$filename_yml}" />
					<button type="submit" class="button_green">Считать названия полей</button>
                </form>
			</div>
		</div>
	{/if}

{/if}

<script>
{literal}
	function colorize()
	{
		$("#list div.row:even").addClass('even');
		$("#list div.row:odd").removeClass('even');
	}
	// Раскрасить строки сразу
	colorize();
{/literal}
</script>
