<?php

namespace Telegram;
use Telegram;

class Config
{
    public static $settings = [];
    private static $filepath = __FILE__ . ".conf";


    private function __construct(){}
    private function __clone(){}
    private function __wakeup(){}
    public static function set($name, $value){
        self::$settings[$name] = $value;
        return true;

    }

    public static function get($name){
        //load default config
        if(empty(self::$settings)){
            self::load(self::$filepath);
        }
        if (isset(self::$settings[$name])) {
            return self::$settings[$name];
        }
        return null;
    }

    public static function forget($name){
        if (isset(self::$settings[$name])) {
            unset(self::$settings[$name]);
            return true;
        }
        return false;
    }

    public static function load($filepath, $force = true){
        if(!empty(self::$settings) && !$force){
            return true;
        }
        if (file_exists($filepath)) {
            self::$settings = include($filepath);
            self::$filepath = $filepath;
            return true;
        }
        return false;
    }

    public static function save($filepath = null){
        if (!$filepath) {
            if (self::$filepath) {
                $filepath = self::$filepath;
            } else {
                return false;
            }
        }
        return file_put_contents($filepath, "<?php return " . var_export(self::$settings, true) . ";");
    }

}


 
 class Req{
 
    private function __construct(){}
    private function __clone(){}
    private function __wakeup(){}
    
       /**
     * Get a web file (HTML, XHTML, XML, image, etc.) from a URL.  Return an
     * array containing the HTTP server response header fields and content.
     */
    public static function get_page( $url, $user_agent = null ){

        /* old opera user-agent: "Opera/9.80 (Windows NT 6.0) Presto/2.12.388 Version/12.14" */


        $opt_array = array(

            CURLOPT_CUSTOMREQUEST  =>"GET",        //set request type post or get
            CURLOPT_POST           =>false,        //set to GET
            CURLOPT_USERAGENT      => "Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0", //set user agent
            CURLOPT_COOKIEFILE     =>"cookie.txt", //set cookie file
            CURLOPT_COOKIEJAR      =>"cookie.txt", //set cookie jar
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER         => false,    // don't return headers
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING       => "",       // handle all encodings
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT        => 120,      // timeout on response
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
        );
        
		if($user_agent){
			$opt_array[CURLOPT_USERAGENT] = $user_agent; //set user agent
		}

        $ch      = curl_init( $url );
        curl_setopt_array( $ch, $opt_array );
        $content = curl_exec( $ch );
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        $header  = curl_getinfo( $ch );
        curl_close( $ch );

        $header['errno']   = $err;
        $header['errmsg']  = $errmsg;
        $header['content'] = $content;
        return $header;
    }
    
    
    public static function get($data, $user_agent = null){
		$opt_array = [
            CURLOPT_URL => Config::get("url") . Config::get("token") . "/" . implode('/', $data),
            CURLOPT_RETURNTRANSFER => True,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array("content-type: application/json",),
        ];
        
		if($user_agent){
			$opt_array[CURLOPT_USERAGENT] = $user_agent; //set user agent
		}
        
		return self::fetch($opt_array);
	} 
    
    public static function post($data, $user_agent = null){
		$opt_array = [
            CURLOPT_URL => Config::get("url_api") . Config::get("token") . "/",
            CURLOPT_RETURNTRANSFER => True,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array("content-type: application/json",),
        ];
        
        if($user_agent){
			$opt_array[CURLOPT_USERAGENT] = $user_agent; //set user agent
		}
		
        return self::fetch($opt_array);
	} 
	
	public static function fetch($opt_array){
		$curl = curl_init();
        curl_setopt_array($curl, $opt_array);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            error_log( __METHOD__ . " CURL req error");
            return null;
        } else {
            return json_decode($response, true);
        }
	}
	
	

	public static function getElementsByClass(&$parentNode, $className) {
		$nodes= [];

		$childNodeList = $parentNode->childNodes;
		for ($i = 0; $i < $childNodeList->length; $i++) {
			$temp = $childNodeList->item($i);
			if (method_exists($temp,"getAttribute") 
			&& stripos($temp->getAttribute('class'), $className) !== false) {
				$nodes[]=$temp;
			}
		}

		return $nodes;
	}
	
	
}


class Api{
	
	public static function help(){
		$class = new \ReflectionClass("\Telegram\Api");
		return array_column( (array)$class->getMethods(\ReflectionMethod::IS_STATIC), "name");
	}


