{* Список товаров *}

{* Канонический адрес страницы *}
{if isset($category, $brand) && $category && $brand}
{$condition = 1}
{$canonical="/catalog/{$category['trans']}/{$brand['trans']}" scope=root}
{elseif isset($category) && $category}
{$condition = 2}
{$canonical="/catalog/{$category['trans']}" scope=root}
{elseif isset($brand) && $brand}
{$condition = 3}
{$canonical="/brands/{$brand['trans']}" scope=root}
{elseif isset($keyword) && $keyword}
{$condition = 4}
{$canonical="/products?keyword={$keyword|escape}" scope=root}
{else}
{$condition = 5}
{$canonical="/products" scope=root}
{/if}

<!-- Хлебные крошки /-->
<div id="path">
	<a href="/">Главная</a>
	{if isset($category) && $category}
	{foreach $category['path'] as $cat}
	→ <a href="catalog/{$cat['trans']}">{$cat['name']|escape}</a>
	{/foreach}  
	{if isset($brand) && $brand}
	→ <a href="catalog/{$cat['trans']}/{$brand['trans']}">{$brand['name']|escape}</a>
	{/if}
	{elseif isset($brand) && $brand}
	→ <a href="brands/{$brand['trans']}">{$brand['name']|escape}</a>
	{elseif isset($keyword) && $keyword}
	→ Поиск
	{/if}
</div>
<!-- Хлебные крошки #End /-->

{* Заголовок страницы *}
{if isset($keyword) && $keyword}
<h1>Поиск {$keyword|escape}</h1>
{elseif isset($page) && $page}
<h1>{$page['name']|escape}</h1>
{else}
<h1>{$category['name']|escape} {$brand['name']|escape}</h1>
{/if}


{* Описание страницы (если задана) *}
{$page['body']}

{if $current_page_num==1}
{* Описание категории *}
{$category['description']}
{/if}
{* Фильтр по брендам *}
{if isset($category['brands']) && $category['brands']}

<div id="brands">
	<a href="{chpu_url params=[brand=>[], page=> null ]}" {if !$brand['id']}class="selected"{/if}>Все бренды</a>
	{foreach $category['brands'] as $b}
		{if $b['image']}
		<a data-brand="{$b['id']}" href="{chpu_url params=['page' => null, 'brand'=>$b['trans']]}"><img src="" alt="{$b['name']|escape}"></a>
		{else}
		<a data-brand="{$b['id']}" href="{chpu_url params=['page' => null, 'brand'=>$b['trans']]}" {if $b['id'] == $brand['id']}class="selected"{/if}>{$b['name']|escape}</a>
		{/if}
	{/foreach}
</div>
{/if}

{if $current_page_num==1}
{* Описание бренда *}
{$brand['description']}
{/if}

{* Фильтр по свойствам *}
{if $features}
<table id="features">
	{*$filter['features']|@debug_print_var*}
	{foreach $features as $fid=>$f}
	<tr>
	<td class="feature_name" data-feature="{$f['id']}">
		{$f['name']}:
	</td>
	<td class="feature_values">
		<a href="{chpu_url params=[filter=>[$f['trans']=>[]], page=>'' ]}">Все</a>
		{foreach $options['full'][$f['id']]['vals'] as $k=>$o}
			{$otrans=$options['full'][$f['id']]['trans'][$k]}
			{if isset($options['filter'][$f['id']][$k])}
			<a {if isset($filter['features'][$f['id']][$k])}class="selected" {/if}
			href="{chpu_url params=[features=>[$f['trans']=>[$otrans]], page=>null ]}">{$o|escape}</a>
			{else}
			{$o|escape}
			{/if}
		{/foreach}
	</td>
	</tr>
	{/foreach}
</table>
{/if}


<!--Каталог товаров-->
{if $products}

{* Сортировка *}
{if $products|count>0}
<div class="sort">
	Сортировать по 
	<a {if $sort=='pos'} class="selected"{/if} href="{url sort=pos page=null}">умолчанию</a>
	<a {if $sort=='price'}    class="selected"{/if} href="{url sort=price page=null}">цене</a>
	<a {if $sort=='name'}     class="selected"{/if} href="{url sort=name page=null}">названию</a>
</div>
{/if}


{include file='pagination.tpl'}


<!-- Список товаров-->
<ul class="products">

	{foreach $products as $product}
	{include file="product_card.tpl"}
	{/foreach}
			
</ul>

{include file='pagination.tpl'}	
<!-- Список товаров (The End)-->

{else}
Товары не найдены
{/if}
<!--Каталог товаров (The End)-->
