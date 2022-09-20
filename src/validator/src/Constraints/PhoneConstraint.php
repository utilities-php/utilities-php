<?php
declare(strict_types=1);

namespace Utilities\Validator\Constraints;

/**
 * The phone constraint.
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
class PhoneConstraint extends \Utilities\Validator\Constraint
{

    /**
     * @error_message This value is not a phone number.
     * @error_code INVALID
     *
     * @return bool
     */
    public function isPhone(): bool
    {
        return !preg_match('/^(\+98|0)?9\d{9}$/', $this->data);
    }

    /**
     * Check the country code is valid.
     *
     * @param int $countryCode Checks numbers starts with
     *
     * @error_message This value is not a valid country code.
     * @error_code INVALID
     *
     * @return bool
     */
    public function countryCodeIs(int $countryCode): bool
    {
        return str_starts_with((string)$this->data, (string)$countryCode);
    }


}