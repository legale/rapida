{* Вкладки *}
{capture name=tabs}
	{if isset($userperm['features'])}
		<li {if $ogroup['id'] == 0}class="active"{/if}><a href="{url module=Options_groupsAdmin gid=0}">Без группы</a></li>
	{foreach $ogroups as $g}
		<li {if $g['id'] == $ogroup['id']}class="active"{/if}><a href="{url module=Options_groupsAdmin gid=$g['id']}">{$g['name']}</a></li>
	{/foreach}
	{/if}
{/capture}

{* Title *}
{$meta_title = {$ogroup['name']} scope=parent}

{* Заголовок *}
<div id="header">
	<h1>{$ogroup['name']}</h1>
	<a class="add" href="{url module=FeatureAdmin return=$smarty.server.REQUEST_URI}">Добавить свойство</a>
</div>

{if !empty($opts)}
<div id="main_list">
	<form id="list_form" method="post">
		<input type="hidden" name="session_id" value="{$smarty.session.id}">
		<div id="list">
			{foreach $opts as $o}
			<div class="{if $o['in_filter']}in_filter{/if} row">
				<input type="hidden" name="positions[]" value="{$o['id']}">
				<div class="move cell"><div class="move_zone"></div></div>
		 		<div class="checkbox cell">
					<input type="checkbox" name="check[]" value="{$o['id']}" />
				</div>
				<div class="cell">
					<a href="{url module=FeatureAdmin id=$o['id'] return=$smarty.server.REQUEST_URI}">{$o['name']|escape}</a>
				</div>
				<div class="icons cell">
					<a title="Использовать в фильтре" class="in_filter" href='#' ></a>
					<a title="Удалить" class="delete" href='#' ></a>
				</div>
				<div class="clear"></div>
			</div>
			{/foreach}
		</div>
	
		<div id="action">
			<label id="check_all" class="dash_link">Выбрать все</label>
		
			<span id="select">
			<select name="action">
				<option value="set_in_filter">Использовать в фильтре</option>
				<option value="unset_in_filter">Не использовать в фильтре</option>
				<option value="delete">Удалить</option>
			</select>
			</span>
		
			<input id="apply_action" class="button_green" type="submit" value="Применить">
		</div>
	</form>	
</div>
{else}
	Нет страниц
{/if}

{* On document load *}
{literal}
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
	
	// Сортировка списка
	$("#list").sortable({
		items:             ".row",
		tolerance:         "pointer",
		handle:            ".move_zone",
		axis: 'y',
		scrollSensitivity: 40,
		opacity:           0.7, 
		forcePlaceholderSize: true,
		
		helper: function(event, ui){		
			if($('input[type="checkbox"][name*="check"]:checked').size()<1) return ui;
			var helper = $('<div/>');
			$('input[type="checkbox"][name*="check"]:checked').each(function(){
				var item = $(this).closest('.row');
				helper.height(helper.height()+item.innerHeight());
				if(item[0]!=ui[0]) {
					helper.append(item.clone());
					$(this).closest('.row').remove();
				}
				else {
					helper.append(ui.clone());
					item.find('input[type="checkbox"][name*="check"]').attr('checked', false);
				}
			});
			return helper;			
		},	
 		start: function(event, ui) {
  			if(ui.helper.children('.row').size()>0)
				$('.ui-sortable-placeholder').height(ui.helper.height());
		},
		beforeStop:function(event, ui){
			if(ui.helper.children('.row').size()>0){
				ui.helper.children('.row').each(function(){
					$(this).insertBefore(ui.item);
				});
				ui.item.remove();
			}
		},
		update:function(event, ui)
		{
			$("#list_form input[name*='check']").attr('checked', false);
			$("#list_form").ajaxSubmit(function() {
				colorize();
			});
		}
	});
	
	// Выделить все
	$("#check_all").click(function() {
		$('#list input[type="checkbox"][name*="check"]').attr('checked', $('#list input[type="checkbox"][name*="check"]:not(:checked)').length>0);
	});	
	
	// Указать "в фильтре"/"не в фильтре"
	$("a.in_filter").click(function() {
		var icon        = $(this);
		var line        = icon.closest(".row");
		var id          = line.find('input[type="checkbox"][name*="check"]').val();
		var state       = line.hasClass('in_filter')?0:1;
		icon.addClass('loading_icon');
		$.ajax({
			type: 'POST',
			url: 'ajax/update_object.php',
			data: {'object': 'feature', 'id': id, 'values': { 'in_filter': state}, 'session_id': '{/literal}{$smarty.session.id}{literal}'},
			success: function(data){
				icon.removeClass('loading_icon');
				if(!state)
					line.removeClass('in_filter');
				else
					line.addClass('in_filter');				
			},
			dataType: 'json'
		});	
		return false;	
	});

	// Удалить
	$("a.delete").click(function() {
		$('#list input[type="checkbox"][name*="check"]').attr('checked', false);
		$(this).closest("div.row").find('input[type="checkbox"][name*="check"]').attr('checked', true);
		$(this).closest("form").find('select[name="action"] option[value=delete]').attr('selected', true);
		$(this).closest("form").submit();
	});
	
	// Подтверждение удаления
	$("form").submit(function() {
		if($('#list input[type="checkbox"][name*="check"]:checked').length>0)
			if($('select[name="action"]').val()=='delete' && !confirm('Подтвердите удаление'))
				return false;	
	});
	
});
</script>
{/literal}
