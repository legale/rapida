/*
* element.closest() polyfill
 */
(function (ELEMENT) {
    ELEMENT.matches = ELEMENT.matches || ELEMENT.mozMatchesSelector || ELEMENT.msMatchesSelector || ELEMENT.oMatchesSelector || ELEMENT.webkitMatchesSelector;
    ELEMENT.closest = ELEMENT.closest || function closest(selector) {
        if (!this) return null;
        if (this.matches(selector)) return this;
        if (!this.parentElement) {
            return null
        }
        else return this.parentElement.closest(selector)
    };
}(Element.prototype));


/*
 * В этом классе собраны все функции js, которые используются для работы
 */
window.ra = {
    walk: function (obj, cb) {
        if (typeof obj !== 'object') {
            return false;
        }
        for (var i = 0; i < obj.length; i++) {
            cb(obj[i]);
        }
        return true;
    },

    parse_uri: function (uri) {
        "use strict";
        var a = document.createElement('a');
        a.href = uri;
        return {
            scheme: a.protocol, user: a.username, pass: a.password, host: a.hostname, port: a.port,
            path: a.pathname, query: a.search, fragment: a.hash
        }
    },

    parse_uri_path: function (path) {
        "use strict";
        return ra.trim(path, '/').split('/');
    },

    trim: function (s, char) {
        "use strict";
        char = char !== undefined ? char : ' ';
        while (s.charAt(0) == char) {
            s = s.substring(1);
        }

        while (s.charAt(s.length - 1) == char) {
            s = s.substring(0, s.length - 1);
        }
        return s;
    },

    implode: function (glue, arr) {
        "use strict";
        return arr.__proto__.constructor.name === 'Array' ? arr.join(glue) : false;
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
        xhr.onreadystatechange = function () {
            if (xhr.readyState > 3 && xhr.status == 200) success(xhr.responseText);
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
            function (k) {
                return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
            }
        ).join('&');

        let xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
        xhr.open('POST', url);
        xhr.onreadystatechange = function () {
            if (xhr.readyState > 3 && xhr.status == 200) {
                success(xhr.responseText);
            }
        };
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send(params);
        return xhr;
    },


    setCookie: function (key, val) {
        "use strict";
        window.document.cookie = key + '=' + val;
        return true;
    },

    getCookie: function (key) {
        "use strict";
        let a = window.document.cookie.split('; ');
        let o = {};
        for (let i in a) {
            let k, v;
            [k, v] = a[i].split('=');
            o[k] = v;
        }
        if (key === undefined) {
            return o;
        } else {
            return o[key] !== undefined ? o[key] : false;
        }
    },

    addwishlist: function (pid) {
        "use strict";
        window.document.cookie;
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
        if (typeof(elements) !== 'object') {
            return false;
        }

        let type = elements.__proto__.constructor.name;
        switch (type) {
            //это HTML коллекция или список узлов - сделаем рекурсию,
            //потому что нам нужны элементы, а не коллекции
            case 'HTMLCollection':
            case 'NodeList':
                for (let k = 0; k < elements.length; k++) {
                    ra.live(eventType, elements[k], event);
                }
                break;
            //если это не коллекция или лист, значит элемент, на него и будем вешать обработчик
            default:
                elements.addEventListener(eventType, event);
                break;
        }

    },

    stopDefAction: function (ev) {
        "use strict";
        ev.preventDefault();
        ev.target.removeEventListener('click', ra.stopDefAction, false);
    },

    getType: function (o) {
        "use strict";
        let type = o.constructor.name;

        if (type.match(/HTML.+?Element/)) {
            return 'element';
        } else {
            return type.toLowerCase();
        }
    },


    hideShow: function (el) {
        "use strict";
        switch (ra.getType(el)) {
            case 'element':
                break;
            case 'string':
                el = document.querySelector(el);
                if (el === null) {
                    return false;
                }
                break;
        }
        el.style.display = window.getComputedStyle(el).display !== 'none' ? 'none' : 'block';
    },

    createHtml: function (html) {
        let t = document.createElement('div');
        t.innerHTML = html;
        if (t.children[0] === undefined) {
            return false;
        }
        return t.children.length > 1 ? t.children : t.children[0];
    },

    xhr: function (uri, method) {
        "use strict";
        console.error(uri + ' ' + method);
        if (uri === undefined) {
            console.error("args error");
            return false;
        }
        method = method ? method : 'GET';

        return new Promise((yes, no) => {
            let xhr;
            //first variant for IE
            if (window.XDomainRequest !== undefined) {
                xhr = new XDomainRequest();
                xhr.open(method, uri);
            } else if (window.XMLHttpRequest !== undefined) {
                xhr = new XMLHttpRequest();

                if (xhr.withCredentials === undefined) {
                    console.error('Cross-Origin Resource Sharing not supported!');
                    return false;
                }

                xhr.open(method, uri, true);
            }

            //set event listeners
            xhr.onreadystatechange = function () {
                if (xhr.readyState > 3 && xhr.status >= 200 && xhr.status < 300) {
                    yes(xhr.responseText);
                } else if (xhr.readyState > 3 && xhr.status != 200) {
                    no({status: xhr.status, statusText: xhr.statusText});
                }
            };
            //send request
            xhr.send();
        });
    },

    appendScript: function (par, elScript) {
        "use strict";
        let el = document.createElement('script');
        el.innerHTML = elScript.innerHTML;
        elScript.src !== undefined ? el.src = elScript.src : null;
        return par.appendChild(el);
    },

    append: function (par, html) {
        "use strict";
        //element script
        let type = ra.getType(html), res;

        switch (type) {
            case 'element':
                if (html.nodeName.toLowerCase() === 'script') {
                    return ra.appendScript(par, html);
                } else {
                    return par.appendChild(html);
                }
                break;

            case 'string':
                let obj = ra.createHtml(html);
                type = ra.getType(obj);
                if (type === 'element' && obj.nodeName.toLowerCase() === 'script') {
                    return ra.appendScript(par, obj);
                } else if (type === 'htmlcollection' || type === 'nodelist') {
                    res = [];
                    for (let i = 0; obj.length > 0; i++) {
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

};

window.ra.api = function (data, success) {
    "use strict";
    if (data === undefined && success === undefined) {
        let text = `
			example: 
			let data = {'class': 'products', 'method': 'get_products', 'args': {'filter': {id: [1,2,3,4,5,6,7,8,9,10]}}};
			ra.api(data, console.log);
		`;
        console.log(text);
        return;
    }
    let l = window.location;
    let params = 'json=' + JSON.stringify(data);

    let xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
    xhr.open('POST', l.protocol + '//' + l.host + '?xhr=1');
    xhr.onreadystatechange = function () {
        if (xhr.readyState > 3 && xhr.status == 200) {
            success(JSON.parse(xhr.responseText));
        }
    };
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send(params);
    return xhr;
};
