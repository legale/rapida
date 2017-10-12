// Аяксовая корзина
$('form.variants').live('submit', function(e) {
	e.preventDefault();
	button = $(this).find('input[type="submit"]');
	if($(this).find('input[name=variant]:checked').size()>0)
		variant = $(this).find('input[name=variant]:checked').val();
	if($(this).find('select[name=variant]').size()>0)
		variant = $(this).find('select').val();
	$.ajax({
		url: "ajax/cart.php",
		data: {variant: variant,amount: $(this).find('input[name="amount"]').val()},
		dataType: 'json',
		success: function(data){
			$('#cart').html(data);
			if(button.attr('data-result-text'))
				button.val(button.attr('data-result-text'));
		}
	});
	var o1 = $(this).offset();
	var o2 = $('#cart').offset();
	var dx = o1.left - o2.left;
	var dy = o1.top - o2.top;
	var distance = Math.sqrt(dx * dx + dy * dy);
	$(this).find('.image img').effect("transfer", { to: $("#cart"), className: "transfer_class" }, distance);	
	$('.transfer_class').html($(this).find('.image').html());
	$('.transfer_class').find('img').css('height', '100%');
	return false;
});


function display(view) {
	if (view == 'list') {
		$('.product-grid ').attr('class', 'product-list');
		
		$('.product-list ul li').each(function(index, element) {
			html  = '<div class="right">';
				html += '  <div class="price">' + $(element).find('.price').html() + '</div>';
				
				html += '<div class="rating">' + $(element).find('.rating').html() + '</div>';				
				
			html += '  <div class="cart">' + $(element).find('.cart').html() + '</div>';

		
			html += '</div>';			
			
			html += '<div class="left">';
			
			var image = $(element).find('.image').html();
			
			if (image != null) { 
				html += '<div class="image">' + image + '</div>';
			}
								
			html += '  <div class="name">' + $(element).find('.name').html() + '</div>';
			html += '  <div class="description">' + $(element).find('.description').html() + '</div>';

			html += '</div>';

						
			$(element).html(html);
		});		
		
		$('.display').html('<b>Показывать :</b> <div id="list_b"></div> <a id="grid_a" onclick="display(\'grid\');">Grid</a>');
		
		$.totalStorage('display', 'list');
	// Выбор вариантов
	$('select[name=variant]').change(function() {
		price = $(this).find('option:selected').attr('price');
		compare_price = '';
		if(typeof $(this).find('option:selected').attr('compare_price') == 'string')
			compare_price = $(this).find('option:selected').attr('compare_price');
		$(this).find('option:selected').attr('compare_price');
		$(this).closest('form').find('span.price-new').html(price);
		$(this).closest('form').find('span.price-old').html(compare_price);
		return false;		
	});     
	} else {
		$('.product-list').attr('class', 'product-grid');
		
		$('.product-grid ul li').each(function(index, element) {
			html = '';
					
			var image = $(element).find('.image').html();
			
			if (image != null) {
				html += '<div class="image">' + image + '</div>';
			}
			
		html += '<div class="name">' + $(element).find('.name').html() + '</div>';
			
			
			var price = $(element).find('.price').html();
			
			if (price != null) {
				html += '<div class="price">' + price  + '</div>';
			}
	
			html += '<div class="description">' + $(element).find('.description').html() + '</div>';
			
			var rating = $(element).find('.rating').html();
			
			
				html += '<div class="rating">' + rating + '</div>';
			
						
			html += '<div class="cart">' + $(element).find('.cart').html() + '</div>';

			
			$(element).html(html);
		});	
					
		$('.display').html('<b>Показывать :</b> <a id="list_a" onclick="display(\'list\');">List</a>  <div id="grid_b"></div>');
		
		$.totalStorage('display', 'grid');
	// Выбор вариантов
	$('select[name=variant]').change(function() {
		price = $(this).find('option:selected').attr('price');
		compare_price = '';
		if(typeof $(this).find('option:selected').attr('compare_price') == 'string')
			compare_price = $(this).find('option:selected').attr('compare_price');
		$(this).find('option:selected').attr('compare_price');
		$(this).closest('form').find('span.price-new').html(price);
		$(this).closest('form').find('span.price-old').html(compare_price);
		return false;		
	});    
	}
	//$(".wishlist a.tip").easyTooltip();
	//$(".compare a.tip2").easyTooltip();
}

