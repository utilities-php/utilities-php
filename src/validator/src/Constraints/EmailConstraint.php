<?php
declare(strict_types=1);

namespace Utilities\Validator\Constraints;

use Utilities\Validator\Constraint;
use Utilities\Validator\Operators\DomainOperators;

/**
 * The email constraint.
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
class EmailConstraint extends Constraint
{

    use DomainOperators;

    /**
     * @error_message The email address is not valid.
     * @error_code INVALID
     *
     * @return bool
     */
    public function isEmail(): bool
    {
        return filter_var($this->data, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * @param string $username
     *
     * @error_message The email address is not valid.
     * @error_code INVALID
     *
     * @return bool
     */
    public function usernameIs(string $username): bool
    {
        return $this->isEmail() && substr($this->data, 0, strpos($this->data, '@')) === $username;
    }

    /**
     * @param string $username
     *
     * @error_message The email address is not valid.
     * @error_code INVALID
     *
     * @return bool
     */
    public function usernameIsNot(string $username): bool
    {
        return $this->isEmail() && substr($this->data, 0, strpos($this->data, '@')) !== $username;
    }

    /**
     * @param array $usernames
     *
     * @error_message The email address is not valid.
     * @error_code INVALID
     *
     * @return bool
     */
    public function usernameIn(array $usernames): bool
    {
        return $this->isEmail() && in_array(substr($this->data, 0, strpos($this->data, '@')), $usernames);
    }

    /**
     * @param array $usernames
     *
     * @error_message The email address is not valid.
     * @error_code INVALID
     *
     * @return bool
     */
    public function usernameNotIn(array $usernames): bool
    {
        return $this->isEmail() && !in_array(substr($this->data, 0, strpos($this->data, '@')), $usernames);
    }

}