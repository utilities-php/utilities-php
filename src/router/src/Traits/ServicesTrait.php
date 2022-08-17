<?php
declare(strict_types=1);

namespace Utilities\Router\Traits;

use Utilities\Router\Services;

/**
 * ServicesTrait class
 *
 * @link    https://github.com/utilities-php/router
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/router/blob/master/LICENSE (MIT License)
 */
trait ServicesTrait
{

    /**
     * Add a service.
     *
     * @param array $services [['key', 'class', 'interval'], ...]
     * @return void
     */
    public function addService(array $services): void
    {
        Services::add($services);
    }

}