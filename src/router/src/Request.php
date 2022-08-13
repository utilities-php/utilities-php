<?php
declare(strict_types=1);

namespace Utilities\Router;

use Utilities\Common\Validator;

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