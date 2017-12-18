{* Вкладки *}
{capture name=tabs}
	<li class="active"><a href="?module=UsersAdmin">Покупатели</a></li>
	{if isset($userperm['groups'])}<li><a href="?module=GroupsAdmin">Группы</a></li>{/if}
	{if isset($userperm['coupons'])}<li><a href="?module=CouponsAdmin">Купоны</a></li>{/if}
{/capture}

{if $user['id']}
{$meta_title = $user['name']|escape scope=parent}
{/if}

{if $message_success}
<!-- Системное сообщение -->
<div class="message message_success">
	<span class="text">{if $message_success=='updated'}Пользователь отредактирован{else}{$message_success|escape}{/if}</span>
	{if $smarty.get.return}
	<a class="button" href="{$smarty.get.return}">Вернуться</a>
	{/if}
</div>
<!-- Системное сообщение (The End)-->
{/if}

{if $message_error}
<!-- Системное сообщение -->
<div class="message message_error">
	<span class="text">{if $message_error=='login_exists'}Пользователь с таким email уже зарегистрирован
	{elseif $message_error=='empty_name'}Введите имя пользователя
	{elseif $message_error=='empty_email'}Введите email пользователя
	{else}{$message_error|escape}{/if}</span>
	{if $smarty.get.return}
	<a class="button" href="{$smarty.get.return}">Вернуться</a>
	{/if}
</div>
<!-- Системное сообщение (The End)-->
{/if}

<!-- Основная форма -->
<form method=post>
<input type=hidden name="session_id" value="{$smarty.session.id}">
<input class="name" name=name type="text" value="{$user['name']|escape}"/> 
<input name=id type="hidden" value="{$user['id']|escape}"/> 


<div class="column_left ">
	<!-- Левая колонка -->

		<!-- Параметры страницы -->
		<div class="block">
			<ul class="list">
				<li>
				<div class="switcher">
					<label class="switcher__text">Аккаунт</label>
					<input class="switcher__input" value="1" type="checkbox" name="enabled" id="enabled_switch" {if $user['enabled']}checked{/if}>
					<label class="switcher__label" for="enabled_switch">
						<span class="switcher__text">Вкл.</span>
					</label>
				</div>
				</li>
				<li>
				<div class="switcher">
					<label class="switcher__text">Администратор</label>
					<input class="switcher__input" value="1" type="checkbox" name="admin" id="admin_switch" {if $user['admin'] }checked{/if}>
					<label class="switcher__label" for="admin_switch">
						<span class="switcher__text">Вкл.</span>
					</label>
				</div>
				</li>

				{if $groups}
				<li>
					<label class=property>Группа</label>
					<select name="group_id">
						<option value='0'>Не входит в группу</option>
				   		{foreach $groups as $g}
				        	<option value='{$g['id']}' {if $user['group_id'] == $g['id']}selected{/if}>{$g['name']|escape}</option>
				    	{/foreach}
					</select>
				</li>
				{/if}
				<li><label class=property>Email</label><input name="email"  type="text" value="{$user['email']|escape}" /></li>
				<li><label class=property>Дата регистрации</label><input name="email"  type="text" disabled value="{$user['created']|date}" /></li>
				<li><label class=property>Последний IP</label><input name="email"  type="text" disabled value="{$user['last_ip']|escape}" /></li>
				<li><label class=property>Последний вход</label><input name="email"  type="text" disabled value="{$user['last_login']|escape}" /></li>
			</ul>
			<input class="button_green button_save" type="submit" name="user_info" value="Сохранить" />
			
		</div>

		<!-- Параметры страницы (The End)-->
</div>
	 
	<!-- Левая колонка (The End)--> 

	<!-- Правая колонка -->
	<div class=" column_right">
		
		<h2>Права доступа: </h2>
		<div class="block"><label id="check_all" class="dash_link">Включить все</label></div>

		<!-- Параметры  -->
		<div class="block">
			<ul id="perms">
				
				{foreach $perm_list as $p=>$name}

				<li>
				<div class="switcher">
					<label class="switcher__text">{$name}</label>
					<input class="switcher__input" value="1" type="checkbox" name="perm[{$p}]" id="{$p}_switch" {if isset($user['perm'][$p]) }checked{/if}>
					<label class="switcher__label" for="{$p}_switch"></label>
				</div>
				</li>
				{/foreach}
				
			</ul>
		</div>
		<!-- Параметры (The End)-->
	</div>
	<!-- Правая колонка (The End)--> 


	
		
</form>
<!-- Основная форма (The End) -->
 

{if $orders}
<div class="block column_left">
<form id="list" method="post">
	<input type="hidden" name="session_id" value="{$smarty.session.id}">
	<h2>Заказы пользователя</h2>

	<div>		
		{foreach $orders as $order}
		<div class="{if $order['paid']}green{/if} row">
	 		<div class="checkbox cell">
				<input type="checkbox" name="check[]" value="{$order['id']}" />				
			</div>
			<div class="order_date cell">
				{$order['date']|date} {$order['date']|time}
			</div>
			<div class="name cell">
				<a href="{url module=OrderAdmin id=$order['id'] return=$smarty.server.REQUEST_URI}">Заказ №{$order['id']}</a>
			</div>
			<div class="name cell">
				{$order['total_price']}&nbsp;{$currency['sign']}
			</div>
			<div class="icons cell">
				{if $order['paid']}
					<img src='design/images/cash_stack.png' alt='Оплачен' title='Оплачен'>
				{else}
					<img src='design/images/cash_stack_gray.png' alt='Не оплачен' title='Не оплачен'>				
				{/if}	
			</div>
			<div class="icons cell">
				<a href='#' class=delete></a>		 	
			</div>
			<div class="clear"></div>
		</div>
		{/foreach}
	</div>

	<div id="action">
	<label id='check_all' class='dash_link'>Выбрать все</label>

	<span id=select>
	<select name="action">
		<option value="delete">Удалить</option>
	</select>
	</span>


	<input id="apply_action" class="button_green" name="user_orders" type="submit" value="Применить">
	</form>
	</div>
</div>
{/if}
{* On document load *}
{literal}

<script>
$(function() {

	// Раскраска строк
	function colorize()
	{
		$("#list div.row:even").addClass('even');
		$("#list div.row:odd").removeClass('even');
	}
	// Раскрасить строки сразу
	colorize();

	// Выделить все
	$("#check_all").click(function() {
		$('#perms input[type="checkbox"]').attr('checked', true);
	});

	// Удалить 
	$("a.delete").click(function() {
		$('#list input[type="checkbox"][name*="check"]').attr('checked', false);
		$(this).closest(".row").find('input[type="checkbox"][name*="check"]').attr('checked', true);
		$(this).closest("form#list").find('select[name="action"] option[value=delete]').attr('selected', true);
		$(this).closest("form#list").submit();
	});

	// Подтверждение удаления
	$("#list").submit(function() {
		if($('select[name="action"]').val()=='delete' && !confirm('Подтвердите удаление'))
			return false;	
	});
});

</script>
{/literal}
