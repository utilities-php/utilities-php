<?php
declare(strict_types=1);

namespace Utilities\Validator\Constraints;

use Utilities\Validator\Operators\NumericOperator;

/**
 * The number constraint.
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
class NumberConstraint extends \Utilities\Validator\Constraint
{

    use NumericOperator;

    /**
     * @error_message This value is not a number.
     * @error_code INVALID
     *
     * @return bool
     */
    public function isNumber(): bool
    {
        return is_numeric($this->data);
    }

    /**
     * @error_message This value is not an integer.
     * @error_code INVALID
     *
     * @return bool
     */
    public function isInteger(): bool
    {
        return is_int($this->data);
    }

    /**
     * @error_message This value is not a float.
     * @error_code INVALID
     *
     * @return bool
     */
    public function isFloat(): bool
    {
        return is_float($this->data);
    }

}