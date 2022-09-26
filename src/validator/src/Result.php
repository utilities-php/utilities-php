<?php declare(strict_types=1);

namespace Utilities\Validator;

/**
 * The validation result class.
 *
 *
 * This is part of the Utilities package.
 *
 * @link https://github.com/utilities-php/utilities-php
 * @author Shahrad Elahi <shahrad@litehex.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Result
{

    /**
     * @param bool $valid
     * @param array $errors
     */
    public function __construct(protected bool $valid = true, protected array $errors = [])
    {
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->valid;
    }

    /**
     * @return Error[]
     */
    public function getErrors(): array
    {
        return array_map(function ($error) {
            return new Error($error['code'], $error['message']);
        }, $this->errors);
    }

    /**
     * Get the first error.
     *
     * @return Error|null
     */
    public function getFirstError(): Error|null
    {
        return $this->getErrors()[0] ?? null;
    }

    /**
     * Add an error to the result.
     *
     * @param string $code
     * @param string $message
     *
     * @return Result
     */
    public function addError(string $code, string $message): Result
    {
        $this->valid = false;
        $this->errors[] = [
            'code' => $code,
            'message' => $message,
        ];

        return $this;
    }

}