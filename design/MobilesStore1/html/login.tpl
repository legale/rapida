<div id="page_title"><p><a href="./">Главная</a></p><h1>Вход / Авторизация в каталоге для учета скидки</h1></div>
<form class="form login_form" method="post">
	<p style='padding:10px 0;'>Выполнив вход, Вы сможете заказывать товары с учетом скидки, назначенной администрацией для вашего аккаунта. Уточнить Вашу персональную скидку Вы можете по нашим телефонам.</p>
	{if $error}
		<div class="message_error">
		{if $error == 'login_incorrect'}Неверный логин или пароль
		{elseif $error == 'user_disabled'}Ваш аккаунт еще не активирован.
		{else}{$error}{/if}
		</div>
	{/if}
	<label>Email</label>
	<input type="text" name="email" data-format="email" data-notice="Введите email" value="{$email|escape}" maxlength="255" />
    <label>Пароль (<a href="user/password_remind">напомнить</a>)</label>
    <input type="password" name="password" data-format=".+" data-notice="Введите пароль" value="" />
	<input type="submit" class="button right" name="login" value="Войти">
</form>