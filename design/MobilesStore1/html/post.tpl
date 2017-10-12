{$name_title = "Комментарии к этой записи в блоге"}
<div id="page_title">
<p>
<a href="./">Главная</a> » <a href="blog" class='link_2'>Все новости</a>
<h1 data-post="{$post->id}">{$post->name|escape}</h1>
</p>
</div>

<div id="category_description">
<p><b class='color'>{$post->date|date}</b></p>
{$post->text}
</div>

<div id="back_forward">
{if $prev_post}<a class="prev_page_link hover_mouse" href="blog/{$prev_post->url}">{$prev_post->name}</a>{/if}
{if $next_post}<a class="next_page_link hover_mouse" href="blog/{$next_post->url}">{$next_post->name}</a>{/if}
</div>
{include file='tpl_comments.tpl'}