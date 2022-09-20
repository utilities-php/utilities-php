<?php
declare(strict_types=1);

namespace Utilities\Validator\Operators;

/**
 * The NumericOperator trait.
 *
 *
 * This is part of the Utilities package.
 *
 * @link https://github.com/utilities-php/utilities-php
 * @author Shahrad Elahi <shahrad@litehex.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
trait NumericOperator
{

    protected function isGreaterThan(int $limit): bool
    {
        return $this->data > $limit;
    }

    protected function isLessThan(int $limit): bool
    {
        return $this->data < $limit;
    }

    protected function isGreaterThanOrEqual(int $limit): bool
    {
        return $this->data >= $limit;
    }

    protected function isLessThanOrEqual(int $limit): bool
    {
        return $this->data <= $limit;
    }

    protected function isEqual(int $limit): bool
    {
        return $this->data == $limit;
    }

    protected function isNotEqual(int $limit): bool
    {
        return $this->data != $limit;
    }

    protected function isBetween(int $min, int $max): bool
    {
        return $this->data >= $min && $this->data <= $max;
    }

    protected function isNotBetween(int $min, int $max): bool
    {
        return $this->data < $min || $this->data > $max;
    }

}