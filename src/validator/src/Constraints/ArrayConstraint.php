<?php
declare(strict_types=1);

namespace Utilities\Validator\Constraints;

use Utilities\Validator\Operators\LengthOperator;

/**
 * The array constraint.
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
class ArrayConstraint extends \Utilities\Validator\Constraint
{

    use LengthOperator;

    /**
     * Is array
     *
     * @error_message This value is not an array.
     * @error_code INVALID
     *
     * @return bool
     */
    protected function isArray(): bool
    {
        return !is_array($this->data);
    }

    /**
     * Is associative array.
     *
     * @error_message This value is not an associative array.
     * @error_code INVALID
     *
     * @return bool
     */
    public function isAssoc(): bool
    {
        return array_keys($this->data) !== range(0, count($this->data) - 1);
    }

    /**
     * has this key.
     *
     * @error_message This array does not have this key.
     * @error_code INVALID
     *
     * @param string $key
     * @return bool
     */
    public function hasKey(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Strictly has this key.
     *
     * @error_message This array does not have this key.
     * @error_code INVALID
     *
     * @param string $key
     * @return bool
     */
    public function hasStrictKey(string $key): bool
    {
        return self::hasKey($key) && $this->data[$key] !== null;
    }

}