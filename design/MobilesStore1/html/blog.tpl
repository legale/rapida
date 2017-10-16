<div id="page_title">
<p><a href="./">Главная</a> » {$page->name}</p>
<h1>{$page->name}</h1>
</div>

{include file='pagination.tpl'}<br />
{foreach $posts as $post}
	<div id="category_description">
	<p class='right'><b>{$post->date|date}</b></p>
	<h2><a class='color' data-post="{$post->id}" href="blog/{$post->url}">{$post->name|escape}</a></h2>
	<p>{$post->annotation}</p>
	</div>
	<div class="clear_dot"></div>
{/foreach}
{include file='pagination.tpl'}