<div id="page_title"><p><a href="./">Главная</a></p><h1>Регистрация</h1></div>
<p style='padding:10px 0;'>Для наших постоянных клиентов мы предлагаем зарегистрироваться в нашем каталоге. Выполнив вход, Вы сможете заказывать товары с учетом скидки, назначенной администрацией для вашего аккаунта. Уточнить Вашу персональную скидку Вы можете по нашим телефонам.</p>

{if $error}
<div class="message_error">
	{if $error == 'empty_name'}Введите имя
	{elseif $error == 'empty_email'}Введите email
	{elseif $error == 'empty_password'}Введите пароль
	{elseif $error == 'user_exists'}Пользователь с таким email уже зарегистрирован
	{elseif $error == 'captcha'}Неверно введена капча
	{else}{$error}{/if}
</div>
{/if}

<form class="form register_form" method="post">
	<label>Имя</label>
	<input type="text" name="name" data-format=".+" data-notice="Введите имя" value="{$name|escape}" maxlength="255" />
	<label>Email</label>
	<input type="text" name="email" data-format="email" data-notice="Введите email" value="{$email|escape}" maxlength="255" />
    <label>Пароль</label>
    <input type="password" name="password" data-format=".+" data-notice="Введите пароль" value="" />
	<label>Введите число с картинки</label>
	<div class="captcha"><img src="captcha/image.php?{math equation='rand(10,10000)'}" alt='captcha'/></div>
	<input class="input_captcha" id="comment_captcha" type="text" name="captcha_code" value="" data-format="\d\d\d\d" data-notice="Введите капчу" maxlength="4"/>
	<input type="submit" name="register" class="button right" value="Зарегистрироваться">
</form>