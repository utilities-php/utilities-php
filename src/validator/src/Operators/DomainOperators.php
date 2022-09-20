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
     * Domain name is
     *
     * @param string $domain
     * @return bool
     */
    public function domainIs(string $domain): bool
    {
        return $this->getDomain($this->data) === $domain;
    }

    /**
     * Domain name is not
     *
     * @param string $domain
     * @return bool
     */
    public function domainIsNot(string $domain): bool
    {
        return $this->getDomain($this->data) !== $domain;
    }

    /**
     * Domain name is in
     *
     * @param array $domains
     * @return bool
     */
    public function domainIn(array $domains): bool
    {
        return in_array($this->getDomain($this->data), $domains);
    }

    /**
     * Domain name is not in
     *
     * @param array $domains
     * @return bool
     */
    public function domainNotIn(array $domains): bool
    {
        return !$this->domainIn($domains);
    }

    /**
     * Domain name is valid
     *
     * @return bool
     */
    public function isDomain(): bool
    {
        return checkdnsrr($this->getDomain($this->data));
    }

    /**
     * Domain name is not valid
     *
     * @return bool
     */
    public function isDomainNotValid(): bool
    {
        return !$this->isDomain();
    }

}