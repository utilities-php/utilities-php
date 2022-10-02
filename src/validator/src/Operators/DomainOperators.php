<?php
declare(strict_types=1);

namespace Utilities\Validator\Operators;

/**
 * The DomainOperators trait.
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
trait DomainOperators
{

    /**
     * Get domain from the given address.
     *
     * @param string $address It can be an email address or a URL.
     * @return string
     */
    protected function getDomain(string $address): string
    {
        if (filter_var($address, FILTER_VALIDATE_EMAIL)) {
            return substr($address, strpos($address, '@') + 1);
        }

        if (filter_var($address, FILTER_VALIDATE_URL)) {
            return parse_url($address, PHP_URL_HOST);
        }

        return '';
    }

    /**
     * Check if the domain name is valid.
     *
     * @error_code INVALID_DOMAIN
     * @error_message The domain name is not valid.
     *
     * @return bool
     */
    public function isDomain(): bool
    {
        return checkdnsrr($this->getDomain($this->data));
    }

    /**
     * Checks if domains is exactly same.
     *
     * @error_code INVALID_DOMAIN
     * @error_message The domain name is not valid.
     *
     * @param string $domain
     *
     * @return bool
     */
    public function isDomainEqual(string $domain): bool
    {
        return $this->getDomain($this->data) === $domain;
    }

    /**
     * Checks if domains is not exactly same.
     *
     * @error_code INVALID_DOMAIN
     * @error_message The domain name is not valid.
     *
     * @param string $domain
     *
     * @return bool
     */
    public function isDomainNotEqual(string $domain): bool
    {
        return $this->getDomain($this->data) !== $domain;
    }

    /**
     * Check if the domain name is in
     *
     * @error_code INVALID_DOMAIN
     * @error_message The domain name is not valid.
     *
     * @param array $domains
     *
     * @return bool
     */
    public function isDomainIn(array $domains): bool
    {
        return in_array($this->getDomain($this->data), $domains);
    }

    /**
     * Check if the domain name is not in
     *
     * @error_code INVALID_DOMAIN
     * @error_message The domain name is not valid.
     *
     * @param array $domains
     *
     * @return bool
     */
    public function isDomainNotIn(array $domains): bool
    {
        return !$this->isDomainIn($domains);
    }

}