{* Вкладки *}
{capture name=tabs}
	{if in_array('import', $manager['permissions'])}<li><a href="?module=ImportAdmin">Импорт</a></li>{/if}
	{if in_array('import', $manager['permissions'])}<li><a href="?module=ImportYmlAdmin">Импорт YML</a></li>{/if}
	{if in_array('export', $manager['permissions'])}<li><a href="?module=ExportAdmin">Экспорт</a></li>{/if}
	{if in_array('export', $manager['permissions'])}<li><a href="?module=BackupAdmin">Бекап</a></li>{/if}
	<li class="active"><a href="?module=SystemAdmin">Обслуживание системы</a></li>
{/capture}

{* Title *}
{$meta_title = {$menu->name} scope=parent}

{* Заголовок *}
<div id="header">
	<h1>Обслуживание системы</h1>
</div>

<div id="main_list">
 
	<form id="list_form" method="post">
		<input type="hidden" name="session_id" value="{$smarty.session.id}">
		<div id="list">		
			<div class="row">
				<div class="name cell">
					Очистить таблицу значений опций от неиспользуемых в товарах значений.
				</div>
				<div class="icons cell">
				<button class="button" type="submit" name="action" value="clear_options">Выполнить</button>
				</div>
				<div class="clear"></div>
			</div>

			<div class="row">
				<div class="name cell">
					Загрузить и сохранить в системе все изображения по ссылкам на внешние источники (может выполнятся долго)
				</div>
				<div class="icons cell">
				<button class="button" type="submit" name="action" value="download_all_images">Выполнить</button>
				</div>
				<div class="clear"></div>
			</div>
		</div>

	</form>	
</div>

{* On document load *}
{literal}
<script>
$(function() {

 
	// Раскраска строк
	function colorize()
	{
		$(".row:even").addClass('even');
		$(".row:odd").removeClass('even');
	}
	// Раскрасить строки сразу
	colorize();


	$("form").submit(function() {
		if($('select[name="action"]') && !confirm('Подтвердите выполнение'))
			return false;	
	});
});
</script>
{/literal}