	public static function init(){
		return Api::setWebhook();
	}
	
	public static function run(){
		$args = func_get_args()[0];
		$command = array_shift($args);
		return Commands::$command($args);
	}
	
	public static function read(){
		// Получаем запрос от Telegram 
		$json = file_get_contents("php://input");
		//return Api::sendMessage([Config::get("logchannel"), $json, "HTML"]);

		$decoded = json_decode($json, TRUE);		
		if(!$decoded || !isset($decoded["message"])){
			return;
		}
		$message = $decoded["message"];

		//send to the channel
		Api::sendMessage([Config::get("logchannel"), "<code>".var_export($decoded, true)."</code>", "HTML"]);

		// Получаем внутренний номер чата Telegram и команду, введённую пользователем в чате 
		$chat_id = $message["chat"]["id"];
		$text = $message["text"];
		$user = $message['from']['username'];


		
		
		if(substr($text, 0, 1) == '/'){
			$pos = stripos($text, " ");
			if ($pos !== false){
				$command = substr($text, 0, $pos);
			} else {
				$command = $text;
			}

			$command = strtolower(substr($command, 1));
		} else {
			return;
		}
		$args = $content[1] ?? null;
			
		
			
		if(method_exists("\Telegram\Commands", $command)){
			$res = Commands::$command($decoded);
			if(is_array($res) && isset($res["method"]) && isset($res["body"])){
				$method = $res["method"];
				$body = $res["body"];
				return Api::$method([$chat_id, $body]);
			} else {
				return Api::sendMessage([$chat_id, $res, "HTML"]);
			}
		}else{
			return Api::sendMessage([$chat_id, "<code>$command is unknown command</code>", "HTML"]);
		}
	}
	
	public static function setWebhook($params = []){
		$defaults = [
			"url" => Config::get("url"), //bot url
			"certificate", //input file (optional)
			"max_connections", //int (optional)
			"allowed_updates", //Array of String (optional)
		];
		$params = array_merge($defaults, $params);
		$params["method"] = "setWebhook";
		return Req::post($params);
	}
	
	public static function deleteWebhook(){
		$params["method"] = "deleteWebhook";
		return Req::get($params);
	}
	
	public static function getWebhookInfo(){
		$params = [];
		$params["method"] = "getWebhookInfo";
		return Req::post($params);
	}
	
	public static function sendMessage($params){
		$defaults = [
			"chat_id", //	int or string
			"text", // string
			"parse_mode", //string (optional) Markdown or HTML
			"disable_web_page_preview", // bool (optional)
			"disable_notification", // bool (optional)
			"reply_to_message_id", //int (optional) message id
			"reply_markup", /* string (optional) InlineKeyboardMarkup 
			 * or ReplyKeyboardMarkup or ReplyKeyboardRemove 
			 * or ForceReply */
		];
		$params = array_combine(array_slice($defaults, 0, count($params)), $params);
		$params["method"] = "sendMessage";

		return Req::post($params);
	}
	
	public static function sendPhoto($params){
		$defaults = [
			"chat_id", // int or string
			"photo", // input file or string
			"caption", //string Photo caption (may also be used when resending photos by file_id), 0-1024 characters (optional)
			"parse_mode", // bool (optional)
			"disable_notification", // bool (optional)
			"reply_to_message_id", //int (optional) message id
			"reply_markup", /* string (optional) InlineKeyboardMarkup 
			 * or ReplyKeyboardMarkup or ReplyKeyboardRemove 
			 * or ForceReply */
		];
		$params = array_combine(array_slice($defaults, 0, count($params)), $params);
		$params["method"] = "sendPhoto";
		
		return Req::post($params);
	}
	
}


class Commands {
    private function __construct(){}

    private function __clone(){}
    
    private function __wakeup(){}
    
    public static function start(){
		return "Hello, my name is Pohape. Type /help to get help";
	}

    public static function help(){
		$class = new \ReflectionClass("\Telegram\Commands");
		$commands = array_column( (array)$class->getMethods(\ReflectionMethod::IS_STATIC), "name");
		$s = "Доступны следующие команды:\n";
		foreach($commands as $i=>$c){
			$n = $i + 1;
			$s .= "$n. /$c\n";
		}
		return $s;
	}

