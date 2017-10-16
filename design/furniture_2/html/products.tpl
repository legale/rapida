{* Список товаров *}
<div class="breadcrumbs">
    <ul>
	 <li class="home"><a href="/">Главная</a></li>
	{if $category}
	{foreach from=$category->path item=cat}
	<li><span>&gt;</span> <a href="catalog/{$cat->url}">{$cat->name|escape}</a></li>
	{/foreach}  
	{if $brand}
	<li><span>&gt;</span> <a href="catalog/{$cat->url}/{$brand->url}">{$brand->name|escape}</a></li>
	{/if}
	{elseif $brand}
	<li><span>&gt;</span> <a href="brands/{$brand->url}">{$brand->name|escape}</a></li>
	{elseif $keyword}
	<li><span>&gt;</span> Поиск</li>
	{/if}
                                    
            </ul>	
</div>
<!-- Хлебные крошки #End /-->

<div class="page-title category-title">
{* Заголовок страницы *}
{if $keyword}
<h1>Поиск {$keyword|escape}</h1>
{elseif $page}
<h1>{$page->name|escape}</h1>
{else}
<h1>{$category->name|escape} {$brand->name|escape} {$keyword|escape}</h1>
{/if}
</div>

{* Описание страницы (если задана) *}
{$page->body}

{if $current_page_num==1}
{* Описание категории *}
{$category->description}
{/if}

{* Фильтр по брендам *}
{if $category->brands}
<div id="brands" class="breadcrumbs">
	<a href="catalog/{$category->url}" {if !$brand->id}class="selected"{/if}>Все бренды</a>
	{foreach name=brands item=b from=$category->brands}
		{if $b->image}
		<img src="{$config->brands_images_dir}{$b->image}" alt="{$b->name|escape}">
		{/if}
		<a data-brand="{$b->id}" href="catalog/{$category->url}/{$b->url}" {if $b->id == $brand->id}class="selected"{/if}>{$b->name|escape}</a>
	{/foreach}
</div>
{/if}

{* Описание бренда *}
{$brand->description}

{* Фильтр по свойствам *}
{if $features}
<table id="features" class="breadcrumbs">
	{foreach $features as $f}
	<tr>
	<td class="feature_name" data-feature="{$f->id}">
		{$f->name}:
	</td>
	<td class="feature_values">
		<a href="{url params=[$f->id=>null, page=>null]}" {if !$smarty.get.$f@key}class="selected"{/if}>Все</a>
		{foreach $f->options as $o}
		<a href="{url params=[$f->id=>$o->value, page=>null]}" {if $smarty.get.$f@key == $o->value}class="selected"{/if}>{$o->value|escape}</a>
		{/foreach}
	</td>
	</tr>
	{/foreach}
</table>
{/if}




<!--Каталог товаров-->
{if $products}

<div class="category-products">
    <div class="toolbar">
    <div class="pager">


        
    
    
    
    

    </div>

        <div class="sorter">
                <p class="view-mode">
                                    <label>Вид:</label>
                                  
 {if $smarty.cookies.view == 'list'}

                                                <a  onclick="document.cookie='view=grid;path=/';document.location.reload();" href="javascript:;" title="Сетка" class="grid">Сетка</a>&nbsp;
                                                                <strong title="Список" class="list">Список</strong>&nbsp;

 {else}                                   
                                    
                                                <strong title="Сетка" class="grid">Сетка</strong>&nbsp;
                                                                <a onclick="document.cookie='view=list;path=/';document.location.reload();" href="javascript:;" title="Список" class="list">Список</a>&nbsp;
 {/if}                                                               
                                                </p>
            
        <div class="sort-by">
            <label>Тип сортировки по </label>
<select onchange="location.href = this.options[this.selectedIndex].value;">
<option value="{url sort=position page=null}"{if $sort=='position'} selected{/if}>умолчанию</option>
<option value="{url sort=price page=null}"{if $sort=='price'} selected{/if}>цене</option>
<option value="{url sort=name page=null}"{if $sort=='name'} selected{/if}>названию</option>

