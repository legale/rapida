{* Страница товара *}

  <div class="breadcrumb">
	<a href="./">Главная</a>
	{foreach from=$category->path item=cat}
	&raquo;  <a href="catalog/{$cat->url}">{$cat->name|escape}</a>
	{/foreach}
	{if $brand}
	&raquo;  <a href="catalog/{$cat->url}/{$brand->url}">{$brand->name|escape}</a>
	{/if}
	&raquo;   {$product->name|escape} 
      </div>

  <div class="product-info product">
   <form class="variants" action="/cart"> 
    <div class="wrapper indent-bot">
            <div class="fleft left spacing">
            {if $product->images}
            {foreach $product->images as $i=>$image}
                      <div class="zoom-top">
<a href="{$image->filename|resize:800:600:w}" title="{$product->name|escape}"  data-gal="prettyPhoto[gallery]"><img src="{$image->filename|resize:300:300}" alt="{$product->name|escape}" /></a>
                            </div>
             {/foreach}               

                                               
          <div class="image"> 
<a href="{$product->image->filename|resize:800:600:w}" title="{$product->name|escape}"  class="cloud-zoom" id="zoom1" rel="position: 'right'">
<img src="{$product->image->filename|resize:300:300}" title="{$product->name|escape}"  style="display: block;" alt="{$product->name|escape}" /></a>          

         

          </div>
                              <div class="image-additional ">
              <ul>
 {foreach $product->images as $i=>$image}       
                                         <li>
                                <a href="{$image->filename|resize:800:600:w}" title="{$product->name|escape}" class="cloud-zoom-gallery" rel="useZoom: 'zoom1', smallImage: '{$image->filename|resize:300:300}' ">
                                <img src="{$image->filename|resize:90:90}" title="{$product->name|escape}" alt="{$product->name|escape}">
                                </a>
                        </li>
  {/foreach}                      

                              </ul>
              </div>
                    
        {/if}       
        </div>
            <div class="extra-wrap">
      <h1 data-product="{$product->id}">{$product->name|escape}</h1>
      <div class="description">
                <div class="padd-avalib">{$product->annotation}</div>
            <div class="price">
        <span class="text-price">Цена:</span>
                <span class="price-new">{$product->variant->price|convert}</span> {$currency->sign|escape}                        
                <span class="price-old">
				{if $product->variant->compare_price > 0}
				{$product->variant->compare_price|convert}
				{/if}
				</span><br>
                              </div>
                  <div class="cart">
        <div class="prod-row">
          <div class="cart-top">
            <div class="cart-top-padd">
                <label>К-во:</label>
                <input type="text" name="amount" size="2" value="1">

              </div>
               <input id="button-cart" class="button-prod" type="submit"  value="в корзину" data-result-text="добавлено"/>
          </div>
        </div>
        <div class="extra-button">
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
              </div>
            <div class="review">
                    <div>&nbsp;&nbsp;<div class="btn-rew"><a onclick="$('a[href=\'#tab-review\']').trigger('click');">{$comments|count} комментариев</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="$('a[href=\'#tab-review\']').trigger('click');">Написать комментарий</a></div></div>
           <div class="share"><!-- AddThis Button BEGIN -->
             <!-- AddThis Button BEGIN -->
            <div class="addthis_toolbox addthis_default_style ">
            <a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
            <a class="addthis_button_tweet"></a>
            <a class="addthis_button_google_plusone" g:plusone:size="medium"></a>
            <a class="addthis_button_pinterest_pinit" pi:pinit:layout="horizontal"></a>
            <a class="addthis_counter addthis_pill_style"></a>
            </div>
            <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=ra-4f419f410efe76d3"></script>
            <!-- AddThis Button END -->
            </div>
          </div>
          </div>
  </div>
  </div>
  </form>
  	<!-- Соседние товары /-->
	<div id="back_forward" class="wrapper mb-1">
		{if $prev_product}
			←&nbsp;<a class="prev_page_link" href="products/{$prev_product->url}">{$prev_product->name|escape}</a>
		{/if}
		{if $next_product}
			<a class="next_page_link" href="products/{$next_product->url}">{$next_product->name|escape}</a>&nbsp;→
		{/if}
	</div>
