<?php
declare(strict_types=1);

namespace Utilities\Routing\Traits;

use Utilities\Routing\Controller;
use Utilities\Routing\Request;
use Utilities\Routing\Router;
use Utilities\Routing\URL;
use Utilities\Routing\Utils\Assistant;

/**
 * RouterTrait class
 *
 * @link    https://github.com/utilities-php/routing
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/routing/blob/master/LICENSE (MIT License)
 */
trait RouterTrait
{

    /**
     * Auto resolve
     *
     * @var bool
     */
    protected static bool $AUTO_RESOLVE = true;

    /**
     * @var array
     */
    private static array $routes = [];

    /**
     * @var array|Controller[]
     */
    private static array $controllers = [];

    /**
     * The resorces routes
     *
     * @var array
     */
    private static array $resources = [];

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
                Request::setParams($find['params']);
                Assistant::passDataToCallback($callback, [
                    'params' => $find['params'],
                ]);
            }
        }
    }

    /**
     * Find dynamic route with the given uri
     *
     * @param string $uri
     * @return array|false {method, route, params}
     */
    private static function find(string $uri): array|false
    {
        foreach (static::$routes[URL::getMethod()] ?? [] as $route => $callback) {
            if (preg_match_all(self::convertUri2RegExp($route), $uri)) {
                return [
                    'params' => URL::parseParams($route),
                    'method' => URL::getMethod(),
                    'route' => $route,
                ];
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