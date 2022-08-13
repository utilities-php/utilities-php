<?php
declare(strict_types=1);

namespace Utilities\Router\Traits;

use Utilities\Common\Common;
use Utilities\Common\Connection;
use Utilities\Common\Loop;

/**
 * WatcherTrait class
 *
 * @link    https://github.com/utilities-php/router
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/router/blob/master/LICENSE (MIT License)
 */
trait WatcherTrait
{

    /**
     * Add a watcher.
     *
     * @param array $watchers [['key', 'class', 'interval'], ...]
     * @return void
     */
    public function addWatcher(array $watchers): void
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

            $this->watchers[] = $watcher;
        }
    }

    /**
     * execute the watchers
     *
     * @return void
     */
    private function runWatchers(): void
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

            foreach ($this->watchers as $watcher) {
                if (time() - $last_runs[$watcher['key']] >= $watcher['interval']) {
                    (new $watcher['class']())->__run();
                    $last_runs[$watcher['key']] = time();
                }
            }
        });

        die(200);
    }

}