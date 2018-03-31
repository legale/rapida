<?php
if(defined('PHP7')) {
     eval("declare(strict_types=1);");
}

if (!function_exists('convert')) {

//функция для конвертации величин измерения информации
    /**
     * @param $size
     * @return int|string
     */
    function convert($size)
    {
        if ($size == 0) {
            return 0;
        }
        $unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
        $i = (int)floor(log($size, 1024));
        return @round($size / pow(1024, $i), 1) . $unit[$i];
    }
}

if (!function_exists('convert_time')) {
//функция для конвертации времени, принимает значения в секундах
    /**
     * @param $time
     * @return int|string
     */
    function convert_time($time)
    {
        if ($time == 0) {
            return 0;
        }
        //допустимые единицы измерения
        $unit = array(-4 => 'ps', -3 => 'ns', -2 => 'mcs', -1 => 'ms', 0 => 's');
        //логарифм времени в сек по основанию 1000
        //берем значение не больше 0, т.к. секунды у нас последняя изменяемая по тысяче величина, дальше по 60
        $i = min(0, floor(log($time, 1000)));

        //тут делим наше время на число соответствующее единицам измерения т.е. на миллион для секунд,
        //на тысячу для миллисекунд
        $t = @round($time / pow(1000, $i), 1);
        return $t . $unit[$i];
    }
}


/**
 * Class dtimer
 */
class dtimer
{

    /**
     * @var bool
     */
    public static $enabled = true;
    /**
     * @var
     */
    protected static $startTime;
    /**
     * @var array
     */
    protected static $points = array();
    /**
     * @var array
     */
    private static $color_array = array(1 => '#f00', 2 => '#ff0', 3 => '#fff');

    /**
     *
     */
    public static function reset()
    {
        self::$points = null;
        self::$startTime = null;
    }

    /**
     * @param string $message
     * @param null $type
     * @return bool
     */
    public static function log($message = '', $type = null)
    {
        //останавливаемся, если отключено
        if (self::$enabled !== true) {
            return false;
        }

        //тут будем заводить тип сообщения, если он не задан
        // 3 - информация, 2 - предупреждение, 1 - ошибка
        //по умолчанию сообщение будет информационным
        if (!isset($type)) {
            $type = '3';
        }

        if (self::$startTime === null)
            self::run();

        self::$points[] = array('message' => $message,
            'type' => $type, 'ram' => convert(memory_get_usage(true)), 'time' => microtime(true) - self::$startTime);
    return true;
    }

    /**
     *
     */
    public static function run()
    {
        self::$startTime = microtime(true);
    }

    /**
     * @return bool
     */
    public static function show()
    {
        if (self::$enabled !== true) {
            return false;
        }

        $oldtime = 0;

        echo '
			<table style="table-layout: fixed; overflow-wrap: break-word; width: calc(100% - 20px); margin: 10px; !important; box-sizing: border-box; right:0; top:0; z-index:200; background:#fff !important">
			 <tr>
				<th style="width:2%; box-sizing: border-box; border: 1px dotted;">T.</th>
				<th style="width:75%; box-sizing: border-box; border: 1px dotted;">Message</th>
				<th style="width: 5.5%; box-sizing: border-box; border: 1px dotted;">RAM</th>
				<th style="width: 6.5%; box-sizing: border-box; border: 1px dotted;">Diff</th>
				<th style="width: 4.5%; box-sizing: border-box; border: 1px dotted;">Perc</th>
				<th style="width: 6.5%; box-sizing: border-box; border: 1px dotted;">Time</th>
			</tr>
		';
        $last = end(self::$points);
        //reset(self::$points);

        foreach (self::$points as $item) {

            $type = $item['type'];
            $color_type = self::$color_array[$item['type']];
            $message = $item['message'];
            $ram = $item['ram'];
            //время из последней записи
            $total = $last['time'];
            //разница во времени
            $diff = $item['time'] - $oldtime;
            //время из записи пишем для сравнения на следующем цикле
            $oldtime = $item['time'];

            if ($total != 0) {
                $perc = $diff / $total;
                $color = round(99 - $perc * 50, 3);
                $perc = round($perc * 100, 1);
            } else {
                $color = 255;
                $perc = 0;
            }
            //тут сконвертируем все величины времени
            //время из записи
            $time = convert_time($item['time']);
            $diff = convert_time($diff);
            echo "
				<tr>
					<td style='padding: 3px; box-sizing: border-box; border: 1px dotted;background: $color_type;'>{$type}</td>
					<td style='padding: 3px; box-sizing: border-box; border: 1px dotted;background: hsl( 0, 100%, $color% );'>$message</td>
					<td style='padding: 3px; box-sizing: border-box; border: 1px dotted;'>{$ram}</td>
					<td style='padding: 3px; box-sizing: border-box; border: 1px dotted;'>{$diff}</td>
					<td style='padding: 3px; box-sizing: border-box; border: 1px dotted;'>{$perc}</td>
					<td style='padding: 3px; box-sizing: border-box; border: 1px dotted;'>{$time}</td>
				</tr>
			";

        };
        echo "</table>\n";
        //self::$points = array();
        return true;
    }

    /**
     * @param int $width
     * @return bool
     */
    public static function show_console($width = 100)
    {
        require_once(dirname(__FILE__) . '/Table2ascii.php');
        $table = new Table2ascii($width);
        if (self::$enabled !== true) {
            return false;
        }

        $oldtime = 0;


        $last = end(self::$points);
        //reset(self::$points);
        $res = array();
        foreach (self::$points as $item) {

            $type = $item['type'];
            $message = $item['message'];
            $ram = $item['ram'];
            //время из последней записи
            $total = $last['time'];
            //разница во времени
            $diff = $item['time'] - $oldtime;
            //время из записи пишем для сравнения на следующем цикле
            $oldtime = $item['time'];

            if ($total != 0) {
                $perc = $diff / $total;
                $perc = round($perc * 100, 1);
            } else {
                $perc = 0;
            }
            //тут сконвертируем все величины времени
            //время из записи
            $time = convert_time($item['time']);
            $diff = convert_time($diff);
//           echo "****    $type    ram:{$ram}    diff:{$diff}    perc:{$perc}   time:{$time}    ***********\n";
//           echo "$message\n";
//           echo "******************************************************\n";
            $res[] = array(
                'type' => $type,
                'ram' => $ram,
                'diff' => $diff,
                'perc' => $perc,
                'time' => $time,
                'message' => $message,
            );

        };
        //print_r($res);
        print $table->draw($res);
        self::$points = array();
        return true;
    }

}
