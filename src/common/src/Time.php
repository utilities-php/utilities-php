<?php
declare(strict_types=1);

namespace Utilities\Common;

/**
 * Time class
 *
 * @link    https://github.com/utilities-php/common
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/common/blob/master/LICENSE (MIT License)
 */
class Time
{

    /**
     * Get the current elapsed time from request in milliseconds
     *
     * @return float
     */
    public static function elapsedTime(): float
    {
        return round(((microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]) * 1000));
    }

    /**
     * Get the current time in milliseconds
     *
     * @return float
     */
    public static function getMillisecond(): float
    {
        return round(microtime(true) * 1000);
    }

    /**
     * Get Time in UTC format
     *
     * @param int $time
     * @param bool $zeroTime By setting this to true, it will return 'Y-m-d' format
     * @return string
     */
    public static function UTCTime(int $time, bool $zeroTime = false): string
    {
        date_default_timezone_set('UTC');
        if ($zeroTime) return date("Y-m-d", $time) . "T00:00:00Z";
        else return date("Y-m-d", $time) . "T" . date("H:i:s", $time) . "Z";
    }

}