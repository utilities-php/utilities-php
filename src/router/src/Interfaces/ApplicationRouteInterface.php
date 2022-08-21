<?php
declare(strict_types=1);

namespace Utilities\Router\Interfaces;

/**
 * ApplicationInterface class
 *
 * @link    https://github.com/utilities-php/router
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/router/blob/master/LICENSE (MIT License)
 */
interface ApplicationRouteInterface extends CommonRouteInterface
{

    /**
     * On exception, this method will be called.
     *
     * @param \Exception $exception the exception
     * @return void
     */
    public function __exception(\Exception $exception): void;

    /**
     * On not found, this method will be called.
     *
     * @return void
     */
    public function __notFound(): void;

    /**
     * Define the allowed domains.
     *
     * @return array
     */

}