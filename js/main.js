/*
 * В этом классе собраны все функции js, которые используются для работы
 */
window.ra = {
	walk: function(obj, cb){
		if (typeof obj !== 'object'){
			return false;
		}
		for (var i = 0; i < obj.length; i++) {
			cb(obj[i]);
		};
		return true;
	},
	
	parse_uri: function(uri){
		"use strict";
		var a = document.createElement('a');
		a.href = uri;
		return {scheme: a.protocol, user: a.username, pass: a.password, host: a.hostname, port: a.port, 
			path: a.pathname, query: a.search, fragment: a.hash}
	},
	
	parse_uri_path: function(path){
		"use strict";
		return ra.trim(path, '/').split('/');
	},
	
	trim: function(s, char) {
		"use strict";
		char = char !== undefined ? char : ' '; 
		while(s.charAt(0) == char) {
			s = s.substring(1);
		}

		while(s.charAt(s.length-1) == char) {
			s = s.substring(0, s.length-1);
		}
		return s;
	},

	implode: function( glue, arr ) {
		"use strict";
		return arr.__proto__.constructor.name === 'Array' ? arr.join ( glue ) : false;
	},


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
			return false;
		} else {
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
		"use strict";
		ev.preventDefault();
		ev.target.removeEventListener('click', ra.stopDefAction, false);
	},

	getType: function(o){
		"use strict";
		let type = o.__proto__.constructor.name;
		
		if(type.match(/HTML.+?Element/)){
			return 'element';
		}else {
			return type.toLowerCase();
		}
	},


	hideShow: function(el){
		"use strict";
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

	xhr: function(uri, method){
		"use strict";
		console.error(uri +  ' ' + method);
		if(uri === undefined){
			console.error("args error");
			return false;
		}
		method = method ? method : 'GET';
		
		return new Promise((yes,no)=>{
			let xhr;
			//first variant for IE
			if(window.XDomainRequest !== undefined){
				 xhr = new XDomainRequest();
					xhr.open(method, uri);
			}else if(window.XMLHttpRequest !== undefined){
				 xhr = new XMLHttpRequest();
				
				if(xhr.withCredentials === undefined){
					console.error('Cross-Origin Resource Sharing not supported!');
					return false;
				}
				
				xhr.open(method, uri, true);
			}
			
			//set event listeners
			xhr.onreadystatechange = function() {
				if (xhr.readyState > 3 && xhr.status >= 200 && xhr.status < 300){
					yes(xhr.responseText);
				}else if(xhr.readyState > 3 && xhr.status!=200){
					no({status: xhr.status, statusText: xhr.statusText});
				}
			}
			//send request
			xhr.send();
		});
	},

	appendScript: function(par, elScript){
		"use strict";
		let el = document.createElement('script');
		el.innerHTML = elScript.innerHTML;
		elScript.src !== undefined ? el.src = elScript.src : null;
		return par.appendChild(el);
	},

	append: function(par, html){
		"use strict";
		//element script
		let type = ra.getType(html), res;
		
		switch(type){
			case 'element': 
				if(html.nodeName.toLowerCase() === 'script'){
					return ra.appendScript(par, html);
				}else{
					return par.appendChild(html);
				}
			break;
			
			case 'string':
				let obj = ra.createHtml(html);
				type = ra.getType(obj);
				if(type === 'element' && obj.nodeName.toLowerCase() === 'script'){
					return ra.appendScript(par, obj);
				}else if (type === 'htmlcollection' || type === 'nodelist'){
					res = [];
					for(let i = 0; obj.length > 0; i++){
						res[i] = ra.append(par, obj[0]);
					}
				} else {
					res = ra.append(par, obj);
				}
				
			break;
			
			default: 
				return false;
		}
		return res;
	}

	}

