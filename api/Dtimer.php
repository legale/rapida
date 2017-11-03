<?php
class dtimer
{
    public static $disabled = false;
    protected static $startTime;
    protected static $points = array();

    public static function reset()
    {
        self::$points = null;
        self::$startTime = null;
    }

    public static function run()
    {
        self::$startTime = microtime(true);
    }

    public static function log($message = '')
    {
        if (self::$disabled === true) {
            return false;
        }

        if (self::$startTime === null)
            self::run();

        self::$points[] = array('message' => $message, 'time' => sprintf('%0.3F', microtime(true) - self::$startTime));
    }

    public static function show()
    {
        if (self::$disabled === true) {
            return false;
        }

        $oldtime = 0;

        echo '
            <table style="table-layout: fixed; overflow-wrap: break-word; width: calc(100% - 20px); margin: 10px; !important; box-sizing: border-box; right:0; top:0; z-index:200; background:#fff !important">
             <tr>
                <th style="width:90%; box-sizing: border-box; border: 1px dotted;">Message</th>
                <th style="box-sizing: border-box; border: 1px dotted;">Diff</th>
                <th style="box-sizing: border-box; border: 1px dotted;">Perc</th>
                <th style="box-sizing: border-box; border: 1px dotted;">Time</th>
            </tr>
        ';
        $last = end(self::$points);
		//reset(self::$points);

        foreach (self::$points as $item) {

            $message = $item['message'];
            $time = $item['time'] * 1000;
            $total = (int)$last['time'] * 1000;
            $diff = round( ($item['time'] - $oldtime) * 1000, 4);
            //$perc = $diff / $endtime;
            if ($total != 0) {
                $perc = $diff / $total;
                $color = round(99 - $perc * 50, 3);
                $perc = round($perc * 100, 2);
            }
            else {
                $color = 255;
                $perc = 0;
            }
            echo "
                <tr>
                    <td style='padding: 3px; box-sizing: border-box; border: 1px dotted;background: hsl( 0, 100%, $color% );'>$message</td>
                    <td style='padding: 3px; box-sizing: border-box; border: 1px dotted;'>{$diff}</td>
                    <td style='padding: 3px; box-sizing: border-box; border: 1px dotted;'>{$perc}</td>
                    <td style='padding: 3px; box-sizing: border-box; border: 1px dotted;'>{$time}</td>
                </tr>
            ";

            $oldtime = $item['time'];
        };
        echo "</table>\n";
    }
    public static function savetxt($filename)
    {
        if (!isset($filename)) return false;
        $oldtime = 0;


        foreach (self::$points as $item) {

            $message = $item['message'];
            $time = $item['time'] * 1000;
            $diff = ($item['time'] - $oldtime) * 1000;

            file_put_contents($filename, $message . " " . $diff . " " . $time . "\n", FILE_APPEND);

            $oldtime = $item['time'];
        };
    }
}
