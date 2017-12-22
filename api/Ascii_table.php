<?php

/**
 * This library can generate ASCII pseudographic table from an array
 * One public method draw()
 */
class Ascii_table
{
    const UPLEFT = '┌';
    const UPRIGHT = '┐';
    const DOWNLEFT = '└';
    const DOWNRIGHT = '┘';

    const SPACE = ' ';
    const CROSS = '┼';
    const UPCROSS = '┬';
    const DOWNCROSS = '┴';
    //const HORIZ = '─';
    const HORIZ = '-';
    const VERT = '│';
    const MAX_WIDTH = 55;


    /**
     * @param $table
     * @return string
     */
    public function draw($table)
    {
        if (!is_array($table) || !is_array(reset($table))) {
            return 'wrong table';
        }

        $headers = $this->columns_headers($table);
        $col_len = $this->columns_lengths($table, $headers);

        $res = array();
        $res[] = "\n";
        $res[] = $this->draw_separator($col_len, 'top');
        $res[] = $this->draw_row($headers, $col_len, true);
        foreach ($table as $row) {
            $res[] = $this->draw_separator($col_len);
            $res[] = $this->draw_row($row, $col_len);
        }
        $res[] = $this->draw_separator($col_len, 'bottom');
        return implode('', $res);

    }

    /**
     * @param $table
     * @return array
     */
    private function columns_headers($table)
    {
        $k = array_keys(reset($table));
        return array_combine($k, $k);
    }

    /**
     * @param $table
     * @param $headers
     * @return array
     */
    private function columns_lengths($table, $headers)
    {
        $headers = array_map('mb_strlen', $headers);

        $cnt = count($headers);
        $max_width = self::MAX_WIDTH  - $cnt - 1;
        $width = 0;
        foreach ($table as $num => $row) {
            foreach ($row as $col => $cell) {
                $headers[$col] = max($headers[$col], mb_strlen($cell));
                $width += $headers[$col];
            }
        }

        //average column length
        $col_len = max(2, floor($max_width / $cnt));

        //computed max. table width
        $max_width_comp = $col_len * $cnt;
        $max_width = ($max_width_comp > $max_width) ? $max_width_comp : $max_width;

        $chars_left = $max_width;
        $headers_orig = $headers;

        foreach ($headers as &$len) {
            if ($len > $col_len) {
                $len = $col_len;
            }
            $chars_left -= $len;
        }
        unset($len);

        //оставшиеся свободными символы, которые можно добавить к ячейкам
        $free_chars = $chars_left;
        //счетчик циклов
        $cycle = 1;

        //крутим пока есть свободные симвовы и пока счетчик циклов меньше начального количества свободных символов
        while ($chars_left > 0 && $cycle < $free_chars) {
            foreach ($headers_orig as $k => $len) {
                if ($len > $headers[$k]) {
                    $headers[$k]++;
                    $chars_left--;
                }
            }
            $cycle++;
        }


        return $headers;
    }

    /**
     * @param $col_len
     * @return string
     */
    private function draw_separator($col_len, $type = 'middle')
    {
        $str = '';
        $first = true;
        $i = 1;
        $cnt = count($col_len);

        switch ($type) {
            case 'top':
                foreach ($col_len as $len) {
                    $str .= $i === 1 ? self::UPLEFT : self::UPCROSS;
                    $str .= str_repeat(self::HORIZ, $len);
                    $str .= $i === $cnt ? self::UPRIGHT . "\n" : '';
                    $i++;
                }
                break;

            case 'middle':
                foreach ($col_len as $len) {
                    $str .= $i === 1 ? self::VERT : self::CROSS;
                    $str .= str_repeat(self::HORIZ, $len);
                    $str .= $i === $cnt ? self::VERT . "\n" : '';
                    $i++;
                }
                break;

            case 'bottom':
                foreach ($col_len as $len) {
                    $str .= $i === 1 ? self::DOWNLEFT : self::DOWNCROSS;
                    $str .= str_repeat(self::HORIZ, $len);
                    $str .= $i === $cnt ? self::DOWNRIGHT . "\n" : '';
                    $i++;
                }
                break;
        }

        return $str;
    }

    /**
     * @param $row
     * @param $col_len
     * @param bool $align
     * @return string
     */
    private function draw_row($row, $col_len, $align = false)
    {
        $str = '';
        $multirow = $this->make_row($row, $col_len);
        foreach ($multirow as $line => $cell_array) {
            $st = array();
            foreach ($cell_array as $col => $cell) {
                $max = $col_len[$col];
                $len = mb_strlen($cell);
                $start = $align ? intdiv($max - $len, 2) : 0;

                $end = $max - $start - $len;
                preg_match_all('`.`u', $cell, $arr);
                $str .= self::VERT;
                $str .= str_repeat(self::SPACE, $start);
                $str .= $cell;
                $str .= str_repeat(self::SPACE, $end);
            }
            $str .= self::VERT . "\n";
        }
        return $str;
    }

    /**
     * @param $row
     * @param $col_len
     * @return array
     */
    private function make_row($row, $col_len)
    {
        $res = array();
        foreach ($row as $col => $cell) {
            $max_len = $col_len[$col];
            $res[$col] = mb_str_split($cell, $max_len);
        }
        //max. lines per one row
        $max_lines = array_reduce($res, function ($c, $i) {
            return max($c, count($i));
        });

        foreach ($res as &$el) {
            $el = array_pad($el, $max_lines, '');
        }
        $lines = array_keys($el);
        $cols = array_keys($col_len);
        unset($el);

        $final = array();
        foreach ($cols as $col) {
            foreach ($lines as $line) {
                $final[$line][$col] = strtr(html_entity_decode($res[$col][$line]), array("\n" =>" ", "\r" => " ", "\t"=> " "));
            }
        }

        return $final;
    }

    /**
     * @param $cell
     * @param $max_len
     */
    public function make_cell($cell, $max_len)
    {
        return;
    }


}


/**
 * @param $string
 * @param int $split_length
 * @return array
 */
function mb_str_split($string, $split_length = 1)
{
    preg_match_all('`.`u', $string, $arr);
    $arr = array_chunk($arr[0], $split_length);
    $arr = array_map('implode', $arr);
    return $arr;
}

?>
