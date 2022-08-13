<?php
declare(strict_types=1);

namespace Utilities\Router;

use Utilities\Router\Interfaces\ApplicationRouteInterface;
use Utilities\Router\Traits\ControllerTrait;
use Utilities\Router\Traits\DirectoryTrait;
use Utilities\Router\Traits\WatcherTrait;
use Utilities\Router\Utils\StatusCode;

/**
 * Application class
 *
 * @link    https://github.com/utilities-php/router
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/router/blob/master/LICENSE (MIT License)
 */
abstract class Application implements ApplicationRouteInterface
{

    use ControllerTrait;
    use WatcherTrait;
    use DirectoryTrait;

    /**
     * Reserved words in router.
     *
     * @var array
     */
    public static array $reservedWords = [
        'execute-watchers',
    ];

    /**
     * for handle the not found path exception, implement this method
     *
     * @return void
     */
    public function __notFound(): void
    {
        Response::send(StatusCode::NOT_FOUND, [
            'description' => "Not Found",
        ]);
    }

    /**
     * Find the directories, route and controller by defined values
     *
     * @param array $options ["insensitive"]
     * @return void
     */
    protected function findPath(array $options = []): void
    {
        if (!Origin::validate()) {
            Response::send(StatusCode::FORBIDDEN, [
                'error_message' => 'Origin not allowed',
            ]);
        }

        Router::any('/execute-watchers', function () {
            $this->runWatchers();
        });
    }

    /**
     * Resolve the route
     *
     * @param array $options (optional) [insensitive]
     * @return void
     */
    public function resolve(array $options = []): void
    {
        set_exception_handler(function (\Throwable $throwable) {
            $this->__exception($throwable);
            die(500);
        });

        $this->__process(Router::createRequest());

        self::findPath($options);
    }

    /**
     * to handle the exceptions, implement this method in your class
     *
     * @param \Throwable $throwable
     * @return void
     */
    public function __exception(\Throwable $throwable): void
    {
        Response::send(StatusCode::INTERNAL_SERVER_ERROR, [
            'description' => "Internal Server Error",
        ]);
    }

}