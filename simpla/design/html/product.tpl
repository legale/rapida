{capture name=tabs}
	<li class="active"><a href="{url module=ProductsAdmin category_id=$product['category_id'] return=null brand_id=null id=null}">Товары</a></li>
	{if isset($userperm['categories'])}<li><a href="?module=CategoriesAdmin">Категории</a></li>{/if}
	{if isset($userperm['brands'])}<li><a href="?module=BrandsAdmin">Бренды</a></li>{/if}
	{if isset($userperm['features'])}<li><a href="?module=FeaturesAdmin">Свойства</a></li>{/if}
{/capture}

{if $product['id']}
{$meta_title = $product['name'] scope=parent}
{else}
{$meta_title = 'Новый товар' scope=parent}
{/if}

{* Подключаем Tiny MCE *}
{include file='tinymce_init.tpl'}

{* On document load *}
{literal}
<!--
<script src="design/js/autocomplete/jquery.autocomplete-min.js"></script>
-->

<script>
document.addEventListener("DOMContentLoaded", ready);


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

function ready(){
	"use strict";
	
	//включаем обработчик на все элементы, у которых есть аттрибут add_link
	live('click', document.querySelectorAll('[add_link=true]'), add_link);
	live('click', document.querySelectorAll('[delete_link=true]'), delete_link);

	/* DragnDrop */
	var holder = document.getElementById('holder'),
	product_id,
	tests = {
	  filereader: typeof FileReader != 'undefined',
	  dnd: 'draggable' in document.createElement('span'),
	  formdata: !!window.FormData,
	  progress: "upload" in new XMLHttpRequest
	}, 
	support = {
	  filereader: document.getElementById('filereader'),
	  formdata: document.getElementById('formdata'),
	  progress: document.getElementById('progress')
	},
	acceptedTypes = {
	  'image/png': true,
	  'image/jpeg': true,
	  'image/gif': true
	},
	progress = document.getElementById('uploadprogress'),
	fileupload = document.getElementById('upload');
	if(holder !== null){
		product_id = holder.getAttribute('product_id');
	} else {
		return false;
	}


	"filereader formdata progress".split(' ').forEach(function (api) {
	  if (tests[api] === false) {
		support[api].className = 'fail';
	  } else {
		// FFS. I could have done el.hidden = true, but IE doesn't support
		// hidden, so I tried to create a polyfill that would extend the
		// Element.prototype, but then IE10 doesn't even give me access
		// to the Element object. Brilliant.
		support[api].className = 'hidden';
	  }
	});

	function previewfile(file) {
	  if (tests.filereader === true && acceptedTypes[file.type] === true) {
		var reader = new FileReader();
		reader.onload = function (event) {
		  var image = new Image();
		  image.src = event.target.result;
		  image.width = 100; // a fake resize
		  imagelist.appendChild(image);
		};

		reader.readAsDataURL(file);
	  }  else {
		holder.innerHTML += '<p>Uploaded ' + file.name + ' ' + (file.size ? (file.size/1024|0) + 'K' : '');
		console.log(file);
	  }
	}

	function readfiles(files) {
		//debugger;
		//console.log(files);
		var formData = tests.formdata ? new FormData() : null;
		for (var i = 0; i < files.length; i++) {
		  if (tests.formdata){
			   formData.append('file[]', files[i]);
		   }
			formData.append('product_id', product_id );
		}
		//console.log(formData);

		// now post a new XHR request
		if (tests.formdata) {
		  var xhr = new XMLHttpRequest();
		  xhr.open('POST', '/simpla/ajax/upload_image.php');
		  xhr.onload = function() {
			progress.value = progress.innerHTML = 100;
			for (var i = 0; i < files.length; i++) {
				previewfile(files[i]);
			}
		  };

		  if (tests.progress) {
			xhr.upload.onprogress = function (event) {
			  if (event.lengthComputable) {
				var complete = (event.loaded / event.total * 100 | 0);
				progress.value = progress.innerHTML = complete;
			  }
			}
		  }

		  xhr.send(formData);
		}
	}

	if (tests.dnd) { 
	  holder.ondragover = function () { this.className = 'hover'; return false; };
	  holder.ondragend = function () { this.className = ''; return false; };
	  holder.ondrop = function (e) {
		this.className = '';
		e.preventDefault();
		readfiles(e.dataTransfer.files);
	  }
	} else {
		alert('else');
	  fileupload.className = 'hidden';
	  fileupload.querySelector('input').onchange = function () {
		readfiles(this.files);
	  };
	}
	/* DragnDrop (The end) */
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
<form method="post" id="product" enctype="multipart/form-data">
<input type="hidden" name="session_id" value="{$smarty.session.id}">

<a class="link" target="_blank" href="../products/{$product['url']}">Открыть товар на сайте</a>
	<div id="name">
		<input class="name" name="save[product][name]" type="text" value="{$product['name']|escape}"/> 
		<input name="save[product][id]" type="hidden" value="{$product['id']|escape}"/> 
		<div class="checkbox">
			<input name="save[product][visible]" value="1" type="checkbox" id="active_checkbox" {if $product['visible']}checked{/if}/> <label for="active_checkbox">Активен</label>
		</div>
		<div class="checkbox">
			<input name="save[product][featured]" value="1" type="checkbox" id="featured_checkbox" {if $product['featured']}checked{/if}/> <label for="featured_checkbox">Рекомендуемый</label>
		</div>
	</div> 
	
	<div id="product_brand" {if !isset($brands)}style="display:none;"{/if}>
		<label>Бренд</label>
		<select name="save[product][brand_id]">
			<option value="0" {if !$product['brand_id']}selected{/if} brand_name=''>Не указан</option>
			{foreach $brands as $brand}
				<option value="{$brand['id']}" {if $product['brand_id'] == $brand['id']}selected{/if} brand_name='{$brand['name']|escape}'>{$brand['name']|escape}</option>
			{/foreach}
		</select>
	</div>
	
	
	<div id="product_categories">
		<label>Категория</label>
		<div>
			<ul>
			{function name=category_select level=0}
			{foreach $categories as $cat}
					<option value="{$cat['id']}" {if $cat['id'] == $selected_id}selected{/if} category_name="{$cat['name']|escape}">{section name=sp loop=$level}&nbsp;&nbsp;&nbsp;&nbsp;{/section}{$cat['name']|escape}</option>
					{category_select categories=$cat['subcategories'] selected_id=$selected_id  level=$level+1}
			{/foreach}
			{/function}
			{if isset($cats)}
				{foreach $product['cats'] as $pc}
				<li container="true">
					<select name="save[cats][]">
						{category_select categories=$cats selected_id=$pc['category_id']}
					</select>
					<a delete_link="true" class="icons delete"></a>
				</li>
				{/foreach}
			{/if}
			
			<!-- Шаблон для новой категории -->
				<li container="true" container_id="category" style="display:none;">
					<select name="save[cats][]">
						<option value="{$cat['id']}" category_name="">-</option>
						{category_select categories=$cats selected_id=0}
					</select>
					<a delete_link="true" class="icons delete"></a>
				</li>
			<!-- Шаблон для новой категории (The end) -->

				<span><i add_link="true" link_id="category" class="dash_link">Дополнительная категория</i></span>
			</ul>
		</div>
	</div>


	<!-- Варианты товара -->
	<div id="variants_block">
		<ul id="header">
			<li class="variant_move"></li>
			<li class="variant_name">Название варианта</li>	
			<li class="variant_sku">Артикул</li>	
			<li class="variant_price">Цена, {$currency['sign']}</li>	
			<li class="variant_price">Цена1, {$currency['sign']}</li>	
			<li class="variant_price">Цена2, {$currency['sign']}</li>	
			<li class="variant_price">Цена3, {$currency['sign']}</li>	
			<li class="variant_discount">Старая, {$currency['sign']}</li>	
			<li class="variant_amount">Кол-во</li>
		</ul>
		<div id="variants">
		<!-- Это шаблон строки нового варианта -->
		<ul container="true" container_id="variant" style='display:none;'>
			<li class="variant_move"><div class="move_zone"></div></li>
			<input name="save[variants][product_id][]" type="hidden" value="{$product['id']}" />
			<input name="save[variants][id][]" type="hidden" value="" />
			<li class="variant_name"><input name="save[variants][name][]" type="" value="" /></li>
			<li class="variant_sku"><input name="save[variants][sku][]" type="" value="" /></li>
			<li class="variant_price"><input  name="save[variants][price][]" type="" value="" /></li>
			<li class="variant_price"><input  name="save[variants][price1][]" type="" value="" /></li>
			<li class="variant_price"><input  name="save[variants][price2][]" type="" value="" /></li>
			<li class="variant_price"><input  name="save[variants][price3][]" type="" value="" /></li>
			<li class="variant_discount"><input name="save[variants][old_price][]" type="" value="" /></li>
			<li class="variant_amount"><input name="save[variants][stock][]" type="" value="∞" />{$settings->units}</li>
			<a delete_link="true" class="icons delete"></a>
		</ul>
		<!-- Это шаблон строки нового варианта (The end) -->

		{foreach $product['variants'] as $v}
		<ul container="true">
			<li class="variant_move"><div class="move_zone"></div></li>
			<li class="variant_name">  
				<input name="save[variants][product_id][]" type="hidden" value="{$product['id']|escape}" />
				<input name="save[variants][id][]" type="hidden" value="{$v['id']|escape}" />
				<input name="save[variants][name][]" type="text" value="{$v['name']|escape}" />
			</li>
			<li class="variant_sku">  
				<input name="save[variants][sku][]" type="text" value="{$v['sku']|escape}" />
			</li>
			<li class="variant_price">
				<input name="save[variants][price][]" type="text" value="{$v['price']|escape}" />
			</li>
			<li class="variant_price">
				<input name="save[variants][price1][]" type="text" value="{$v['price1']|escape}" />
			</li>
			<li class="variant_price">
				<input name="save[variants][price2][]" type="text" value="{$v['price2']|escape}" />
			</li>
			<li class="variant_price">
				<input name="save[variants][price3][]" type="text" value="{$v['price3']|escape}" />
			</li>
			<li class="variant_discount"> 
				<input name="save[variants][old_price][]" type="text" value="{$v['old_price']|escape}" />
			</li>
			<li class="variant_amount"> 
				<input name="save[variants][stock][]" type="text" value="{$v['stock']|escape}" />
			{$settings->units}</li>
			<a delete_link="true" class="icons delete"></a>
		</ul>
		{/foreach}		
		</div>


		<input class="button_green button_save" type="submit" name="" value="Сохранить" />
		<span><i  add_link="true" link_id="variant" class="dash_link">Добавить вариант</i></span>
	</div>
	<!-- Варианты товара (The End)--> 
	
	<!-- Левая колонка свойств товара -->
	<div class="column_left">
			
		<!-- Параметры страницы -->
		<div class="block layer">
			<h2>Параметры страницы</h2>
			<ul class="list">
				<li><label class=property>Адрес</label>
					<div class="page_url"> /products/</div>
					<input name="save[product][url]" class="page_url" type="text" value="{$product['url']|escape}" />
				</li>
				<li><label class=property>Заголовок</label>
					<input name="save[product][meta_title]"  type="text" value="{$product['meta_title']|escape}" />
				</li>
				<li>
					<label class=property>Ключевые слова</label>
					<input name="save[product][meta_keywords]"  type="text" value="{$product['meta_keywords']|escape}" />
				</li>
				<li>
					<label class=property>Описание</label>
					<textarea name="save[product][meta_description]"  />{$product['meta_description']|escape}</textarea>
				</li>
			</ul>
		</div>
		<!-- Параметры страницы (The End)-->


	</div>
	<!-- Левая колонка свойств товара (The End)--> 
	
	<!-- Правая колонка свойств товара -->	
	<div class="column_right">
		
		<!-- Изображения товара -->	
		<div class="block layer images">
			<h2>Изображения товара
			</h2>
			<ul id="imagelist">
			{if $product['images']}
			{foreach $product['images'] as $image}
				<li container="true">
					<a delete_link="true" class="delete"></a>
					<img id="{$image['id']}" src="{$image['filename']|resize:100:100}" alt="" />
					<input type="hidden" name="save[images][]" value="{$image['id']}">
				</li>{/foreach}
			{/if}
			</ul>

			<div class="block">

				<!-- dropzone для перетаскивания изображений -->	
				{if isset($product['id'])}
					<div id="holder" product_id="{$product['id']}">
						<div class="holder__text">Тяни файл сюда</div>
					</div> 
					<p id="upload" class="hidden"><label>Drag & drop not supported, but you can still upload via this input field:<br><input type="file"></label></p>
					<p id="filereader">File API & FileReader API not supported</p>
					<p id="formdata">XHR2's FormData is not supported</p>
					<p id="progress">XHR2's upload progress isn't supported</p>
					<p>Upload progress: <progress id="uploadprogress" max="100" value="0">0</progress></p>

				{/if}
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

		<div class="block layer">
			<h2>Связанные товары</h2>
			<div id=list class="sortable related_products">
				{if $product['related']}
				{foreach $product['related'] as $p}
				<div class="row">
					<input type="hidden" name="save[related][]" value="{$p['id']}">
					<div class="move cell">
						<div class="move_zone"></div>
					</div>
					<div class="image cell">
					<a href="{url id=$p['id']}">
					<img class="product_icon" src="{$p['image']|resize:35:35}">
					</a>
					</div>
					<div class="name cell">
					<a href="{url id=$p['id']}">{$p['name']}</a>
					</div>
					<div class="icons cell">
					<a delete_link="true" class="delete"></a>
					</div>
					<div class="clear"></div>
				</div>
				{/foreach}
				{/if}
				
				{* шаблон блока для нового сязанного товара *}
				<div container="true" container_id="related_product" class="row" style='display:none;'>
					<input type="hidden" name="save[related][]" value="">
					<div class="move cell">
						<div class="move_zone"></div>
					</div>
					<div class="image cell">
					<img class="product_icon" src=''>
					</div>
					<div class="name cell">
					<a class="related_product_name" href=""></a>
					</div>
					<div class="icons cell">
					<a delete_link="true" class="delete"></a>
					</div>
					<div class="clear"></div>
				</div>
				{* шаблон для нового сязанного товара (The end) *}
			</div>
			<input type="text" id="related_products_autocomplete" class="input_autocomplete" placeholder='Выберите товар чтобы добавить его'>

{* тут заводим скрипт для автозаполнения связанных товаров *}
{literal}
<script>
var res;
var related = new autoComplete({
	selector: '#related_products_autocomplete',
	minChars: 2,
	delay: 200,
	source: function(term, suggest){
		term = term.toLowerCase();
apiAjax( 
{'class': 'products', 'method': 'get_products_ids', 'args': 
	{filter:
			{
	'keyword': term 
			}
	}
} , function(e){
  res = JSON.parse(e);
  console.log(res);
  var data = [];
  for(var k in res){
     data.push(res[k].id);
  }
  console.log(data);
  suggest(data);

  });

	},
    renderItem: function (id, search){
		console.log('id: ' + id);
		var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");
		return '<div class="autocomplete-suggestion" data-id="' + res[id].id + '" data-val="' + res[id].name + '">' + res[id].name.replace(re, "<b>$1</b>") + '</div>';
    },
    onSelect: function(e, term, item){
        console.log(item);
        var el = res[item.getAttribute('data-id')];
        add_item_related(el);
        return item;
    }
});

// ссылка на добавление
function add_item_related(el) {
	"use strict";
	console.log(el);
	let t = document.querySelector('[container=true][container_id=related_product]')
	let n = t.cloneNode(true);
	t.parentNode.insertBefore(n,t);
	n.setAttribute('style','');
	n.querySelector('input[type=hidden]').setAttribute('value', el.id);
	n.querySelector('a[class=related_product_name]').innerHTML = el.name;
	live('click', n.querySelector('[delete_link=true]'), delete_link);
}


</script>
{/literal}

		</div>

		<input class="button_green button_save" type="submit" name="" value="Сохранить" />
		
	</div>
	<!-- Правая колонка свойств товара (The End)--> 
			<!-- Свойства товара -->
		<div class="block layer">
			<h2>Свойства товара</h2>
			{if isset($product['options'])}
			<ul class="prop_ul">
				{foreach $product['options'] as $fid=>$o}
				<li>
					<label class="property inrow" fid="{$fid}" vid="{$o['vid']}">{$features[$fid]['name']}</label>
					<input type="hidden" name="save[options][fid][]" value="{$fid}"/>
					<input type="hidden" name="save[options][fname][]" value="{$features[$fid]['name']}"/>
					<input class="inrow" type="text" name="save[options][val][]" value="{$o['val']}"/>
				</li>
				{/foreach}
			</ul>
			{/if}

			<!-- Шаблон для новой опции -->
			<ul container="true" container_id="option" style="display: none;">
				<li>
					<input type="hidden" name="save[options][fid][]" value=""/>
					<label class="property inrow">
						<input type="text" class="inrow" name="save[options][fname][]">
					</label>
					<input class="inrow" type="text" name="save[options][val][]" />
				</li>
			</ul>
			<!-- Шаблон для новой опции (The end) -->

			<div class="clear"></div>
			<input class="button_green button_save" type="submit" name="" value="Сохранить" />
			<span><i add_link="true" link_id="option" class="dash_link" id="add_new_feature">Добавить новое свойство</i></span>
		</div>
		
		<!-- Свойства товара (The End)-->
	<!-- Описагние товара -->
	<div class="block layer">
		<h2>Краткое описание</h2>
		<textarea name="save[product][annotation]" class="editor_small">{$product['annotation']|escape}</textarea>
	</div>
		
	<div class="block">		
		<h2>Полное  описание</h2>
		<textarea name="save[product][body]" class="editor_large">{$product['body']|escape}</textarea>
	</div>
	<!-- Описание товара (The End)-->
	<input class="button_green button_save" type="submit" name="" value="Сохранить" />
	
</form>
<!-- Основная форма (The End) -->

