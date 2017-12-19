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
    private $type;
    private $basename;
    private $w;
    private $h;
    private $debug = false; //set false for production

    public function __construct()
    {
        dtimer::$enabled = $this->debug ? true : false;
        dtimer::log(__METHOD__ . ' start');
        //['dir'][0] - is the first part (ex: products) ['dir'][1] is the second (ex: resize)
        $this->uri = $this->coMaster->uri_arr;

        $this->type = isset($this->uri['path_arr']['dir'][0])
            ? $this->uri['path_arr']['dir'][0] : null;

        $this->w = isset($this->uri['path_arr']['size'][0])
            ? $this->uri['path_arr']['size'][0] : null;

        $this->h = isset($this->uri['path_arr']['size'][1])
            ? $this->uri['path_arr']['size'][1] : null;

        $this->basename = isset($this->uri['path_arr']['basename'])
            ? $this->uri['path_arr']['basename'] : null;
    }

    public function action()
    {
        dtimer::log(__METHOD__ . ' start');
        $image_id = isset($this->uri['path_arr']['dir'][1])
            ? (int)$this->uri['path_arr']['dir'][1] : null;

        $q = isset($this->uri['query_arr'])
            ? $this->uri['query_arr'] : null;


        if (isset($q['scheme'], $image_id)) {
            //trying to download remote image
            $url = unparse_url($q);
            dtimer::log(__METHOD__ . " unparse_url: $url");
            dtimer::log(__METHOD__ . " image_id: $image_id");
            $filepath = $this->download($image_id);
        } else {
            $filepath = $this->resize($this->basename, $this->w, $this->h);
        }

        dtimer::log(__METHOD__ . " filepath to read: $filepath");

        if (!$this->debug) {
            $this->read($this->config->root_dir . $filepath);
            exit();
        }
        return true;
    }

    private function download($image_id)
    {
        dtimer::log(__METHOD__ . " start image_id: $image_id");
        $type = $this->type;
        if ($image = $this->image->get($type, array('id' => $image_id))) {
//            print_r($image);
            $image = reset($image);
            $url = $image['basename'];
            dtimer::log(__METHOD__ . " is_url check: $url");
            if (!$this->image->is_url($url)) {
                dtimer::log(__METHOD__ . " local image file detected! trying to resize $url");
                return $this->resize($url, $this->w, $this->h);
            }
        } else {
            dtimer::log(__METHOD__ . " unable to get image with id $image_id return false", 1);
            return false;
        }

        dtimer::log(__METHOD__ . " trying to download url: $url");
        if ($a = $this->image->download($this->type, $image_id)) {
            $basename = $a['basename'];
            return $this->resize($basename, $this->w, $this->h);
        } else {
            return false;
        }
    }

    private function resize($basename, $w, $h)
    {
        dtimer::log(__METHOD__ . " start basename: $basename w: $w h: $h");
        if (!isset($basename, $w, $h)) {
            dtimer::log(__METHOD__ . " args error! return false", 1);
            return false;
        }
        return $this->image->resize('img/' . $this->type . '/' . $basename, $w, $h);
    }

    private function read($res)
    {
        dtimer::log(__METHOD__ . " start file: $res");

        if (is_file($res) && is_readable($res)) {
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
                print $output;
                return true;
            } else {
                $length = filesize($res);
                header("Content-length: $length");
                header("Content-type: image");
                print @file_get_contents($res);
                return true;
            }
        } else {
            header("HTTP/1.1 404 Not Found");
            header("Status: 404 Not Found");
            dtimer::log(__METHOD__ . " ubable to read", 1);
            return false;
        }

    }
}
