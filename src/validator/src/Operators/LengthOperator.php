<?php
declare(strict_types=1);

namespace Utilities\Validator\Operators;

/**
 * This is part of the Utilities package.
 *
 * @link https://github.com/utilities-php/utilities-php
 * @author Shahrad Elahi <shahrad@litehex.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
trait LengthOperator
{

    /**
     * Get Length of the data.
     *
     * @return int
     */
    private function getLength(): int
    {
        return match (self::getType()) {
            'Array' => count($this->data),
            'Object' => count((array)$this->data),
            'String' => mb_strlen($this->data),
            default => false
        };
    }

    /**
     * Checks if string is exactly the specified length.
     *
     * @error_code INVALID_LENGTH
     * @error_message The length of the string is not exactly the specified length.
     *
     * @param mixed $limit
     *
     * @return bool
     */
    public function isLengthEqual(int $limit): bool
    {
        return $this->getLength() === $limit;
    }

    /**
     * Checks if string is not exactly the specified length.
     *
     * @error_code INVALID_LENGTH
     * @error_message The length of the string is not exactly the specified length.
     *
     * @param mixed $limit
     *
     * @return bool
     */
    public function isNotLengthEqual(int $limit): bool
    {
        return !$this->isLengthEqual($limit);
    }

    /**
     * Checks if string is greater than the specified length.
     *
     * @error_code INVALID_LENGTH
     * @error_message The length of the string is not greater than the specified length.
     *
     * @param mixed $limit
     *
     * @return bool
     */
    public function isLengthGreaterThan(int $limit): bool
    {
        return $this->getLength() > $limit;
    }

    /**
     * Checks if string is not greater than the specified length.
     *
     * @error_code INVALID_LENGTH
     * @error_message The length of the string is not greater than the specified length.
     *
     * @param mixed $limit
     *
     * @return bool
     */
    public function isNotLengthGreaterThan(int $limit): bool
    {
        return !$this->isLengthGreaterThan($limit);
    }

    /**
     * Checks if string is less than the specified length.
     *
     * @error_code INVALID_LENGTH
     * @error_message The length of the string is not less than the specified length.
     *
     * @param mixed $limit
     *
     * @return bool
     */
    public function isLengthLessThan(int $limit): bool
    {
        return $this->getLength() < $limit;
    }

    /**
     * Checks if string is not less than the specified length.
     *
     * @error_code INVALID_LENGTH
     * @error_message The length of the string is not less than the specified length.
     *
     * @param mixed $limit
     *
     * @return bool
     */
    public function isNotLengthLessThan(int $limit): bool
    {
        return !$this->isLengthLessThan($limit);
    }

    /**
     * Checks if string is greater than or equal to the specified length.
     *
     * @error_code INVALID_LENGTH
     * @error_message The length of the string is not greater than or equal to the specified length.
     *
     * @param mixed $limit
     *
     * @return bool
     */
    public function isLengthGreaterThanOrEqual(int $limit): bool
    {
        return $this->getLength() >= $limit;
    }

    /**
     * Checks if string is not greater than or equal to the specified length.
     *
     * @error_code INVALID_LENGTH
     * @error_message The length of the string is not greater than or equal to the specified length.
     *
     * @param mixed $limit
     *
     * @return bool
     */
    public function isNotLengthGreaterThanOrEqual(int $limit): bool
    {
        return !$this->isLengthGreaterThanOrEqual($limit);
    }

    /**
     * Checks if string is between two lengths.
     *
     * @error_code INVALID_LENGTH
     * @error_message The length of the string is not between the specified limits.
     *
     * @param int $min
     * @param int $max
     *
     * @return bool
     */
    public function isLengthBetween(int $min, int $max): bool
    {
        return $this->getLength() >= $min && $this->getLength() <= $max;
    }

    /**
     * Checks if string is not between two lengths.
     *
     * @error_code INVALID_LENGTH
     * @error_message The length of the string is not between the specified limits.
     *
     * @param int $min
     * @param int $max
     *
     * @return bool
     */
    public function isNotLengthBetween(int $min, int $max): bool
    {
        return !$this->isLengthBetween($min, $max);
    }

}