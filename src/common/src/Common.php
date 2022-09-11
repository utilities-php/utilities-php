<?php
declare(strict_types=1);

namespace Utilities\Common;

use EasyHttp\Client;
use RuntimeException;

/**
 * Common class
 *
 * @link    https://github.com/utilities-php/common
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/common/blob/master/LICENSE (MIT License)
 */
class Common
{

    /**
     * @param array|string $data
     * @return string
     */
    public static function prettyJson(array|string $data): string
    {
        $res = $data;

        if (Validator::isJson($data)) {
            return json_decode($data, true);
        }

        if (!is_array($data)) {
            throw new RuntimeException(sprintf(
                'The given data is not a valid json string or array. Given: %s',
                $data
            ));
        }

        return json_encode($res, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
    }

    /**
     * Prints a string between <pre> tags
     *
     * @param string $string
     * @return void
     */
    public static function htmlCode(string $string): void
    {
        echo "<pre>$string</pre>";
    }


    /**
     * @param string $string
     * @return string
     */
    public static function filterApostrophe(mixed $string): string
    {
        return str_replace("'", "\'", "$string");
    }

    /**
     * Get the IP information by using the ipinfo.io API
     *
     * @param string $ip IP Address
     * @return array ["ip", "city", "region", "country", "loc", "org", "postal", "timezone"]
     */
    public static function getIpInfo(string $ip): array
    {
        $Response = (new Client())->get("https://ipinfo.io/$ip/json");
        return json_decode($Response->getBody(), true);
    }

    /**
     * Check values of specified indexes in array are empty
     *
     * @param array $haystack
     * @param array $keys
     * @return bool
     */
    public static function hasValues(array $haystack, array $keys): bool
    {
        foreach ($keys as $key) {
            if ($haystack[$key] == null) return false;
        }
        return true;
    }

    /**
     * Get multiple values from array by key
     *
     * @param array $haystack
     * @param array $needle
     * @param bool $skipEmpty (default: false)
     * @return array
     */
    public static function CatchParams(array $haystack, array $needle, bool $skipEmpty = false): array
    {
        $result = [];
        foreach ($needle as $key) {
            if ($skipEmpty && $haystack[$key] == null) continue;
            $result[$key] = $haystack[$key];
        }
        return $result;
    }

    /**
     * Filter keys and only keep the given keys
     *
     * @param array $haystack Array to filter
     * @param array $needle Array of keys to keep
     * @return array
     */
    public static function FilterParams(array $haystack, array $needle): array
    {
        $result = [];
        foreach ($needle as $key) {
            if ($haystack[$key] == null) continue;
            $result[$key] = $haystack[$key];
        }
        return $result;
    }

    /**
     * Get random index in array
     *
     * @param array $array
     * @return mixed
     */
    public static function randomIndex(array $array): mixed
    {
        return $array[rand(0, count($array) - 1)];
    }

    /**
     * Compare two strings with case-insensitive
     *
     * @param ?string $haystack
     * @param string $needle
     * @return bool
     */
    public static function insensitiveString(?string $haystack, string $needle): bool
    {
        if ($haystack == null) return false;
        return preg_match("/^($needle)$/i", $haystack);
    }

    /**
     * Multiple Compare
     *
     * @param mixed $haystack
     * @param mixed ...$needles
     * @return bool
     */
    public static function multipleCompare(mixed $haystack, mixed ...$needles): bool
    {
        foreach ($needles as $needle) {
            if ($haystack == $needle) return true;
        }
        return false;
    }

    /**
     * Get two array and mix them, and if $duplicate is true, it will avoid duplicate
     *
     * @param array $array1
     * @param array $array2
     * @param bool $duplicate
     * @return array
     */
    public static function mixedArray(array $array1, array $array2, bool $duplicate = false): array
    {
        $result = array_merge($array1, $array2);
        if ($duplicate) {
            $result = array_unique($result);
        }
        return $result;
    }

    /**
     * Generate random string with given length and chars
     *
     * @param int $length
     * @param string|null $chars [optional] If null, it will use default chars
     * @return string
     */
    public static function randomString(int $length = 10, string $chars = null): string
    {
        $characters = $chars ?? '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Remove a part of string
     *
     * @param string $needle
     * @param string $haystack
     * @return string
     */
    public static function removeStr(string $needle, string $haystack): string
    {
        return str_replace($needle, "", $haystack);
    }

    /**
     * Get the data between an HTML tag by URL
     *
     * @param string $url
     * @param string $tag
     * @return string
     */
    public static function getTagContent(string $url, string $tag): string
    {
        $page = file_get_contents($url);
        return preg_match('/<' . $tag . '[^>]*>(.*?)<\/' . $tag . '>/ims', $page, $match) ? $match[1] : "";
    }

    /**
     * get the data of a meta tag by URL
     *
     * @param string $url
     * @param string $needle
     * @return string
     */
    public static function getMetaContent(string $url, string $needle): string
    {
        $tags = get_meta_tags($url);
        return $tags[$needle] ?? "";
    }

    /**
     * By using this function, Fatal Error will be thrown
     *
     * @return void
     */
    public static function setDebugMode(): void
    {
        error_reporting(E_ERROR);
        ini_set('display_errors', '1');
    }

    /**
     * Find a string index in array with option of insensitive-case
     *
     * @param array $haystack
     * @param string $needle
     * @param bool $insensitive
     * @return mixed false means not found, otherwise returns it's a value
     */
    public static function findIndex(array $haystack, string $needle, bool $insensitive): mixed
    {
        if ($insensitive) {
            return array_change_key_case($haystack)[strtolower($needle)] ?? false;
        }
        return $haystack[$needle] ?? false;
    }

    /**
     * Get ping of the given host
     *
     * @param string $host The ip or domain name
     * @param int $timeout default is 10
     * @return int|false
     */
    public static function getPing(string $host, int $timeout = 10): int|false
    {
        $output = array();
        $com = 'ping -n -w ' . $timeout . ' -c 1 ' . escapeshellarg($host);

        $exitCode = 0;
        exec($com, $output, $exitCode);

        if ($exitCode == 0 || $exitCode == 1) {
            foreach ($output as $cline) {
                if (str_contains($cline, ' bytes from ')) {
                    return (int)ceil(floatval(substr($cline, strpos($cline, 'time=') + 5)));
                }
            }
        }

        return false;
    }

    /**
     * Convert CamelCase to snake_case
     *
     * @param string $string
     * @return string
     */
    public static function camelCaseToSnake(string $string): string
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $string));
    }

    /**
     * Redirect to the given url
     *
     * @param string $url The url to redirect to
     * @param int $code The http code (302 means temporary redirect and 301 means permanent redirect)
     * @return void
     */
    public static function redirect(string $url, int $code = 302): void
    {
        header('Location: ' . $url, true, $code);
        exit;
    }

}