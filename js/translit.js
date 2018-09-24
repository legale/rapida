function object_flip(obj){
    "use strict";
    let new_obj = {};
      for (let prop in obj) {
        if(obj.hasOwnProperty(prop)) {
          new_obj[obj[prop]] = prop;
        }
      }
      return new_obj;
}

function strtr(str, obj){
    "use strict";
    let res = '';
    for (let i = 0; i < str.length; i++) {
        let s = str.charAt(i);
        if(obj[str[i]] !== undefined){
            res += obj[str[i]];            
        } else {
            res += str[i];
        }
    }
    return res;
}


function translit_url(str, reverse)
{
    "use strict";
    let f = 'translit_url';
	if (typeof(str) !== 'string' ) {
		console.log(f + ' argument type error');
		return false;
    }
	//самая быстрая функция для замены подстроки в строке strtr пробел меняем на подчеркивание
	let pairs = {' ': '_', '-': '+'};
	if (reverse === true) {
		pairs = object_flip(pairs);
	}
	str = strtr(str, pairs);
	
	//тут удаляем все кроме букв, цифр и _ + ~
	str = str.replace("/[^\w\d\_\~\+]+/gi", '');

	let converter = {
		'а': 'a', 'б': 'b', 'в': 'v',
		'г': 'g', 'д': 'd', 'ё': 'e',
		'е': 'e', 'ж': 'zh', 'з': 'z',
		'и': 'i', 'й': 'j', 'к': 'k',
		'л': 'l', 'м': 'm', 'н': 'n',
		'о': 'o', 'п': 'p', 'р': 'r',
		'с': 's', 'т': 't', 'у': 'u',
		'ф': 'f', 'х': 'h', 'ц': 'c',
		'ч': 'ch', 'ш': 'sh', 'щ': 'sch',
		'ь': '~', 'ы': 'y', 'ъ': '~~',
		'э': 'eh', 'ю': 'yu', 'я': 'ya'
    };
	if (reverse === true) {
		converter = object_flip(converter);
	}
	str = str.toLowerCase();

	return strtr(str, converter);
}