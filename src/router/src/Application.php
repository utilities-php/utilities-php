<?php
declare(strict_types=1);

namespace Utilities\Router;

use ReflectionClass;
use ReflectionMethod;
use Throwable;
use Utilities\Auth\Session;
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
     * Resolve the route
     *
     * @param array $options (optional) [insensitive]
     * @return void
     */
    public function resolve(array $options = []): void
    {
        set_exception_handler(function (Throwable $throwable) {
            $this->__exception($throwable);
            die(500);
        });

        self::addExtraDimensions($options);

        $this->__process(Router::createRequest());

        $this->findDirectory([
            'sector' => URL::segment(0),
            'method' => URL::segment(1),
        ]);

        if (Response::getStatusCode() === -1) {
            $this->__notFound();
        }
    }

    /**
     * to handle the exceptions, implement this method in your class
     *
     * @param Throwable $throwable
     * @return void
     */
    public function __exception(Throwable $throwable): void
    {
        Response::send(StatusCode::INTERNAL_SERVER_ERROR, [
            'description' => "Internal Server Error",
        ]);
    }

    /**
     * Find the directories, route and controller by defined values
     *
     * @param array $options ["insensitive"]
     * @return void
     */
    private function addExtraDimensions(array $options = []): void
    {
        if (!str_contains('cli', php_sapi_name())) {
            if (!Origin::validate()) {
                Response::send(StatusCode::FORBIDDEN, [
                    'error_message' => 'Forbidden: Your origin has been blocked from the server.',
                ]);
            }

            Session::start([
                'samesite' => Origin::isLocalhost() ? 'None' : 'Lax',
                'secure' => $_SERVER['HTTPS'] == 'on',
                'httponly' => true
            ]);
        }

        Router::any('/execute-watchers', function () {
            Services::run();
        });

        foreach ((new ReflectionClass($this))->getMethods(ReflectionMethod::IS_PUBLIC) as $refMethod) {
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

}