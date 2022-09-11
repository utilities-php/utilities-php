<?php
declare(strict_types=1);

namespace Utilities\Router;

use RuntimeException;
use Utilities\Common\Common;
use Utilities\Common\Loop;

/**
 * Services class
 *
 * @link    https://github.com/utilities-php/router
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/router/blob/master/LICENSE (MIT License)
 */
class Services
{

    /**
     * Services.
     *
     * @var array
     */
    private static array $services = [];

    /**
     * Add a services
     *
     * @param array $services [['key', 'class', 'interval'], ...]
     * @return void
     */
    public static function add(array $services): void
    {
        foreach ($services as $service) {
            if (!is_array($service) || !is_string($service['class']) || !is_numeric($service['interval'])) {
                throw new RuntimeException(sprintf(
                    'The service `%s` is not valid.',
                    $service
                ));
            }

            if (!method_exists($service['class'], '__run')) {
                throw new RuntimeException(sprintf(
                    'The service `%s` does not have a __run method.',
                    $service['class']
                ));
            }

            static::$services[] = $service;
        }
    }

    /**
     * Create instance of the service.
     *
     * @param string $key The key of the service.
     * @param string $class Service class name.
     * @param int $interval Service interval. (e.g. 60 for 1 minute)
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
     * Remove a service.
     *
     * @param string $key
     * @return void
     */
    public static function remove(string $key): void
    {
        foreach (static::$services as $index => $service) {
            if ($service['key'] === $key) {
                unset(static::$services[$index]);
            }
        }
    }

    /**
     * Run the services.
     *
     * @return void
     */
    public static function run(): void
    {
        header('Content-Type: application/json');
        Response::closeConnection(Common::prettyJson([
            'ok' => true,
            'message' => 'Services are running...'
        ]));

        $last_runs = [];
        $startTime = time();
        Loop::run(500, function () use (&$last_runs, $startTime) {
            if (time() - $startTime > 60) {
                Loop::stop();
            }

            foreach (static::$services as $service) {
                if (time() - $last_runs[$service['key']] >= $service['interval']) {
                    (new $service['class']())->__run();
                    $last_runs[$service['key']] = time();
                }
            }
        });

        die(200);
    }

}