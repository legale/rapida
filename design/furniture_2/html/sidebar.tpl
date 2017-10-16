{if $module == 'MainView'}			
{* Акционные товары *}
{get_discounted_products var=discounted_products limit=3}
{if $discounted_products}
<div class="block block-sale">
        <div class="block-title">
            <strong><span>Акции и скидки</span></strong>
        <span class="toggle"></span></div>
        <div class="block-content">
        {foreach $discounted_products as $product}
                                        <div class="item_sale">
 <a class="product-image" href="products/{$product->url}">
 <img src="{$product->image->filename|resize:100:100}" alt="{$product->name|escape}"/></a>
                    <div class="product-shop">
                            <p class="product-name">
 <p class="product-name"><a href="products/{$product->url}" title="{$product->name|escape}">{$product->name|escape}</a></p>
                            </p>
<div class="desc_grid">{$product->annotation|strip_tags|truncate:50}</div>
                            

                
    <div class="price-box">
                                
                    <p class="old-price">
                <span class="price-label">Regular Price:</span>
                <span class="price" >
                    {$product->variant->compare_price|convert}    {$currency->sign|escape}            </span>
            </p>

                        <p class="special-price">
                <span class="price-label">Special Price:</span>
                <span class="price">
                    {$product->variant->price|convert}    {$currency->sign|escape}                </span>
            </p>
                    
    
        </div>

                    </div>
                                            <div class="label-product">             
                                                    <span class="sale">Скидка</span>                      </div>
                </div>
		{/foreach}
        </div>
</div>

{/if}

{elseif $module == 'ProductView'}

{if $related_products}
<div class="block block-sale">
        <div class="block-title">
            <strong><span>Сопутствующие товары</span></strong>
        <span class="toggle"></span></div>
        <div class="block-content">
        {foreach $related_products as $product}
                                        <div class="item_sale">
 <a class="product-image" href="products/{$product->url}">
 <img src="{$product->image->filename|resize:100:100}" alt="{$product->name|escape}"/></a>
                    <div class="product-shop">
                            <p class="product-name">
 <p class="product-name"><a href="products/{$product->url}" title="{$product->name|escape}">{$product->name|escape}</a></p>
                            </p>
<div class="desc_grid">{$product->annotation|strip_tags|truncate:50}</div>
                            

                
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

                    </div>
                                            <div class="label-product">             
 {if $product->featured}                               
<span class="new">Хит</span>
{/if}
{if $product->variant->compare_price > 0}
<span class="sale">Скидка</span>
{/if}
                                                                          </div>
                </div>
		{/foreach}
        </div>
</div>

{/if}
				
{/if}



		<!-- Все бренды -->
			{* Выбираем в переменную $all_brands все бренды *}
			{get_brands var=all_brands}
			{if $all_brands}
		
    <div class="block">		
			<div id="all_brands">
        <div class="block-title">
            <strong><span>	Все бренды:</span></strong>
        </div>
        <div class="block-content">
 <select name="brand_cat" size="10" id="brand_cat" onchange="document.location.href = 'brands/' + this.options[this.selectedIndex].value">
{foreach $all_brands as $b}
                              <option value="{$b->url}" {if $b@iteration%2 ==0}class="grey_bg"{/if}>{if $b@iteration<10}0{/if}{$b@iteration} {$b->name|escape}</option>
{/foreach}
                          </select>	       
			</div>
</div>
</div>			
			{/if}
			<!-- Все бренды (The End)-->

<div class="block block-slider-sidebar block-slider-start">
    <div class="slider-block-content">
        <ul class="slider-sidebar">
            <li>
                <a href="#" class="slider_img">
                <img src="design/{$settings->theme|escape}/images/slider-sid-1.jpg" alt=""  /></a>
                <h2>Excepteur sint occaecat</h2>
                <p>Lorem ipsum dolor sit amet conse ctetur adiapisicing elit, sed do eiusmod.</p>
                <a href="#" class="shop_side">Shop now!</a>
            </li>
            <li>
                <a href="#" class="slider_img">
                <img src="design/{$settings->theme|escape}/images/slider-sid-2.jpg" alt=""  /></a>
                <h2>Excepteur sint occaecat</h2>
                <p>Lorem ipsum dolor sit amet conse ctetur adiapisicing elit, sed do eiusmod.</p>
                <a href="#" class="shop_side">Shop now!</a>
            </li>
            <li>
                <a href="#" class="slider_img">
                <img src="design/{$settings->theme|escape}/images/slider-sid-3.jpg" alt=""  /></a>
                <h2>Excepteur sint occaecat</h2>
                <p>Lorem ipsum dolor sit amet conse ctetur adiapisicing elit, sed do eiusmod.</p>
                <a href="#" class="shop_side">Shop now!</a>
            </li>
        </ul>
        <br>
        <div class="slider-sidebar-pager"></div>
    </div>
</div>

			
			<!-- Просмотренные товары -->
			{get_browsed_products var=browsed_products limit=20}
			{if $browsed_products}
			<div class="block">
			<div class="block-title">
            <strong><span>Вы просматривали:</span></strong>
        </div>
        <div class="block-content">
				<ul id="browsed_products">
				{foreach $browsed_products as $browsed_product}
					<li>
					<a href="products/{$browsed_product->url}"><img src="{$browsed_product->image->filename|resize:50:50}" alt="{$browsed_product->name}" title="{$browsed_product->name}"></a>
					</li>
				{/foreach}
				</ul>
			</div>
			</div>	
			{/if}
			<!-- Просмотренные товары (The End)-->
			
			
			<!-- Меню блога -->
			{* Выбираем в переменную $last_posts последние записи *}
			{get_posts var=last_posts limit=5}
			{if $last_posts}
			<div id="blog_menu" class="block">
				<div class="block-title">
            <strong><span>Новые записи в <a href="blog">блоге</a></span></strong>
        </div>
        <div class="block-content">
				{foreach $last_posts as $post}
				<ul>
					<li data-post="{$post->id}">{$post->date|date} <a href="blog/{$post->url}">{$post->name|escape}</a></li>
				</ul>
				{/foreach}
			</div>
			</div>
			<br>
			{/if}
			<!-- Просмотренные товары -->

<div class="free_shipping">
	<a href="#">
 	<h2>Free Shipping</h2>
  <h3>on orders over $99.</h3>
  <p>This offer is valid on all our store items.</p>
 </a>
</div>