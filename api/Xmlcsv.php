<?php
if (defined('PHP7')) {
    eval("declare(strict_types=1);");
}

/**
 * Class Xmlcsv
 */
class Xmlcsv
{

    public function __construct()
    {
    }

    /**
     * @param $xmlfile
     * @return XMLReader
     */
    public function xml_open($xmlfile)
    {
        $z = new XMLReader;
        $z->open($xmlfile);
        return $z;
    }
}