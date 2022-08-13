<?php
declare(strict_types=1);

namespace Utilities\Router;

use Utilities\Common\Common;
use Utilities\Common\Connection;
use Utilities\Common\Loop;

/**
 * Watcher class
 *
 * @link    https://github.com/utilities-php/router
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/router/blob/master/LICENSE (MIT License)
 */
class Watcher
{

    /**
     * Watchers.
     *
     * @var array
     */
    private static array $watchers = [];

    /**
     * Add a watcher.
     *
     * @param array $watchers [['key', 'class', 'interval'], ...]
     * @return void
     */
    public static function add(array $watchers): void
    {
        foreach ($watchers as $watcher) {
            if (!is_array($watcher) || !is_string($watcher['class']) || !is_numeric($watcher['interval'])) {
                throw new \RuntimeException(sprintf(
                    'The watcher `%s` is not valid.',
                    $watcher
                ));
            }

            if (!method_exists($watcher['class'], '__run')) {
                throw new \RuntimeException(sprintf(
                    'The watcher `%s` does not have a __run method.',
                    $watcher['class']
                ));
            }

            static::$watchers[] = $watcher;
        }
    }

    /**
     * Create instance of the watcher.
     *
     * @param string $key The key of the watcher.
     * @param string $class Watcher class name.
     * @param int $interval Watcher interval. (e.g. 60 for 1 minute)
     * @return array ['key', 'class', 'interval']
     */
    public static function create(string $key, string $class, int $interval): array
    {
        return [
            'key' => $key,
            'class' => $class,
            'interval' => $interval
        ];
    }

    /**
     * Remove a watcher from the list.
     *
     * @param string $key
     * @return void
     */
    public static function remove(string $key): void
    {
        foreach (static::$watchers as $index => $watcher) {
            if ($watcher['key'] === $key) {
                unset(static::$watchers[$index]);
            }
        }
    }

    /**
     * Run the watchers.
     *
     * @return void
     */
    public static function run(): void
    {
        header('Content-Type: application/json');
        Connection::closeConnection(Common::prettyJson([
            'ok' => true,
            'message' => 'Services are running...'
        ]));

        $last_runs = [];
        $startTime = time();
        Loop::run(function () use (&$last_runs, $startTime) {
            if (time() - $startTime > 60) {
                Loop::stop();
            }

            foreach (static::$watchers as $watcher) {
                if (time() - $last_runs[$watcher['key']] >= $watcher['interval']) {
                    (new $watcher['class']())->__run();
                    $last_runs[$watcher['key']] = time();
                }
            }
        });

        die(200);
    }

}