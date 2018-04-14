{capture name=tabs}
	<li class="active"><a href="?module=BlogAdmin">Блог</a></li>
{/capture}

{if $post['id']}
{$meta_title = $post['name'] scope=parent}
{else}
{$meta_title = 'Новая запись в блоге' scope=parent}
{/if}

{* Подключаем Tiny MCE *}
{include file='tinymce_init.tpl'}

{literal}
<script>
$(function() {
	//включаем обработчик на все элементы, у которых есть аттрибут add_link
	ra.live('click', document.querySelectorAll('[add_link=true]'), add_link);
	ra.live('click', document.querySelectorAll('[delete_link=true]'), delete_link);

	//сортировка картинок
	$("#imagelist").sortable();
	
	// Удаление изображений
	$(".images a.delete").click( function() {
		$("input[name='delete_image']").val('1');
		$(this).closest("ul").fadeOut(200, function() { $(this).remove(); });
		return false;
	});
  
});

// ссылка на добавление
function add_link(e) {
	"use strict";
	console.log(e.target);
	let link_id = e.target.getAttribute('link_id');
	console.log(link_id);
	let t = document.querySelector('[container=true][container_id=' + link_id + ']');
	let n = t.cloneNode(true);
	t.parentNode.insertBefore(n,t);
	n.setAttribute('style','');
	ra.live('click', n.querySelector('[delete_link=true]'), delete_link);
}

//функция для удаления
function delete_link(e) {
	"use strict";
	console.log(e.target);
	ra.search_tree('attribute', 'container', e.target).then(function(v){console.log(v); v.remove()});
}
</script>
 
{/literal}


{foreach $status as $s}
{if $s['status'] === 3}
{$s_class = "message message_success"}
{elseif $s['status'] === 2}
{$s_class = "message message_warning"}
{elseif $s['status'] === 1}
{$s_class = "message message_error"}
{/if}

<div class="{$s_class}">
	<span class="text">{$s['message']}</span>
</div>
{/foreach}

{if isset($post['trans'])}
<div class="message message_success">
	<span class="text"><a href="/blog/{$post['trans']}">Открыть на сайте</a></span>
</div>
{/if}

<!-- Основная форма -->
<form method=post id=product enctype="multipart/form-data">
<input type=hidden name="session_id" value="{$smarty.session.id}">
	<div id="name">
		<input class="name" name=post[name] type="text" value="{$post['name']|escape}"/> 
		<input name=id type="hidden" value="{$post['id']|escape}"/> 
		<div class="checkbox">
			<input name=post[visible] value='1' type="checkbox" id="active_checkbox" {if $post['visible']}checked{/if}/> <label for="active_checkbox">Активна</label>
		</div>

	</div> 

	<!-- Левая колонка свойств товара -->
	<div class="column_left">
			
		<!-- Параметры страницы -->
		<div class="block">
			<ul>
				<li><label class=property>Дата</label><input type=text name=post[date] value='{$post['date']|date}'></li>
			</ul>
		</div>
		<div class="block layer">
		<!-- Параметры страницы (The End)-->
			<h2>Параметры страницы</h2>
		<!-- Параметры страницы -->
			<ul>
				<li><label class=property>Адрес</label><div class="page_url"> /blog/</div><input name=post[trans] class="page_url" type="text" value="{$post['trans']|escape}" /></li>
				<li><label class=property>Заголовок</label><input name=post[meta_title] type="text" value="{$post['meta_title']|escape}" /></li>
				<li><label class=property>Ключевые слова</label><input name=post[meta_keywords]  type="text" value="{$post['meta_keywords']|escape}" /></li>
				<li><label class=property>Описание</label><textarea name=post[meta_description] />{$post['meta_description']|escape}</textarea></li>
			</ul>
		</div>
		<!-- Параметры страницы (The End)-->


			
	</div>
	<!-- Левая колонка свойств товара (The End)--> 
	
	<!-- Правая колонка свойств товара -->	
	<div class="column_right">

		<!-- Изображения -->	
		<div class="block layer images">
			<h2>Изображения 
			</h2>
			<ul id="imagelist">
			{if $images}
				{foreach $images as $image_id=>$image}
				<li container="true">
					<a delete_link="true" class="delete"></a>
					<a href="/img/blog/{$image['basename']}">
						<img id="{$image_id}" src="{$image['basename']|resize:blog:$image_id:100:100}" alt="" />
					</a>
					<input type="hidden" name="images[]" value="{$image_id}">
				</li>
				{/foreach}
			{/if}
			</ul>

			<div class="block">

				<!-- dropzone для перетаскивания изображений -->	
<!--
				{if isset($category['id'])}
					<div id="holder" type="categories" product_id="{$product['id']}">
						<div class="holder__text">Тяни файл сюда</div>
					</div> 
					<p id="upload" class="hidden"><label>Drag & drop not supported, but you can still upload via this input field:<br><input type="file"></label></p>
					<p id="filereader">File API & FileReader API not supported</p>
					<p id="formdata">XHR2's FormData is not supported</p>
					<p id="progress">XHR2's upload progress isn't supported</p>
					<p>Upload progress: <progress id="uploadprogress" max="100" value="0">0</progress></p>

				{/if}
-->
				<!-- dropzone для перетаскивания изображений (The End) -->

				<span class=upload_image><i  add_link="true" link_id="upload_image" class="dash_link" id="upload_image">Добавить изображение</i></span>
				 или 
				<span class=add_image_url><i class="dash_link"  add_link="true" link_id="image_url_upload">загрузить из интернета</i></span>
				
				<!-- Шаблон для кнопки загрузки нового изображения -->
				<div container="true" container_id="upload_image" style="display: none;">
					<input name=new_images[] type=file multiple  accept='image/jpeg,image/png,image/gif'>
					<a delete_link="true" class="delete"></a>
				</div>
				<!-- Шаблон для кнопки загрузки нового изображения (The end) -->
			</div>
		</div>
		<!-- Изображения  END -->


		
	</div>
	<!-- Правая колонка свойств товара (The End)--> 
	
	<!-- Описагние товара -->
	<div class="block layer">
		<h2>Краткое описание</h2>
		<textarea name=post[annotation] class='editor_small'>{$post['annotation']|escape}</textarea>
	</div>
		
	<div class="block">
		<h2>Полное  описание</h2>
		<textarea name=post[text]  class='editor_large'>{$post['text']|escape}</textarea>
	</div>
	<!-- Описание товара (The End)-->
	<input class="button_green button_save" type="submit" name="" value="Сохранить" />
	
</form>
<!-- Основная форма (The End) -->
