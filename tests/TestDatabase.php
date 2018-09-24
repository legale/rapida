<?php
use PHPUnit\Framework\TestCase;

require_once('../api/Database.php');
require_once('C:/cygwin64/home/ru/composer/vendor/autoload.php');


class TestDatabase extends TestCase
{
	use \Xpmock\TestCaseTrait;
    /**
     * @test
     */
    public function query_bad()
    {
        $s = new Database();
        $r = $s->query("SELECT SELECT 1");
        $this->assertFalse($r);
    }

    /**
     * @test
     */
    public function query()
    {
        $s = new Database();
        $r = $s->query("SELECT 1");
        $this->assertTrue($r);
    }

    /**
     * @test
     */
    public function result_array()
    {
        $s = new Database();
        $r = $s->query("SELECT * FROM __products LIMIT 5");
        $res = $s->results_array();
        $this->assertInternalType('array', $res);
        $this->assertCount(5, $res);
    }

    /**
     * @test
     */
    public function result_array_empty()
    {
        $s = new Database();
        $r = $s->query("SELECT * FROM __products WHERE id = -1");
        $res = $s->results_array();
        $this->assertFalse($res);
        $this->assertEquals(1, true);
    }
    /**
     * @test
     */
    public function dump_table()
    {
        $s = new Database();
        $f = tempnam( sys_get_temp_dir(), 'temp' );
        $h = fopen($f, 'w+');
        $this->reflect($s)->dump_table('s_variants', $h);
        rewind($h);
		$res = fread($h, 9999999);
		$this->assertNotEmpty($res);
    }
}


?>
