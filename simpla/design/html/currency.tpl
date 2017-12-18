{capture name=tabs}
	{if isset($userperm['settings'])}<li><a href="?module=SettingsAdmin">Настройки</a></li>{/if}
	<li class="active"><a href="?module=CurrencyAdmin">Валюты</a></li>
	{if isset($userperm['delivery'])}<li><a href="?module=DeliveriesAdmin">Доставка</a></li>{/if}
	{if isset($userperm['payment'])}<li><a href="?module=PaymentMethodsAdmin">Оплата</a></li>{/if}
{/capture}

{$meta_title = 'Валюты' scope=parent}

{* On document load *}
{literal}
<script>
$(function() {

	// Сортировка списка
	// Сортировка вариантов
	$("#currencies_block").sortable({ items: 'ul.sortable' , axis: 'y',  cancel: '#header', handle: '.move_zone' });

	// Добавление валюты
	var curr = $('#new_currency').clone(true);
	$('#new_currency').remove().removeAttr('id');
	$('a#add_currency').click(function() {
		$(curr).clone(true).appendTo('#currencies').fadeIn('slow').find("input[name*=currency][name*=name]").focus();
		return false;		
	});	
 

	// Скрыт/Видим
	$("a.enable").click(function() {
		var icon        = $(this);
		var line        = icon.closest("ul");
		var id          = line.find('input[name*="currency[id]"]').val();
		var state       = line.hasClass('invisible')?1:0;
		icon.addClass('loading_icon');
		$.ajax({
			type: 'POST',
			url: 'ajax/update_object.php',
			data: {'object': 'currency', 'id': id, 'values': {'enabled': state}, 'session_id': '{/literal}{$smarty.session.id}{literal}'},
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
		this.closest('ul').remove();
	});
	
	
	//Подтверждение действия
	$("form").submit(function() {
		if( !confirm('Подтвердите действие') )
			return false;
	});

});

</script>
{/literal}

		
	<!-- Заголовок -->
	<div id="header">
		<h1>Валюты</h1>
		<a class="add" id="add_currency" href="#">Добавить</a>
	<!-- Заголовок (The End) -->	
	</div>	


	
 
	<form method=post>
	<input type="hidden" name="session_id" value="{$smarty.session.id}">
	
	
	<!-- Валюты -->
	<div id="currencies_block">
		<ul id="header">
			<li class="move"></li>
			<li class="name">Название валюты</li>	
			<li class="icons"></li>	
			<li class="sign">Знак</li>	
			<li class="iso">Код ISO</li>	
			<li class="rate">Курс</li>	
			<li class="precision">Округление</li>	
		</ul>
		<div id="currencies">
		{foreach $currencies as $c}
		<ul class="sortable {if !$c['enabled']}invisible{/if}">
			<li class="move"><div class="move_zone"></div></li>
			<li class="name">
				<input name="currency[id][{$c['id']}]" type="hidden" 	value="{$c['id']|escape}" /><input name="currency[name][{$c['id']}]" type="text" value="{$c['name']|escape}" />
			</li>
			<li class="icons">
				<a class="enable" href="#" title="Показывать на сайте"></a>
			</li>
			<li class="sign">		
				<input name="currency[sign][{$c['id']}]" type="text" 	value="{$c['sign']|escape}" />
				</li>
			<li class="iso">		
				<input name="currency[code][{$c['id']}]" type="text" 	value="{$c['code']|escape}" />
				</li>
			<li class="rate">
				<input name="currency[rate][{$c['id']}]" type="text" value="{$c['rate']|escape}" />
			</li>
			<li class="precision">
				<input name="currency[cents][{$c['id']}]" type="text" value="{$c['cents']|escape}" />
			</li>
			<li class="icons">
				<a class="delete" href="#" title="Удалить"></a>				
			</li>
		</ul>
		{/foreach}		
		<ul id="new_currency" style='display:none;'>
			<li class="move"><div class="move_zone"></div></li>
			<li class="name"><input name="currency[id][]" type="hidden" value="" /><input name="currency[name][]" type="" value="" /></li>
			<li class="icons">
			</li>
			<li class="sign"><input name="currency[sign][]" type="text" value="" /></li>
			<li class="iso"><input  name="currency[code][]" type="text" value="" /></li>
			<li class="rate">
				<div class=rate><input name="currency[rate][]" type="text" value="1" /> </div>
			</li>
			<li class="precision">
				<input name="currency[cents][]" type="text" value="" />
			</li>
			<li class="icons">
			
			</li>
		</ul>
		</div>

	</div>
	<!-- Валюты (The End)--> 


	<div id="action">

	<input type=hidden name=recalculate value='0'>
	<input type=hidden name=action value=''>
	<input type=hidden name=action_id value=''>
	<input id='apply_action' class="button_green" type=submit value="Применить">

	
	</div>
	</form>
	
 
