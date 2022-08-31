<?php
declare(strict_types=1);

namespace Utilities\Router;

use Utilities\Router\Traits\OriginTrait;

/**
 * Origin class
 *
 * @link    https://github.com/utilities-php/router
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/router/blob/master/LICENSE (MIT License)
 */
class Origin
{

    use OriginTrait;

    /**
     * Strict mode.
     *
     * By setting this to true, the origin will be checked strictly.
     *
     * @var bool
     */
    public static bool $STRICT_MODE = false;

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
            'domain' => $domain,
            'allowCredentials' => $allowCredentials,
            'maxAge' => $maxAge,
        ];
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

        if (isset($_SERVER['HTTP_ORIGIN']) && ($domain = self::validateOrigin($_SERVER['HTTP_ORIGIN'])) !== false) {
            self::sendHeader('Access-Control-Allow-Origin', $_SERVER['HTTP_ORIGIN']);
            self::sendHeader('Access-Control-Allow-Credentials', $domain['allowCredentials'] ? 'true' : 'false');
            self::sendHeader('Access-Control-Max-Age', $domain['maxAge'] ?? $options['maxAge']);
            $flag = true;
        }

        if (isset($_SERVER['REMOTE_ADDR']) && self::validateIp($_SERVER['REMOTE_ADDR']) !== false) {
            $flag = true;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS' && $flag) {

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                $requestMethods = $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] . ', OPTIONS';
                self::sendHeader('Access-Control-Allow-Methods', $requestMethods);
            }

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {

                $allowedHeaders = [];
                $rqdHeaders = $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'];

                if (static::$STRICT_MODE) {
                    foreach (str_contains(',', $rqdHeaders) ? explode(',', $rqdHeaders) : [$rqdHeaders] as $header) {
                        if (in_array($header, $options['allowedHeaders'])) {
                            $allowedHeaders[] = $header;
                        }
                    }
                    self::sendHeader('Access-Control-Allow-Headers', implode(',', $allowedHeaders));

                } else {
                    self::sendHeader('Access-Control-Allow-Headers', $rqdHeaders);
                }
            }

            exit(0);
        }

        if (static::$STRICT_MODE !== true) {
            return isset($_SERVER['HTTP_ORIGIN']) ? $flag : true;
        }

        return $flag;
    }

}