<?php

namespace Telegram;
use Telegram;

class Config
{
    public static $settings = [];
    private static $filepath = "telegram.conf";


    private function __construct(){}

    private function __clone(){}

    private function __wakeup(){}

    public static function set($name, $value)
    {
        self::$settings[$name] = $value;
        return true;

    }

    public static function get($name)
    {
        //load default config
        if(empty(self::$settings)){
            self::load(dirname(__FILE__) . "/" . self::$filepath);
        }
        if (isset(self::$settings[$name])) {
            return self::$settings[$name];
        }
        return null;
    }

    public static function forget($name)
    {
        if (isset(self::$settings[$name])) {
            unset(self::$settings[$name]);
            return true;
        }
        return false;
    }

    public static function load($filepath, $force = true)
    {
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

    public static function save($filepath = null)
    {
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

    public static function get($data){
        $opt_array = [
            CURLOPT_URL => Config::get("url") . Config::get("token") . "/" . implode('/', $data),
            CURLOPT_RETURNTRANSFER => True,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array("content-type: application/json",),
        ];


        return self::fetch($opt_array);
    }

    public static function post($data){
        $opt_array = [
            CURLOPT_URL => Config::get("url") . Config::get("token") . "/",
            CURLOPT_RETURNTRANSFER => True,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array("content-type: application/json",),
        ];


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
}


class Api{

    public static function init($params){
        if(!Config::get("webhook_status")){
            Api::deleteWebhook();
            $res = Api::setWebhook($params);
            print_r($res);
            return $res;
        }
    }

    public static function run(){
        $args = func_get_args()[0];
        $command = array_shift($args);
        print_r(Commands::$command($args));
    }

    public static function read(){
        // Получаем запрос от Telegram
        $json = file_get_contents("php://input");

        $decoded = json_decode($json, TRUE);
        if(!$decoded || !isset($decoded["message"])){
            return null;
        }
        $message = $decoded["message"];

        //send to the channel
        //Api::sendMessage([Config::get("channel"), "<code>".print_r($decoded, true)."</code>", "HTML"]);

        // Получаем внутренний номер чата Telegram и команду, введённую пользователем в чате
        $chat_id = $message["chat"]["id"];
        $text = $message["text"];
        $user = $message['from']['username'];

        $content = explode(' ', $text, 2);
        $command = strtolower($content[0]);
        $args = $content[1] ?? null;


        if(method_exists("\Telegram\Commands", $command)){
            return Api::sendMessage([$chat_id, Commands::$command($decoded), "HTML"]);
        }else{
            return Api::sendMessage([$chat_id, "<code>$command is unknown</code>", "HTML"]);
        }
    }

    public static function setWebhook($params){
        $defaults = [
            "url", //string
            "certificate", //input file (optional)
            "max_connections", //int (optional)
            "allowed_updates", //Array of String (optional)
        ];
        $params = array_combine(array_slice($defaults, 0, count($params)), $params);
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
        //~print_r($params);
        //~print_r(Req::post($params));
        return Req::post($params);
    }

}


class Commands {
    private function __construct(){}

    private function __clone(){}

    private function __wakeup(){}

    public static function start(){
        return "Welcome, friend!!!";
    }

    public static function help(){
        $class = new \ReflectionClass("\Telegram\Commands");
        return array_column( (array)$class->getMethods(\ReflectionMethod::IS_STATIC), "name");
    }

    public static function update_stock($decoded){
        if(in_array($decoded["message"]["from"]["username"], Config::get("admins"))){
            return shell_exec("php /var/www/html/cron/xmlread.php http://vokruglamp.ru/export/get.php?id=sevenlight 41 50");
        }else{
            return "You are not allowed to run this command!";
        }
    }

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


    public static function status($decoded){
        return shell_exec("mytop");
    }


    public static function nginx_status($decoded){
        if(in_array($decoded["message"]["from"]["username"], Config::get("admins"))){
            return shell_exec("curl localhost/nginx_status/");
        }else{
            return "You are not allowed to run this command!";
        }
    }

    public static function get_orders(){
        require_once(__DIR__."/api/Simpla.php");

        $simpla = new \Simpla();
        $res = $simpla->db->query("SELECT 
		p.order_id,
		b.name as brand,
		prod.name,
		p.amount,
		p.price
		FROM s_purchases as p  
		INNER JOIN s_orders as o ON o.id = p.order_id
		INNER JOIN s_products prod ON p.product_id = prod.id
		LEFT JOIN s_brands b ON prod.brand_id = b.id 
		WHERE 1 AND o.status = 0 ORDER BY b.name, p.order_id DESC, p.id");
        $res = $simpla->db->results_array();
        if(!is_array($res)){
            return null;
        }

        $table = "<code>";
        foreach($res as $order){
            foreach($order as $key=>$val){
                $table .= "$key: $val\n";
            }
            $table .= "\n\n";
        }
        $table .= "</code>";
        return $table;
    }


}


if(php_sapi_name() === "cli"){
    if(count($argv) < 2){
        echo "Missing operand\n try '$argv[0] --help' \n";
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
} elseif(count($args) === 1){
    exit(1);
}


$command = array_shift($args);

Api::$command($args);
