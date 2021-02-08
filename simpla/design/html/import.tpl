{* Вкладки *}
{capture name=tabs}
	{if isset($userperm['import'])}<li class="active"><a href="?module=ImportAdmin">Импорт</a></li>{/if}
	{if isset($userperm['import'])}<li><a href="?module=ImportYmlAdmin">Импорт YML</a></li>{/if}
	{if isset($userperm['import'])}<li><a href="?module=ImportSingleAdmin">Импорт отдельных параметров</a></li>{/if}
	{if isset($userperm['export'])}<li><a href="?module=ExportAdmin">Экспорт</a></li>{/if}
	{if isset($userperm['backup'])}<li><a href="?module=BackupAdmin">Бекап</a></li>{/if}
	{if isset($userperm['export'])}<li><a href="?module=SystemAdmin">Обслуживание системы</a></li>{/if}
{/capture}
{$meta_title='Импорт товаров' scope=root}

<script src="{$config->root_url}/simpla/design/js/piecon/piecon.js"></script>
<script>
{if $filename}
{literal}
	
	var in_process=false;
	var count=1;

	// On document load
	$(function(){
 		Piecon.setOptions({fallback: false});
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

    				Piecon.setProgress(Math.round(100*data.from/data.total_size * 100) / 100);
   					$("#progressbar").progressbar({ value: 100*data.from/data.total_size });
   					$("ul#import_result").text('progress: ' + Math.round(data.from / 1024) + ' of ' + Math.round(data.total_size / 1024) + ' kb');
  				
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
	.ui-progressbar-value { background-color:#b4defc;
	background-image: url(design/images/progress.gif); 
	background-position:left; border-color: #009ae2;}
	#progressbar{ clear: both; height:29px;}
	#result{ clear: both; width:100%;}
</style>

{if isset($message_error)}
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

	
	{if !empty($filename)}
	<div>
		<h1>Импорт {$filename|escape}</h1>
	</div>
	<div id='progressbar'></div>
	<ul id='import_result'></ul>
	{else}
	
		<h1>Импорт товаров</h1>

		<div class="block">	
		<form method=post class=product enctype="multipart/form-data">
			<input type=hidden name="session_id" value="{$smarty.session.id}">
			<input name="file" class="import_file" type="file" value="" />
			<input class="button_green" type="submit" name="" value="Загрузить" />
			<p>
				(максимальный размер файла &mdash; {$config->max_upload_filesize()})
			</p>

			
		</form>
		</div>		
	
		<div class="block block_help">
		<p> 
			Создайте бекап на случай неудачного импорта. 
		</p>
		<p>
			Сохраните таблицу в формате CSV. (Теперь поддерживается сжатие GZIP!)
		</p>
		<p>
			В первой строке таблицы должны быть указаны названия колонок в таком формате:
	
			<ul>
				<li><label>Товар</label> название товара</li>
				<li><label>Категория</label> категория товара</li>
				<li><label>Бренд</label> бренд товара</li>
				<li><label>Вариант</label> название варианта</li>
				<li><label>Цена</label> цена товара</li>
				<li><label>Старая цена</label> старая цена товара</li>
				<li><label>Склад</label> количество товара на складе</li>
				<li><label>Артикул</label> артикул товара</li>
				<li><label>Видим</label> отображение товара на сайте (0 или 1)</li>
				<li><label>Рекомендуемый</label> является ли товар рекомендуемым (0 или 1)</li>
				<li><label>Аннотация</label> краткое описание товара</li>
				<li><label>Адрес</label> адрес страницы товара</li>
				<li><label>Описание</label> полное описание товара</li>
				<li><label>Изображения</label> имена локальных файлов или url изображений в интернете, через запятую</li>
				<li><label>Заголовок страницы</label> заголовок страницы товара (Meta title)</li>
				<li><label>Ключевые слова</label> ключевые слова (Meta keywords)</li>
				<li><label>Описание страницы</label> описание страницы товара (Meta description)</li>
			</ul>
		</p>
		<p>
			Любое другое название колонки трактуется как название свойства товара
		</p>
		<p>
			<a href='files/import/example.csv'>Скачать пример файла</a>
		</p>
		</div>		
	
	{/if}

