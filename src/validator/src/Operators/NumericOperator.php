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

    /**
     * Is greater than.
     *
     * @error_message This value is not greater than {[0]}.
     * @error_code NOT_GREATER_THAN
     *
     * @param int $limit
     * @return bool
     */
    public function isGreaterThan(int $limit): bool
    {
        return $this->data > $limit;
    }

    /**
     * Is less than.
     *
     * @error_message This value is not less than {[0]}.
     * @error_code NOT_LESS_THAN
     *
     * @param int $limit
     * @return bool
     */
    public function isLessThan(int $limit): bool
    {
        return $this->data < $limit;
    }

    /**
     * Is greater than or equal.
     *
     * @error_message This value is not greater than or equal to {[0]}.
     * @error_code NOT_GREATER_THAN_OR_EQUAL
     *
     * @param int $limit
     * @return bool
     */
    public function isGreaterThanOrEqual(int $limit): bool
    {
        return $this->data >= $limit;
    }

    /**
     * Is less than or equal.
     *
     * @error_message This value is not less than or equal to {[0]}.
     * @error_code NOT_LESS_THAN_OR_EQUAL
     *
     * @param int $limit
     * @return bool
     */
    public function isLessThanOrEqual(int $limit): bool
    {
        return $this->data <= $limit;
    }

    /**
     * Is equal.
     *
     * @error_message This value is not equal to {[0]}.
     * @error_code NOT_EQUAL
     *
     * @param int $limit
     * @return bool
     */
    public function isEqual(int $limit): bool
    {
        return $this->data == $limit;
    }

    /**
     * Is not equal.
     *
     * @error_message This value is equal to {[0]}.
     * @error_code EQUAL
     *
     * @param int $limit
     * @return bool
     */
    public function isNotEqual(int $limit): bool
    {
        return $this->data != $limit;
    }

    /**
     * Is between.
     *
     * @error_message This value is not between {[0]} and {[1]}.
     * @error_code NOT_BETWEEN
     *
     * @param int $min
     * @param int $max
     * @return bool
     */
    public function isBetween(int $min, int $max): bool
    {
        return $this->data >= $min && $this->data <= $max;
    }

    /**
     * Is not between.
     *
     * @error_message This value is between {[0]} and {[1]}.
     * @error_code BETWEEN
     *
     * @param int $min
     * @param int $max
     * @return bool
     */
    public function isNotBetween(int $min, int $max): bool
    {
        return $this->data < $min || $this->data > $max;
    }

}