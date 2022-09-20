<?php
declare(strict_types=1);

namespace Utilities\Routing\Interfaces;

/**
 * RouteCommonInterface class
 *
 * @link    https://github.com/utilities-php/routing
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/routing/blob/master/LICENSE (MIT License)
 */
interface SecureRouteInterface
{

    /**
     * Implement this method to check if the user is authorized to access the route.
     *
     * @return bool
     */
    public function __isAuthorized(): bool;

    /**
     * By implementing this method, you can customize your unauthorized response.
     *
     * @return void
     */
    public function __unauthorized(): void;

}