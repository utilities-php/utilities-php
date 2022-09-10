<?php
declare(strict_types=1);

namespace Utilities\Router\Attributes;

use Attribute;

/**
 * Route class
 *
 * @link    https://github.com/utilities-php/router
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/router/blob/master/LICENSE (MIT License)
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Route
{

    public function __construct(protected string $method, protected string $uri, protected bool $secure = false)
    {

    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return str_starts_with($this->uri, "/") ? $this->uri : "/{$this->uri}";
    }

    /**
     * @return bool
     */
    public function isSecure(): bool
    {
        return $this->secure;
    }

}