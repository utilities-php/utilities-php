<?php
declare(strict_types=1);

namespace Utilities\Validator\Constraints;

use Utilities\Validator\Operators\LengthOperator;

/**
 * The string constraint.
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
class StringConstraint extends \Utilities\Validator\Constraint
{

    use LengthOperator;

    /**
     * @return bool
     */
    public function isString(): bool
    {
        return is_string($this->data);
    }

    /**
     * @return bool
     */
    public function isJson(): bool
    {
        json_decode($this->data);

        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * @return bool
     */
    public function isXml(): bool
    {
        return preg_match('/<\?xml.*\?>/i', $this->data) === 1;
    }

    /**
     * @return bool
     */
    public function isSerialized(): bool
    {
        return @unserialize($this->data) !== false;
    }

    /**
     * Check if the string is a valid email address.
     *
     * @return bool
     */
    public function isEmail(): bool
    {
        return filter_var($this->data, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Check if the string is a valid URL.
     *
     * @return bool
     */
    public function isUrl(): bool
    {
        return filter_var($this->data, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Check if the string is a valid IP address.
     *
     * @return bool
     */
    public function isIp(): bool
    {
        return filter_var($this->data, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * Check if the string is a valid IPv4 address.
     *
     * @return bool
     */
    public function isIpv4(): bool
    {
        return filter_var($this->data, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false;
    }

    /**
     * Check if the string is a valid IPv6 address.
     *
     * @return bool
     */
    public function isIpv6(): bool
    {
        return filter_var($this->data, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false;
    }

    /**
     * Check if the string is a valid MAC address.
     *
     * @return bool
     */
    public function isMac(): bool
    {
        return filter_var($this->data, FILTER_VALIDATE_MAC) !== false;
    }

    /**
     * Check if the string is a valid HTML.
     *
     * @return bool
     */
    public function isHtml(): bool
    {
        return preg_match('/<[^>]*>/', $this->data) === 1;
    }

    /**
     * Check if the string starts with the given substring.
     *
     * @param string $substring
     * @return bool
     */
    public function startsWith(string $substring): bool
    {
        return str_starts_with($this->data, $substring);
    }

    /**
     * Check if the string ends with the given substring.
     *
     * @param string $substring
     * @return bool
     */
    public function endsWith(string $substring): bool
    {
        return str_ends_with($this->data, $substring);
    }

}