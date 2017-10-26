function live(eventType, elements, cb) {
	//если у нас объект, то проверим какой именно
    if(typeof(elements) === 'object'){
		let type = elements.__proto__.constructor.name;
		switch(type) {
			case 'HTMLCollection':  
			
				break;
			case 'HTMLFormElement':
				break;
			default:
				console.log(return this.live.name + ' argument 2 is unk ' + type);
				return false;				
		}
	} else {
		console.log(return this.live.name + ' argument is not an obj ');
		return false;
	}
		
		
    document.addEventListener(eventType, function (event) {
        if (event.target === element) {
            cb.call(event.target, event);
        }
    });
}

live("click", "test", function (event) {
    alert(this.id);
});


function callAjax(obj){
	if(obj.url !== undefined){		
		req = obj.url;			
	}else{
		return false;
	}
	
	if(obj.data !== undefined){
		req += '?';
		for(k in obj.data){
			req += '&' + k + '=' + obj.data[k];
		}
	}
	//~ console.log('callAjax url: ' + req);
	
	var xmlhttp;
	// compatible with IE7+, Firefox, Chrome, Opera, Safari
	xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function(){
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
			if(obj.dataType == 'json'){
				response = JSON.parse(xmlhttp.responseText);			
			}else{
				response = xmlhttp.responseText;
			}
			obj.success(response);
			response = undefined;
		}
	}
	xmlhttp.open("GET", req, true);
	xmlhttp.send();
};



//старая функция jquery для подсказок поиска
$(function() {
	//  Автозаполнитель поиска
	$(".input_search").autocomplete({
		serviceUrl:'ajax/search_products.php',
		minChars:1,
		noCache: false, 
		onSelect:
			function(suggestion){
				 $(".input_search").closest('form').submit();
			},
		formatResult:
			function(suggestion, currentValue){
				var reEscape = new RegExp('(\\' + ['/', '.', '*', '+', '?', '|', '(', ')', '[', ']', '{', '}', '\\'].join('|\\') + ')', 'g');
				var pattern = '(' + currentValue.replace(reEscape, '\\$1') + ')';
				return (suggestion.data.image?"<img align=absmiddle src='"+suggestion.data.image+"'> ":'') + suggestion.value.replace(new RegExp(pattern, 'gi'), '<strong>$1<\/strong>');
			}	
	});
	});



/* 

//Аяксовая корзина, но без jquery
live('submit', form.variants function(e) {
	e.preventDefault();
	button = $(this).find('input[type="submit"]');
	if($(this).find('input[name=variant]:checked').size()>0)
		variant = $(this).find('input[name=variant]:checked').val();
	if($(this).find('select[name=variant]').size()>0)
		variant = $(this).find('select').val();
	$.ajax({
		url: "ajax/cart.php",
		data: {variant: variant},
		dataType: 'json',
		success: function(data){
			$('#cart_informer').html(data);
			if(button.attr('data-result-text'))
				button.val(button.attr('data-result-text'));
		}
	});
	var o1 = $(this).offset();
	var o2 = $('#cart_informer').offset();
	var dx = o1.left - o2.left;
	var dy = o1.top - o2.top;
	var distance = Math.sqrt(dx * dx + dy * dy);
	$(this).closest('.product').find('.image img').effect("transfer", { to: $("#cart_informer"), className: "transfer_class" }, distance);	
	$('.transfer_class').html($(this).closest('.product').find('.image').html());
	$('.transfer_class').find('img').css('height', '100%');
	return false;
});

*/
