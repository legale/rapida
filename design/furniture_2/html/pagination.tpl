{* Постраничный вывод *}

{if $total_pages_num>1}
{* Скрипт для листания через ctrl → *}
{* Ссылки на соседние страницы должны иметь id PrevLink и NextLink *}
<script type="text/javascript" src="js/ctrlnavigate.js"></script>           
<div class="pages">
        <strong>Страница:</strong>
        <ol>
        
        
        
                                    <li class="current">1</li>
                                                <li><a href="http://livedemo00.template-help.com/magento_47678/accessories-pillows.html?limit=5&amp;p=2">2</a></li>
                    

        
        
                    <li>
                <a class="next i-next icon-caret-right" href="http://livedemo00.template-help.com/magento_47678/accessories-pillows.html?limit=5&amp;p=2" title="Следующая">
                                            <!-- <img src="http://livedemo00.template-help.com/magento_47678/skin/frontend/default/theme217k/images/pager_arrow_right.gif" alt="Следующая" class="v-middle" /> -->
                                    </a>
            </li>
                </ol>

    </div>
<!-- Листалка страниц -->
<div class="pagination2">
	
	{* Количество выводимых ссылок на страницы *}
	{$visible_pages = 13}

	{* По умолчанию начинаем вывод со страницы 1 *}
	{$page_from = 1}
	
	{* Если выбранная пользователем страница дальше середины "окна" - начинаем вывод уже не с первой *}
	{if $current_page_num > floor($visible_pages/2)}
		{$page_from = max(1, $current_page_num-floor($visible_pages/2)-1)}
	{/if}	
	
	{* Если выбранная пользователем страница близка к концу навигации - начинаем с "конца-окно" *}
	{if $current_page_num > $total_pages_num-ceil($visible_pages/2)}
		{$page_from = max(1, $total_pages_num-$visible_pages-1)}
	{/if}
	
	{* До какой страницы выводить - выводим всё окно, но не более ощего количества страниц *}
	{$page_to = min($page_from+$visible_pages, $total_pages_num-1)}

	{* Ссылка на 1 страницу отображается всегда *}
	<a {if $current_page_num==1}class="selected"{/if} href="{url page=1}">1</a>
	
	{* Выводим страницы нашего "окна" *}	
	{section name=pages loop=$page_to start=$page_from}
		{* Номер текущей выводимой страницы *}	
		{$p = $smarty.section.pages.index+1}	
		{* Для крайних страниц "окна" выводим троеточие, если окно не возле границы навигации *}	
		{if ($p == $page_from+1 && $p!=2) || ($p == $page_to && $p != $total_pages_num-1)}	
		<a {if $p==$current_page_num}class="selected"{/if} href="{url page=$p}">...</a>
		{else}
		<a {if $p==$current_page_num}class="selected"{/if} href="{url page=$p}">{$p}</a>
		{/if}
	{/section}

	{* Ссылка на последнююю страницу отображается всегда *}
	<a {if $current_page_num==$total_pages_num}class="selected"{/if}  href="{url page=$total_pages_num}">{$total_pages_num}</a>
	
	{if $current_page_num>1}<a class="prev_page_link" href="{url page=$current_page_num-1}">←назад</a>{/if}
	{if $current_page_num<$total_pages_num}<a class="next_page_link" href="{url page=$current_page_num+1}">вперед→</a>{/if}
	
</div>
<!-- Листалка страниц (The End) -->
{/if}
