<?php
declare(strict_types=1);

namespace Utilities\Router\Traits;

use Utilities\Router\Response;
use Utilities\Router\Utils\StatusCode;

/**
 * ControllerDefaultTrait class
 *
 * @link    https://github.com/utilities-php/router
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/router/blob/master/LICENSE (MIT License)
 */
trait ControllerDefaultTrait
{

    /**
     * @return bool
     */
    public function __isAuthorized(): bool
    {
        return true;
    }

    /**
     * @return void
     */
    public function __unauthorized(): void
    {
        Response::send(StatusCode::UNAUTHORIZED, [
            'message' => 'Unauthorized: You are not authorized to access this resource.'
        ]);
    }

}