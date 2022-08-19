<?php
declare(strict_types=1);

namespace Utilities\Router\Utils;

use Utilities\Router\AnonymousController;
use Utilities\Router\Attributes\Route;
use Utilities\Router\Controller;
use Utilities\Router\Request;
use Utilities\Router\Router;
use Utilities\Router\URLs;

/**
 * Assistant class
 *
 * @link    https://github.com/utilities-php/router
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/router/blob/master/LICENSE (MIT License)
 */
class Assistant
{

    /**
     * Pass data to anonymous function
     *
     * @param callable $callback
     * @param array $mergeWith (optional)
     * @return bool
     */
    public static function passDataToCallback(callable $callback, array $mergeWith = []): bool
    {
        if (!is_callable($callback)) {
            return false;
        }

        try {

            $reflection = new \ReflectionFunction($callback);
            $arguments = $reflection->getParameters();

            call_user_func_array($callback, self::generatePassingData($arguments, $mergeWith));

            return true;

        } catch (\Exception $e) {
            throw new \RuntimeException(
                $e->getMessage(),
                $e->getCode(),
                $e->getPrevious()
            );
        }
    }

    /**
     * Generate passing data
     *
     * @param \ReflectionParameter[] $require ["headers", "queries", "params", "input"]
     * @param array $mergeWith
     * @return array
     */
    private static function generatePassingData(array $require, array $mergeWith = []): array
    {
        $methodParams = [];
        $fetchRequirements = [];

        foreach ($require as $key => $value) {
            $fetchRequirements[$key] = $value->getName();
        }

        foreach ($fetchRequirements as $key) {
            if ($key == 'headers') {
                $methodParams['headers'] = getallheaders();
            }
            if ($key == 'queries') {
                $methodParams['queries'] = array_merge($_GET, $_POST, URLs::QueryString());
            }
            if ($key == 'params') {
                $methodParams['params'] = Request::getParams();
            }
            if ($key == 'input') {
                $methodParams['input'] = file_get_contents('php://input');
            }
        }

        return array_merge($methodParams, $mergeWith);
    }

    /**
     * Register route from a controller
     *
     * @param Controller $class controller class
     * @param string $method method name
     * @param Route $route
     * @return void
     */
    public static function registerRoute(Controller $class, string $method, Route $route): void
    {
        if (!method_exists($class, $method)) {
            throw new \RuntimeException(sprintf(
                'Method `%s` not found in class `%s`',
                $method,
                get_class($class)
            ));
        }

        Router::match($route->getMethod(), $route->getUri(), function () use ($class, $method) {
            Assistant::passDataToMethod($class, $method);
        });
    }

    /**
     * Pass the request to the controller for self-processing
     *
     * @param object $class the controller class
     * @param string $method the method name
     * @param array $mergeWith (optional)
     * @return bool
     */
    public static function passDataToMethod(object $class, string $method, array $mergeWith = []): bool
    {
        if (!method_exists($class, $method)) {
            return false;
        }

        $reflection = new \ReflectionMethod($class, $method);
        $arguments = $reflection->getParameters();

        if (Assistant::hasRouteAttribute($reflection)) {
            foreach (self::extractRouteAttributes($reflection) as $route) {
                if ($route->isSecure()) {
                    $authenticated = call_user_func([$class, '__isAuthenticated']);
                    if (!$authenticated) {
                        $class->__unauthorized();
                    }
                }
            }
        }

        call_user_func_array([$class, $method], self::generatePassingData($arguments, $mergeWith));

        return true;
    }

    /**
     * Check method has Route attribute
     *
     * @param \ReflectionMethod $reflection
     * @return bool
     */
    public static function hasRouteAttribute(\ReflectionMethod $reflection): bool
    {
        return count($reflection->getAttributes(Route::class)) > 0;
    }

    /**
     * Extract route attributes from method
     *
     * @param \ReflectionMethod $reflection
     * @return array|Route[]
     */
    public static function extractRouteAttributes(\ReflectionMethod $reflection): array
    {
        $attributes = [];
        foreach ($reflection->getAttributes(Route::class) as $attribute) {
            $attributes[] = $attribute->newInstance();
        }
        return $attributes;
    }

}