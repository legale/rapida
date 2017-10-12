{$name_title = "{$product->name|escape} - Комментарии"}
<div id="page_title">
	<p>
	<a href="./">Главная</a>
	{foreach from=$category->path item=cat}» <a class='link_2' href="catalog/{$cat->url}">{$cat->name|escape}</a>{/foreach}
	{if $brand}» <a class='link_2' href="catalog/{$cat->url}/{$brand->url}">{$brand->name|escape}</a>{/if}
	</p>
	<h1 data-product="{$product->id}">{$product->name|escape}</h1>
</div>

<div class="product page">
	{if $product->featured}<div class="label label_featured" title='Рекомендуемый товар (Лидер продаж)'></div>
	{elseif $product->variant->compare_price > 0}<div class="label label_sale" title='Предложение со скидкой'></div>{/if}

	<div class="image">
	<a href="{$product->image->filename|resize:800:600:w}" class="zoom" data-rel="group"><img src="{if $product->image}{$product->image->filename|resize:220:220}{else}design/{$settings->theme}/images/bg/nofoto.png{/if}" alt="{$product->product->name|escape}" /></a>
	</div>

	<div class="product_info">

		{if $product->images|count>1}
		{foreach $product->images|cut as $i=>$image}
		<div class="images"><a href="{$image->filename|resize:800:600:w}" class="zoom" data-rel="group"><img class='hover_mouse' src="{$image->filename|resize:50:50}" alt="{$product->name|escape}" /></a></div>
		{/foreach}
		<div class="clear"></div>
		{/if}

		{if $product->variants|count > 0}
			<form class="cart" action="/cart">
				<div class="price">
				<strike class='compare_price'>{if $product->variant->compare_price > 0}{$product->variant->compare_price|convert}{else}{/if}</strike>
				{if $product->variant->price >0}<span class='color'>{$product->variant->price|convert}</span><i class='color'>{$currency->sign|escape}</i>{else}<i style='margin:0;font-size:19px;' title='Не назначена цена'>Под заказ</i>{/if}
				</div>

				<select name="variant" {if $product->variants|count==1  && !$product->variant->name}style='display:none;'{/if}>
				{foreach $product->variants as $v}
				{if $v->price > 0}<option value="{$v->id}" {if $v->compare_price > 0}compare_price="{$v->compare_price|convert}"{/if} price="{$v->price|convert}">{$v->name}</option>{/if}
				{/foreach}
				</select>

				{if $product->variant->price >0}<input type="submit" class="but_add to_cart" value="" data-result-text=""/>{/if}
			</form>
		{else}
		<div class="price"><i style='margin:0;' title='Нет на складе'><br />Нет в наличии</i></div>
		{/if}
	</div>

	<div class="clear"></div>
	{if $product->body}<div id="page_title"><h1>{$product->name|escape} - Информация</h1></div><div id="category_description">{$product->body}</div>{/if}
	{if $product->features}
		<div id="page_title"><h1>{$product->name|escape} - Характеристики</h1></div>
		<div id="features">
		<ul>
		{foreach $product->features as $f}
		<li><p class="name">{$f->name}:</p><p class="values">{$f->value}</p></li>
		{/foreach}
		<ul>
		</div>
	{/if}
</div>

<div id="back_forward">
{if $prev_product}<a class="prev_page_link hover_mouse link_2" href="products/{$prev_product->url}">{$prev_product->name|escape}</a>{/if}
{if $next_product}<a class="next_page_link hover_mouse link_2" href="products/{$next_product->url}">{$next_product->name|escape}</a>{/if}
</div>

{if $related_products}
	<div id="page_title"><h1>С {$product->name|escape} также смотрят</h1></div>
	<ul class="tiny_products main">
	{foreach $related_products as $product}
	<li class="product">{include file='tpl_products_blocks.tpl'}</li>
	{/foreach}
	</ul>
{/if}
<br />
{include file='tpl_comments.tpl'}

{literal}
<script>
$(function() {
	$("#features li:even").addClass('even');

	$("a.zoom").fancybox({ 'hideOnContentClick' : true });

	$('select[name=variant]').change(function() {
		price = $(this).find('option:selected').attr('price');
		compare_price = '';
		if(typeof $(this).find('option:selected').attr('compare_price') == 'string')
			compare_price = $(this).find('option:selected').attr('compare_price');
		$(this).find('option:selected').attr('compare_price');
		$(this).closest('form').find('span').html(price);
		$(this).closest('form').find('strike').html(compare_price);
		return false;
	});
});
</script>
{/literal}