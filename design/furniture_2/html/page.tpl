{* Шаблон текстовой страницы *}
<div class="page-title">
<!-- Заголовок страницы -->
<h1 data-page="{$page->id}">{$page->header|escape}</h1>
            </div>
<!-- Тело страницы -->
{$page->body}
