<?php
declare(strict_types=1);

namespace Utilities\Router\Attributes;

use Attribute;

/**
 * RateLimit class
 *
 * @link    https://github.com/utilities-php/router
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/router/blob/master/LICENSE (MIT License)
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class RateLimit
{

    /**
     * @param int $period The number of seconds in which the limit is applied. (in milliseconds)
     * @param int $rate The number of requests allowed in the period.
     */
    public function __construct(protected int $period, protected int $rate)
    {

    }

    /**
     * @return int
     */
    public function getPeriod(): int
    {
        return $this->period;
    }

    /**
     * @return int
     */
    public function getRate(): int
    {
        return $this->rate;
    }

}