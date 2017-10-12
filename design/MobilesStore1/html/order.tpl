{$meta_title = "Ваш заказ №`$order->id`" scope=parent}
<div id="page_title">
<p><a href="./">Главная</a> » Заказ №{$order->id}
<h1>Ваш заказ №{$order->id}
	{if $order->status == 0}ПРИНЯТ{/if}
	{if $order->status == 1}в обработке{elseif $order->status == 2}выполнен{/if}
	{if $order->paid == 1}, оплачен{else}{/if}
</h1>
</div>
<br />

<table id="purchases">

	{foreach $purchases as $purchase}
	<tr>
		<td class="image">
		{$image = $purchase->product->images|first}
		{if $image}<a href="products/{$purchase->product->url}"><img src="{$image->filename|resize:50:50}" alt="{$product->name|escape}"></a>{/if}
		</td>
		<td class="name">
		<a href="products/{$purchase->product->url}">{$purchase->product->name|escape}</a> <b>{$purchase->variant->name|escape}</b>
		{if $order->paid && $purchase->variant->attachment}<a class="download_attachment" href="order/{$order->url}/{$purchase->variant->attachment}">скачать файл</a>{/if}
		</td>
		<td class="price" colspan="2">{($purchase->variant->price)|convert} {$currency->sign} &times; {$purchase->amount}&nbsp;{$settings->units}</td>
		<td class="price">{($purchase->price*$purchase->amount)|convert}&nbsp;{$currency->sign}</td>
	</tr>
	{/foreach}

	{if $order->discount > 0}<tr><th class="price" colspan="5" style='color:#9C9C9C'>Cкидка по авторизации {$user->discount}&nbsp;%</th></tr>{/if}
	{if $order->coupon_discount > 0}<tr><th class="price" colspan="5" style='color:#9C9C9C'>Скидка по купону: {$order->coupon_discount|convert}&nbsp;{$currency->sign}</th></tr>{/if}

	{if !$order->separate_delivery && $order->delivery_price>0}
		<tr>
		<td class="name" colspan="2" style='padding:2px;'>{$delivery->name|escape}</td>
		<td class="price" colspan="3" style='padding:2px;'>{$order->delivery_price|convert}&nbsp;{$currency->sign}</td>
		</tr>
	{/if}

	<tr><th class="price" colspan="5" style='font-size:18px;'>Итого: {$order->total_price|convert}&nbsp;{$currency->sign}</th></tr>

	{if $order->separate_delivery}
		<tr>
		<td class="name" colspan="4"><br />Дополнительно необходимо оплатить:<br /><h2 class='color'>{$delivery->name|escape}</h2></td>
		<td class="price" style='font-size:20px;'>{$order->delivery_price|convert}&nbsp;{$currency->sign}</td>
		</tr>
	{/if}

</table>
<div class="clear_dot"></div><br />

<div class='qr_block '>
	<h1>Код быстрого доступа к заказу</h1>
	<a onclick="creambee.preview(this); return false;" class="creambee qr-120-1-c6" href="http://creambee.ru/"><script type="text/javascript" src="http://qr.creambee.ru/widget/loader.js"></script>QR-код страницы Вашего заказа</a>
</div>

<br /><h1>Указанная информация по заказу</h1>
<table class="table_info">
<tr><td class='name'>Дата заказа</td><td>{$order->date|date} в {$order->date|time}</td></tr>
{if $order->name}	<tr><td class='name'>Имя</td><td>{$order->name|escape}</td></tr>{/if}
{if $order->email}	<tr><td class='name'>Email</td><td>{$order->email|escape}</td></tr>{/if}
{if $order->phone}	<tr><td class='name'>Телефон</td><td>{$order->phone|escape}</td></tr>{/if}
{if $order->address}<tr><td class='name'>Адрес доставки</td><td>{$order->address|escape}</td></tr>{/if}
{if $order->comment}<tr><td class='name'>Комментарий</td><td>{$order->comment|escape|nl2br}</td></tr>{/if}
</table>
<div class="clear_dot"></div><br />

{if !$order->paid}
	{if $payment_methods && !$payment_method && $order->total_price>0}
		<form method="post">
			<h1>Выберите способ оплаты</h1>
			<ul id="deliveries">
			{foreach $payment_methods as $payment_method}
			<li>
			<div class="checkbox"><input type=radio name=payment_method_id value='{$payment_method->id}' {if $payment_method@first}checked{/if} id=payment_{$payment_method->id}></div>
			<h3><label for=payment_{$payment_method->id}>	{$payment_method->name}, к оплате {$order->total_price|convert:$payment_method->currency_id}&nbsp;{$all_currencies[$payment_method->currency_id]->sign}</label></h3>
			<div class="description">{$payment_method->description}</div>
			</li>
			{/foreach}
			</ul>
			<input type='submit' class="button right" value='Закончить заказ'>
		</form>

	{* Выбраный способ оплаты *}
	{elseif $payment_method}
		<div id="category_description">
		<h1><form method=post><input class="button right" type=submit name='reset_payment_method' value='Выбрать другой способ оплаты'></form>Вы выбрали способ оплаты<br />'{$payment_method->name}'</h1>
		<p>{$payment_method->description}</p>
		</div>
		<div class='form'><h1 class="color">К оплате {$order->total_price|convert:$payment_method->currency_id}&nbsp;{$all_currencies[$payment_method->currency_id]->sign}</h1>{checkout_form order_id=$order->id module=$payment_method->module}</div>
	{/if}
{/if}