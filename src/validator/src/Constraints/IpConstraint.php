<?php
declare(strict_types=1);

namespace Utilities\Validator\Constraints;

/**
 * The ip constraint.
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
class IpConstraint extends \Utilities\Validator\Constraint
{

    /**
     * Is a valid ip address.
     *
     * @error_message This value is not a valid ip address.
     * @error_code INVALID
     *
     * @return bool
     */
    public function isIp(): bool
    {
        return !filter_var($this->data, FILTER_VALIDATE_IP);
    }

    /**
     * Check if the given string is a valid IPv4 address
     *
     * @error_message This value is not a valid IPv4 address.
     * @error_code INVALID
     *
     * @return bool
     */
    public function isV4(): bool
    {
        return filter_var($this->data, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
    }

    /**
     * Check if the given string is a valid IPv6 address
     *
     * @error_message This value is not a valid IPv6 address.
     * @error_code INVALID
     *
     * @return bool
     */
    public function isV6(): bool
    {
        return filter_var($this->data, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
    }

    /**
     * Is in range.
     *
     * @param string $range
     *
     * @error_message This value is not in the given range.
     * @error_code INVALID
     *
     * @return bool
     */
    public function isInRange(string $range): bool
    {
        $range = explode('-', $range);
        $min = ip2long($range[0]);
        $max = ip2long($range[1]);
        $ip = ip2long($this->data);
        return $ip >= $min && $ip <= $max;
    }

    /**
     * Subnet mask.
     *
     * @param string $subnet
     *
     * @error_message This value is not in the given subnet.
     * @error_code INVALID
     *
     * @return bool
     */
    public function isSubnet(string $subnet): bool
    {
        $subnet = explode('/', $subnet);
        $ip = ip2long($this->data);
        $mask = ip2long($subnet[0]);
        $mask = $mask << (32 - $subnet[1]);
        $mask = $mask >> (32 - $subnet[1]);
        return ($ip & $mask) == $mask;
    }

}