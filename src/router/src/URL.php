<?php
declare(strict_types=1);

namespace Utilities\Router;

/**
 * URL class
 *
 * @link    https://github.com/utilities-php/router
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/router/blob/master/LICENSE (MIT License)
 */
class URL
{

    /**
     * @param bool $withoutQuery If true, the query string will be removed from the url
     * @return string
     */
    public static function getURL(bool $withoutQuery = true): string
    {
        if (str_contains($_SERVER['REQUEST_URI'], "?") && $withoutQuery) {
            return explode("?", $_SERVER['REQUEST_URI'])[0];
        }
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * @param int $index Get the segment by index
     * @return string|false
     */
    public static function segment(int $index): string|false
    {
        $segments = self::getSegments();

        if (isset($segments[$index])) {
            return $segments[$index];
        }

        return false;
    }

    /**
     * @return array
     */
    public static function getSegments(): array
    {
        if (str_contains($_SERVER['REQUEST_URI'], "?")) {
            if ($_SERVER['QUERY_STRING'] != null) {
                $url = str_replace("?{$_SERVER['QUERY_STRING']}", "", $_SERVER['REQUEST_URI']);
            }
        }

        $url = $url ?? $_SERVER['REQUEST_URI'];

        if (strlen($url) > 1 && str_contains($url, "/")) {
            if ($url[0] == "/") {
                $url = substr($url, 1);
            }

            if ($url[strlen($url) - 1] == "?") {
                $url = substr($url, 0, strlen($url) - 1);
            }

            return explode('/', $url ?? $_SERVER['REQUEST_URI']);
        }

        return [];
    }

    /**
     * @param array $extra (optional) extra query string parameters
     * @return array
     */
    public static function QueryString(array $extra = []): array
    {
        $raw_data = explode("&", $_SERVER['QUERY_STRING'] ?? '');
        $result = [];

        if (count($raw_data) > 0 && $raw_data[0] != "") {
            foreach ($raw_data as $Query) {
                [$key, $val] = explode("=", $Query);
                $result[$key] = urldecode($val);
            }
        }

        foreach ($extra as $key => $val) {
            $result[$key] = $val;
        }

        return $result;
    }

    /**
     * @param string $url (without http:// and domain) (ex: /home/shahrad/testEcho)
     * @param string $pattern Example: '/{sector}/{subsector}/{method}/'
     * @return array the matched segments
     */
    public static function parseURL(string $url, string $pattern): array
    {
        $url = explode('/', $url);
        $pattern = explode('/', $pattern);
        $data = [];
        foreach ($pattern as $key => $value) {
            if (str_contains($value, '{')) {
                $data[str_replace('{', '', str_replace('}', '', $value))] = $url[$key];
            }
        }
        return $data;
    }

    /**
     * Find index of segment by use a keyword
     *
     * @param string $keyword
     * @param bool $insensitive (optional) if true, the keyword will be case-insensitive
     * @return int
     */
    public static function findIndexOfSegment(string $keyword, bool $insensitive = false): int
    {
        if ($insensitive) {
            $keyword = strtolower($keyword);
        }

        foreach (URL::getSegments() as $index => $value) {
            if ($insensitive) {
                $value = strtolower($value);
            }

            if ($value == $keyword) {
                return $index;
            }
        }

        return -1;
    }

    /**
     * Get request method
     *
     * @retrun string
     */
    public static function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    /**
     * @param string $uri
     * @return string
     */
    private static function convertUri2RegExp(string $uri): string
    {
        if (str_contains($uri, '{')) {
            $uri = preg_replace('/\{([^}]+)}/', '(?<$1>[^/]+)', $uri);
        }

        return '#^' . $uri . '$#';
    }

    /**
     * Parse the url parameters
     *
     * @param string $uri
     * @return array
     */
    public static function parseParams(string $uri): array
    {
        if (preg_match_all(self::convertUri2RegExp($uri), self::getURL(), $matches)) {
            $params = [];
            foreach ($matches as $key => $value) {
                if (is_numeric($key)) {
                    continue;
                }

                $params[$key] = $value[0];
            }

            return $params;
        }

        return [];
    }

}