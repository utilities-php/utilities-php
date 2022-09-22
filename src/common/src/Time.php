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
     * @param int|string $time [optional] The timestamp to convert
     * @return int
     */
    public static function getMillisecond(int|string $time = ''): int
    {
        $time = $time != '' ? strtotime($time) : microtime(true);
        return (int)round($time * 1000);
    }

    /**
     * Get Time in UTC format
     *
     * @param int $time
     * @param bool $zeroTime By setting this to true, it will return 'Y-m-d' format
     * @return string
     */
    public static function UTCFormat(int $time, bool $zeroTime = false): string
    {
        date_default_timezone_set('UTC');
        if ($zeroTime) {
            return date("Y-m-d", $time) . "T00:00:00Z";
        }

        return date("Y-m-d", $time) . "T" . date("H:i:s", $time) . "Z";
    }

    /**
     * Get instance of DateTime
     *
     * @return \DateTime
     */
    public static function now(): \DateTime
    {
        return new \DateTime();
    }

}