<?php
declare(strict_types=1);

namespace Utilities\Router;

use Utilities\Common\Common;
use Utilities\Common\Time;
use Utilities\Router\Utils\StatusCode;

/**
 * Response class
 *
 * @link    https://github.com/utilities-php/router
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/router/blob/master/LICENSE (MIT License)
 */
class Response
{

    /**
     * @var int
     */
    private static int $status_code = -1;

    /**
     * Kill process after sending response.
     *
     * @var bool
     */
    public static bool $PROCESS_KILLER = true;

    /**
     * Require given keys in array and send error if not found
     *
     * @param array $haystack
     * @param array $needle
     * @return array
     */
    public static function RequireParams(array $haystack, array $needle): array
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
     * Send response.
     *
     * @param int $statusCode
     * @param string|array $body
     * @return void
     */
    public static function send(int $statusCode, string|array $body = []): void
    {
        static::$status_code = $statusCode;
        $status = ($statusCode >= 200 && $statusCode < 300);

        http_response_code($statusCode);
        if (!headers_sent()) {
            header('Content-type: application/json');
        }

        if (is_array($body)) {
            $message['ok'] = $status;
            if (!$status) $message['error_code'] = $statusCode;
            $message['elapsed_time'] = Time::elapsedTime() . "ms";

            foreach ($body as $key => $value) {
                $message[$key] = $value;
            }

            $body = Common::prettyJson($message);
        }

        echo $body;
        if (static::$PROCESS_KILLER) die($statusCode);
    }

    /**
     * Check are values are empty (API Response)
     *
     * @param array $params
     * @return void
     */
    public static function EmptyParams(array $params = []): void
    {
        foreach ($params as $key => $value) {
            if ($value == null) Response::send(StatusCode::BAD_REQUEST, [
                'description' => sprintf("Bad Request: the '%s' parameter is empty", $key)
            ]);
        }
    }

    /**
     * This method returns the last status code. On empty, it returns -1.
     *
     * @return int
     */
    public static function getStatusCode(): int
    {
        return static::$status_code;
    }

}