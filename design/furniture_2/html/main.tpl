{* Главная страница магазина *}

{* Для того чтобы обернуть центральный блок в шаблон, отличный от index.tpl *}
{* Укажите нужный шаблон строкой ниже. Это работает и для других модулей *}
{$wrapper = 'index.tpl' scope=parent}


{* Рекомендуемые товары *}
{get_featured_products var=featured_products limit=6}
{if $featured_products}
<br><br>
<!-- Список товаров-->
<div class="page-title category-title">
        <h1>Рекомендуемые товары</h1>
</div>
                            <ul class="products-grid row">
                     {foreach name=featured_products item=product from=$featured_products}       
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
            
{if $smarty.foreach.featured_products.iteration%3 == 0 && !$product@last}
                </ul>
                                <ul class="products-grid row">
{/if}            
            {/foreach}
                </ul>
{/if}


{* Новинки *}
{get_new_products var=new_products limit=3}
{if $new_products}
<div class="page-title category-title">
        <h1>Новые товары</h1>
</div>
<!-- Список товаров-->
                            <ul class="products-grid row">
                     {foreach name=new_products item=product from=$new_products}       
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
            
{if $smarty.foreach.new_products.iteration%3 == 0 && !$product@last}
                </ul>
                                <ul class="products-grid row">
{/if}            
            {/foreach}
                </ul>
{/if}	

<div class="page-title category-title">
{* Заголовок страницы *}
<h1>{$page->header}</h1>
</div>


{* Тело страницы *}
{$page->body}


