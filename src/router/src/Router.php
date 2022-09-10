<?php
declare(strict_types=1);

namespace Utilities\Router;

use BadMethodCallException;
use ReflectionClass;
use ReflectionException;
use Utilities\Auth\Session;
use Utilities\Common\Time;
use Utilities\Router\Exceptions\ControllerException;
use Utilities\Router\Exceptions\SessionException;
use Utilities\Router\Traits\RouterTrait;
use Utilities\Router\Utils\Assistant;

/**
 * Router class
 *
 * @method static void any(string $uri, callable $callback) Create a route that matches any HTTP method
 * @method static void get(string $uri, callable $callback) Adds a GET route to the router.
 * @method static void post(string $uri, callable $callback) Adds a POST route to the router.
 * @method static void put(string $uri, callable $callback) Adds a PUT route to the router.
 * @method static void delete(string $uri, callable $callback) Adds a DELETE route to the router.
 * @method static void options(string $uri, callable $callback) Adds a OPTIONS route to the router.
 *
 * @link    https://github.com/utilities-php/router
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/router/blob/master/LICENSE (MIT License)
 */
class Router
{

    use RouterTrait;

    protected static bool $CAN_RESOLVE = true;

    /**
     * rate limit
     *
     * @param string $uri the uri to rate limit (e.g. /api/v1/users)
     * @param int $period the time period (e.g. 1 minute)
     * @param int $rate the limit of requests (e.g. 100 requests per minute)
     * @return void
     */
    public static function rateLimiter(string $uri, int $period, int $rate): void
    {
        if (Session::getStatus() !== 2) {
            throw new SessionException(
                'Session is not started yet. Please call Session::start() first.'
            );
        }

        static::$rateLimitedRoutes[$uri] = [
            'rate' => $rate,
            'period' => $period,
            'created_at' => Time::getMillisecond(),
            'updated_at' => Time::getMillisecond(),
            'status' => true,
        ];
    }

    /**
     * add a controller to the router
     *
     * @param string $slug the slug of the controller (e.g. users)
     * @param string $uri the uri to rate limit (e.g. /api/v1/users)
     * @param string|AnonymousController $controller the controller class name (e.g. \App\Controllers\UsersController)
     * @return void
     */
    public static function controller(string $slug, string $uri, string|AnonymousController $controller): void
    {
        if (!is_subclass_of($controller, AnonymousController::class)) {
            throw new ControllerException(sprintf(
                'Class `%s` does not exist or not instance of `%s`.',
                $controller::class, Controller::class
            ));
        }

        if (is_string($controller)) {
            $controller = new $controller($slug);
        }

        try {
            static::$CAN_RESOLVE = false;
            foreach ((new ReflectionClass($controller))->getMethods() as $refMethod) {
                if (str_starts_with($refMethod->getName(), '__')) {
                    continue;
                }

                if (Assistant::hasRouteAttribute($refMethod)) {
                    foreach (Assistant::extractRouteAttributes($refMethod) as $route) {
                        $methodName = $refMethod->getName();
                        $routeUri = $uri . $route->getUri();
                        Router::match($route->getMethod(), $routeUri, function (...$args) use ($controller, $methodName) {
                            Assistant::passDataToMethod($controller, $methodName);
                        });
                    }

                } else {
                    $methodName = $refMethod->getName();
                    $routeUri = $uri . '/' . $methodName;

                    if ($methodName === 'index' && !self::isRegistered($uri)) {
                        $routeUri = $uri;
                    }

                    Router::any($routeUri, function (...$args) use ($controller, $methodName) {
                        Assistant::passDataToMethod($controller, $methodName);
                    });
                }

            }

        } catch (ReflectionException $e) {
            throw new ControllerException($e->getMessage(), $e->getCode(), $e);

        } finally {
            static::$CAN_RESOLVE = true;
        }

        self::$controllers[$uri] = $controller;
        self::resolve();
    }

