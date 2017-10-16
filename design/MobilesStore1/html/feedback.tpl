<div id="page_title"><p><a href="./">Главная</a> » {$page->name|escape}</p><h1>{$page->name|escape}</h1></div>

{if $message_sent}<h4 style='padding:50px 0;'>{$name|escape}, Ваше сообщение отправлено.<br />Благодарим за внимание к нашему сайту</h4>
{else}
	<div id="category_description">{$page->body}</div>
	<a name='ask'></a>
	<div class="clear_dot"></div>
	<h1>Обратная связь</h1>

	<form class="form feedback_form" method="post">
		{if $error}
		<div class="message_error">
			{if $error=='captcha'}Неверно введена капча
			{elseif $error=='empty_name'}Введите имя
			{elseif $error=='empty_email'}Введите email
			{elseif $error=='empty_text'}Введите сообщение
			{/if}
		</div>
		{/if}

		<label>Имя</label>
		<input data-format=".+" data-notice="Введите имя" value="{$name|escape}" name="name" maxlength="255" type="text"/>
		<label>Email</label>
		<input data-format="email" data-notice="Введите email" value="{$email|escape}" name="email" maxlength="255" type="text"/>
		<label>Сообщение</label>
		<textarea data-format=".+" data-notice="Введите сообщение" value="{$message|escape}" name="message">{$message|escape}</textarea>
		<label>Введите число с картинки</label>
		<div class="captcha"><img src="captcha/image.php?{math equation='rand(10,10000)'}"/></div>
		<input class="input_captcha" id="comment_captcha" type="text" name="captcha_code" value="" data-format="\d\d\d\d" data-notice="Введите капчу" maxlength="4"/>
		<input class="button right" type="submit" name="feedback" value="Отправить" />
	</form>
{/if}