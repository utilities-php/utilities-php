<?php
declare(strict_types=1);

namespace Utilities\Validator\Constraints;

use Utilities\Validator\Operators\CommonOperators;
use Utilities\Validator\Operators\LengthOperator;

/**
 * The date constraint.
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
class DateConstraint extends \Utilities\Validator\Constraint
{

    /**
     * @error_message This value is not a date.
     * @error_code INVALID
     *
     * @return bool
     */
    public function isDate(): bool
    {
        return $this->isTimestamp() && strtotime(date('d-m-Y', $this->data)) === (int) $this->data;
    }

    /**
     * @error_message This value is not a timestamp.
     * @error_code INVALID
     *
     * @return bool
     */
    public function isTimestamp(): bool
    {
        return is_numeric($this->data) && (int) $this->data == $this->data;
    }

    /**
     * @error_message This value is not a valid date.
     * @error_code INVALID
     *
     * @return bool
     */
    public function isTime(): bool
    {
        return $this->isTimestamp() && strtotime(date('H:i:s', $this->data)) === (int) $this->data;
    }

    /**
     * @error_message This value is not a valid date.
     * @error_code INVALID
     *
     * @return bool
     */
    public function isDateTime(): bool
    {
        return $this->isTimestamp() && strtotime(date('d-m-Y H:i:s', $this->data)) === (int) $this->data;
    }

    /**
     * @param mixed $limit
     *
     * @error_message This value is not a valid date.
     * @error_code INVALID
     *
     * @return bool
     */
    public function isGreaterOrEqual(mixed $limit): bool
    {
        return $this->isTimestamp() && $this->data >= $limit;
    }

}