</select>
        </div>
    </div>
    </div>
 {if $smarty.cookies.view == 'list'}
 
                            <ol class="products-list" id="products-list">
                     {foreach name=products item=product from=$products}       
                    <li class="item product">
                <a href="products/{$product->url}" title=" {$product->name|escape} " class="product-image">
                <img src="{$product->image->filename|resize:200:200}" alt="{$product->name|escape}"></a>
                <div class="product-shop">
                 <h2 class="product-name">
                 <a href="products/{$product->url}" title=" {$product->name|escape} ">
                 {$product->name|escape}</a>
                 </h2>
                 <div class="desc_grid">{$product->annotation}</div>
                 

 {if $product->variants|count > 0}
 <form class="variants" action="/cart">
 <div class="bottom">               
    <div class="price-box">
{if $product->variant->compare_price > 0}                                                   
                        <p class="special-price">
                <span class="price-label">Special Price</span>
                <span class="price">
                    <span class="o-pr">{$product->variant->compare_price|convert}</span>&nbsp;{$currency->sign|escape}
                </span>
            </p>
            <p class="old-price">
                <span class="price-label">Обычная цена:</span>
                <span class="price">
                   <span class="pr">{$product->variant->price|convert}</span>&nbsp;{$currency->sign|escape}
                </span>
            </p>
{else}                    
<span class="regular-price">
<span class="price"><span class="pr">{$product->variant->price|convert}</span>&nbsp;{$currency->sign|escape}</span>
</span>
{/if}    
        </div>                                  
                 <div class="actions">                 
			<!-- Выбор варианта товара -->
<div class="clear"></div>
			<select name="variant" {if $product->variants|count==1  && !$product->variant->name}style='display:none;'{/if}>
				{foreach $product->variants as $v}
				<option value="{$v->id}" {if $v->compare_price > 0}compare_price="{$v->compare_price|convert}"{/if} price="{$v->price|convert}">
				{$v->name}
				</option>
				{/foreach}
			</select>
			<!-- Выбор варианта товара (The End) -->                 
 <div class="clear"></div>                
                                     <button type="submit" title="B корзину" class="button btn-cart"><span><span><i class="icon-shopping-cart"></i>&nbsp;&nbsp;&nbsp; B корзину</span></span></button>
                                    <button type="button" title="Подробности" class="button btn-details" onclick="setLocation('products/{$product->url}')"><span><span>Подробности</span></span></button>                  
                 </div>
</div>
</form>                 
{else}
 <div class="bottom"> 
<div class="price-box">
<span class="price">
                    Нет в наличии               </span>
</div>
</div>
{/if}                 

                </div>
                <div class="clear"></div>
                                <div class="label-product">
 {if $product->featured}                               
<span class="new">Хит</span>
{/if}
{if $product->variant->compare_price > 0}
<span class="sale">Скидка</span>
{/if}                                              
                                </div>
                                <div class="clear"></div> 
                
            </li>
            
            {/foreach}
                </ol> 
         
 {else}    
                            <ul class="products-grid row">
                     {foreach name=products item=product from=$products}       
                    <li class="item product {cycle values='first,,last'} col-xs-12 col-sm-4">
                <div class="grid_wrapper">
                <a href="products/{$product->url}" title=" {$product->name|escape} " class="product-image">
                <img src="{$product->image->filename|resize:200:200}" alt="{$product->name|escape}"></a>
                <div class="product-shop">
                 <h2 class="product-name">
                 <a href="products/{$product->url}" title=" {$product->name|escape} ">
                 {$product->name|escape}</a>
                 </h2>
                 <div class="desc_grid">{$product->annotation|strip_tags|truncate:100}</div>
                 

 {if $product->variants|count > 0}
 <form class="variants" action="/cart">
 <div class="bottom">               
    <div class="price-box">
{if $product->variant->compare_price > 0}                                                   
                        <p class="special-price">
                <span class="price-label">Special Price</span>
                <span class="price">
                    <span class="o-pr">{$product->variant->compare_price|convert}</span>&nbsp;{$currency->sign|escape}
                </span>
            </p>
            <p class="old-price">
                <span class="price-label">Обычная цена:</span>
                <span class="price">
                   <span class="pr">{$product->variant->price|convert}</span>&nbsp;{$currency->sign|escape}
                </span>
            </p>
{else}                    
<span class="regular-price">
<span class="price"><span class="pr">{$product->variant->price|convert}</span>&nbsp;{$currency->sign|escape}</span>
</span>
{/if}    
        </div>                                  
                 <div class="actions">                 
			<!-- Выбор варианта товара -->
<div class="clear"></div>
			<select name="variant" {if $product->variants|count==1  && !$product->variant->name}style='display:none;'{/if}>
				{foreach $product->variants as $v}
				<option value="{$v->id}" {if $v->compare_price > 0}compare_price="{$v->compare_price|convert}"{/if} price="{$v->price|convert}">
				{$v->name}
				</option>
				{/foreach}
			</select>
			<!-- Выбор варианта товара (The End) -->                 
<div class="clear"></div>                 
                                     <button type="submit" title="B корзину" class="button btn-cart"><span><span><i class="icon-shopping-cart"></i>&nbsp;&nbsp;&nbsp; B корзину</span></span></button>
                                    <button type="button" title="Подробности" class="button btn-details" onclick="setLocation('products/{$product->url}')"><span><span>Подробности</span></span></button>                  
                 </div>
</div>
</form>                 
{else}
 <div class="bottom"> 
<div class="price-box">
<span class="price" id="product-price-54">
                    Нет в наличии               </span>
