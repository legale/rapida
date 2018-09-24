

{if $feature['id']}
{$meta_title = $feature['name']|escape scope=root}
{else}
{$meta_title = 'Новое свойство' scope=root}
{/if}

{* On document load *}
{literal}
<script>
$(function() {

 
});
</script>
{/literal}

{if isset($message_success)}
<!-- Системное сообщение -->
<div class="message message_success">
	<span class="text">{if $message_success=='added'}Свойство добавлено{elseif $message_success=='updated'}Свойство обновлено{else}{$message_success}{/if}</span>
	{if $smarty.get.return}
	<a class="button" href="{$smarty.get.return}">Вернуться</a>
	{/if}
</div>
<!-- Системное сообщение (The End)-->
{/if}

{if isset($message_error)}
<!-- Системное сообщение -->
<div class="message message_error">
	<span class="text">{$message_error}</span>
	<a class="button" href="">Вернуться</a>
</div>
<!-- Системное сообщение (The End)-->
{/if}

<!-- Основная форма -->
<form method=post class=product>

	<div id="name">
		<input class="name" name="name" type="text" value="{$feature['name']|escape}"/> 
		<input name="id" type="hidden" value="{$feature['id']|escape}"/> 
	</div> 

	<!-- Левая колонка свойств товара -->
	<div class="column_left">
		
		<!-- Группа свойств -->	
		<div class="block">
		<h2>Группа свойств</h2>
		<ul>
			<li container="true">
				<select name="gid">
						<option value="{$o['id']}" {if $feature['gid'] == 0}selected{/if}>Без группы</option>
					{foreach $ogroups as $o}
						<option value="{$o['id']}" {if $o['id'] == $feature['gid']}selected{/if}>{$o['name']|escape}</option>
					{/foreach}
				</select>
			</li>
		</ul>
		</div>
		<!-- Группа свойств (END) -->

		<!-- Категории -->
		<div class="block">
			<h2>Использовать в категориях</h2>
				<select class=multiple_categories multiple name="feature_categories[]">
					{function name=category_select selected_id=$product_category level=0}
					{foreach $categories as $category}
							<option value='{$category['id']}' {if in_array($category['id'], $feature_categories)}selected{/if} category_name="{$category['single_name']}">{section name=sp loop=$level}&nbsp;&nbsp;&nbsp;&nbsp;{/section}{$category['name']}</option>
							{category_select categories=$category['subcategories'] selected_id=$selected_id  level=$level+1}
					{/foreach}
					{/function}
					{category_select categories=$categories}
				</select>
		</div>
 
	</div>
	<!-- Левая колонка свойств товара (The End)--> 
	
	<!-- Правая колонка свойств товара -->	
	<div class="column_right">
		
		<!-- Параметры страницы -->
		<div class="block">
			<h2>Настройки свойства</h2>
			<ul>
				<li><input type="text" name="trans" value="{$feature['trans']|escape}"><label>translit для адресной строки</label></li>
				<li><input type="checkbox" name="in_filter" {if $feature['in_filter']}checked{/if} value="1"><label>Использовать в фильтре</label></li>
				<li><input type="checkbox" name="tpl" {if $feature['tpl']}checked{/if} value="1"><label>Разрешить переменные &#123;${$feature['trans']}&#125; &#123;${$feature['trans']}_list&#125; для мета тегов</label></li>
				<li><input type="checkbox" name="visible" {if $feature['visible']}checked{/if} value="1"><label>Видимый</label></li>
				<li><input type="checkbox" name="isrange" {if $feature['isrange']}checked{/if} value="1"><label>диапазонный ползунок</label></li>
			</ul>
		</div>
		<!-- Параметры страницы (The End)-->
		<input type=hidden name='session_id' value='{$smarty.session.id}'>
		<input class="button_green" type="submit" name="" value="Сохранить" />
		
	</div>
	<!-- Правая колонка свойств товара (The End)--> 
	

</form>
<!-- Основная форма (The End) -->

