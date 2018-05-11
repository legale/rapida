{* Вкладки *}
{capture name=tabs}
	{if isset($userperm['products'])}<li><a href="?module=ProductsAdmin">Товары</a></li>{/if}
	{if isset($userperm['categories'])}<li><a href="?module=CategoriesAdmin">Категории</a></li>{/if}
	<li class="active"><a href="?module=BrandsAdmin">Бренды</a></li>
	{if isset($userperm['features'])}<li><a href="?module=FeaturesAdmin">Свойства</a></li>{/if}
{/capture}

{if $brand['id']}
{$meta_title = $brand['name'] scope=parent}
{else}
{$meta_title = 'Новый бренд' scope=parent}
{/if}

{* Подключаем Tiny MCE *}
{include file='tinymce_init.tpl'}


{* On document load *}
{literal}
<script>
$(function() {

	// Удаление изображений
	$(".images a.delete").click( function() {
		$("input[name='delete_image']").val('1');
		$(this).closest("ul").fadeOut(200, function() { $(this).remove(); });
		return false;
	});


});

</script>
 
{/literal}

{if isset($message_success)}
<!-- Системное сообщение -->
<div class="message message_success">
	<span class="text">{if $message_success=='added'}Бренд добавлен{elseif $message_success=='updated'}Бренд обновлен{else}{$message_success}{/if}</span>
	<a class="link" target="_blank" href="../brands/{$brand['trans']}">Открыть бренд на сайте</a>
	{if $smarty.get.return}
	<a class="button" href="{$smarty.get.return}">Вернуться</a>
	{/if}
	
	<span class="share">		
		<a href="#" onClick='window.open("http://vkontakte.ru/share.php?url={$config->root_url|urlencode}/brands/{$brand['trans']|urlencode}&title={$brand['name']|urlencode}&description={$brand['description']|urlencode}&image={$config->root_url|urlencode}/files/brands/{$brand['image']|urlencode}&noparse=true","displayWindow","width=700,height=400,left=250,top=170,status=no,toolbar=no,menubar=no");return false;'>
  		<img src="{$config->root_url}/simpla/design/images/vk_icon.png" /></a>
		<a href="#" onClick='window.open("http://www.facebook.com/sharer.php?u={$config->root_url|urlencode}/brands/{$brand['trans']|urlencode}","displayWindow","width=700,height=400,left=250,top=170,status=no,toolbar=no,menubar=no");return false;'>
  		<img src="{$config->root_url}/simpla/design/images/facebook_icon.png" /></a>
		<a href="#" onClick='window.open("http://twitter.com/share?text={$brand['name']|urlencode}&url={$config->root_url|urlencode}/brands/{$brand['trans']|urlencode}&hashtags={$brand['meta_keywords']|replace:' ':''|urlencode}","displayWindow","width=700,height=400,left=250,top=170,status=no,toolbar=no,menubar=no");return false;'>
  		<img src="{$config->root_url}/simpla/design/images/twitter_icon.png" /></a>
	</span>
	
</div>
<!-- Системное сообщение (The End)-->
{/if}

{if isset($message_error)}
<!-- Системное сообщение -->
<div class="message message_error">
	<span class="text">{if $message_error=='url_exists'}Бренд с таким адресом уже существует{else}{$message_error}{/if}</span>
	<a class="button" href="{$smarty.get.return}">Вернуться</a>
</div>
<!-- Системное сообщение (The End)-->
{/if}


<!-- Основная форма -->
<form method=post class=product enctype="multipart/form-data">
<input type=hidden name="session_id" value="{$smarty.session.id}">
	<div id="name">
		<input class="name" name=name type="text" value="{$brand['name']|escape}"/> 
		<input name=id type="hidden" value="{$brand['id']|escape}"/> 
	</div> 

 		
	<!-- Левая колонка свойств товара -->
	<div class="column_left">
			
		<!-- Параметры страницы -->
		<div class="block layer">
			<h2>Параметры страницы</h2>
			<ul>
				<li><label class=property>Адрес</label><div class="page_url"> /brands/</div><input name="trans" class="page_url" type="text" value="{$brand['trans']|escape}" /></li>
				<li><label class=property>Заголовок</label><input name="meta_title" class="simpla_inp" type="text" value="{$brand['meta_title']|escape}" /></li>
				<li><label class=property>Ключевые слова</label><input name="meta_keywords" class="simpla_inp" type="text" value="{$brand['meta_keywords']|escape}" /></li>
				<li><label class=property>Описание</label><textarea name="meta_description" class="simpla_inp" />{$brand['meta_description']|escape}</textarea></li>
			</ul>
		</div>
		<!-- Параметры страницы (The End)-->
		
 		{*
		<!-- Экспорт-->
		<div class="block">
			<h2>Экспорт товара</h2>
			<ul>
				<li><input id="exp_yad" type="checkbox" /> <label for="exp_yad">Яндекс Маркет</label> Бид <input class="simpla_inp" type="" name="" value="12" /> руб.</li>
				<li><input id="exp_goog" type="checkbox" /> <label for="exp_goog">Google Base</label> </li>
			</ul>
		</div>
		<!-- Свойства товара (The End)-->
		*}
			
	<input class="button_green button_save" type="submit" name="" value="Сохранить" />
	</div>
	<!-- Левая колонка свойств товара (The End)--> 
	
	<!-- Правая колонка свойств товара -->	
	<div class="column_right">
	
		<!-- Изображение категории -->	
		<div class="block layer images">
			<h2>Изображение бренда</h2>
			<input class='upload_image' name=image type=file>			
			<input type=hidden name="delete_image" value="">
			{if $brand['image']}
			<ul>
				<li>
					<a href='#' class="delete"><img src='design/images/cross-circle-frame.png'></a>
					<img src="../{$config->brands_images_dir}{$brand['image']}" alt="" />
				</li>
			</ul>
			{/if}
		</div>
		
	</div>
	<!-- Правая колонка свойств товара (The End)--> 

	<!-- Короткое описание -->
	<div class="block layer">
		<h2>Короткое описание до 65 535 символов</h2>
		<textarea name="annotation" class="editor_small">{$brand['annotation']|escape}</textarea>
	</div>
	<!-- Короткое описание (The End)-->

	<!-- Описание -->
	<div class="block layer">
		<h2>Описание до 16 777 215 символов</h2>
		<textarea name="description" class="editor_large">{$brand['description']|escape}</textarea>
	</div>
	<!-- Описание (The End)-->







	<input class="button_green button_save" type="submit" name="" value="Сохранить" />
	
	
</form>
<!-- Основная форма (The End) -->

