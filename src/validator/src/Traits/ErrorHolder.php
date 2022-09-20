<?php
declare(strict_types=1);

namespace Utilities\Validator\Traits;

trait ErrorHolder
{

    protected array $errors = [];

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param string $error
     * @return void
     */
    protected function addError(string $error): void
    {
        $this->errors[] = $error;
    }

    /**
     * @return bool
     */
    public function hasError(): bool
    {
        return count($this->errors) > 0;
    }

    /**
     * @return void
     */
    protected function clearErrors(): void
    {
        $this->errors = [];
    }

}