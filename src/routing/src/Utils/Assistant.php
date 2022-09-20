<?php
declare(strict_types=1);

namespace Utilities\Routing\Utils;

use ReflectionFunction;
use ReflectionMethod;
use ReflectionParameter;
use RuntimeException;
use Utilities\Routing\Attributes\Route;
use Utilities\Routing\Controller;
use Utilities\Routing\Request;
use Utilities\Routing\Router;
use Utilities\Routing\URL;

/**
 * Assistant class
 *
 * @link    https://github.com/utilities-php/routing
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/routing/blob/master/LICENSE (MIT License)
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
            $arguments = (new ReflectionFunction($callback))->getParameters();
        } catch (\ReflectionException $e) {
            throw new RuntimeException(
                $e->getMessage(),
                (int)$e->getCode(),
                $e->getPrevious()
            );
        }

        call_user_func_array($callback, self::generatePassingData($arguments, $mergeWith));

        return true;
    }

    /**
     * Generate passing data
     *
     * @param ReflectionParameter[] $require ["request", "headers", "query", "params", "input"]
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
            if ($key == 'request') {
                $methodParams['request'] = Request::getInstance();
            }
            if ($key == 'headers') {
                $methodParams['headers'] = getallheaders();
            }
            if ($key == 'query') {
                $methodParams['query'] = array_merge($_GET, $_POST, URL::QueryString());
            }
            if ($key == 'params') {
                $methodParams['params'] = Request::getParams();
            }
            if ($key == 'input') {
                $methodParams['input'] = file_get_contents('php://input');
            }
        }

        foreach ($mergeWith as $key => $value) {
            if (in_array($key, $fetchRequirements)) {
                $methodParams = array_merge($methodParams, [
                    $key => $value
                ]);
            }
        }

        return $methodParams;
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
            throw new RuntimeException(sprintf(
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

        $reflection = new ReflectionMethod($class, $method);
        $arguments = $reflection->getParameters();

        if (Assistant::hasRouteAttribute($reflection)) {
            foreach (self::extractRouteAttributes($reflection) as $route) {
                if ($route->isSecure()) {
                    $authenticated = call_user_func([$class, '__isAuthorized']);
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
     * @param ReflectionMethod $reflection
     * @return bool
     */
    public static function hasRouteAttribute(ReflectionMethod $reflection): bool
    {
        return count($reflection->getAttributes(Route::class)) > 0;
    }

    /**
     * Extract route attributes from method
     *
     * @param ReflectionMethod $reflection
     * @return array|Route[]
     */
    public static function extractRouteAttributes(ReflectionMethod $reflection): array
    {
        $attributes = [];
        foreach ($reflection->getAttributes(Route::class) as $attribute) {
            $attributes[] = $attribute->newInstance();
        }
        return $attributes;
    }

}