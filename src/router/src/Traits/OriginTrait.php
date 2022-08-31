<?php
declare(strict_types=1);

namespace Utilities\Router\Traits;

/**
 * OriginTrait class
 *
 * @link    https://github.com/utilities-php/router
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/router/blob/master/LICENSE (MIT License)
 */
trait OriginTrait
{

    /**
     * Send the headers but in safe mode.
     *
     * @param string $header The header. (e.g. Access-Control-Allow-Origin)
     * @param string $value The value. (e.g. * or example.com)
     * @return void
     */
    private static function sendHeader(string $header, mixed $value): void
    {
        if (headers_sent()) {
            return;
        }

        header("{$header}: {$value}");
    }

    /**
     * Validate the origin
     *
     * @param string $origin
     * @return array|false
     */
    private static function validateOrigin(string $origin): array|false
    {
        foreach (static::$allowedDomains as $domain) {
            if (self::isLocalhost() && $domain['domain'] === 'localhost') {
                return $domain;
            }

            $path = parse_url($origin)['path'] ?? $domain['domain'];
            if (preg_match_all('/' . self::domainToRegex($domain['domain']) . '/i', $path) > 0) {
                return $domain;
            }
        }

        return false;
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
     * Validate the ip address.
     *
     * @param string $ip The ip address.
     * @return string|false
     */
    private static function validateIp(string $ip): string|false
    {
        if (in_array($ip, static::$allowedIps)) {
            return $ip;
        }

        return false;
    }

    /**
     * Get origin host
     *
     * @retrun string
     */
    private static function getOriginHostName(): string
    {
        $data = preg_replace('/(http(s*)):\/\//i', '', $_SERVER['HTTP_ORIGIN'] ?? '');
        if (str_ends_with('/', $data)) {
            return substr($data, 0, strlen($data) - 1);
        }

        return $data;
    }

    /**
     * Is same site?
     *
     * @retrun bool
     */
    public static function isSameSite(): bool
    {
        return $_SERVER['SERVER_NAME'] === self::getOriginHostName();
    }

    /**
     * @return bool
     */
    public static function isLocalhost(): bool
    {
        return preg_match_all('/((http([s])?:\/\/)?(localhost|127.0.0.1){1}(([:])?[\0-9]{4})?\/?){1}/', self::getOriginHostName()) !== false;
    }

    /**
     * This method will delete every trusted domains and ip addresses
     *
     * @retrun void
     */
    public static function reset(): void
    {
        static::$allowedDomains = [];
        static::$allowedIps = [];
    }

}