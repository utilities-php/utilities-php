<?php
declare(strict_types=1);

namespace Utilities\Routing;

use BadMethodCallException;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Mime\MimeTypes;
use Utilities\Auth\Session;
use Utilities\Common\Time;
use Utilities\Routing\Exceptions\ControllerException;
use Utilities\Routing\Exceptions\SessionException;
use Utilities\Routing\Traits\RouterTrait;
use Utilities\Routing\Utils\Assistant;

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
 * @link    https://github.com/utilities-php/routing
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/routing/blob/master/LICENSE (MIT License)
 */
class Router
{

    use RouterTrait;

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
            static::$AUTO_RESOLVE = false;
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
            static::$AUTO_RESOLVE = true;
        }

        static::$controllers[$uri] = $controller;
        if (static::$AUTO_RESOLVE) {
            self::resolve();
        }
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
        if (!defined('DISABLE_AUTO_RESOLVE')) {
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

        $uri = $request::getUri();
        $uri = str_ends_with($uri, '/') ? substr($uri, 0, -1) : $uri;
        $uri = !str_starts_with($uri, '/') ? '/' . $uri : $uri;

        $isResource = false;
        foreach (static::$resources as $resource => $location) {
            if (str_starts_with($uri, $resource)) {
                $isResource = true;
                break;
            }
        }

        self::dispatch($request::getMethod(), $uri, $isResource);
    }

    /**
     * dispatch the router
     *
     * @param string $method
     * @param string $uri
     * @param bool $isResource
     * @return void
     */
    protected static function dispatch(string $method, string $uri, bool $isResource): void
    {
        if ($isResource) {
            $resource = self::getResource($uri);
            $path = str_replace($resource['uri'], '', $uri);

            if (file_exists(($source = $resource['location'] . $path))) {

                if (is_dir($source)) {
                    self::loadIndexFile($source);
                }

                if (is_file($source)) {
                    self::loadFile($source);
                }

                exit(0);
            }

            header('HTTP/1.1 404 Not Found');
            exit(0);
        }

        self::mergeAvailableRoutes($method);
        self::findAndPassData($uri);
    }

    /**
     * Load file
     *
     * @param string $source
     * @return void
     */
    protected static function loadFile(string $source): void
    {
        $extension = pathinfo($source, PATHINFO_EXTENSION);

        if ($extension !== 'php') {
            $mime = (new MimeTypes())->getMimeTypes($extension)[0] ?? 'application/octet-stream';
            header('Content-Type: ' . $mime);
        }

        require_once $source;
        exit(0);
    }

    /**
     * Load index file
     *
     * @param string $source
     * @return void
     */
    protected static function loadIndexFile(string $source): void
    {
        $defaultIndex = ['index.php', 'index.html', 'index.htm'];
        $files = glob($source . '/index.*');

        if (empty($files)) {
            header('HTTP/1.1 404 Not Found');
            exit(0);
        }

        foreach ($files as $file) {
            if (in_array(pathinfo($file, PATHINFO_BASENAME), $defaultIndex)) {
                self::loadFile($file);
            }
        }
    }

    /**
     * Get resource
     *
     * @param string $uri
     * @return array
     */
    protected static function getResource(string $uri): array
    {
        $resource = [];
        foreach (static::$resources as $resourceUri => $location) {
            if (str_starts_with($uri, $resourceUri)) {
                $resource = [
                    'uri' => str_ends_with($resourceUri, '/') ? substr($resourceUri, 0, -1) : $resourceUri,
                    'location' => str_ends_with($location, '/') ? substr($location, 0, -1) : $location,
                ];
                break;
            }
        }

        return $resource;
    }

    /**
     * Merge routes for initialization dispatch
     *
     * @param string $currentMethod
     * @return void
     */
    private static function mergeAvailableRoutes(string $currentMethod): void
    {
        if (isset(static::$routes['ANY'])) {
            $merge_data = static::$routes[$currentMethod] ?? [];
            static::$routes[$currentMethod] = array_merge($merge_data, static::$routes['ANY']);
            unset(static::$routes['ANY']);
        }
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
        foreach (static::$routes as $routes) {
            if (array_key_exists($uri, $routes)) {
                return true;
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
     * @param string $uri the uri to rate limit (e.g. /docs). it will be like example.com/docs/*
     * @param string $localPath the absolute path to the directory (e.g. /var/www/html/docs)
     * @return void
     */
    public static function resource(string $uri, string $localPath): void
    {
        static::$resources[$uri] = $localPath;
        if (!defined('DISABLE_AUTO_RESOLVE')) {
            self::resolve();
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