<?php
declare(strict_types=1);

namespace Utilities\Common;

/**
 * Temporary class
 *
 * @link    https://github.com/utilities-php/common
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/common/blob/master/LICENSE (MIT License)
 */
class Temporary
{

    /**
     * Put a value in the temporary storage
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function put(string $key, mixed $value): void
    {
        $_SESSION['x-local-temp'][$key] = $value;
    }

    /**
     * Get a value from the temporary storage
     *
     * @param string $key
     * @return mixed
     */
    public static function get(string $key): mixed
    {
        return $_SESSION['x-local-temp'][$key];
    }

}