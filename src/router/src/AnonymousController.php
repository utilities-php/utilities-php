<?php
declare(strict_types=1);

namespace Utilities\Router;

use Utilities\Router\Traits\ControllerDefaultTrait;
use Utilities\Router\Utils\Assistant;

/**
 * AnonymousController class
 *
 * @link    https://github.com/utilities-php/router
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/router/blob/master/LICENSE (MIT License)
 */
class AnonymousController
{

    use ControllerDefaultTrait;

    /**
     * @var string|null
     */
    protected ?string $key = null;

    /**
     * AnonymousController constructor.
     *
     * @param mixed ...$args
     */
    public function __construct(mixed ...$args)
    {
        call_user_func_array([$this, '__process'], [Router::createRequest(), ...$args]);
        $this->key = is_string($args[0]) ? $args[0] : 'anonymous@' . __CLASS__;
        Assistant::passDataToMethod($this, $this->key);
    }

    /**
     * Create an instance of the controller for the application
     *
     * @param string $uri The uri to be matched
     * @param string $class The class name
     * @return array
     */
    public static function __create(string $uri, string $class): array
    {
        $segments = explode('\\', $class);

        $uri = str_ends_with($uri, '/') ? substr($uri, 0, -1) : $uri;
        $uri = str_starts_with($uri, '/') ? $uri : '/' . $uri;

        return [
            end($segments) => [
                'controller' => $class,
                'uri' => $uri
            ]
        ];
    }

    /**
     * Get controller key
     *
     * @return string
     */
    public function __routeKeyName(): string
    {
        return $this->key;
    }

}