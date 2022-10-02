<?php declare(strict_types=1);

namespace Utilities\Routing;

use BadMethodCallException;
use RuntimeException;
use Utilities\Common\Common;
use Utilities\Common\Validation;
use Utilities\Routing\Utils\StatusCode;
use Utilities\Validator\Validate;

/**
 * Request class
 *
 * @method static string getUri() Returns the request URI.
 * @method static string getMethod() Returns the request method.
 * @method static array getQueryString() Returns the request query string.
 * @method static array getHeaders() Returns the request headers.
 * @method static array getParams() Returns the request params.
 * @method static void setParams(array $params) Returns the request params.
 *
 * @link    https://github.com/utilities-php/routing
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/routing/blob/master/LICENSE (MIT License)
 */
class Request
{

    /**
     * @var array
     */
    private static array $data = [];

    /**
     * The allowed data.
     *
     * @var array
     */
    private static array $allowedData = [
        'uri' => "string",
        'method' => "string",
        'query_string' => "array",
        'headers' => "array",
        "params" => "array",
    ];

    /**
     * Request constructor.
     *
     * @param array $initial
     */
    public function __construct(array $initial)
    {
        foreach (self::$allowedData as $key => $value) {
            if (!in_array($key, array_keys($initial))) {
                throw new RuntimeException(sprintf(
                    'The given data of %s, must contain the following keys: %s',
                    '\Utilities\Routing\Request',
                    implode(', ', array_keys(self::$allowedData))
                ));
            }
        }

        if (!Validation::validateType($initial, self::$allowedData)) {
            throw new RuntimeException(sprintf(
                "The type of %s must be %s",
                '\Utilities\Routing\Request',
                implode(', ', self::$allowedData)
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
     * @param array $haystack Array to filter
     * @param array $needle Array of keys to check
     * @return void
     */
    public static function emptyParams(array $haystack, array $needle): void
    {
        foreach ($needle as $key) {
            if (!isset($haystack[$key]) || gettype($haystack[$key]) === 'NULL') {
                Response::send(StatusCode::BAD_REQUEST, [
                    'description' => sprintf(
                        "Bad Request: the '%s' parameter is empty or not set.",
                        $key
                    )
                ]);
            }
        }
    }

    /**
     * Parse json and on the error send error response.
     *
     * @param string $json Any string to parse
     * @param array $filter [optional] Array of keys to filter, throw error if any of them is empty
     * @return array
     */
    public static function parseJson(string $json, array $filter = []): array
    {
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Response::send(StatusCode::BAD_REQUEST, [
                'description' => "Bad Request: the request body is not a valid JSON."
            ]);
        }

        if (!empty($filter)) {
            $data = Common::filterArrayKeys($data, $filter);
            self::emptyParams($data, $filter);
            return $data;
        }

        return $data;
    }

    /**
     * Parse json and return the filtered array.
     *
     * @param string $json Any string to parse
     * @param array $filter Array of keys to filter, throw error if any of them is empty
     * @return array
     */
    public static function filterJson(string $json, array $filter): array
    {
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Response::send(StatusCode::BAD_REQUEST, [
                'description' => "Bad Request: the request body is not a valid JSON."
            ]);
        }

        return Common::filterArrayKeys($data, $filter);
    }

    /**
     * Filter and Validate a given array
     *
     * Using keys of the given rules array, filter the given array and validate the values.
     *
     * @param array $data Array to filter and validate
     * @param array $rules Array of rules to validate.
     * @return array
     */
    public static function filterWithValidation(array $data, array $rules): array
    {
        $data = Common::filterArrayKeys($data, array_keys($rules));
        $result = ($validate = new Validate($data))->withRule($rules);

        if (!$result) {
            Response::send(StatusCode::BAD_REQUEST, [
                'description' => "Bad Request: " . $validate->getErrors()[0]
            ]);
        }

        return $data;
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
            $result[$key] = $haystack[$key];
        }
        return $result;
    }

    /**
     * Get instance of Request with available data
     *
     * @return Request
     */
    public static function getInstance(): Request
    {
        return new static(static::$data);
    }

    /**
     * Returns the request quick data.
     *
     * @param string $name The name of the data.
     * @return mixed
     */
    protected static function get(string $name): mixed
    {
        $uri = static::$data['uri'] ?? URL::getURL();

        return match ($name) {
            'uri' => $uri,
            'method' => static::$data[$name] ?? URL::getMethod(),
            'query_string' => static::$data[$name] ?? URL::QueryString(),
            'headers' => static::$data[$name] ?? getallheaders(),
            'params' => static::$data[$name] ?? URL::parseParams($uri),
            default => null
        };
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic(string $name, array $arguments): mixed
    {
        if (str_starts_with($name, 'set') || str_starts_with($name, 'get')) {
            $type = substr($name, 0, 3);
            $property = substr($name, 3);
            $snake = strtolower(ltrim(preg_replace('/[A-Z]/', '_$0', $property), '_'));

            if (in_array($snake, array_keys(static::$allowedData))) {
                if ($type == 'get') {
                    return isset(static::$data[$snake]) && static::$data[$snake] != null
                        ? static::$data[$snake]
                        : static::get($snake);
                }

                static::$data[] = $arguments[0];
                return null;
            }
        }

        if (method_exists(static::class, $name)) {
            return static::$name(...$arguments);
        }

        throw new BadMethodCallException(sprintf(
            'The method %s::%s does not exist',
            self::class,
            $name
        ));
    }

}