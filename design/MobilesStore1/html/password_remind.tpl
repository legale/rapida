<div id="page_title"><p><a href="./">Главная</a><h1>Напоминание пароля</h1></p></div>
{if $email_sent}<p style='margin:30px;'><h1>Вам отправлено письмо</h1>На {$email|escape} отправлено письмо для восстановления пароля.</p>
{else}
	<form class="form" method="post">
	{if $error}
	<div class="message_error">
	{if $error == 'user_not_found'}Пользователь не найден
	{else}{$error}{/if}
	</div>
	{/if}
	<label>Введите email, который вы указывали при регистрации</label>
	<input type="text" name="email" data-format="email" data-notice="Введите email" value="{$email|escape}"  maxlength="255"/>
	<input type="submit" class="button right" value="Вспомнить" />
	</form>
{/if}