<?php declare(strict_types=1);

namespace Utilities\Validator\Operators;

use Utilities\Validator\Validate;

trait ClassicValidator
{

    /**
     * @param mixed $limit
     * @return Validate
     */
    public function is(mixed $limit): Validate
    {
        if ($this->data !== $limit) {
            $this->result->addError('INVALID', 'Value is not valid.');
        }

        return $this;
    }

    /**
     * @param mixed $limit
     * @return Validate
     */
    public function isNot(mixed $limit): Validate
    {
        if ($this->data === $limit) {
            $this->result->addError('INVALID', 'Value is not valid.');
        }

        return $this;
    }

    /**
     * @param array $haystack
     * @return Validate
     */
    public function isIn(array $haystack): Validate
    {
        if (!in_array($this->data, $haystack)) {
            $this->result->addError('INVALID', 'Value is not in the array.');
        }

        return $this;
    }

    /**
     * @param array $haystack
     * @return Validate
     */
    public function isNotIn(array $haystack): Validate
    {
        if (in_array($this->data, $haystack)) {
            $this->result->addError('INVALID', 'Value is in the array.');
        }

        return $this;
    }

    /**
     * Is empty.
     *
     * @return Validate
     */
    public function isEmpty(): Validate
    {
        if (!empty($this->data)) {
            $this->result->addError('EMPTY', 'Value is not empty.');
        }

        return $this;
    }

    /**
     * Is not empty.
     *
     * @return Validate
     */
    public function isNotEmpty(): Validate
    {
        if (empty($this->data)) {
            $this->result->addError('EMPTY', 'Value is empty.');
        }

        return $this;
    }

    /**
     * Contains
     *
     * @param mixed $needle
     * @return bool
     */
    private function contains(string $needle): bool
    {
        return match (gettype($this->data)) {
            'string' => str_contains($this->data, $needle),
            'array' => in_array($needle, $this->data),
            'object' => property_exists($this->data, $needle),
            default => false
        };
    }

    /**
     * Contains with.
     *
     * @param mixed $needle
     * @return Validate
     */
    public function isContains(mixed $needle): Validate
    {
        if ($this->contains($needle) === false) {
            $this->result->addError('INVALID', 'Value does not contain the given data.');
        }

        return $this;
    }

    /**
     * Does not contain with.
     *
     * @param mixed $needle
     * @return Validate
     */
    public function doesNotContains(mixed $needle): Validate
    {
        if ($this->contains($needle)) {
            $this->result->addError('INVALID', 'Value contains the given data.');
        }

        return $this;
    }

    /**
     * Match the given pattern.
     *
     * @param string $pattern The regular expression pattern.
     * @return Validate
     */
    public function match(string $pattern): Validate
    {
        if (preg_match($pattern, $this->data) < 1) {
            $this->result->addError('INVALID', 'Value does not match the pattern.');
        }

        return $this;
    }

    /**
     * Is Valid.
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->result->isValid();
    }

}