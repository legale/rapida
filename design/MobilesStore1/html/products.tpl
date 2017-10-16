<div id="page_title">
	<p><a href="./">Главная</a>
	{if $category}
		{foreach from=$category->path item=cat} » <a class='link_2' href="catalog/{$cat->url}">{$cat->name|escape}</a>{/foreach}
		{if $brand}» <a class='link_2' href="catalog/{$cat->url}/{$brand->url}">{$brand->name|escape}</a>{/if}
	{elseif $brand}» <a class='link_2' href="brands/{$brand->url}">{$brand->name|escape}</a>
	{elseif $keyword}» Поиск
	{/if}
	</p>	
	<h1>
	{if $keyword}Поиск {$keyword|escape}
	{elseif $page}{$page->name|escape}
	{else}{$category->name|escape} {$brand->name|escape} {$keyword|escape}
	{/if}
	</h1>	
</div>

{if $current_page_num==1 && $category->description}<div id="category_description">{if $page->body}<p>{$page->body}</p>{/if}{$category->description}</div>{/if}
{if $brand->description && $current_page_num==1}<div id="category_description"><h2>{$brand->name}</h2>{$brand->description}</div>{/if}
{if $features || $category->brands}
	<div id="features">
	<ul>
	{if $category->brands}
		<li>
		<p class="name">Бренды</p>
		<p class="values">
		<a href="catalog/{$category->url}" class='hover_mouse{if !$brand->id} selected{/if}'>Все</a>
		{foreach name=brands item=b from=$category->brands}
		<a href="catalog/{$category->url}/{$b->url}" class='hover_mouse{if $b->id == $brand->id} selected{/if}' data-brand="{$b->id}">{$b->name|escape}</a>
		{/foreach}
		</p>
		</li>
	{/if}

	{if $features}
		{foreach $features as $f}
		<li>
		<p class="name" data-feature="{$f->id}">{$f->name}:</p>
		<p class="values">
		<a href="{url params=[$f->id=>null, page=>null]}" class='hover_mouse{if !$smarty.get.$f@key} selected{/if}'>Все</a>
		{foreach $f->options as $o}<a href="{url params=[$f->id=>$o->value, page=>null]}" class='hover_mouse{if $smarty.get.$f@key == $o->value} selected{/if}'>{$o->value|escape}</a>{/foreach}
		</p>
		</li>
		{/foreach}
	{/if}
	</ul>
	</div>
{/if}

{if $products}
	{include file='pagination.tpl'}
	<ul class="tiny_products">
	{foreach $products as $product}
	<li class="product">{include file='tpl_products_blocks.tpl'}</li>
	{/foreach}
	</ul>
	{include file='pagination.tpl'}
{else}<h4 style='padding:50px 0;'>Сейчас здесь нет предложений<br />Попробуйте зайти позже</h4>{/if}

{literal}
<script>
$(function() {
	// Раскраска строк характеристик
	$("#features li:even").addClass('even');
	// Выбор вариантов
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