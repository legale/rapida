<div id="comments">
	<div id="page_title"><h1>{$name_title}</h1></div>
	{if $comments}
		<ul class="comment_list">
		{foreach $comments as $comment}
		<a name="comment_{$comment->id}"></a>
		<li>
			<div class="comment_header">
			{$comment->name|escape} <i>{$comment->date|date}, {$comment->date|time}</i>
			{if !$comment->approved}ожидает модерации</b>{/if}
			</div>
			{$comment->text|escape|nl2br}
		</li>
		{/foreach}
		</ul>
	{/if}

	<form class="form" method="post">
		<h2>Написать свой комментарий</h2>
		{if $error}
		<div class="message_error">
			{if $error=='captcha'}Неверно введена капча
			{elseif $error=='empty_name'}Введите имя
			{elseif $error=='empty_comment'}Введите комментарий
			{/if}
		</div>
		{/if}
		<textarea class="comment_textarea" id="comment_text" name="text" data-format=".+" data-notice="Введите комментарий">{$comment_text}</textarea>
		<label for="comment_name">Ваше Имя</label>
		<input class="input_name" type="text" id="comment_name" name="name" value="{$comment_name}" data-format=".+" data-notice="Введите имя"/>
		<label for="comment_captcha">Число с картинки</label>
		<div class="captcha"><img src="captcha/image.php?{math equation='rand(10,10000)'}" alt='captcha'/></div>
		<input class="input_captcha" id="comment_captcha" type="text" name="captcha_code" value="" data-format="\d\d\d\d" data-notice="Введите капчу"/>
		<input class="button right" type="submit" name="comment" value="Отправить" />
	</form>
</div>
{literal}
<script>
$(function() {
	// Раскраска строк
	$(".comment_list li:even").addClass('even');
});
</script>
{/literal}