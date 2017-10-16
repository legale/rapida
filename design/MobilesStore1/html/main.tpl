<a name="new_products"></a>
<div class="container">

	<ul class="tabs">
	{get_featured_products var=featured_products limit=1}{if $featured_products}<li><a href="#tab1"><h2 title='Показать'>Хиты продаж и лучшее</h2></a></li>{/if}
	{get_new_products var=new_products limit=1}{if $new_products}<li><a href="#tab2"><h2 title='Показать'>Новинки каталога</h2></a></li>{/if}
	{get_discounted_products var=discounted_products limit=1}{if $discounted_products}<li><a href="#tab3"><h2 title='Показать'>Акция, Скидки</h2></a></li>{/if}
	</ul>

<div class="tab_container">

	{get_featured_products var=featured_products limit=12 order='RAND()'}
	{if $featured_products}
		<div id="tab1" class="tab_content">
			<ul class="tiny_products main">
			{foreach $featured_products as $product}
			<li class="product">{include file='tpl_products_blocks.tpl'}</li>
			{/foreach}
			</ul>
		</div>
	{/if}

	{get_new_products var=new_products limit=12}
	{if $new_products}
		<div id="tab2" class="tab_content">
			<ul class="tiny_products main">
			{foreach $new_products as $product}
			<li class="product"><div class="label label_new"></div>{include file='tpl_products_blocks.tpl'}</li>
			{/foreach}
			</ul>
		</div>
	{/if}

	{get_discounted_products var=discounted_products limit=12 order='RAND()'}
	{if $discounted_products}
		<div id="tab3" class="tab_content">
			<ul class="tiny_products main">
			{foreach $discounted_products as $product}
			<li class="product">{include file='tpl_products_blocks.tpl'}</li>
			{/foreach}
			</ul>
		</div>
	{/if}
</div></div>

{if $page->body}
<div id="page_title"><h1>{$page->header}</h1></div>
<div id="category_description">{$page->body}</div>
{/if}

{literal}
<script>
$(function() {
	// Выбор вариантов
	$('select[name=variant]').change(function() {
		price = $(this).find('option:selected').attr('price');
		compare_price = '';
		if(typeof $(this).find('option:selected').attr('compare_price') == 'string')
			compare_price = $(this).find('option:selected').attr('compare_price');
		$(this).find('option:selected').attr('compare_price');
		$(this).closest('form').find('span').html(price);
		$(this).closest('form').find('strike').html(compare_price);
		return false;
	});
});

$(document).ready(function() {

	$(".tab_content").hide();
	$("ul.tabs li:first").addClass("active").show();
	$(".tab_content:first").show();

	$("ul.tabs li").click(function() {
		$("ul.tabs li").removeClass("active");
		$(this).addClass("active");
		$(".tab_content").hide();
		var activeTab = $(this).find("a").attr("href");
		$(activeTab).fadeIn();
		return false;
	});

});
</script>
{/literal}