	/*
    public static function echo($decoded){
		return print_r($decoded, true);	
	}
	
    public static function sendMessage($decoded){
        if(in_array($decoded["message"]["from"]["username"], Config::get("admins"))){
            $extracted = explode(' ', $decoded["message"]["text"],3);
            return Api::sendMessage([$extracted[1], $extracted[2], "HTML"]);
        }else{
            return "You are not allowed to run this command!";
        }
    }
	*/

    public static function status($decoded){
		return shell_exec("mytop");	
	}
	
	
	public static function img($decoded){
		if(isset($decoded["message"]["text"])){
			$text = explode(" ", $decoded["message"]["text"], 2);
			array_shift($text); /*remove first element - command*/
		} else {
			$text = $decoded;
		}
		if(count($text) < 1){
			return "Извини, бро, не понял тебя.
Писать надо так: <b>/img 'название картинки'</b>";
		}
		
		/* old user agent to avoid JS page */
		$user_agent =  "Opera/9.80 (Windows NT 6.0) Presto/2.12.388 Version/12.14";
		
		$keyword = array_shift($text);
		$raw = rawurlencode($keyword);
		$host = "https://www.google.com";
		$res = Req::get_page("$host/search?tbm=isch&q=$raw", $user_agent);
		//return $res["content"];

		$dom = new \DOMDocument;
		$dom->loadHTML($res["content"]);
		$main = $dom ? $dom->getElementById("search") : null;
		
		$images = $main ? $main->getElementsByTagName("img") : null;
		$image = $images ? $images->item(0) : null;
		$src = $image->getAttribute("src") ?? null;
		if($src){
			return [
				"method" => "sendPhoto",
				"body" => $src,
			];
		} else {
			return "no images found";
		}
	}

    public static function wiki($decoded){
		if(isset($decoded["message"]["text"])){
			$text = explode(" ", $decoded["message"]["text"], 3);
			array_shift($text); /*remove first element command*/
		} else {
			$text = $decoded;
		}
		if(count($text) != 2){
			return "Извини, бро, не понял тебя.
Писать надо так: <b>/wiki ru аргумент</b> 
Для поиска в английской википедии: <b>/wiki en love</b>";
		}
		
		$language = array_shift($text);
		$keyword = array_shift($text);
		$raw = rawurlencode($keyword);
		$host = "https://$language.wikipedia.org";
		
		$res = Req::get_page("$host/wiki/$raw");

		$dom = new \DOMDocument;
		$dom->loadHTML($res["content"]);
		$main = $dom->getElementById("mw-content-text");
		$classname = 'mw-parser-output';
		$sub = Req::getElementsByClass($main, $classname);
		
		$s = "";
		$nodes = isset ($sub[0]) ? $sub[0]->getElementsByTagName("p") : [];
		if($nodes->length == 1){
			$s .= $nodes->item(0)->nodeValue;
			$nodes = $sub[0]->getElementsByTagName("li");
			foreach($nodes as $i=>$n){
				/* 0xc2a0 nbsp char */
				$text = str_replace(chr(0xc2) . chr(0xa0) , " ", trim($n->nodeValue));
				$pos = stripos($text, " ");
				if($pos !== false){
					/* first word */
					$word = substr($text, 0, $pos);
					/*tail text */
					$tail = substr($text, $pos);
					
					$a = $n->getElementsByTagName("a")->item(0); /* first a */
					if($a){
						$href = $a->getAttribute("href"); /* href attrib */
						$s .= "<a href='$host$href'>$word</a>". $tail . PHP_EOL;
					} else {
						$s .= $text . PHP_EOL;
					}
				} else{
					$s .= $text . PHP_EOL;
				}
			}
		} else {		
			foreach($nodes as $i=>$n){
				$s .= trim($n->nodeValue) . PHP_EOL;
				if($i == 3) break;
			}
		}
		return  empty($s) ? "empty result" : $s ;
		//$html = $dom->saveHTML($first_p);

	}
}


if(PHP_SAPI === "cli"){
	if(count($argv) < 2){
		echo "Missing operand\n try '$argv[0] help' \n";
		exit(1);
	}
	array_shift($argv);
	$args = $argv;
} else{
	$args = $_GET;
}



if(empty($args)){
    Api::read();
	exit(0);
}
$command = array_shift($args);


print_r(Api::$command($args));

