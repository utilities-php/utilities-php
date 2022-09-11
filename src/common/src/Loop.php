<?php
declare(strict_types=1);

namespace Utilities\Common;

/**
 * Loop class
 *
 * @link    https://github.com/utilities-php/common
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/common/blob/master/LICENSE (MIT License)
 */
class Loop
{

    /**
     * Whether the loop is running or not
     *
     * @var bool
     */
    protected static bool $running = false;

    /**
     * Create an infinite loop with custom interval
     *
     * NOTE: To stop the loop, call the stop() method.
     *
     * @param int $interval (e.g. 500 for 500 milliseconds)
     * @param callable $callback The callback function
     * @return void
     */
    public static function run(int $interval, callable $callback): void
    {
        static::$running = true;
        $last_hit = Time::getMillisecond();
        while (static::$running) {
            if (Time::getMillisecond() - $last_hit > $interval) {
                $callback();
                $last_hit = Time::getMillisecond();
            }
        }
    }

    /**
     * Stop the current loop
     *
     * @return void
     */
    public static function stop(): void
    {
        static::$running = false;
    }

}