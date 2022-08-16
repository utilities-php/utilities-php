<?php
declare(strict_types=1);

namespace Utilities\Router\Traits;

use Utilities\Router\Controller;
use Utilities\Router\Router;

/**
 * RouterTrait class
 *
 * @link    https://github.com/utilities-php/router
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/router/blob/master/LICENSE (MIT License)
 */
trait RouterTrait
{

    /**
     * @var array
     */
    private static array $routes = [];

    /**
     * @var array|Controller[]
     */
    private static array $controllers = [];

    /**
     * The rate limited routes.
     *
     * @var array
     */
    private static array $rateLimitedRoutes = [];

    /**
     * @var array|string[]
     */
    private static array $defaultMethods = [
        'ANY',
        'GET',
        'POST',
        'PUT',
        'PATCH',
        'DELETE',
        'OPTIONS',
    ];

    /**
     * Find the route and pass the data to the callback
     *
     * @param string $uri
     * @return void
     */
    private static function findAndPassData(string $uri): void
    {
        if (($find = Router::find($uri)) !== false) {
            if (is_callable(($callback = static::$routes[$find['method']][$find['route']]))) {
                call_user_func_array($callback, [...$find['params']]);
            }
        }
    }

    /**
     * Find dynamic route with the given uri
     *
     * @param string $uri
     * @return array|false [method, route, params]
     */
    private static function find(string $uri): array|false
    {
        foreach (static::$routes as $method => $routes) {
            foreach ($routes as $route => $callback) {
                if (preg_match_all(self::convertUri2RegExp($route), $uri, $matches)) {
                    $params = [];
                    foreach ($matches as $key => $value) {
                        if (is_numeric($key)) {
                            continue;
                        }

                        $params[$key] = $value[0];
                    }

                    return [
                        'method' => $method,
                        'route' => $route,
                        'params' => $params,
                    ];
                }
            }
        }

        return false;
    }

    /**
     * @param string $uri
     * @return string
     */
    private static function convertUri2RegExp(string $uri): string
    {
        if (str_contains($uri, '{')) {
            $uri = preg_replace('/\{([^}]+)}/', '(?<$1>[^/]+)', $uri);
        }

        return '#^' . $uri . '$#';
    }

}