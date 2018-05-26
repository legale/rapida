{capture name=tabs}
	{if isset($userperm['pages'])}
	{foreach $menus  as $m}
		<li {if $m['id'] == $menu['id']}class="active"{/if}><a href='?module=PagesAdmin&menu_id={$m['id']}'>{$m['name']}</a></li>
	{/foreach}
	{/if}
{/capture}

{if $page['id']}
{$meta_title = $page['name'] scope=parent}
{else}
{$meta_title = 'Новая страница' scope=parent}
{/if}

{* Подключаем Tiny MCE *}
{*include file='tinymce_init.tpl'*}

{* Подключаем codemirror *}
{include file='codemirror.tpl'}


{* On document load *}
{literal}
<script src="design/js/jquery/jquery.js"></script>
<script src="design/js/jquery/jquery-ui.min.js"></script>


<script>


</script>


{/literal}


{if isset($message_success)}
<!-- Системное сообщение -->
<div class="message message_success">
	<span class="text">{if $message_success == 'added'}Страница добавлена{elseif $message_success == 'updated'}Страница обновлена{/if}</span>
	<a class="link" target="_blank" href="../{$page['trans']}">Открыть страницу на сайте</a>
	{if $smarty.get.return}
	<a class="button" href="{$smarty.get.return}">Вернуться</a>
	{/if}
	
	<span class="share">		
		<a href="#" onClick='window.open("http://vkontakte.ru/share.php?url={$config->root_url|urlencode}/{$page['trans']|urlencode}&title={$page['name']|urlencode}&description={$page['body']|urlencode}&noparse=false","displayWindow","width=700,height=400,left=250,top=170,status=no,toolbar=no,menubar=no");return false;'>
  		<img src="{$config->root_url}/simpla/design/images/vk_icon.png" /></a>
		<a href="#" onClick='window.open("http://www.facebook.com/sharer.php?u={$config->root_url|urlencode}/{$page['trans']|urlencode}","displayWindow","width=700,height=400,left=250,top=170,status=no,toolbar=no,menubar=no");return false;'>
  		<img src="{$config->root_url}/simpla/design/images/facebook_icon.png" /></a>
		<a href="#" onClick='window.open("http://twitter.com/share?text={$page['name']|urlencode}&url={$config->root_url|urlencode}/{$page['trans']|urlencode}&hashtags={$page['meta_keywords']|replace:' ':''|urlencode}","displayWindow","width=700,height=400,left=250,top=170,status=no,toolbar=no,menubar=no");return false;'>
  		<img src="{$config->root_url}/simpla/design/images/twitter_icon.png" /></a>
	</span>
	
</div>
<!-- Системное сообщение (The End)-->
{/if}

{if isset($message_error)}
<!-- Системное сообщение -->
<div class="message message_error">
	<span class="text">{if $message_error == 'url_exists'}Страница с таким адресом уже существует{else}{$message_error}{/if}</span>
	<a class="button" href="">Вернуться</a>
</div>
<!-- Системное сообщение (The End)-->
{/if}



<!-- Основная форма -->
<form method=post class=product enctype="multipart/form-data">
	<input type=hidden name="session_id" value="{$smarty.session.id}">
	<div id="name">
		<input class="name" name=header type="text" value="{$page['header']|escape}"/> 
		<input name=id type="hidden" value="{$page['id']|escape}"/> 
		<div class="checkbox">
			<input name=visible value='1' type="checkbox" id="active_checkbox" {if $page['visible']}checked{/if}/> <label for="active_checkbox">Активна</label>
		</div>
	</div> 

		<!-- Параметры страницы -->
		<div class="block">
			<ul>
				<li><label class=property>Название пункта в меню</label><input name="name" class="simpla_inp" type="text" value="{$page['name']|escape}" /></li>
				<li><label class=property>Меню</label>	
					<select name="menu_id">
				   		{foreach $menus as $m}
				        	<option value='{$m['id']}' {if $page['menu_id'] == $m['id']}selected{/if}>{$m['name']|escape}</option>
				    	{/foreach}
					</select>		
				</li>
			</ul>
		</div>
		<!-- Параметры страницы (The End)-->

	<!-- Левая колонка свойств товара -->
	<div class="column_left">
			
		<!-- Параметры страницы -->
		<div class="block layer">
			<h2>Параметры страницы</h2>
			<ul>
				<li><label class=property>Адрес</label><div class="page_url">/</div><input name="trans" class="page_url" type="text" value="{$page['trans']|escape}" /></li>
				<li><label class=property>Заголовок</label><input name="meta_title" class="simpla_inp" type="text" value="{$page['meta_title']|escape}" /></li>
				<li><label class=property>Ключевые слова</label><input name="meta_keywords" class="simpla_inp" type="text" value="{$page['meta_keywords']|escape}" /></li>
				<li><label class=property>Описание</label><textarea name="meta_description" class="simpla_inp"/>{$page['meta_description']|escape}</textarea></li>
			</ul>
		</div>
		<!-- Параметры страницы (The End)-->		

			
	</div>
	<!-- Левая колонка свойств товара (The End)--> 
		
	<!-- Описагние товара -->
	<div class="block layer">
		<h2>Текст страницы</h2>
		<textarea name="body"  class="editor_large">{$page['body']|escape}</textarea>
	</div>
	<!-- Описание товара (The End)-->
	<input class="button_green button_save" type="submit" name="" value="Сохранить" />
	
</form>
<!-- Основная форма (The End) -->

