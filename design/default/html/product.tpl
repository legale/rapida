{* Страница товара *}

{* Канонический адрес страницы *}
{$canonical="/products/{$product['url']}" scope=parent}

<!-- Хлебные крошки /-->
<div id="path">
	<a href="./">Главная</a>
	{foreach $category['path'] as $cat}
	→ <a href="catalog/{$cat['url']}">{$cat['name']|escape}</a>
	{/foreach}
	{if $brand}
	→ <a href="catalog/{$cat['url']}/{$brand['url']}">{$brand['name']|escape}</a>
	{/if}
	→  {$product['name']|escape}                
</div>
<!-- Хлебные крошки #End /-->
			{$pid = $product['id']}
			{$url = $product['url']}
			{$name = $product['name']}
			{$image = $product['images'][0]['filename']}

<h1 data-product="{$pid}">{$name|escape}</h1>
<div class="product">

	<!-- Большое фото -->
	{if $image}
	<div class="image">
		<a href="{$image|resize:800:600:w:$pid}" class="zoom" rel="group">
			<img src="{$image|resize:300:300:false:$pid}" alt="{$name|escape}" /></a>
	</div>
	{/if}
	<!-- Большое фото (The End)-->

	<!-- Описание товара -->
	<div class="description">
	
		{$product['body']}
		
		
	</div>
	<!-- Описание товара (The End)-->

	{if $product['variants']|count > 0}
	<!-- Выбор варианта товара -->
	<form class="variants" action="/cart">
		<table>
		{foreach $product['variants'] as $v}
		<tr class="variant">
			<td>
				<input id="product_{$v['id']}" name="variant" value="{$v['id']}" type="radio" class="variant_radiobutton" {if $product['variant']['id']==$v['id']}checked{/if} {if $product['variants']|count<2}style="display:none;"{/if}/>
			</td>
			<td>
				{if $v['name']}<label class="variant_name" for="product_{$v['id']}">{$v['name']}</label>{/if}
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


	<!-- Дополнительные фото продукта -->
	{if $product['images']|count>1}
	<div class="images">
		{* cut удаляет первую фотографию, если нужно начать 2-й - пишем cut:2 и тд *}
		{foreach $product['images']|cut as $i=>$image}
			<a href="{$image['filename']|resize:800:600:w:$pid:$i}" class="zoom" rel="group"><img src="{$image['filename']|resize:95:95:false:$pid:$i}" alt="{$name|escape}" /></a>
		{/foreach}
	</div>
	{/if}
	<!-- Дополнительные фото продукта (The End)-->


	{if $product['options']}
	<!-- Характеристики товара -->
	<h2>Характеристики</h2>
	<ul class="features">
	{foreach $product['options'] as $fid=>$o}
	<li>
		<label fid="{$fid}" vid="{$o['vid']}">{$features[$fid]['name']}</label>
		<span>{$o['val']}</span>
	</li>
	{/foreach}
	</ul>
	<!-- Характеристики товара (The End)-->
	{/if}

	<!-- Соседние товары /-->
	<div id="back_forward">
		{if $prev_product}
			←&nbsp;<a class="prev_page_link" href="products/{$prev_product['url']}">{$prev_product['name']|escape}</a>
		{/if}
		{if $next_product}
			<a class="next_page_link" href="products/{$next_product['url']}">{$next_product['name']|escape}</a>&nbsp;→
		{/if}
	</div>
	
</div>
<!-- Описание товара (The End)-->

{* Связанные товары *}
{if $related_products}
<h2>Так же советуем посмотреть</h2>
<!-- Список каталога товаров-->
<ul class="tiny_products">
	{foreach $related_products as $related_product}
			{$pid = $related_product['id']}
			{$url = $related_product['url']}
			{$name = $related_product['name']}
			{$image = $related_product['image']}
	<!-- Товар-->
	<li class="product">
		
		<!-- Фото товара -->
		{if $image}
		<div class="image">
			<a href="products/{$related_product['url']}"><img src="{$image|resize:200:200:false:$pid}" alt="{$name|escape}"/></a>
		</div>
		{/if}
		<!-- Фото товара (The End) -->

		<!-- Название товара -->
		<h3><a data-product="{$pid}" href="products/{$url}">{$name|escape}</a></h3>
		<!-- Название товара (The End) -->

		{if $related_product['variants']|count > 0}
		<!-- Выбор варианта товара -->
		<form class="variants" action="/cart">
			<table>
			{foreach $related_product['variants'] as $v}
			<tr class="variant">
				<td>
					<input id="related_{$v['id']}" name="variant" value="{$v['id']}" type="radio" class="variant_radiobutton"  {if $v@first}checked{/if} {if $related_product['variants']|count<2} style="display:none;"{/if}/>
				</td>
				<td>
					{if $v['name']}<label class="variant_name" for="related_{$v['id']}">{$v['name']}</label>{/if}
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


	</li>
	<!-- Товар (The End)-->
	{/foreach}
</ul>
{/if}

<!-- Комментарии -->
<div id="comments">

	<h2>Комментарии</h2>
	
	{if $comments}
	<!-- Список с комментариями -->
	<ul class="comment_list">
		{foreach $comments as $comment}
		<a name="comment_{$comment['id']}"></a>
		<li>
			<!-- Имя и дата комментария-->
			<div class="comment_header">	
				{$comment['name']|escape} <i>{$comment['date']|date}, {$comment['date']|time}</i>
				{if !$comment['approved']}ожидает модерации</b>{/if}
			</div>
			<!-- Имя и дата комментария (The End)-->
			
			<!-- Комментарий -->
			{$comment['text']|escape|nl2br}
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

{* Увеличитель картинок *}
{literal}
<script type="text/javascript" src="js/fancybox/jquery.fancybox.pack.js"></script>
<link rel="stylesheet" href="js/fancybox/jquery.fancybox.css" type="text/css" media="screen" />

<script>
$(function() {
	// Раскраска строк характеристик
	$(".features li:even").addClass('even');

	// Зум картинок
	$("a.zoom").fancybox({
		prevEffect	: 'fade',
		nextEffect	: 'fade'});
	});
</script>
{/literal}
