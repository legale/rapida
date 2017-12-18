<?php

/*
 * Это класс контроллера XHR запросов, для доступа к API. 
 * Здесь выполняется разбор XHR запроса, получение необходимых данных,
 * и передача информации клиенту
 */

require_once('Simpla.php');

class ControllerResize extends Simpla
{
    private $uri;
    private $q;
    private $type;

    public function __construct()
    {
        dtimer::$enabled = false; //set true to enable debugger
        dtimer::log(__METHOD__ . ' start');
        //['dir'][0] - is the first part (ex: products) ['dir'][1] is the second (ex: resize)
        $this->uri = $this->coMaster->uri_arr;
        $this->type = $this->uri['path_arr']['dir'][0];
    }

    public function action()
    {
        dtimer::log(__METHOD__ . ' start');
        //There is 2 scenarios
        //if image is on the remote server
        if (isset($this->uri['query_arr'])) {
            $this->q = $this->uri['query_arr'];
            dtimer::log(__METHOD__ . " var_export query array: " . var_export($this->q, true));
        }

        if (isset($this->q['scheme']) && isset($this->uri['path_arr']['dir'][1])) {
            $filepath = $this->download();
        } else {
            $filepath = $this->resize();
        }
        $output = $this->read($filepath);
        print $output;
        exit();
        return true;
    }

    private function download()
    {
        dtimer::log(__METHOD__ . ' start');
        //trying to download remote image
        $item_id = (int)$this->uri['path_arr']['dir'][1];
        $url = unparse_url($this->q);
        dtimer::log(__METHOD__ . " unparsed url: $url");
        if ($a = $this->image->download($this->type, $item_id, $url)) {
            $basename = $a['basename'];
            return $this->resize($basename);
        } else {

            return false;
        }
    }

    private function resize($basename = null, $w = null, $h = null)
    {

        dtimer::log(__METHOD__ . " start basename: $basename w: $w h: $h");
        if (!isset($w)) {
            $w = $this->uri['path_arr']['size'][0];
        }
        if (!isset($h)) {
            $h = $this->uri['path_arr']['size'][1];
        }
        if (!isset($basename)) {
            $basename = $this->uri['path_arr']['basename'];
        }

        if (isset($w, $h, $basename)) {
            $res = $this->image->resize('img/' . $this->type . '/' . $basename, $w, $h);
            return $res;
        } else {
            dtimer::log(__METHOD__ . " resize failed unable to get width, height and/or basename", 1);
            dtimer::log(__METHOD__ . " var_export uri_array " . var_export($this->uri['path_arr'], true), 1);

            return false;
        }
    }

    private function read($res)
    {
        dtimer::log(__METHOD__ . " start file: $res");
        if (isset($res) && is_readable($res)) {
            if (isset($_SERVER['HTTP_RANGE'])) {
                $bytes = explode('=', $_SERVER['HTTP_RANGE'], 2);
                $range = explode('-', $bytes[1], 2);
                $length = $range[1] - $range[0];
                $offset = (int)$range[0];
                $f = @fopen($res, 'r');
                @fseek($f, $offset);
                header("Content-length: $length");
                header("Content-type: image");
                $output = @fread($f, $length);
                fclose($f);
                return $output;
            } else {
                $length = filesize($res);
                header("Content-length: $length");
                header("Content-type: image");
                return @file_get_contents($res);
            }
        }
    }
}