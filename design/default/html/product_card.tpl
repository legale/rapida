
{$pid = $product['id']}
{$url = $product['trans']}
{$name = $product['name']}
{$image = $product['image']}
{$image_id = $product['image_id']}

	<li class="product">
		
		<!-- Фото товара -->
		{if $image}
		<div class="image">
			<a href="products/{$url}"><img src="{$image|resize:products:$image_id:200:200}" alt="{$name|escape}"/></a>
		</div>
		{/if}
		<!-- Фото товара (The End) -->

		<div class="product_info">
		<!-- Название товара -->
		<h3 class="{if $product['featured']}featured{/if}"><a data-product="{$pid}" href="products/{$url}">{$name|escape}</a></h3>
		<!-- Название товара (The End) -->

		<!-- Описание товара -->
		<div class="annotation">{$product['annotation']}</div>
		<!-- Описание товара (The End) -->
		{*print_r($product)*}
		{if $product['variants']|count > 0}
		<!-- Выбор варианта товара -->
		<form class="variants" action="/cart">
			<table>
			{foreach $product['variants'] as $v}
			<tr class="variant">
				<td>
					<input id="variants_{$v['id']}" name="variant" value="{$v['id']}" type="radio" class="variant_radiobutton" {if $v@first}checked{/if} {if $product['variants']|count<2}style="display:none;"{/if}/>
				</td>
				<td>
					{if $v['name']}<label class="variant_name" for="variants_{$v['id']}">{$v['name']}</label>{/if}
				</td>
				<td>
					{if $v['old_price'] > 0}<span class="old_price">{$v['old_price']|convert}</span>{/if}
					<span class="price">{$v['price']|convert} <span class="currency">{$currency['sign']|escape}</span></span>
				</td>
			</tr>
			{/foreach}
			</table>
			<input type="submit" class="button" value="в корзину" data-result-text="добавлено"/>
		</form>
		<!-- Выбор варианта товара (The End) -->
		{else}
			Нет в наличии
		{/if}

		</div>
		
	</li>
	<!-- Товар (The End)-->
