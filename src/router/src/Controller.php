<?php

namespace Utilities\Router;

/**
 * Controller class
 *
 * @link    https://github.com/utilities-php/router
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/router/blob/master/LICENSE (MIT License)
 */
class Controller
{

    /**
     * Key
     *
     * @var string
     */
    protected string $key;

    /**
     * Controller Constructor
     *
     * @param string $method The name of method to be called
     * @param array $middleSegments The next segment of the URL
     */
    public function __construct(string $method, array $middleSegments)
    {
        $this->key = $method;

        if (method_exists($this, '__process')) {
            $this->__process($method, $middleSegments);
        }

        Application::passDataToMethod($this, $method);
    }

}