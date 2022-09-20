<?php
declare(strict_types=1);

namespace Utilities\Routing;

/**
 * Service class
 *
 * @link    https://github.com/utilities-php/routing
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/routing/blob/master/LICENSE (MIT License)
 */
abstract class Service
{

    /**
     * On execution this method will be called.
     *
     * @return void
     */
    abstract public function __run(): void;

}