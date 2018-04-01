<?php
use PHPUnit\Framework\TestCase;
ob_start();
require_once('../api/ControllerResize.php');


class TestControllerResize extends TestCase
{
    use \Xpmock\TestCaseTrait;

    private $class;

    protected function setUp()
    {
        dtimer::$enabled = true;
        $this->class = new ControllerResize();

    }

    protected function tearDown()
    {
        dtimer::show_console();
    }

    /**
     * @test
     */
    public function nothing()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function read(){
        $c = $this->class;
        $content = "This is my test content";
        $file = tempnam(sys_get_temp_dir(), 'tmp');
        file_put_contents($file, $content);
        $output = $this->reflect($c)->read($file);
        unlink($file);
        $this->assertEquals($content, $output);
    }

    /**
     * @test
     */
    public function read_partial(){
        $c = $this->class;
        $content = "This is my test content";
        $_SERVER['HTTP_RANGE'] = 'range = 0-10';
        $file = tempnam(sys_get_temp_dir(), 'tmp');
        file_put_contents($file, $content);
        $output = $this->reflect($c)->read($file);
        unlink($file);
        $this->assertEquals(substr($content,0,10), $output);
    }

}


