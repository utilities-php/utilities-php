<?php
declare(strict_types=1);

namespace Utilities\Common;

/**
 * Thread class
 *
 * NOTE: This class is not thread safe.
 *
 * @link    https://github.com/utilities-php/common
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/common/blob/master/LICENSE (MIT License)
 */
class Thread
{

    /**
     * Run a callback with a delay
     *
     * @param int $delay The delay in milliseconds
     * @param callable $callback The anonymous callback function
     * @return void
     */
    public static function delay(int $delay, callable $callback): void
    {
        Thread::millisecondSleep($delay);
        $callback();
    }

    /**
     * Run a callback in with random delay
     *
     * @param int $minInterval in milliseconds
     * @param int $maxInterval in milliseconds
     * @param callable $callback The anonymous callback function
     * @return void
     */
    public static function random(int $minInterval, int $maxInterval, callable $callback): void
    {
        self::rangedSleep($minInterval, $maxInterval);
        $callback();
    }

    /**
     * Sleep for the random time in given range and time is milliseconds
     *
     * @param int $min
     * @param int $max
     * @return void
     */
    public static function rangedSleep(int $min, int $max): void
    {
        self::millisecondSleep(rand($min, $max));
    }

    /**
     * Sleep for given time in milliseconds
     *
     * @param int $millisecond
     * @return void
     */
    public static function millisecondSleep(int $millisecond): void
    {
        usleep($millisecond * 1000);
    }

}