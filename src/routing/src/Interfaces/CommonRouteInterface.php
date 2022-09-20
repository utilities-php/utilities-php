<?php
declare(strict_types=1);

namespace Utilities\Routing\Interfaces;

use Utilities\Routing\Request;

/**
 * RouteCommonInterface class
 *
 * @link    https://github.com/utilities-php/routing
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/routing/blob/master/LICENSE (MIT License)
 */
interface CommonRouteInterface
{

    /**
     * When the route has been called, this method will be called.
     *
     * @param Request $request
     * @return void
     */
    public function __process(Request $request): void;

}