<?php declare(strict_types=1);

use Utilities\Validator\Validate;

if (!function_exists('validate')) {
    /**
     * A quick instance of validator class
     *
     * @param mixed $data
     * @param array $options
     * @return Validate
     */
    function validate(mixed $data, array $options = []): Validate
    {
        return new Validate($data, $options);
    }
}