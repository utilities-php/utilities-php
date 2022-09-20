<?php
declare(strict_types=1);

namespace Utilities\Validator\Operators;

/**
 * The LengthOperator trait.
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
            'String' => strlen($this->data),
            default => false
        };
    }

    /**
     * @param mixed $limit
     * @return bool
     */
    public function isLengthEqual(int $limit): bool
    {
        return $this->getLength() === $limit;
    }

    /**
     * @param mixed $limit
     * @return bool
     */
    protected function isLengthGreaterThan(int $limit): bool
    {
        return $this->getLength() > $limit;
    }

    /**
     * @param mixed $limit
     * @return bool
     */
    protected function isLengthLessThan(int $limit): bool
    {
        return $this->getLength() < $limit;
    }

    /**
     * @param mixed $limit
     * @return bool
     */
    protected function isLengthGreaterThanOrEqual(int $limit): bool
    {
        return $this->getLength() >= $limit;
    }

    /**
     * @param mixed $limit
     * @return bool
     */
    protected function isLengthLessThanOrEqual(int $limit): bool
    {
        return $this->getLength() <= $limit;
    }

}