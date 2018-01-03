{* Вкладки *}
{capture name=tabs}
	{if isset($userperm['products'])}<li><a href="?module=ProductsAdmin">Товары</a></li>{/if}
	<li class="active"><a href="?module=CategoriesAdmin">Категории</a></li>
	{if isset($userperm['brands'])}<li><a href="?module=BrandsAdmin">Бренды</a></li>{/if}
	{if isset($userperm['features'])}<li><a href="?module=FeaturesAdmin">Свойства</a></li>{/if}
{/capture}

{if $category['id']}
{$meta_title = $category['name'] scope=parent}
{else}
{$meta_title = 'Новая категория' scope=parent}
{/if}

{* Подключаем Tiny MCE *}
{include file='tinymce_init.tpl'}

{* On document load *}
{literal}
<script src="design/js/jquery/jquery.js"></script>
<script src="design/js/jquery/jquery-ui.min.js"></script>
<script src="design/js/autocomplete/jquery.autocomplete-min.js"></script>
<style>
.autocomplete-w1 { background:url(img/shadow.png) no-repeat bottom right; pos:absolute; top:0px; left:0px; margin:6px 0 0 6px; /* IE6 fix: */ _background:none; _margin:1px 0 0 0; }
.autocomplete { border:1px solid #999; background:#FFF; cursor:default; text-align:left; overflow-x:auto; min-width: 300px; overflow-y: auto; margin:-6px 6px 6px -6px; /* IE6 specific: */ _height:350px;  _margin:0; _overflow-x:hidden; }
.autocomplete .selected { background:#F0F0F0; }
.autocomplete div { padding:2px 5px; white-space:nowrap; }
.autocomplete strong { font-weight:normal; color:#3399FF; }
</style>

<script>
$(function() {
	//включаем обработчик на все элементы, у которых есть аттрибут add_link
	live('click', document.querySelectorAll('[add_link=true]'), add_link);
	live('click', document.querySelectorAll('[delete_link=true]'), delete_link);

	//сортировка картинок
	$("#imagelist").sortable();
	
	// Удаление изображений
	$(".images a.delete").click( function() {
		$("input[name='delete_image']").val('1');
		$(this).closest("ul").fadeOut(200, function() { $(this).remove(); });
		return false;
	});

	// Автозаполнение мета-тегов
	meta_title_touched = true;
	meta_keywords_touched = true;
	meta_description_touched = true;
	url_touched = true;
	
	if($('input[name="save[category][meta_title]"]').val() == generate_meta_title() || $('input[name="save[category][meta_title]"]').val() == '')
		meta_title_touched = false;
	if($('input[name="save[category][meta_keywords]"]').val() == generate_meta_keywords() || $('input[name="save[category][meta_keywords]"]').val() == '')
		meta_keywords_touched = false;
	if($('textarea[name="save[category][meta_description]"]').val() == generate_meta_description() || $('textarea[name="save[category][meta_description]"]').val() == '')
		meta_description_touched = false;
	if($('input[name="save[category][url]"]').val() == generate_url() || $('input[name="save[category][url]"]').val() == '')
		url_touched = false;
		
	$('input[name="meta_title"]').change(function() { meta_title_touched = true; });
	$('input[name="meta_keywords"]').change(function() { meta_keywords_touched = true; });
	$('textarea[name="meta_description"]').change(function() { meta_description_touched = true; });
	$('input[name="url"]').change(function() { url_touched = true; });
	
	$('input[name="name"]').keyup(function() { set_meta(); });
	  
});

function set_meta()
{
	if(!meta_title_touched)
		$('input[name="meta_title"]').val(generate_meta_title());
	if(!meta_keywords_touched)
		$('input[name="meta_keywords"]').val(generate_meta_keywords());
	if(!meta_description_touched)
		$('textarea[name="meta_description"]').val(generate_meta_description());
	if(!url_touched)
		$('input[name="url"]').val(generate_url());
}

function generate_meta_title()
{
	name = $('input[name="name"]').val();
	return name;
}

function generate_meta_keywords()
{
	name = $('input[name="name"]').val();
	return name;
}

function generate_meta_description()
{
	if(typeof(tinyMCE.get("description")) =='object')
	{
		description = tinyMCE.get("description").getContent().replace(/(<([^>]+)>)/ig," ").replace(/(\&nbsp;)/ig," ").replace(/^\s+|\s+$/g, '').substr(0, 512);
		return description;
	}
	else
		return $('textarea[name=description]').val().replace(/(<([^>]+)>)/ig," ").replace(/(\&nbsp;)/ig," ").replace(/^\s+|\s+$/g, '').substr(0, 512);
}

function generate_url()
{
	url = $('input[name="name"]').val();
	url = url.replace(/[\s]+/gi, '-');
	url = translit(url);
	url = url.replace(/[^0-9a-z_\-]+/gi, '').toLowerCase();	
	return url;
}

function translit(str)
{
	var ru=("А-а-Б-б-В-в-Ґ-ґ-Г-г-Д-д-Е-е-Ё-ё-Є-є-Ж-ж-З-з-И-и-І-і-Ї-ї-Й-й-К-к-Л-л-М-м-Н-н-О-о-П-п-Р-р-С-с-Т-т-У-у-Ф-ф-Х-х-Ц-ц-Ч-ч-Ш-ш-Щ-щ-Ъ-ъ-Ы-ы-Ь-ь-Э-э-Ю-ю-Я-я").split("-")   
	var en=("A-a-B-b-V-v-G-g-G-g-D-d-E-e-E-e-E-e-ZH-zh-Z-z-I-i-I-i-I-i-J-j-K-k-L-l-M-m-N-n-O-o-P-p-R-r-S-s-T-t-U-u-F-f-H-h-TS-ts-CH-ch-SH-sh-SCH-sch-'-'-Y-y-'-'-E-e-YU-yu-YA-ya").split("-")   
 	var res = '';
	for(var i=0, l=str.length; i<l; i++)
	{ 
		var s = str.charAt(i), n = ru.indexOf(s); 
		if(n >= 0) { res += en[n]; } 
		else { res += s; } 
    } 
    return res;  
}

// ссылка на добавление
function add_link(e) {
	"use strict";
	console.log(e.target);
	let link_id = e.target.getAttribute('link_id');
	console.log(link_id);
	let t = document.querySelector('[container=true][container_id=' + link_id + ']')
	let n = t.cloneNode(true);
	t.parentNode.insertBefore(n,t);
	n.setAttribute('style','');
	live('click', n.querySelector('[delete_link=true]'), delete_link);
}

//функция для удаления
function delete_link(e) {
	"use strict";
	console.log(e.target);
	search_tree('attribute', 'container', e.target).then(function(v){console.log(v); v.remove()});
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


<!-- Основная форма -->
<form method=post id=category enctype="multipart/form-data">
<input type=hidden name="session_id" value="{$smarty.session.id}">
	<div id="name">
		<input class="name" name='save[category][name]' type="text" value="{$category['name']|escape}"/> 
		<input name='save[category][id]' type="hidden" value="{$category['id']|escape}"/> 
		<div class="checkbox">
			<input name='save[category][visible]' value='1' type="checkbox" id="active_checkbox" {if $category['visible']}checked{/if}/> <label for="active_checkbox">Активна</label>
		</div>
	</div> 

	<div id="product_categories">
			<select name="parent_id">
				<option value='0'>Корневая категория</option>
				{function name=category_select level=0}
				{foreach $cats as $cat}
					{if $category['id'] != $cat['id']}
						<option value='save[category]{$cat['id']}' {if $category['parent_id'] == $cat['id']}selected{/if}>{section name=sp loop=$level}&nbsp;&nbsp;&nbsp;&nbsp;{/section}{$cat['name']}</option>
						{category_select cats=$cat['subcategories'] level=$level+1}
					{/if}
				{/foreach}
				{/function}
				{category_select cats=$categories}
			</select>
	</div>
		
	<!-- Левая колонка -->
	<div class="column_left">
			
		<!-- Параметры страницы -->
		<div class="block layer">
			<h2>Параметры страницы</h2>
			<ul>
				<li><label class=property>Адрес</label><div class="page_url">/catalog/</div><input name="save[category][url]" class="page_url" type="text" value="{$category['url']|escape}" /></li>
				<li><label class=property>Заголовок</label><input name="save[category][meta_title]" class="simpla_inp" type="text" value="{$category['meta_title']|escape}" /></li>
				<li><label class=property>Ключевые слова</label><input name="save[category][meta_keywords]" class="simpla_inp" type="text" value="{$category['meta_keywords']|escape}" /></li>
				<li><label class=property>Описание</label><textarea name="save[category][meta_description]" class="simpla_inp">{$category['meta_description']|escape}</textarea></li>
			</ul>
		</div>
		<!-- Параметры страницы (The End)-->
		
	</div>
	<!-- Левая колонка  (The End)--> 
	
	<!-- Правая колонка -->	
	<div class="column_right">
		
		<!-- Изображения -->	
		<div class="block layer images">
			<h2>Изображения 
			</h2>
			<ul id="imagelist">
			{if $category['images']}
				{foreach $category['images'] as $image_id=>$image}
				<li container="true">
					<a delete_link="true" class="delete"></a>
					<img id="{$image_id}" src="{$image['basename']|resize:categories:$image_id:100:100}" alt="" />
					<input type="hidden" name="save[images][]" value="{$image_id}">
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
	<!-- Правая колонка (The End)--> 

	<!-- Описагние категории -->
	<div class="block layer">
		<h2>Описание</h2>
		<textarea name="save[category][description]" class="editor_large">{$category['description']|escape}</textarea>
	</div>
	<!-- Описание категории (The End)-->
	<input class="button_green button_save" type="submit" name="" value="Сохранить" />
	
</form>
<!-- Основная форма (The End) -->

