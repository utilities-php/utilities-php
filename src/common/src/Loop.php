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
     * @param callable $callback
     * @param int $interval in milliseconds
     * @return void
     */
    public static function run(callable $callback, int $interval = 500): void
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
     * @return void
     */
    public static function stop(): void
    {
        static::$running = false;
    }

    /**
     * @param callable $callback
     * @param int $interval (optional) in milliseconds
     * @return void
     */
    public static function runForever(callable $callback, int $interval = 500): void
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

}