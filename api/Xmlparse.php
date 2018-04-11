<?php
if (defined('PHP7')) {
    eval("declare(strict_types=1);");
}

/**
 * Class Xmlparse
 */
class Xmlparse
{

    public $zed;

    public function __construct()
    {
    }

    /**
     * @param $xmlfile
     * @return XMLReader
     */
    public function xml_open($xmlfile)
    {
        $this->zed = new XMLReader;
        $this->zed->open($xmlfile);
        return $this->zed;
    }

    public function node_goto($name)
    {
        do {
            if($this->zed->read() === false){
                return false;
            }
            if($this->zed->name === $name){
                return true;
            }
        }while(1);
    }

    public function node_next($name)
    {
        return $this->zed->next($name);
    }
    public function read_raw()
    {
        return $this->zed->readOuterXML();
    }



}