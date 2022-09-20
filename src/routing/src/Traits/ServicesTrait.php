<?php
declare(strict_types=1);

namespace Utilities\Routing\Traits;

use Utilities\Routing\Services;

/**
 * ServicesTrait class
 *
 * @link    https://github.com/utilities-php/routing
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/routing/blob/master/LICENSE (MIT License)
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