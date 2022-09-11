<?php

namespace Utilities\Common;

use EasyHttp\Client;

/**
 * Telegram Class
 *
 * @link    https://github.com/utilities-php/common
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/common/blob/master/LICENSE (MIT License)
 */
class Telegram
{

    /**
     * @var string
     */
    private string $token;

    /**
     * Telegram constructor.
     *
     * @param string $api_token
     */
    public function __construct(string $api_token)
    {
        $this->token = $api_token;
    }

    /**
     * Create instance of Telegram
     *
     * @param string $api_token
     * @return Telegram
     */
    public static function create(string $api_token): Telegram
    {
        return new self($api_token);
    }

    /**
     * This method sends a request to the Telegram api.
     *
     * @param string $method
     * @param array $data
     * @return array|false
     */
    public function send(string $method, array $data = []): array|false
    {
        $url = "https://api.telegram.org/bot" . $this->token . "/" . $method;
        $response = (new Client())->post($url, [
            'body' => $data,
        ]);

        if ($response->getStatusCode() == 200) {
            return json_decode($response->getBody(), true);
        }

        return false;
    }

}