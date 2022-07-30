<?php
declare(strict_types=1);

namespace Utilities\Common;

/**
 * Printer class
 *
 * @link    https://github.com/utilities-php/common
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/common/blob/master/LICENSE (MIT License)
 */
class Printer
{

    /**
     * Print Json pretty between <pre> tags
     *
     * @param array $array The array to print
     * @return void
     */
    public static function printPrettyJson(array $array): void
    {
        if ($Json = json_encode($array, JSON_PRETTY_PRINT)) {
            echo sprintf('<pre>%s</pre>', $Json);
        }
    }

    /**
     * @param array $array
     * @param bool $pretty
     * @return void
     */
    public static function printJson(array $array, bool $pretty = false): void
    {
        if ($pretty) echo json_encode($array, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        else echo json_encode($array, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Just a normal echo
     *
     * @param string $string
     * @return void
     */
    public static function echo(string $string): void
    {
        echo $string;
    }

    /**
     * Echo & Die
     *
     * @param string $string
     * @return void
     */
    public static function deco(string $string): void
    {
        echo $string;
        die();
    }

}