{* Вкладки *}
{capture name=tabs}
    {if isset($userperm['products'])}
        <li><a href="?module=ProductsAdmin">Товары</a></li>
    {/if}
    <li class="active"><a href="?module=CategoriesAdmin">Категории</a></li>
    {if isset($userperm['brands'])}
        <li><a href="?module=BrandsAdmin">Бренды</a></li>
    {/if}
    {if isset($userperm['features'])}
        <li><a href="?module=FeaturesAdmin">Свойства</a></li>
    {/if}
{/capture}

{if $category['id']}
    {$meta_title = $category['name'] scope=root}
{else}
    {$meta_title = 'Новая категория' scope=root}
{/if}

{* Подключаем Tiny MCE *}
{include file='tinymce_init.tpl'}

{* On document load *}
{literal}
    <script src="design/js/jquery/jquery.js"></script>
    <script src="design/js/jquery/jquery-ui.min.js"></script>
    <script src="design/js/autocomplete/jquery.autocomplete-min.js"></script>
    <style>
        .autocomplete-w1 {
            background: url(img/shadow.png) no-repeat bottom right;
            pos: absolute;
            top: 0px;
            left: 0px;
            margin: 6px 0 0 6px; /* IE6 fix: */
            _background: none;
            _margin: 1px 0 0 0;
        }

        .autocomplete {
            border: 1px solid #999;
            background: #FFF;
            cursor: default;
            text-align: left;
            overflow-x: auto;
            min-width: 300px;
            overflow-y: auto;
            margin: -6px 6px 6px -6px; /* IE6 specific: */
            _height: 350px;
            _margin: 0;
            _overflow-x: hidden;
        }

        .autocomplete .selected {
            background: #F0F0F0;
        }

        .autocomplete div {
            padding: 2px 5px;
            white-space: nowrap;
        }

        .autocomplete strong {
            font-weight: normal;
            color: #3399FF;
        }
    </style>
    <script>
        $(function () {
            //включаем обработчик на все элементы, у которых есть аттрибут add_link
            ra.live('click', document.querySelectorAll('[add_link=true]'), add_link);
            ra.live('click', document.querySelectorAll('[delete_link=true]'), delete_link);

            //сортировка картинок
            $("#imagelist").sortable();

            // Удаление изображений
            $(".images a.delete").click(function () {
                $("input[name='delete_image']").val('1');
                $(this).closest("ul").fadeOut(200, function () {
                    $(this).remove();
                });
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
            t.parentNode.insertBefore(n, t);
            n.setAttribute('style', '');
            ra.live('click', n.querySelector('[delete_link=true]'), delete_link);
        }

        //функция для удаления
        function delete_link(e) {
            "use strict";
            console.log(e.target);
            ra.search_tree('attribute', 'container', e.target).then(function (v) {
                console.log(v);
                v.remove()
            });
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
<form method="post" class="product" enctype="multipart/form-data">
    <input type=hidden name="session_id" value="{$smarty.session.id}">

    <div class="columns is-variable is-1">
        <div class="column is-10">
            <div class="name">
                <label>Название</label>
                <input class="name" name='save[category][name]' type="text"
                                              value="{$category['name']|escape}"/>
            </div>
        </div>
        <div class="column">
            <div class="checkbox">
                <input name='save[category][enabled]' value='0' type="hidden">
                <input name='save[category][enabled]' value='1' type="checkbox" id="active_checkbox"
                       {if $category['enabled']}checked{/if}/> <label for="active_checkbox">Включена</label>
            </div>
        </div>
    </div>

    <div class="columns is-variable is-1">
        <div class="column is-10">
            <label>Название в ед. числе</label><input class="name" name='save[category][name_singular]' type="text"
                                                      value="{$category['name_singular']|escape}"/>
            <input name='save[category][id]' type="hidden" value="{$category['id']|escape}"/>
        </div>
        <div class="column">
            <div class="checkbox">
                <input name='save[category][visible]' value='0' type="hidden">
                <input name='save[category][visible]' value='1' type="checkbox" id="active_checkbox" {if $category['visible']}checked{/if}/>
                <label for="active_checkbox">Видимость</label>
            </div>
        </div>

    </div>

	{function name=category_select level=0}
		{foreach $cats as $cat}
			{if $category['id'] == $cat['id']}{continue}{/if}
				<option value="{$cat['id']}"
				{if $selected == $cat['id']}selected{/if}>{section name=sp loop=$level}&nbsp;&nbsp;{/section}{$cat['name']}</option>
				{if isset($cat['subcategories'])}
					{category_select cats=$cat['subcategories'] level=$level+1}
				{/if}
		{/foreach}
	{/function}





    <!-- Левая колонка -->
    <div class="column_left">

        <label>Дочерние виртуальные категории</label>
        {if !empty($category['vchildren'])}
        <ol style="list-style: decimal">
            {foreach $category['vchildren'] as $cid}<li cid="{$cid}">{$cats[$cid]['name']}</li>{/foreach}
        </ol>
        {/if}

        <!-- Параметры страницы -->
        <div class="block layer">
            <h2>Параметры страницы</h2>
            <ul>
                <li><label class=property>Адрес</label>
                    <div class="page_url">/catalog/</div>
                    <input name="save[category][trans]" class="page_url" type="text"
                           value="{$category['trans']|escape}"/>
                </li>
                <li><label class=property>Адрес2</label>
                    <div class="page_url">/catalog/</div>
                    <input name="save[category][trans2]" class="page_url" type="text"
                           value="{$category['trans2']|escape}"/></li>

                <li>
                    <label class=property>Title</label>
                    <textarea name="save[category][meta_title]" class="simpla_inp" type="text">{$category['meta_title']|escape}</textarea>
                </li>
                <li>
                    <label class=property>meta keywords</label>
                    <textarea name="save[category][meta_keywords]" class="simpla_inp" type="text">{$category['meta_keywords']|escape}</textarea>
                </li>
                <li>
                    <label class=property>meta description</label>
                    <textarea name="save[category][meta_description]"
                              class="simpla_inp">{$category['meta_description']|escape}</textarea>
                </li>
            </ul>
        </div>
        <!-- Параметры страницы (The End)-->

    </div>
    <!-- Левая колонка  (The End)-->

    <!-- Правая колонка -->
    <div class="column_right">



        <label class=property>Родительская категория</label>
        <div сlass="product_categories">
            <select name="save[category][parent_id]">
                <option value='0'>Корневая категория</option>
                {category_select selected=$category['parent_id'] cats=$cats}
            </select>
        </div>

        <label class=property>Виртуальная родительская категория</label>
        <div сlass="product_categories">
            <select name="save[category][vparent_id]">
                <option value='0'>Корневая категория</option>
                {category_select selected=$category['vparent_id'] cats=$cats}
            </select>
        </div>

        <!-- Авто теги для товаров данной категории -->
        <div class="block layer">
            <h2>Авто теги для товаров из этой категории</h2>
            <ul class="list">
                <li>
                    <label class="property">auto meta title</label>
                    <textarea name="save[category][auto_meta_title]">{$category['auto_meta_title']|escape}</textarea>
                <li>
                    <label class="property">auto meta keywords</label>
                    <textarea
                            name="save[category][auto_meta_keywords]">{$category['auto_meta_keywords']|escape}</textarea>
                <li>
                    <label class="property">auto meta description</label>
                    <textarea
                            name="save[category][auto_meta_description]">{$category['auto_meta_description']|escape}</textarea>
                <li>
                    <label class="property">auto annotation</label>
                    <textarea name="save[category][auto_annotation]">{$category['auto_annotation']|escape}</textarea>
                <li>
                    <label class="property">auto description</label>
                    <textarea name="save[category][auto_description]">{$category['auto_description']|escape}</textarea>
            </ul>
        </div>
        <!-- Авто теги для товаров данной категории (The End)-->

        <!-- Изображения -->
        <div class="block layer images">
            <h2>Изображения
            </h2>
            <ul id="imagelist">
                {if $category['images']}
                    {foreach $category['images'] as $image_id=>$image}
                        <li container="true">
                            <a delete_link="true" class="delete"></a>
                            <img id="{$image_id}" src="{$image['basename']|resize:categories:$image_id:100:100}"
                                 alt=""/>
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

                <span class=upload_image><i add_link="true" link_id="upload_image" class="dash_link" id="upload_image">Добавить изображение</i></span>
                или
                <span class=add_image_url><i class="dash_link" add_link="true" link_id="image_url_upload">загрузить из интернета</i></span>

                <!-- Шаблон для кнопки загрузки нового изображения -->
                <div container="true" container_id="upload_image" style="display: none;">
                    <input name=new_images[] type=file multiple accept='image/jpeg,image/png,image/gif'>
                    <a delete_link="true" class="delete"></a>
                </div>
                <!-- Шаблон для кнопки загрузки нового изображения (The end) -->
            </div>
        </div>
        <!-- Изображения  END -->


    </div>
    <!-- Правая колонка (The End)-->


    <!-- Короткое описание -->
    <div class="block layer">
        <h2>Короткое описание до 65 535 символов</h2>
        <textarea name="save[category][annotation]" class="editor_small">{$category['annotation']|escape}</textarea>
    </div>
    <!-- Короткое описание (The End)-->

    <!-- Описание -->
    <div class="block layer">
        <h2>Описание до 16 777 215 символов</h2>
        <textarea name="save[category][description]" class="editor_large">{$category['description']|escape}</textarea>
    </div>
    <!-- Описание (The End)-->

    <input class="button_green button_save" type="submit" name="" value="Сохранить"/>

</form>
<!-- Основная форма (The End) -->

