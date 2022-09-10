<?php
declare(strict_types=1);

namespace Utilities\Router\Traits;

use RuntimeException;
use Utilities\Router\Application;
use Utilities\Router\Controller;
use Utilities\Router\Router;

/**
 * ControllerTrait class
 *
 * @link    https://github.com/utilities-php/router
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/router/blob/master/LICENSE (MIT License)
 */
trait ControllerTrait
{

    /**
     * Set the controllers
     *
     * @param array $input [slug => {controller, uri}]
     * @return void
     */
    public function addController(array $input): void
    {
        $controllers = [];
        foreach ($input as $controller) {
            $key = array_key_first($controller);
            $controllers[$key] = $controller[$key];
        }

        foreach ($controllers as $slug => $value) {
            if (!is_subclass_of($value['controller'], Controller::class)) {
                throw new RuntimeException(sprintf(
                    'Class `%s` must be a instance of `%s`',
                    $value['controller'],
                    Controller::class
                ));
            }

            if (in_array($slug, Application::$reserved_words)) {
                throw new RuntimeException(sprintf(
                    'The slug `%s` is reserved word',
                    $slug
                ));
            }

            Router::controller($slug, $value['uri'], $value['controller']);
        }
    }

}