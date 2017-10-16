{if $products|count>2 || $total_pages_num>1}
	<script type="text/javascript" src="js/ctrlnavigate.js"></script>
	<div class="pagination">
	{if $products|count>2}
		<div class="sort">Сортировать по:&nbsp;
		<select onchange="location = this.value;">
		<option value="{url sort=position}" {if $sort=='position'} selected{/if}>Умолчанию</option>
		<option value="{url sort=name}" {if $sort=='name'} selected{/if}>Имени</option>
		<option value="{url sort=price}" {if $sort=='price'} selected{/if}>Цене</option>
		</select>
		</div>
	{/if}

	{if $total_pages_num>1}
		{$visible_pages = 9}
		{$page_from = 1}
		{if $current_page_num > floor($visible_pages/2)}{$page_from = max(1, $current_page_num-floor($visible_pages/2)-1)}{/if}
		{if $current_page_num > $total_pages_num-ceil($visible_pages/2)}{$page_from = max(1, $total_pages_num-$visible_pages-1)}{/if}
		{$page_to = min($page_from+$visible_pages, $total_pages_num-1)}
		<a {if $current_page_num==1}class="selected"{/if} href="{url page=null}">1</a>
		{if $visible_pages < $total_pages_num-1 && $current_page_num > ($visible_pages-1)/2+2}<a class="prev_page_link" href="{url page=$current_page_num-floor($visible_pages/2)}">&#9668;</a>{/if}			
			{section name=pages loop=$page_to start=$page_from}
			{$p = $smarty.section.pages.index+1}
			{if ($p == $page_from+1 && $p!=2) || ($p == $page_to && $p != $total_pages_num-1)}
			{else}<a {if $p==$current_page_num}class="selected"{/if} href="{url page=$p}">{$p}</a>{/if}
			{/section}
		{if $visible_pages < $total_pages_num-1 && $total_pages_num - $current_page_num > $visible_pages/2 + 1}<a class="next_page_link" href="{url page=$current_page_num + floor($visible_pages/2)}">&#9658;</a>{/if}			
		<a {if $current_page_num==$total_pages_num}class="selected"{/if}  href="{url page=$total_pages_num}">{$total_pages_num}</a>
		<a href="{url page=all}" title='всё из этой категории на одной странице'>всё</a>
	{/if}
	</div>
{/if}