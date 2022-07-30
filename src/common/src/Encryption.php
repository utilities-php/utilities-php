<?php
declare(strict_types=1);

namespace Utilities\Common;

/**
 * Encryption class
 *
 * @link    https://github.com/utilities-php/common
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/common/blob/master/LICENSE (MIT License)
 */
class Encryption
{

    /**
     * @var string $algorithm
     */
    public static string $algorithm = 'sha256';

    /**
     * @var string $cipher
     */
    public static string $cipher = 'AES-256-CBC';

    /**
     * @param string $secret
     * @param string $input
     * @param bool $plain
     * @return string
     */
    public static function encrypt(string $secret, string $input, bool $plain = true): string
    {
        if (!static::validateSecret($secret)) {
            throw new \RuntimeException(sprintf(
                'This secret key is not usable: %s', $secret
            ));
        }

        $key = hash(static::$algorithm, $secret);
        $len = openssl_cipher_iv_length(static::$cipher);
        if (is_string($len) !== true) {
            throw new \RuntimeException(sprintf(
                'The cipher %s is not supported.', static::$cipher
            ));
        }

        $iv = substr(hash(static::$algorithm, $len), 0, 16);

        return base64_encode(openssl_encrypt(
            $input,
            static::$cipher,
            $plain ? $secret : $key,
            0,
            $plain ? substr($secret, 0, 16) : $iv,
        ));
    }

    /**
     * @param string $secret
     * @return bool
     */
    private static function validateSecret(string $secret): bool
    {
        return strlen($secret) >= 16;
    }

    /**
     * @param string $secret
     * @param string $input
     * @param bool $plain
     * @return string
     */
    public static function decrypt(string $secret, string $input, bool $plain = true): string
    {
        if (!static::validateSecret($secret)) {
            throw new \RuntimeException(sprintf(
                'This secret key is not usable: %s', $secret
            ));
        }

        $key = hash(static::$algorithm, $secret);
        $len = openssl_cipher_iv_length(static::$cipher);
        if (is_string($len) !== true) {
            throw new \RuntimeException(sprintf(
                'The cipher %s is not supported.', static::$cipher
            ));
        }

        $iv = substr(hash(static::$algorithm, $len), 0, 16);

        return openssl_decrypt(
            base64_decode($input),
            static::$cipher,
            $plain ? $secret : $key,
            0,
            $plain ? substr($secret, 0, 16) : $iv,
        );
    }

}