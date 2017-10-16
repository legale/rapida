<div id="page_title"><p><a href="./">Главная</a> » Данные пользователя</p><h1>{$user->name|escape}</h1></div>

{if $error}
	<div class="message_error">
	{if $error == 'empty_name'}Введите имя
	{elseif $error == 'empty_email'}Введите email
	{elseif $error == 'empty_password'}Введите пароль
	{elseif $error == 'user_exists'}Пользователь с таким email уже зарегистрирован
	{else}{$error}{/if}
	</div>
{/if}
<form class="form" method="post">
	<label>Имя</label>
	<input data-format=".+" data-notice="Введите имя" value="{$name|escape}" name="name" maxlength="255" type="text"/>
	<label>Email</label>
	<input data-format="email" data-notice="Введите email" value="{$email|escape}" name="email" maxlength="255" type="text"/>
	<label><a href='#' onclick="$('#password').show();return false;">Изменить пароль</a></label>
	<input id="password" value="" name="password" type="password" style="display:none;"/>
	<input type="submit" class="button right" value="Сохранить">
</form>

{if $orders}
<h1>Ваши заказы в нашем каталоге</h1>
<table class="table_info">
	{foreach name=orders item=order from=$orders}
	<tr>
	<td class='name'><a href='order/{$order->url}'>Заказ №{$order->id} от {$order->date|date}</a></td>
	<td>
	{if $order->paid == 1}<b>оплачен,</b>{/if}
	{if $order->status == 0}ждет обработки{elseif $order->status == 1}в обработке{elseif $order->status == 2}<b>выполнен</b>{/if}
	</td>
	</tr>
	{/foreach}
</table>
{/if}