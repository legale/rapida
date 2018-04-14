{* Шаблон текстовой страницы *}

{* Канонический адрес страницы *}
{$canonical="/{$page['trans']}" scope=parent}

<!-- Заголовок страницы -->
<h1 data-page="{$page['id']}">{$page['header']|escape}</h1>

<!-- Тело страницы -->
{$page['body']}