<div class="wrapper mb-1">
  <div id="tabs" class="htabs">
  <a href="#tab-description" style="display: inline;" class="selected">Описание</a>
  {if $product->features}
  <a href="#tab-feat" style="display: inline;" class="">Характеристики</a>  
  {/if}
  <a href="#tab-review" style="display: inline;" class="">Комментарии ({$comments|count})</a>
          </div>
</div>
<div class="wrapper">
  <div id="tab-description" class="tab-content" style="display: block;">
  <div class="std">
	{$product->body}
	</div>
	</div>
	{if $product->features}
	<!-- Характеристики товара -->
  <div id="tab-feat" class="tab-content" style="display: none;">
  <div class="std">
	<ul class="features">
	{foreach $product->features as $f}
	<li>
		<label>{$f->name}</label>
		<span>{$f->value}</span>
	</li>
	{/foreach}
	</ul>
	</div>
	</div>	
	<!-- Характеристики товара (The End)-->
	{/if}	
      <div id="tab-review" class="tab-content" style="display: none;">
<!-- Комментарии -->
<div id="comments">

	<h2>Комментарии</h2>
	
	{if $comments}
	<!-- Список с комментариями -->
	<ul class="comment_list">
		{foreach $comments as $comment}
		<a name="comment_{$comment->id}"></a>
		<li>
			<!-- Имя и дата комментария-->
			<div class="comment_header">	
				{$comment->name|escape} <i>{$comment->date|date}, {$comment->date|time}</i>
				{if !$comment->approved}ожидает модерации</b>{/if}
			</div>
			<!-- Имя и дата комментария (The End)-->
			
			<!-- Комментарий -->
			{$comment->text|escape|nl2br}
			<!-- Комментарий (The End)-->
		</li>
		{/foreach}
	</ul>
	<!-- Список с комментариями (The End)-->
	{else}
	<p>
		Пока нет комментариев
	</p>
	{/if}
	
	<!--Форма отправления комментария-->	
	<form class="comment_form" method="post">
		<h2>Написать комментарий</h2>
		{if $error}
		<div class="message_error">
			{if $error=='captcha'}
			Неверно введена капча
			{elseif $error=='empty_name'}
			Введите имя
			{elseif $error=='empty_comment'}
			Введите комментарий
			{/if}
		</div>
		{/if}
		<textarea class="comment_textarea" id="comment_text" name="text" data-format=".+" data-notice="Введите комментарий">{$comment_text}</textarea><br />
		<div>
		<label for="comment_name">Имя</label>
		<input class="input_name" type="text" id="comment_name" name="name" value="{$comment_name}" data-format=".+" data-notice="Введите имя"/><br />

		<input class="button" type="submit" name="comment" value="Отправить" />
		
		<label for="comment_captcha">Число</label>
		<div class="captcha"><img src="captcha/image.php?{math equation='rand(10,10000)'}" alt='captcha'/></div> 
		<input class="input_captcha" id="comment_captcha" type="text" name="captcha_code" value="" data-format="\d\d\d\d" data-notice="Введите капчу"/>
		
		</div>
	</form>
	<!--Форма отправления комментария (The End)-->
	
</div>
<!-- Комментарии (The End) -->
  </div>
      </div>
  
  </div>

{* Связанные товары *}
{if $related_products}
<br><br>
<h2>Так же советуем посмотреть</h2>
<!-- Список каталога товаров-->
  <div class="product-grid">
    <ul>
	{foreach $related_products as $product}
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


<script type="text/javascript">
<!--
$('#tabs a').tabs();
//-->
</script>


{literal}
<script>
$(function() {
	// Раскраска строк характеристик
	$(".features li:even").addClass('even');
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
</script>
{/literal}
