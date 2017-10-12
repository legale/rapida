{* Главная страница магазина *}

{* Для того чтобы обернуть центральный блок в шаблон, отличный от index.tpl *}
{* Укажите нужный шаблон строкой ниже. Это работает и для других модулей *}
{$wrapper = 'index.tpl' scope=parent}




{* Рекомендуемые товары *}
{get_featured_products var=featured_products}
{if $featured_products}
<!-- Список товаров-->
<h1>Рекомендуемые товары</h1>
  <div class="product-grid">
    <ul>
	{foreach $featured_products as $product}
    		<form class="variants" action="/cart">	
    <li class="product {if ($product@iteration+2)%3 == 0}first-in-line{elseif $product@iteration%3 == 0}last-in-line{/if}">
            <div class="image">
		{if $product->image}
			<a href="products/{$product->url}" title="{$product->name|escape}">
			<img src="{$product->image->filename|resize:200:200}" alt="{$product->name|escape}"  />
			</a>
		{/if}
            </div>
            <div class="name">
           <a data-product="{$product->id}" href="products/{$product->url}" title="{$product->name|escape}">
            {$product->name|escape}
            </a>
            </div>
      <div class="description">
	{$product->annotation}
		</div>

		      {if $product->variants|count > 0}	
            <div class="price">
                <span class="price-new">{$product->variant->price|convert}</span> {$currency->sign|escape}                        
                <span class="price-old">
				{if $product->variant->compare_price > 0}
				{$product->variant->compare_price|convert}
				{/if}
				</span>
              </div>
        
            <div class="rating">
			<!-- Выбор варианта товара -->
			{* Не показывать выбор варианта, если он один и без названия *}
			<select name="variant" {if $product->variants|count==1  && !$product->variant->name}style='display:none;'{/if}>
				{foreach $product->variants as $v}
				<option value="{$v->id}" {if $v->compare_price > 0}compare_price="{$v->compare_price|convert}"{/if} price="{$v->price|convert}">
				{$v->name}
				</option>
				{/foreach}
			</select>
			<!-- Выбор варианта товара (The End) --> 
			          
            </div>
 <div class="cart">
 <span class="button">
 <input type="submit"  value="в корзину" data-result-text="добавлено"/>
 </span>
 </div>
		{else}
		
			<div class="price">Нет в наличии</div>
		<div class="rating">	
		</div>
		<div class="cart">
		</div>	
		{/if}
		
    </li>
    </form>
	{/foreach}
			
</ul>
</div>
{/if}


{* Новинки *}
{get_new_products var=new_products limit=3}
{if $new_products}
<h1>Новинки</h1>
<!-- Список товаров-->
  <div class="product-grid">
    <ul>
	{foreach $new_products as $product}
    		<form class="variants" action="/cart">	
    <li class="product {if ($product@iteration+2)%3 == 0}first-in-line{elseif $product@iteration%3 == 0}last-in-line{/if}">
            <div class="image">
		{if $product->image}
			<a href="products/{$product->url}" title="{$product->name|escape}">
			<img src="{$product->image->filename|resize:200:200}" alt="{$product->name|escape}"  />
			</a>
		{/if}
            </div>
            <div class="name">
           <a data-product="{$product->id}" href="products/{$product->url}" title="{$product->name|escape}">
            {$product->name|escape}
            </a>
            </div>
      <div class="description">
	{$product->annotation}
		</div>

		      {if $product->variants|count > 0}	
            <div class="price">
                <span class="price-new">{$product->variant->price|convert}</span> {$currency->sign|escape}                        
                <span class="price-old">
				{if $product->variant->compare_price > 0}
				{$product->variant->compare_price|convert}
				{/if}
				</span>
              </div>
        
            <div class="rating">
			<!-- Выбор варианта товара -->
			{* Не показывать выбор варианта, если он один и без названия *}
			<select name="variant" {if $product->variants|count==1  && !$product->variant->name}style='display:none;'{/if}>
				{foreach $product->variants as $v}
				<option value="{$v->id}" {if $v->compare_price > 0}compare_price="{$v->compare_price|convert}"{/if} price="{$v->price|convert}">
				{$v->name}
				</option>
				{/foreach}
			</select>
			<!-- Выбор варианта товара (The End) --> 
			          
            </div>
 <div class="cart">
 <span class="button">
 <input type="submit"  value="в корзину" data-result-text="добавлено"/>
 </span>
 </div>
		{else}
		
			<div class="price">Нет в наличии</div>
		<div class="rating">	
		</div>
		<div class="cart">
		</div>	
		{/if}
		
    </li>
    </form>
	{/foreach}
			
