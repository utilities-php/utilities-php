<?php
declare(strict_types=1);

namespace Utilities\Validator\Constraints;

use Utilities\Validator\Operators\CommonOperators;
use Utilities\Validator\Operators\LengthOperator;

/**
 * The timestamp constraint.
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
class TimestampConstraint extends \Utilities\Validator\Constraint
{

    public function isTimestamp(): bool
    {
        return is_numeric($this->data) && (int) $this->data == $this->data;
    }

    public function isGreaterThan(mixed $limit): bool
    {
        return $this->isTimestamp() && $this->data > $limit;
    }

    public function isGreaterOrEqual(mixed $limit): bool
    {
        return $this->isTimestamp() && $this->data >= $limit;
    }

    public function isLessThan(mixed $limit): bool
    {
        return $this->isTimestamp() && $this->data < $limit;
    }

    public function isLessOrEqual(mixed $limit): bool
    {
        return $this->isTimestamp() && $this->data <= $limit;
    }

    public function isBetween(mixed $min, mixed $max): bool
    {
        return $this->isTimestamp() && $this->data >= $min && $this->data <= $max;
    }

    public function isNotBetween(mixed $min, mixed $max): bool
    {
        return $this->isTimestamp() && $this->data < $min || $this->data > $max;
    }

    public function isEqual(mixed $limit): bool
    {
        return $this->isTimestamp() && $this->data == $limit;
    }

}