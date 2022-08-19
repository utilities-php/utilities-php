<?php
declare(strict_types=1);

namespace Utilities\Router;

use Utilities\Common\Validator;
use Utilities\Router\Utils\StatusCode;

/**
 * Request class
 *
 * @method static string getUri() Returns the request URI.
 * @method static string getMethod() Returns the request method.
 * @method static array getQueryString() Returns the request query string.
 * @method static array getHeaders() Returns the request headers.
 * @method static array getParams() Returns the request params.
 *
 * @link    https://github.com/utilities-php/router
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/router/blob/master/LICENSE (MIT License)
 */
class Request
{

    /**
     * @var array
     */
    private static array $data = [];

    /**
     * Request constructor.
     *
     * @param array $initial
     */
    public function __construct(array $initial)
    {
        $types = [
            'uri' => "string",
            'method' => "string",
            'query_string' => "array",
            'headers' => "array",
            "params" => "array",
        ];

        foreach ($types as $key => $value) {
            if (!in_array($key, array_keys($initial))) {
                throw new \RuntimeException(sprintf(
                    'The given data of %s, must contain the following keys: %s',
                    '\Utilities\Router\Request',
                    implode(', ', array_keys($types))
                ));
            }
        }

        if (!Validator::validateType($initial, $types)) {
            throw new \RuntimeException(sprintf(
                "The type of %s must be %s",
                '\Utilities\Router\Request',
                implode(', ', $types)
            ));
        }

        static::$data = $initial;
    }

    /**
     * Returns the request header line.
     *
     * @param string $name
     * @return mixed
     */
    public static function getHeaderLine(string $name): mixed
    {
        return static::getHeaders()[$name] ?? null;
    }

    /**
     * Returns the request cookie.
     *
     * @param string $name
     * @return mixed
     */
    public static function getCookie(string $name): mixed
    {
        return $_COOKIE[$name] ?? null;
    }

    /**
     * Get the request input data
     *
     * @return string|null
     */
    public static function getInput(): ?string
    {
        return file_get_contents('php://input');
    }

    /**
     * Check are values are empty (API Response)
     *
     * If any value is empty, send error response.
     *
     * @param array $params
     * @return void
     */
    public static function emptyParams(array $params = []): void
    {
        foreach ($params as $key => $value) {
            if ($value == null) Response::send(StatusCode::BAD_REQUEST, [
                'description' => sprintf("Bad Request: the '%s' parameter is empty", $key)
            ]);
        }
    }

    /**
     * Require given keys in array and send error if not found
     *
     * @param array $haystack
     * @param array $needle
     * @return array
     */
    public static function requiredParams(array $haystack, array $needle): array
    {
        $result = [];
        foreach ($needle as $key) {
            if ($haystack[$key] == null) Response::send(StatusCode::BAD_REQUEST, [
                'description' => sprintf("Bad Request: the '%s' parameter is required", $key)
            ]);
            else $result[$key] = $haystack[$key];
        }
        return $result;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic(string $name, array $arguments): mixed
    {
        if (str_starts_with($name, 'get')) {
            $property = substr($name, 3);
            return static::$data[strtolower(ltrim(preg_replace('/[A-Z]/', '_$0', $property), '_'))];
        }

        if (method_exists(static::class, $name)) {
            return static::$name(...$arguments);
        }

        throw new \BadMethodCallException(sprintf(
            'Call to undefined method %s::%s()',
            self::class,
            $name
        ));
    }

}