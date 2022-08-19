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
    private static int $STATUS_CODE = -1;

    /**
     * @var array|string
     */
    private static array|string $LAST_RESPONSE = [];

    /**
     * Kill process after sending response.
     *
     * @var bool
     */
    public static bool $PROCESS_KILLER = true;

    /**
     * Send response.
     *
     * @param int $statusCode
     * @param string|array $body
     * @return void
     */
    public static function send(int $statusCode, string|array $body = []): void
    {
        static::$LAST_RESPONSE = $body;
        static::$STATUS_CODE = $statusCode;
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
     * This method returns the last status code. On empty, it returns -1.
     *
     * @return int
     */
    public static function getStatusCode(): int
    {
        return static::$STATUS_CODE;
    }

    /**
     * This method returns the last response. On empty, it returns null.
     *
     * @param string|null $key (optional) the key of the response If it is an array
     * @return array|string
     */
    public static function getResponse(string $key = null): array|string
    {
        if ($key == null) {
            return static::$LAST_RESPONSE;
        }
        return static::$LAST_RESPONSE[$key] ?? static::$LAST_RESPONSE;
    }

    /**
     * Close and send an empty response
     *
     * @return void
     */
    public static function close(): void
    {
        static::$STATUS_CODE = StatusCode::NO_CONTENT;
        die(StatusCode::NO_CONTENT);
    }

    /**
     * Close the connection with client and let server do rest of the job
     *
     * @param string $text
     * @return void
     */
    public static function closeConnection(string $text = ''): void
    {
        static::$STATUS_CODE = StatusCode::OK;
        ob_end_clean();
        header("Connection: close");
        ignore_user_abort(true);
        ob_start();
        echo($text);
        $size = ob_get_length();
        header("Content-Length: $size");
        ob_end_flush();
        flush();
    }

}