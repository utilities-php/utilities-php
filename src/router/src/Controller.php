<?php
declare(strict_types=1);

namespace Utilities\Router;

use Utilities\Router\Interfaces\ControllerInterface;

/**
 * Controller class
 *
 * @link    https://github.com/utilities-php/router
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/router/blob/master/LICENSE (MIT License)
 */
abstract class Controller extends AnonymousController implements ControllerInterface
{

    public function __process(Request $request): void
    {
        // Simply implement this method is ignored.
    }

}