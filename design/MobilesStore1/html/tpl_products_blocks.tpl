<div class="image">
<a href="products/{$product->url}" title='Просмотреть предложение {$product->name|escape}'><img src="{if $product->image}{$product->image->filename|resize:140:150}{else}design/{$settings->theme}/images/bg/nofoto.png{/if}" alt="{$product->name|escape}"/></a>
</div>

{if $product->featured}<div class="label label_featured" title='Рекомендуемый товар (Лидер продаж)'></div>
{elseif $product->variant->compare_price > 0}	<div class="label label_sale" title='Предложение со скидкой'></div>
{/if}

<div class="product_info">

	<h3><a data-product="{$product->id}" href="products/{$product->url}">{$product->name|escape}</a></h3>
	{if $product->annotation}<div class="annotation">{$product->annotation}</div>{/if}
	{if $product->variants|count > 0}
		<form class="cart" action="/cart">

			<div class="price">
			<strike class='compare_price right'>{if $product->variant->compare_price > 0}{$product->variant->compare_price|convert}{else}{/if}</strike>
			{if $product->variant->price >0}<span>{$product->variant->price|convert}</span><i>{$currency->sign|escape}</i>{else}<small class='right' title='Не назначена цена'><br />Под заказ</small>{/if}
			</div>

			<a class='but_add more hover_mouse' href="products/{$product->url}"></a>
			{if $product->variant->price >0}<input type="submit" class="but_add to_cart" value="" title='Купить {$product->name|escape}' data-result-text=""/>{/if}

			<select name="variant" {if $product->variants|count==1  && !$product->variant->name}style='display:none;'{/if}>
			{foreach $product->variants as $v}
			{if $v->price > 0}<option value="{$v->id}" {if $v->compare_price > 0}compare_price="{$v->compare_price|convert}"{/if} price="{$v->price|convert}">{$v->name}</option>{/if}
			{/foreach}
			</select>
		</form>
	{else}
		<div class="price"><small class='right' title='Нет на складе (Остаток ноль)'><br />Нет на складе</small></div>
		<a class='but_add more hover_mouse' href="products/{$product->url}"></a>
	{/if}
</div>