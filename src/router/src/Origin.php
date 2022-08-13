<?php
declare(strict_types=1);

namespace Utilities\Router;

/**
 * Origin class
 *
 * @link    https://github.com/utilities-php/router
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/router/blob/master/LICENSE (MIT License)
 */
class Origin
{

    /**
     * The allowed domains.
     *
     * @var array
     */
    protected static array $allowedDomains = [];

    /**
     * The allowed ip addresses.
     *
     * @var array
     */
    protected static array $allowedIps = [];

    /**
     * Add allowed domain.
     *
     * @param string $domain The domain. (e.g. example.com or *.example.com)
     * @param bool $allowCredentials If true, the credentials will be allowed.
     * @param int $maxAge The max age. (e.g. 86400)
     * @return void
     */
    public static function addDomain(string $domain, bool $allowCredentials = false, int $maxAge = 86400): void
    {
        static::$allowedDomains[] = [
            'domain' => self::domainToRegex($domain),
            'allowCredentials' => $allowCredentials,
            'maxAge' => $maxAge,
        ];
    }

    /**
     * Convert domain to Regex.
     *
     * @param string $domain The domain. (e.g. example.com or *.example.com)
     * @return string The Regex. (e.g. ^example\.com$ or ^.*\.example\.com$)
     */
    private static function domainToRegex(string $domain): string
    {
        $domain = str_replace('.', '\.', $domain);
        if (str_starts_with($domain, '*')) {
            return '^.*' . substr($domain, 1) . '$';
        }

        return '^' . $domain . '$';
    }

    /**
     * Remove allowed domain.
     *
     * @param string $domain The domain. (e.g. example.com or *.example.com)
     * @return void
     */
    public static function removeDomain(string $domain): void
    {
        foreach (static::$allowedDomains as $key => $value) {
            if ($value['domain'] == self::domainToRegex($domain)) {
                unset(static::$allowedDomains[$key]);
            }
        }
    }

    /**
     * Add allowed ip address.
     *
     * @param string $ip The ip address. (e.g. 31.187.72.206 or 2a02:4780:f:ab01::1)
     * @return void
     */
    public static function addIp(string $ip): void
    {
        static::$allowedIps[] = $ip;
    }

    /**
     * Remove allowed ip address.
     *
     * @param string $ip The ip address. (e.g. 31.187.72.206 or 2a02:4780:f:ab01::1)
     * @return void
     */
    public static function removeIp(string $ip): void
    {
        foreach (static::$allowedIps as $key => $value) {
            if ($value == $ip) {
                unset(static::$allowedIps[$key]);
            }
        }
    }

    /**
     * Validate. Get request and start validation.
     *
     * @param array $options [allowCredentials, maxAge, allowedHeaders]
     * @return bool
     */
    public static function validate(array $options = ['allowCredentials' => false, 'maxAge' => 86400, 'allowedHeaders' => []]): bool
    {
        $flag = false;

        if (isset($_SERVER['HTTP_ORIGIN'])) {
            foreach (static::$allowedDomains as $domain) {
                if (preg_match_all('/' . $domain['domain'] . '/i', parse_url($_SERVER['HTTP_ORIGIN'])['path']) > 0) {
                    self::sendHeader('Access-Control-Allow-Origin', $_SERVER['HTTP_ORIGIN']);
                    self::sendHeader('Access-Control-Allow-Credentials', $domain['allowCredentials'] ? 'true' : 'false');
                    self::sendHeader('Access-Control-Max-Age', $domain['maxAge'] ?? $options['maxAge']);
                    $flag = true;
                    break;
                }
            }
        }

        if (isset($_SERVER['REMOTE_ADDR'])) {
            foreach (static::$allowedIps as $ip) {
                if ($_SERVER['REMOTE_ADDR'] == $ip) {
                    $flag = true;
                    break;
                }
            }
        }

        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                self::sendHeader('Access-Control-Allow-Methods', $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']);
            }

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                $allowedHeaders = [];

                foreach (explode(',', $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']) as $header) {
                    if (in_array($header, $options['allowedHeaders'])) {
                        $allowedHeaders[] = $header;
                    }
                }

                self::sendHeader('Access-Control-Allow-Headers', implode(',', $allowedHeaders));
            }

            return true;
        }

        return $flag;
    }

    /**
     * Send the headers but in safe mode.
     *
     * @param string $header The header. (e.g. Access-Control-Allow-Origin)
     * @param string $value The value. (e.g. * or example.com)
     */
    private static function sendHeader(string $header, mixed $value): void
    {
        if (headers_sent()) {
            return;
        }

        header("{$header}: {$value}");
    }

}