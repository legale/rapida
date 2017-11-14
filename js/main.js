"use strict";
/*
 * Очень простая функция для отправки ajax GET запроса.
 * url - строка с адресом и запросом
 * success - коллбек функция, которой будет вызвана после получения ответа сервера с передачей ей этого ответа
 */ 
function getAjax(url, success) {
	let xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
	xhr.open('GET', url);
	xhr.onreadystatechange = function() {
		if (xhr.readyState>3 && xhr.status==200) success(xhr.responseText);
	};
	xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	xhr.send();
	return xhr;
}

/*
 * Функция для отправки ajax POST запроса.
 * url - строка с адресом и запросом
 * data - строка с уже оформленным запросом или объект, который будет разложен на пары ключ=значение&ключ=значение
 * success - коллбек функция, которой будет вызвана после получения ответа сервера с передачей ей этого ответа
 */
function postAjax(url, data, success) {
	let params = typeof data == 'string' ? data : Object.keys(data).map(
			function(k){ return encodeURIComponent(k) + '=' + encodeURIComponent(data[k]) }
		).join('&');

	let xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
	xhr.open('POST', url);
	xhr.onreadystatechange = function() {
		if (xhr.readyState>3 && xhr.status==200) { success(xhr.responseText); }
	};
	xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	xhr.send(params);
	return xhr;
}


/*
 * Эта функция для отправки особого POST запроса ajax к api системы
 * 2 аргумента:
 * data - объект с параметрами
 * success - коллбек функция, которой будет вызвана после получения ответа сервера с передачей ей этого ответа
 * пример: apiAjax( 
 * {'class': 'products', 'method': 'get_products', 'args': 
 * 		{'id': [1,2,3,4,5] }
 * } , function(e){
 * console.log(JSON.parse(e))
 * });
 */ 
function apiAjax( data, success) {
	let l = window.location;
	let params = 'json=' + JSON.stringify(data);

	let xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
	xhr.open('POST', l.protocol + '//' + l.host + '?xhr=1');
	xhr.onreadystatechange = function() {
		if (xhr.readyState>3 && xhr.status==200) { success(xhr.responseText); }
	};
	xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	xhr.send(params);
	return xhr;
}


/*
 * Функция аналог Jquery .live
 * Предназначена для создания действия на событие
 * eventType - (строка) тип события
 * elements - HTML элемент или HTML коллекция, или NodeList на которых должно срабатывать событие
 * event - функция, которая будет выполнена при срабатывании события
 */
function live(eventType, elements, event) {
	//если у нас объект, то проверим какой именно
	if(typeof(elements) !== 'object'){
		console.log('live func argument is not an obj ');
		return false;
	}

	let type = elements.__proto__.constructor.name;
	switch(type) {
		//это HTML коллекция или список узлов - сделаем рекурсию, 
		//потому что нам нужны элементы, а не коллекции
		case 'HTMLCollection':
		case 'NodeList':  
			for(let k = 0 ; k < elements.length; k++ ){
				live(eventType, elements[k], event);
			}
			break;
		//это HTML элемент, на него и будем вешать обработчик
		case 'HTMLFormElement':
		case 'HTMLAnchorElement':
		case 'HTMLTableCellElement':
		default:
			elements.addEventListener(eventType, event);
			break;
	}

}

function search_tree(type, name, e){
	"use strict";
	if(e === undefined || e === null || e.classList === undefined){
		console.log('element is empty');
		return false;
	}
	
	let t = false;
	switch(type){
		case 'class':
		if(e.classList.contains(name) === true){
			t = true;
		}
		break;
		
		case 'attribute':
			if(e.getAttribute(name) !== undefined){
				t = true;
			}
		break;
		
		default:
		console.log(type + " is unknown");
		return false;
	}
	
	if(t === true){
		return e;
	}
		
	return search_tree(type, name, e.parentNode);
	

}
	