window.ra.api = {


	req: function( data, success ) {
		"use strict";
		let l = window.location;
		let params = 'json=' + JSON.stringify(data);

		let xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
		xhr.open('POST', l.protocol + '//' + l.host + '?xhr=1');
		xhr.onreadystatechange = function() {
			if (xhr.readyState>3 && xhr.status==200) { success(JSON.parse(xhr.responseText)); }
		};
		xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
		xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		xhr.send(params);
		return xhr;
	},

	update_options: function(data){
		"use strict";
		for(let fid in data.full){
			for(let vid in data.full[fid].vals){
				if( window['option_' + fid + '_' + vid] ){
					if( !data.filter[fid] || data.filter[fid][vid] === undefined ){ 
						window['option_' + fid + '_' + vid].classList.add('disabled');
						window['option_' + fid + '_' + vid].getElementsByTagName('input')[0].disabled = true;
					} else if ( data.filter[fid] && data.filter[fid][vid] !== undefined ){
						window['option_' + fid + '_' + vid].classList.remove('disabled');
						window['option_' + fid + '_' + vid].getElementsByTagName('input')[0].disabled = '';
						if(window.filter.features[fid] && window.filter.features[fid][vid] !== undefined){
							window['option_' + fid + '_' + vid].getElementsByTagName('input')[0].checked = true;
						}else {
							window['option_' + fid + '_' + vid].getElementsByTagName('input')[0].checked = false;
						}
					}
				}
			}
		}
	},
	
	draw_tooltip: function(el, amount){
		"use strict";
		let div = window.tooltip || document.createElement('div')
		, coord = el.getBoundingClientRect();

		div.id = 'tooltip';

		div.innerHTML = '<span>Найдено: ' + amount + ' </span><a onclick="ra.api.show_products(event);" href="#" class="apply">Показать</a>';
		div.style = 'top: -4px; left: 0px; opacity: 0;';
		return div;
	},
	
	draw_waiter: function(el){
		"use strict";
		let div = window.tooltip || document.createElement('div')
		, coord = el.getBoundingClientRect();

		div.id = 'tooltip';

		div.innerText = 'Думаю...';
		div.style = 'top: -4px; left: 0px; opacity: 0;';
		return div;
	},

	toggle: function(el, cla){
		if(el.classList.contains('toggle')){
			el.classList.toggle(cla);
		}
		let n = el.nextElementSibling;
		if(n !== null && n.classList.contains('toggle')){
			ra.api.toggle(n, cla);
		}
	},
	
	select_option: function(e){
		"use strict";
		e.preventDefault();
		if(e.target.classList.contains('toggle')){
			return ra.api.toggle(e.target, 'collapsed');
		}
		
		
		let el = e.target.tagName.toLowerCase() === 'span' ? e.target.parentNode.parentNode : e.target.parentNode
		, fid = el.getAttribute('data-option')
		, vid = el.getAttribute('data-option-id')
		, fname = el.getAttribute('data-fname')
		, oname = el.getAttribute('data-oname')
		, arr = ra.api.uri_to_obj(window.location.href);
		if(el.classList.contains('disabled')){
			return false;
		}else if (el.id === 'filter'){
			
		}
		
		if(fid === null || vid === null){
			return false;
		}else{
			el.getElementsByTagName('input')[0].checked = el.getElementsByTagName('input')[0].checked ? false : true;
			let tt = ra.append(el, ra.api.draw_waiter(el));
			tt.style.left = tooltip.previousElementSibling.getBoundingClientRect().width + 10 + 'px';
			tt.style.opacity = '1';
		}
		
		
		if(window.filter.features === undefined){
			window.filter.features = {};
		}
		
		if(window.filter.features[fid] === undefined){
			window.filter.features[fid] = {};
		}
		
		if(window.filter.features[fid][vid] !== undefined){
			delete(window.filter.features[fid][vid]);
			
			if(Object.keys(window.filter.features[fid]).length === 0){
				delete(window.filter.features[fid]);
			}
		} else {
			window.filter.features[fid][vid] = vid;
		}
		
		if( arr.path.data[fname] === undefined ){
			arr.path.data[fname] = [oname];
			arr.path.sort.push(fname);
		}else if (arr.path.data[fname] !== undefined && !arr.path.data[fname].includes(oname)){
			arr.path.data[fname].push(oname);
		} else if (arr.path.data[fname] !== undefined && arr.path.data[fname].includes(oname)){
			arr.path.data[fname].splice(arr.path.data[fname].indexOf(oname), 1);
			if(arr.path.data[fname].length === 0){
				delete(arr.path.fname);
				arr.path.sort.splice(arr.path.sort.indexOf(fname), 1);
			}
		}
		
		window.history.pushState(null, null, ra.api.obj_to_uri(arr));
		
		
		let data = {'class': 'features', 'method': 'get_options_mix', 'args': {'filter': window.filter}};
		let data2 = {'class': 'products', 'method': 'count_products', 'args': {'filter': window.filter}};
		ra.api.req(data, function(obj){
			ra.api.update_options(obj);
		});
		ra.api.req(data2, function(amount){
			let tt = ra.append(el, ra.api.draw_tooltip(el, amount));
			tt.style.left = tooltip.previousElementSibling.getBoundingClientRect().width + 10 + 'px';
			
			setTimeout(function(){tooltip.style.opacity = '';}, 5);
		});
	},
	
	show_products: function(){
		"use strict";
		let uri = window.location.href;
		uri += '?ajax=1';
		ra.getAjax(uri, function(obj){
			window.items.innerHTML = JSON.parse(obj);
		});
		setTimeout(function(){tooltip.style.opacity = '0';}, 30);
	},
	
	uri_to_obj: function(uri){
		"use strict";
		let obj = Object.create(Object.prototype)
		, sort = new Array
		, data = Object.create(Object.prototype)
		, arr = ra.parse_uri(uri);

		let path = ra.parse_uri_path(arr.path);

		for(let i = 0, el = ''; i < path.length; i++){
			el = path[i].split('-' , 2);
			
			data[el[0]] = el.length === 2 ? el[1].split('.') : '';
			sort.push(el[0]);
		}
		obj.data = data;
		obj.sort = sort;
		arr.path = obj;
		return arr;
	},
	
	obj_to_uri: function(obj){
		"use strict";
		let s = '', path = '';
		
		for(let i = 0, sort = obj.path.sort, k = obj.path.sort.length; i < k; i++){
			if(ra.getType(obj.path.data[sort[i]]) === 'string' && obj.path.data[sort[i]] === ''){
				path += '/' + sort[i];
			} else if (ra.getType(obj.path.data[sort[i]]) === 'array'){
				path += '/' + sort[i] + '-' + obj.path.data[sort[i]].join('.');
			}
		}
		s += obj.scheme + '//' + obj.host + path;
		return s;
	}
}