</ul>
</div>
{/if}	


{* Акционные товары *}
{get_discounted_products var=discounted_products limit=9}
{if $discounted_products}
<h1>Акционные товары</h1>
<!-- Список товаров-->
  <div class="product-grid">
    <ul>
	{foreach $discounted_products as $product}
    		<form class="variants" action="/cart">	
    <li class="product {if ($product@iteration+2)%3 == 0}first-in-line{elseif $product@iteration%3 == 0}last-in-line{/if}">
            <div class="image">
		{if $product->image}
			<a href="products/{$product->url}" title="{$product->name|escape}">
			<img src="{$product->image->filename|resize:200:200}" alt="{$product->name|escape}"  />
			</a>
		{/if}
            </div>
            <div class="name">
           <a data-product="{$product->id}" href="products/{$product->url}" title="{$product->name|escape}">
            {$product->name|escape}
            </a>
            </div>
      <div class="description">
	{$product->annotation}
		</div>

		      {if $product->variants|count > 0}	
            <div class="price">
                <span class="price-new">{$product->variant->price|convert}</span> {$currency->sign|escape}                        
                <span class="price-old">
				{if $product->variant->compare_price > 0}
				{$product->variant->compare_price|convert}
				{/if}
				</span>
              </div>
        
            <div class="rating">
			<!-- Выбор варианта товара -->
			{* Не показывать выбор варианта, если он один и без названия *}
			<select name="variant" {if $product->variants|count==1  && !$product->variant->name}style='display:none;'{/if}>
				{foreach $product->variants as $v}
				<option value="{$v->id}" {if $v->compare_price > 0}compare_price="{$v->compare_price|convert}"{/if} price="{$v->price|convert}">
				{$v->name}
				</option>
				{/foreach}
			</select>
			<!-- Выбор варианта товара (The End) --> 
			          
            </div>
 <div class="cart">
 <span class="button">
 <input type="submit"  value="в корзину" data-result-text="добавлено"/>
 </span>
 </div>
		{else}
		
			<div class="price">Нет в наличии</div>
		<div class="rating">	
		</div>
		<div class="cart">
		</div>	
		{/if}
		
    </li>
    </form>
	{/foreach}
			
</ul>
</div>
{/if}	
{* Заголовок страницы *}
<h1>{$page->header}</h1>

{* Тело страницы *}
{$page->body}

<script type="text/javascript">
{literal}
		(function($){$.fn.equalHeights=function(minHeight,maxHeight){tallest=(minHeight)?minHeight:0;this.each(function(){if($(this).height()>tallest){tallest=$(this).height()}});if((maxHeight)&&tallest>maxHeight)tallest=maxHeight;return this.each(function(){$(this).height(tallest)})}})(jQuery)
	$(window).load(function(){
		if($(".cat-height").length){
		$(".cat-height").equalHeights()}
	})
$(function() {

	// Выбор вариантов
	$('select[name=variant]').change(function() {
		price = $(this).find('option:selected').attr('price');
		compare_price = '';
		if(typeof $(this).find('option:selected').attr('compare_price') == 'string')
			compare_price = $(this).find('option:selected').attr('compare_price');
		$(this).find('option:selected').attr('compare_price');
		$(this).closest('form').find('span.price-new').html(price);
		$(this).closest('form').find('span.price-old').html(compare_price);
		return false;		
	});
		
});	
{/literal}	
</script>