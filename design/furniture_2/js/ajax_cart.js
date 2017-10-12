// Аяксовая корзина
(function($){
	$(function() {
			$('.pagination li a').each(function(i){
		  i++;
		    $(this).addClass('item-'+i);
		   });
		//  Автозаполнитель поиска
		$(".input_search").autocomplete({
			serviceUrl:'ajax/search_products.php',
			minChars:1,
			noCache: false, 
			onSelect:
				function(value, data){
					 $(".input_search").closest('form').submit();
				},
			fnFormatResult:
				function(value, data, currentValue){
					var reEscape = new RegExp('(\\' + ['/', '.', '*', '+', '?', '|', '(', ')', '[', ']', '{', '}', '\\'].join('|\\') + ')', 'g');
					var pattern = '(' + currentValue.replace(reEscape, '\\$1') + ')';
	  				return (data.image?"<img align=absmiddle src='"+data.image+"'> ":'') + value.replace(new RegExp(pattern, 'gi'), '<strong>$1<\/strong>');
				}	
		});
	});

$('form.variants').live('submit', function(e) {
	e.preventDefault();
	if($(this).find('input[name=variant]:checked').size()>0)
		variant = $(this).find('input[name=variant]:checked').val();
	if($(this).find('select[name=variant]').size()>0)
		variant = $(this).find('select').val();
	$.ajax({
		url: "ajax/cart.php",
		data: {variant: variant,amount: $(this).find('input[name="amount"]').val()},
		dataType: 'json',
		success: function(data){
			$('#cart_informer').html(data);

        $('.block-cart-header .cart-content').stop(true, true).slideToggle(300);
		}
	});
	var o1 = $(this).offset();
	var o2 = $('#cart_informer').offset();
	var dx = o1.left - o2.left;
	var dy = o1.top - o2.top;
	var distance = Math.sqrt(dx * dx + dy * dy);
	$(this).closest('.product').find('.product-image img').effect("transfer", { to: $("#cart_informer"), className: "transfer_class" }, distance);	
	$('.transfer_class').html($(this).closest('.product').find('.product-image').html());
	$('.transfer_class').find('img').css('height', '100%');
	return false;
});
jQuery(document).ready(function() {
 
 	var maxHeight = 0;
	function setHeight(column) {
		column = jQuery(column);
		column.each(function() {       
			if(jQuery(this).height() > maxHeight) {
				maxHeight = jQuery(this).height();
			}
		});
		column.height(maxHeight);
	}
  setHeight('.products-grid .product-shop');
	//setHeight('.products-grid .desc_grid');
	//setHeight('.products-grid .bottom');
	
});
$(document).ready(function(){
  $('#nav li').hover(
	function(){
		$(this).addClass("hover");
	},
	function(){
		$(this).removeClass("hover");
	}
  );  
  $('#nav ul').each(function(){
	  $(this).parent().addClass('items');
  });  
  var is_playing = false;  
  $('#nav > li, #nav > li > ul > li').not('.active').each(function(){												   
	  $(this).on("mouseenter mouseleave", function(){														
		 if($(this).hasClass('items')){
			 var counter = 30;
			 function slide_check(this_button){		
				 if(counter > 0){
					 if(this_button.hasClass('hover')){
						 if(is_playing == false){
						 	is_playing = true;							
							this_button.children('ul').slideDown('slow', function(){is_playing = false});
							counter = counter - 10;
						 }
					 } else{
						 	if(is_playing == false){
								is_playing = true;
								this_button.children('ul').slideUp('normal', function(){is_playing = false});
							 }
						 }
					counter--;
					setTimeout(function(){slide_check(this_button)}, 200);
				 }				 
			  }
			  slide_check($(this));		  
		  }		  
	  });	  
  });
});
$(function() {

	// Выбор вариантов
	$('select[name=variant]').change(function() {
		price = $(this).find('option:selected').attr('price');
		compare_price = '';
		if(typeof $(this).find('option:selected').attr('compare_price') == 'string')
			compare_price = $(this).find('option:selected').attr('compare_price');
		$(this).find('option:selected').attr('compare_price');
		$(this).closest('form').find('span.pr').html(price);
		$(this).closest('form').find('span.o-pr').html(compare_price);
		return false;		
	});
		
});
$(function() {

	// Раскраска строк характеристик

	$(".features li:even").addClass('even');



	// Зум картинок

	$("a.zoom").fancybox({ 'hideOnContentClick' : true });

});
})(jQuery)
