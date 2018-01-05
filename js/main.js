/*
 * В этом классе собраны все функции js, которые используются для работы
 */
window.ra = {
/*
 * Очень простая функция для отправки ajax GET запроса.
 * url - строка с адресом и запросом
 * success - коллбек функция, которой будет вызвана после получения ответа сервера с передачей ей этого ответа
 */ 
getAjax: function (url, success) {
	"use strict";
	let xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
	xhr.open('GET', url);
	xhr.onreadystatechange = function() {
		if (xhr.readyState>3 && xhr.status==200) success(xhr.responseText);
	};
	xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	xhr.send();
	return xhr;
},

/*
 * Функция для отправки ajax POST запроса.
 * url - строка с адресом и запросом
 * data - строка с уже оформленным запросом или объект, который будет разложен на пары ключ=значение&ключ=значение
 * success - коллбек функция, которой будет вызвана после получения ответа сервера с передачей ей этого ответа
 */
postAjax: function (url, data, success) {
	"use strict";
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
},


/*
 * Эта функция для отправки особого POST запроса ajax к api системы
 * 2 аргумента:
 * data - объект с параметрами
 * success - коллбек функция, которой будет вызвана после получения ответа сервера с передачей ей этого ответа
 * пример:
 *
apiAjax(
{'class': 'products', 'method': 'get_products_ids', 'args':
	{filter:
			{
	'id': [1,2,3,4]
			}
	}
} , function(e){
  console.log(JSON.parse(e))
  });
*/
apiAjax: function ( data, success) {
	"use strict";
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
},

setCookie: function ( key, val ) {
	"use strict";
	window.document.cookie = key + '=' + val;
	return true;
},

getCookie: function ( key ) {
	"use strict";
	let a = window.document.cookie.split('; ');
	let o = {};
	for(let i in a){
		let k, v;
		[k,v] = a[i].split('=');
		o[k] = v;
	}
	if(key === undefined){
		return o;
	} else {
		return o[key] !== undefined ? o[key] : false;
	}
},

addwishlist: function ( pid ) {
	"use strict";
	window.document.cookie
	return xhr;
},


/*
 * Функция аналог Jquery .live
 * Предназначена для создания действия на событие
 * eventType - (строка) тип события
 * elements - HTML элемент или HTML коллекция, или NodeList на которых должно срабатывать событие
 * event - функция, которая будет выполнена при срабатывании события
 */
live: function (eventType, elements, event) {
	"use strict";
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
				ra.live(eventType, elements[k], event);
			}
			break;
		//если это не коллекция или лист, значит элемент, на него и будем вешать обработчик
		default:
			elements.addEventListener(eventType, event);
			break;
	}

},

search_tree: function (type, name, e){
	"use strict";
	if(e === undefined || e === null || e.classList === undefined){
		console.log('element is empty');
		return false;
	} else {
		console.log(e);
	}


	let t = false;
	switch(type){
		case 'class':
			if(e.classList.contains(name) === true){
				t = true;
			}
			break;

		case 'attribute':
			if(e.getAttribute(name) !== null){
				t = true;
			}
			break;

		case 'tag':
			if(e.tagName === name.toUpperCase() ){
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

	return new Promise(function(resolve, reject) {
			resolve(ra.search_tree(type, name, e.parentNode));
			reject();
	});



},

stopDefAction: function(ev){
	console.log(ev);
	ev.preventDefault();
	ev.target.removeEventListener('click', ra.stopDefAction, false);
},

getType: function(o){
	if(typeof o !== 'object'){
		return typeof o;
	}
	
	let type = Object.prototype.toString.call(o);
	
	if(type.match(/\[object HTML.+?Element\]/)){
		return 'element';
	}else if (type === '[object HTMLCollection]'){
		return 'collection';
	}else{
		return type;
	}
},


hideShow: function(el){
	
	switch(ra.getType(el)){
	case 'element': 
		break;
	case 'string':
		el = document.querySelector(el);
		if(el === null){
			return false;
		}
		break;
	}
	el.style.display = window.getComputedStyle(el).display !== 'none' ? 'none' : 'block';
},

createHtml: function(html){
	let t = document.createElement('div');
	t.innerHTML = html;
	if(t.children[0] === undefined){
		return false;
	}
	return t.children.length > 1 ? t.children : t.children[0];
},

append: function(par, html){
	if(ra.getType(html) === 'element'){
		par.appendChild(html);
		return par;
	}
	let obj = ra.createHtml(html);
	if(ra.getType(obj) === 'element'){
		console.log('element created. Trying to append');
		console.log(par.appendChild(obj));
	}else if (ra.getType(obj) === 'collection'){
		console.log('HTMLCollection created. Trying to append');
		while(obj.length > 0){
			console.log(par.appendChild(obj[0]));
		}
	}
	return par;
}

}