</div>
</div>
{/if}                 

                </div>
                <div class="clear"></div>
                                <div class="label-product">
 {if $product->featured}                               
<span class="new">Хит</span>
{/if}
{if $product->variant->compare_price > 0}
<span class="sale">Скидка</span>
{/if}                                              
                                </div>
                                <div class="clear"></div> 
                </div>
                
            </li>
            
{if $smarty.foreach.products.iteration%3 == 0 && !$product@last}
                </ul>
                                <ul class="products-grid row">
{/if}            
            {/foreach}
                </ul>
 {/if}
    
    <div class="toolbar-bottom">
    <div class="toolbar">
    <div class="pager">

    </div>

        <div class="sorter">
                <p class="view-mode">
                                    <label>Вид:</label>
                                  
 {if $smarty.cookies.view == 'list'}

                                                <a  onclick="document.cookie='view=grid;path=/';document.location.reload();" href="javascript:;" title="Сетка" class="grid">Сетка</a>&nbsp;
                                                                <strong title="Список" class="list">Список</strong>&nbsp;

 {else}                                   
                                    
                                                <strong title="Сетка" class="grid">Сетка</strong>&nbsp;
                                                                <a onclick="document.cookie='view=list;path=/';document.location.reload();" href="javascript:;" title="Список" class="list">Список</a>&nbsp;
 {/if}                                                               
                                                </p>
            
        <div class="sort-by">
            <label>Тип сортировки по </label>
<select onchange="location.href = this.options[this.selectedIndex].value;">
<option value="{url sort=position page=null}"{if $sort=='position'} selected{/if}>умолчанию</option>
<option value="{url sort=price page=null}"{if $sort=='price'} selected{/if}>цене</option>
<option value="{url sort=name page=null}"{if $sort=='name'} selected{/if}>названию</option>

</select>
        </div>
    </div>
    </div>
    </div>
</div>


{* Сортировка *}
{if $products|count>0}
<div class="sort">
	Сортировать по 
	<a {if $sort=='position'} class="selected"{/if} href="{url sort=position page=null}">умолчанию</a>
	<a {if $sort=='price'}    class="selected"{/if} href="{url sort=price page=null}">цене</a>
	<a {if $sort=='name'}     class="selected"{/if} href="{url sort=name page=null}">названию</a>
</div>
{/if}


{*include file='pagination.tpl'}


	        <ul class="products-grid">
	        {foreach name=products item=product from=$products}
	        	            <li class="item product {if $smarty.foreach.products.iteration%3 == 0}last{/if}  col-xs-12 col-sm-4">
	        	            
	                <div class="product-box">
	                    <a href="products/{$product->url}" title="{$product->name|escape}" class="product-image image"><img src="{$product->image->filename|resize:200:200}" alt="{$product->name|escape}" /></a>
		                <h3 class="product-name"><a data-product="{$product->id}" href="products/{$product->url}" title="{$product->name|escape}">{$product->name|escape}</a></h3>
                    </div>
                    <div class="clear"></div>
		                		                
 		{if $product->variants|count > 0}
		<!-- Цена и заказ товара -->
		<form class="variants" method="get" action="cart">
                
    <div class="price-box">
                                                            <span class="regular-price">
                             				
				{if $product->variant->compare_price > 0}
				<strike>{$product->variant->compare_price|convert}</strike>
				{/if}
				                               
                                            <span class="price">{$product->variant->price|convert}</span> <i>{$currency->sign|escape}</i>                                    </span>
                        
        </div>

                        
                    <div class="clear"></div>


						
			<!-- Выбор варианта товара -->

			<select name="variant" {if $product->variants|count==1  && !$product->variant->name}style='display:none;'{/if}>
				{foreach $product->variants as $v}
				<option value="{$v->id}" {if $v->compare_price > 0}compare_price="{$v->compare_price|convert}"{/if} price="{$v->price|convert}">
				{$v->name}
				</option>
				{/foreach}
			</select>
			<!-- Выбор варианта товара (The End) -->


		                <div class="actions">
	                <a class="button-view" href="products/{$product->url}" title="Подробнее">Подробнее</a>
	                    	                        <button type="submit" title="В корзину" class="button2 btn-cart"><span><span>В корзину</span></span></button>
	                                            
	                </div>		
		</form>
		<!-- Цена и заказ товара (The End)-->
		
		{else}
			<div  class="actions">Нет в наличии</div>
		{/if}                   

	            </li>
{if $smarty.foreach.products.iteration%3 == 0}<div class="clear"></div>{/if}	            
	            {/foreach}
	                    	           
	        	        </ul>


{include file='pagination.tpl'*}	
<!-- Список товаров (The End)-->

{else}
Товары не найдены
{/if}	
<!--Каталог товаров (The End)-->