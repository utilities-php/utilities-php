<?php
declare(strict_types=1);

namespace Utilities\Router;

use Utilities\Router\Interfaces\ApplicationRouteInterface;
use Utilities\Router\Traits\ControllerTrait;
use Utilities\Router\Traits\DirectoryTrait;
use Utilities\Router\Traits\ServicesTrait;
use Utilities\Router\Utils\Assistant;
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
    use ServicesTrait;
    use DirectoryTrait;

    /**
     * Reserved words in router.
     *
     * @var array
     */
    public static array $reserved_words = [
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
     * to handle the exceptions, implement this method in your class
     *
     * @param \Exception $exception
     * @return void
     */
    public function __exception(\Exception $exception): void
    {
        Response::send(StatusCode::INTERNAL_SERVER_ERROR, [
            'description' => "Internal Server Error",
        ]);
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
            $this->__exception(new \Exception(
                $throwable->getMessage(),
                (int)$throwable->getCode(),
                $throwable->getPrevious()
            ));
            die(500);
        });

        self::addExtraDimensions($options);

        $this->__process(Router::createRequest());

        if (Response::getStatusCode() === -1) {
            $this->__notFound();
        }
    }

    /**
     * Find the directories, route and controller by defined values
     *
     * @param array $options ["insensitive"]
     * @return void
     */
    private function addExtraDimensions(array $options = []): void
    {
        if (!Origin::validate() && !str_contains('cli', php_sapi_name())) {
            Response::send(StatusCode::FORBIDDEN, [
                'error_message' => 'Forbidden: Your origin has been blocked from the server.',
            ]);
        }

        Router::any('/execute-watchers', function () {
            Services::run();
        });

        $this->findDirectory([
            'sector' => URLs::segment(0),
            'method' => URLs::segment(1),
        ]);

        foreach ((new \ReflectionClass($this))->getMethods(\ReflectionMethod::IS_PUBLIC) as $refMethod) {
            if (Assistant::hasRouteAttribute($refMethod)) {
                foreach (Assistant::extractRouteAttributes($refMethod) as $route) {
                    $methodName = $refMethod->getName();
                    Router::match($route->getMethod(), $route->getUri(), function () use ($methodName) {
                        Assistant::passDataToMethod($this, $methodName);
                    });
                }
            }
        }
    }

}