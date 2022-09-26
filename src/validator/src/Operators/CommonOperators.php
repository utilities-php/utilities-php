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
     * Is empty.
     *
     * @error_message This value is empty.
     * @error_code EMPTY
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return validate($this->data)->isEmpty()->isValid();
    }

    /**
     * Is not empty.
     *
     * @error_message This value is empty.
     * @error_code EMPTY
     *
     * @return bool
     */
    public function isNotEmpty(): bool
    {
        return validate($this->data)->isNotEmpty()->isValid();
    }

    /**
     * Contains the given data.
     *
     * @param mixed $data
     * @return bool False if the data is not found or not supported.
     */
    public function isContains(mixed $data): bool
    {
        return validate($this->data)->isContains($data)->isValid();
    }

    /**
     * Does not contain the given data.
     *
     * @param mixed $data
     * @return bool False if the data is not found or not supported.
     */
    public function doesNotContains(mixed $data): bool
    {
        return validate($this->data)->doesNotContains($data)->isValid();
    }

    /**
     * Match the given pattern.
     *
     * @param string $pattern The regular expression pattern.
     * @return bool
     */
    public function match(string $pattern): bool
    {
        return validate($this->data)->match($pattern)->isValid();
    }

}