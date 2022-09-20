<?php
declare(strict_types=1);

namespace Utilities\Validator\Operators;

/**
 * The CommonOperators trait.
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
trait CommonOperators
{

    /**
     * @param mixed $limit
     * @return bool
     */
    public function is(int $limit): bool
    {
        return $this->data === $limit;
    }

    /**
     * @param mixed $limit
     * @return bool
     */
    public function isNot(int $limit): bool
    {
        return $this->data !== $limit;
    }

    /**
     * @param array $array
     * @return bool
     */
    public function isIn(array $array): bool
    {
        return in_array($this->data, $array);
    }

    /**
     * @param array $array
     * @return bool
     */
    public function isNotIn(array $array): bool
    {
        return !in_array($this->data, $array);
    }

    /**
     * Match the given pattern.
     *
     * @param string $pattern The regular expression pattern.
     * @return bool
     */
    public function regex(string $pattern): bool
    {
        return preg_match($pattern, $this->data) === 1;
    }

}