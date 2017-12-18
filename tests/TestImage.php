<?php
use PHPUnit\Framework\TestCase;

require_once('../api/Image.php');


class TestImage extends TestCase
{
    use \Xpmock\TestCaseTrait;

    private $class;
    private $simpla;
    private $root;

    /**
     * @test nothing
     */
    public function nothing()
    {
        $this->assertTrue(true);
    }

    public function typesDataProvider()
    {
        return array(
            array(100, false),
            array(null, false),
            array('', false),
            array('something', false),
            array('products', 'img/products/'),
            array('categories', 'img/categories/'),
            array('brands', 'img/brands/'),
            array('blog', 'img/blog/'),
        );
    }

    public function urlsDataProvider()
    {
        $url = 'https://www.google.com/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png';
        return array(
            '1 pic for product' => array(
                array(
                    array('products', 1, $url, 1),
                ),
            ),
            '2 pics for product' => array(
                array(
                    array('products', 1, $url, 1),
                    array('products', 1, $url, 1),
                ),
            ),
            '3 pics for blog' => array(
                array(
                    array('blog', 1, $url, 1),
                    array('blog', 1, $url, 1),
                    array('blog', 1, $url, 1),
                ),
            ),
            'null' => array(
                array(
                    array(null, null, null, false),
                ),
            ),
            'null url for product' => array(
                array(
                    array('products', 1, null, false),
                ),
            ),
            '\'\' url for product' => array(
                array(
                    array('products', 1, '', false),
                ),
            ),
            'url for null type' => array(
                array(
                    array(null, 1, $url, false),
                ),
            ),
            'type blog item_id -1' => array(
                array(
                    array('blog', -1, $url, false),
                ),
            ),
            'type blog item_id null' => array(
                array(
                    array('blog', null, $url, false),
                ),
            ),
            'bad type good url' => array(
                array(
                    array('shit', 1, $url, false),
                ),
            ),
            'url for blog with item_id not exists' => array(
                array(
                    array('blog', 99999999999, $url, false),
                ),
            ),
        );
    }

    /**
     * @test download http/https file
     * @dataProvider typesDataProvider
     */
    public function gen_original_dirname($type, $expected)
    {
        $c = $this->class;
        $res = $this->reflect($c)->gen_original_dirname($type);
        dtimer::log(__METHOD__." res: $res");
        $this->assertEquals($expected, $res);
    }


    /**
     * @test
     * @param $type
     * @param $item_id
     * @param $basename
     * @param $expected
     */
    public function upload()
    {
        $basename = 'myfile.png';
        $type = 'products';
        $item_id = 1;
        $expected = 1;
        $tmp_name = tempnam(sys_get_temp_dir(), 'curl_tmp');
        $c = $this->class;

        $_FILES = array(
            'filename' => array(
                'name' => $basename,
                'type' => 'image/png',
                'size' => 5093,
                'tmp_name' => $tmp_name,
                'error' => 0
            ),
        );

        $a = $c->upload($type, $item_id, $tmp_name, $basename);
        if (is_array($a)) {
            $res = $a['item_id'];
            $this->assertEquals($expected, $res);
            if(!$c->delete($type, $a['id'])){
                $this->assertTrue(false);
            }

        }
    }

    /**
     * @test download http/https file
     * @dataProvider urlsDataProvider
     */
    public function download_new_multi_images($a)
    {
        $c = $this->class;
        $del = array();
        if (empty($a)) {
            $this->assertTrue(false);
            return false;
        }

        foreach ($a as $e) {
            if (count($e) !== 4) {
                $this->assertTrue(false);
                return false;
            }
            list($type, $item_id, $url, $exp) = $e;

            $a = $c->download_new($type, $item_id, $url);
            if (is_array($a)) {
                $res = $a['item_id'];
                $filepath_absolute = $a['filepath_absolute'];
                if (!isset($del[$a['id']])) {
                    $del[$a['id']] = $a['id'];
                }
            } else{
                $res = $a;
            }

            $this->assertEquals($exp, $res);

        }
        foreach($del as $id){
            if(!$c->delete($type, $id)){
                $this->assertTrue(false);
            }
        }

    }

    protected function setUp()
    {
        dtimer::$enabled = true;
        $this->class = new Image();
        $this->simpla = new Simpla();
        $this->root = $this->simpla->config->root_dir;
    }

    protected function tearDown()
    {
        dtimer::show_console();
    }


    /**
     * @test image->download()
     */
    public function download(){

        $c = $this->class;
        $type = 'products';
        $item_id = 1;
        $basename = 'https://www.google.com/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png';

        $image_id = $c->add($type, $item_id, $basename);
        $res = $c->download($type, $image_id);
        $c->delete($type, $image_id);

        $this->assertEquals($image_id, $res['id'] );
    }

}


?>
