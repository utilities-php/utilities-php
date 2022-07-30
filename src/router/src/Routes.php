<?php

namespace Utilities\Router;

/**
 * Router class
 *
 * @link    https://github.com/utilities-php/router
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/router/blob/master/LICENSE (MIT License)
 */
abstract class Routes
{

    /**
     * @var string
     */
    protected string $key;

    /**
     * @var array
     */
    private array $controllers;

    /**
     * Route constructor.
     *
     * @param string $key
     */
    public function __construct(string $key)
    {
        $this->key = $key ?? '';
        $this->controllers = [];
    }

    /**
     * Set the controllers
     *
     * @param array $input [sector => class]
     * @return bool
     */
    public function addController(array $input): bool
    {
        foreach ($input as $sector => $class) {
            if (!is_subclass_of($class, '\Utilities\API\Controller')) {
                return false;
            }

            $this->controllers[$sector] = $class;
        }

        return true;
    }

    /**
     * Find the controller
     *
     * @param array $request ["sector", "method"]
     * @param array $options ["insensitive"]
     * @return void
     */
    public function findPath(array $request, array $options = []): void
    {
        $insensitive = $options['insensitive'] ?? true;

        if (Application::passDataToMethod($this, $request['method'])) return;

        if (($class = $this->getController($request['sector'], $insensitive)) !== false) {
            $nextSegmentOfMethod = $this->findIndexOfSegment($this->key, $insensitive);
            $middleSegments = array_slice(URLs::getSegments(), $nextSegmentOfMethod + 2);
            array_pop($middleSegments);
            new $class($request['method'], $middleSegments ?? []);
        }
    }

    /**
     * Get the controller by array key
     *
     * @param string $key
     * @param bool $insensitive
     * @return string|false
     */
    public function getController(string $key, bool $insensitive = false): string|false
    {
        if ($insensitive) {
            return array_change_key_case($this->controllers)[strtolower($key)] ?? false;
        }

        return $this->controllers[$key] ?? false;
    }

    /**
     * Find index of segment
     *
     * @param string $segment
     * @param bool $insensitive
     * @return int
     */
    private function findIndexOfSegment(string $segment, bool $insensitive): int
    {
        if ($insensitive) {
            $segment = strtolower($segment);
        }

        foreach (URLs::getSegments() as $index => $value) {
            if ($insensitive) {
                $value = strtolower($value);
            }

            if ($value == $segment) {
                return $index;
            }
        }

        return -1;
    }

    /**
     * When the route has been called, this method will be called.
     *
     * @param array $request ["sector", "method"]
     * @return void
     */
    abstract public function __process(array $request): void;

}