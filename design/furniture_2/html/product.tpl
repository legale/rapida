{* Страница товара *}

<!-- Хлебные крошки /-->
<div class="breadcrumbs">
    <ul>
	 <li class="home"><a href="/">Главная</a></li>
	{foreach from=$category->path item=cat}
	<li><span>&gt;</span> <a href="catalog/{$cat->url}">{$cat->name|escape}</a></li>
	{/foreach}  
	{if $brand}
	<li><span>&gt;</span> <a href="catalog/{$cat->url}/{$brand->url}">{$brand->name|escape}</a></li>
	{/if}
	<li><span>&gt;</span> {$product->name|escape}</li>                                   
            </ul>	
</div>
<!-- Хлебные крошки #End /-->
<div class="product-view prod">
<div class="product-essential">

        <div class="product-img-box">
            <script type="text/javascript" src="design/{$settings->theme|escape}/js/klass.min.js"></script>
<script type="text/javascript" src="design/{$settings->theme|escape}/js/code.photoswipe.jquery-3.0.5.js"></script>


<div class="product-box-customs">
    {if $product->image}    
    <p class="product-image">
        <a  href='{$product->image->filename|resize:800:800:w}' class = 'cloud-zoom' id='zoom1' rel="position:'right',showTitle:1,titleOpacity:0.5,lensOpacity:0.5,adjustX: 10,adjustY:-4">
            
            <img class="big" src="{$product->image->filename|resize:300:300}" alt='{$product->name|escape}' title="{$product->name|escape}" />
        </a>
    </p>
    {/if}        
    {if $product->images|count>1}         
    <div class="more-views">
        <h2>Больше изображений</h2>
        <div class="container-slider">
            <ul class="slider tumbSlider-none" >
            {foreach $product->images as $i=>$image}
                            <li>
                    <a href='{$image->filename|resize:800:800:w}' class='cloud-zoom-gallery' title=''
                    rel="useZoom: 'zoom1', smallImage: '{$image->filename|resize:300:300}' ">
                    <img src="{$image->filename|resize:100:100}" alt=""/>
                    </a>
                </li>
             {/foreach}   
                        </ul>
                    </div>
    </div>
        <div class="gallery-swipe-content">
            <ul id="gallery-swipe" class="gallery-swipe">
            {foreach $product->images as $i=>$image}
                                    <li>
                        <a href='{$image->filename|resize:800:800:w}'  title=''>
                        <img src="{$image->filename|resize:300:300}" alt=""/>
                        </a>
                    </li>
             {/foreach}       
                            </ul>
        </div>
        {/if}
    </div>
        </div>
        <div class="product-shop">
            <div class="product-name">
               <h1 data-product="{$product->id}">{$product->name|escape}</h1>
            </div>


            
            <div class="clear"></div>
                            <div class="short-description">
                    <h2>Краткая информация</h2>
                    <div class="std">
                   {$product->annotation}
                    </div>
                </div>
 
 		{if $product->variants|count > 0}
                                                <p class="availability in-stock">Наличие: <span>Есть в наличии</span></p>
                             
            <div class="clear"></div> 		
 		
		<!-- Выбор варианта товара -->
<div class="product-options-bottom">		
			<table id="variants">
			{foreach $product->variants as $v}
			<tr class="variant">
				<td>
					{if $v->name}<label class="variant_name" for="product_{$v->id}">{$v->name}</label>{/if}
				</td>
				<td>
				<div class="price-box">
{if $v->compare_price > 0}                                                   
                        <p class="special-price">
                <span class="price-label">Special Price</span>
                <span class="price">
                    <span class="o-pr">{$v->compare_price|convert}</span>&nbsp;{$currency->sign|escape}
                </span>
            </p>
            <p class="old-price">
                <span class="price-label">Обычная цена:</span>
                <span class="price">
                   <span class="pr">{$v->price|convert}</span>&nbsp;{$currency->sign|escape}
                </span>
            </p>
{else}                    
<span class="regular-price">
<span class="price"><span class="pr">{$v->price|convert}</span>&nbsp;{$currency->sign|escape}</span>
</span>
{/if}
				</div> 
				</td>
				<td>
				<form class="variants" action="cart">
    <div class="add-to-cart">
        			<div class="qty-block">
				<label for="qty">К-во:</label>
				<input type="text" name="amount" id="qty" maxlength="12" value="1" title="Кол-во" class="input-text qty form-control">
			</div>				
					<input id="product_{$v->id}" name="variant" value="{$v->id}" type="radio" class="variant_radiobutton" checked style="display:none;"/>

                <button type="submit" title="Добавить в корзину" class="button btn-cart"><span><span><i class="icon-shopping-cart"></i>&nbsp;&nbsp;&nbsp; B корзину</span></span></button>
            </div>			
		</form>				
				</td>				
			</tr>
			{/foreach}
			</table>
</div>
		<!-- Выбор варианта товара (The End) -->
		{else}
       <p class="availability in-stock">Наличие: <span style="color:red;">Нет в наличии</span></p>                            
            <div class="clear"></div> 			
		{/if}               
                        <div class="row-product">
                        
<script src="http://odnaknopka.ru/ok3.js" type="text/javascript"></script>

                            </div>
        </div>
        <div class="clearer"></div>
    </div>
 
    
    <div class="product-collateral">
    {if $product->body}
        <div class="box-collateral box-description">
                            <h2>Подробности</h2>
	<div class="box-collateral-content">
		<div class="std">
{$product->body}
		</div>
	</div>
        </div>    
    {/if}
	{if $product->features}
	<!-- Характеристики товара -->
        <div class="box-collateral product">
                           <h2>Характеристики</h2>
	<div class="box-collateral-content">
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
        </div>  
	<!-- Характеристики товара (The End)-->
	{/if}  
    
        <div class="box-collateral box-description">
                            	<h2>Комментарии ({$comments|count})</h2>
	<div class="box-collateral-content">
		<div class="std">
<!-- Комментарии -->
<div id="comments">


	
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
		<h3>Написать комментарий</h3>
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


<button type="submit" name="comment" value="Отправить" class="button"><span><span>Отправить</span></span></button>		
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
    
	</div>    

   </div>






