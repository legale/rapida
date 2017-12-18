{* Вкладки *}
{capture name=tabs}
	{if isset($userperm['settings'])}<li><a href="?module=SettingsAdmin">Настройки</a></li>{/if}
	{if isset($userperm['currency'])}<li><a href="?module=CurrencyAdmin">Валюты</a></li>{/if}
	<li class="active"><a href="?module=DeliveriesAdmin">Доставка</a></li>
	{if isset($userperm['payment'])}<li><a href="?module=PaymentMethodsAdmin">Оплата</a></li>{/if}
{/capture}

{if $delivery['id']}
{$meta_title = $delivery['name'] scope=parent}
{else}
{$meta_title = 'Новый способ доставки' scope=parent}
{/if}

{* Подключаем Tiny MCE *}
{include file='tinymce_init.tpl'}


{if $message_success}
<!-- Системное сообщение -->
<div class="message message_success">
	<span class="text">{if $message_success == 'added'}Способ доставки добавлен{elseif $message_success == 'updated'}Способ доставки изменен{/if}</span>
	{if $smarty.get.return}
	<a class="button" href="{$smarty.get.return}">Вернуться</a>
	{/if}
</div>
<!-- Системное сообщение (The End)-->
{/if}

{if $message_error}
<!-- Системное сообщение -->
<div class="message message_error">
	<span class="text">{if $message_error == 'empty_name'}Не указано название доставки{/if}</span>
	<a class="button" href="">Вернуться</a>
</div>
<!-- Системное сообщение (The End)-->
{/if}


<!-- Основная форма -->
<form method=post id=product enctype="multipart/form-data">
<input type=hidden name="session_id" value="{$smarty.session.id}">
	<div id="name">
		<input class="name" name=name type="text" value="{$delivery['name']|escape}"/> 
		<input name=id type="hidden" value="{$delivery['id']}"/> 
		<div class="checkbox">
			<input name=enabled value='1' type="checkbox" id="active_checkbox" {if $delivery['enabled']}checked{/if}/> <label for="active_checkbox">Активен</label>
		</div>
	</div> 

	<!-- Левая колонка свойств товара -->
	<div class="column_left">
		<!-- Параметры страницы -->
		<div class="block layer">
			<h2>Стоимость доставки</h2>
			<ul>
				<li style="width: 100%" ><label style="width: 100%" class=property>Стоимость, {$currency['sign']}:</label>
					<input style="width: 100%" name="price" type="text" value="{$delivery['price']}" />
				</li>
				<li style="width: 100%" ><label style="width: 100%" class=property>Бесплатна от, {$currency['sign']}:</label>
					<input style="width: 100%" name="free_from" type="text" value="{$delivery['free_from']}" />
				</li>
				<li style="width: 100%" ><label class=property for="separate_payment">Оплачивается отдельно</label>
					<input id="separate_payment" name="separate_payment" type="checkbox" value="1" {if $delivery['separate_payment']}checked{/if} />
				</li>
			</ul>
		</div>
		<!-- Параметры страницы (The End)-->

	</div>
	<!-- Левая колонка свойств товара (The End)--> 
	
	<!-- Левая колонка свойств товара -->
	<div class="column_right">
		<div class="block layer">
		<h2>Возможные способы оплаты</h2>
		<ul>
		{foreach $payment_methods as $pm}
			<li>
			<input type=checkbox name="delivery_payments[]" id="payment_{$pm['id']}" value='{$pm['id']}' 
			{if in_array($pm['id'], $delivery_payments)}checked{/if}> 
			<label for="payment_{$pm['id']}">{$pm['name']}</label><br>
			</li>
		{/foreach}
		</ul>		
		</div>
	</div>
	<!-- Левая колонка свойств товара (The End)--> 
	
	<!-- Описагние товара -->
	<div class="block layer">
		<h2>Описание</h2>
		<textarea name="description" class="editor_small">{$delivery['description']|escape}</textarea>
	</div>
	<!-- Описание товара (The End)-->
	<input class="button_green button_save" type="submit" name="" value="Сохранить" />
	
</form>
<!-- Основная форма (The End) -->