    /**
     * create a route that matches the given HTTP methods
     *
     * @param string $method
     * @param string $uri
     * @param callable $callback
     * @return void
     */
    public static function match(string $method, string $uri, callable $callback): void
    {
        if (str_ends_with($uri, '/') && strlen($uri) > 1) {
            $uri = substr($uri, 0, -1);
        }

        if (!isset(static::$routes[$method])) {
            static::$routes[$method] = [];
        }

        static::$routes[$method][$uri] = $callback;
        if (static::$CAN_RESOLVE) {
            self::resolve();
        }
    }

    /**
     * Resolve the router with the given Request
     *
     * @param Request|null $request
     * @return void
     */
    public static function resolve(Request|null $request = null): void
    {
        if ($request === null) {
            $request = self::createRequest();
        }

        $uri = $request === null ? Request::getUri() : $request::getUri();
        $method = $request === null ? Request::getMethod() : $request::getMethod();
        $uri = str_ends_with($uri, '/') ? substr($uri, 0, -1) : $uri;

        if (isset(static::$routes['ANY'])) {
            $data_to_merge = static::$routes[$method] ?? [];
            static::$routes[$method] = array_merge($data_to_merge, static::$routes['ANY']);
            unset(static::$routes['ANY']);
        }

        self::findAndPassData($uri);
    }

    /**
     * Create request
     *
     * @return Request
     */
    public static function createRequest(): Request
    {
        $find = self::find(URL::getURL());
        $params = $find !== false ? $find['params'] : [];

        $headers = (function () {
            $headers = [];
            foreach ($_SERVER as $key => $value) {
                if (str_starts_with($key, 'HTTP_')) {
                    $headers[str_replace('HTTP_', '', $key)] = $value;
                }
            }
            return $headers;
        })();

        return new Request([
            'uri' => URL::getURL(),
            'method' => URL::getMethod(),
            'headers' => $headers,
            'body' => file_get_contents('php://input'),
            'query_string' => URL::QueryString(),
            'params' => $params,
        ]);
    }

    /**
     * Does uri registered in router?
     *
     * @param string $uri e.g. /api/v1/users or /api/v1/users/{id}
     * @return bool
     */
    public static function isRegistered(string $uri): bool
    {
        foreach (static::$routes as $method => $routes) {
            foreach ($routes as $route => $callback) {
                if ($route === $uri) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * create a resource route. it can be public documents, images, css, js, etc.
     *
     * Note: this will give access to whole directory. (e.g. /public/*)
     *
     * @todo: create test for this method
     *
     * @param string $uri the uri to rate limit (e.g. /docs)
     * @param string $localPath the absolute path to the directory (e.g. /var/www/html/docs)
     * @return void
     */
    public static function resource(string $uri, string $localPath): void
    {
        foreach (scandir($localPath) as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = $localPath . '/' . $file;
                if (is_dir($filePath)) {
                    self::resource($uri . '/' . $file, $filePath);
                } else {
                    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
                    header('Content-Type: ' . PathFinder::getMimeType($extension));
                    self::get($uri . '/' . $file, function () use ($filePath) {
                        return file_get_contents($filePath);
                    });
                }
            }
        }
    }

    /**
     * Clear every all defined routes and controllers
     *
     * @return void
     */
    public static function clear(): void
    {
        static::$routes = [];
        static::$controllers = [];
    }

    /**
     * Remove a route from the router
     *
     * @param string $uri
     * @return void
     */
    public static function removeRoute(string $uri): void
    {
        // TODO: Implement removeRoute() method.
    }

    /**
     * Remove a controller with its routes from the router
     *
     * @param string $slug
     * @return void
     */
    public static function removeController(string $slug): void
    {
        // TODO: Implement removeController() method.
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic(string $name, array $arguments): mixed
    {
        if (in_array(strtoupper($name), self::$defaultMethods)) {
            self::match(strtoupper($name), $arguments[0], $arguments[1]);
            return true;
        }

        if (method_exists(self::class, $name)) {
            return call_user_func_array([self::class, $name], $arguments);
        }

        throw new BadMethodCallException(sprintf(
            'Call to undefined method %s::%s()',
            self::class, $name
        ));
    }

}