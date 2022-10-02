<?php
declare(strict_types=1);

namespace Utilities\Validator\Traits;

use ReflectionMethod;
use Utilities\Validator\Constraint;
use Utilities\Validator\Exception\InvalidOperationException;
use Utilities\Validator\Exception\ValidatorException;
use Utilities\Validator\Result;

/**
 * The RuleValidator class.
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
trait RuleValidator
{

    protected Result $result;

    /**
     * Get available operators.
     *
     * @return array
     */
    public function getOperators(): array
    {
        $res = [];

        foreach (self::$supported_types as $operators) {
            $res = array_merge($res, array_filter(get_class_methods(new $operators('')), function ($method) {
                return !str_starts_with($method, '__');
            }));
        }

        return array_values(array_unique($res));
    }

    /**
     * Check given array has rule.
     *
     * @param array $rules
     * @return bool
     */
    public function hasRule(array $rules): bool
    {
        $keys = array_merge($this->getOperators(), ['type', 'errorCodes', 'errorMessages']);
        return count(array_intersect($keys, array_keys($rules))) > 0;
    }

    /**
     * Validate an associative array with the given rules.
     *
     * @param array $rules The rules to validate the data with.
     * @return Result
     */
    public function withRule(array $rules): Result
    {
        if ($this->hasRule($rules)) {
            if (!isset($rules['type'])) {
                throw new InvalidOperationException('Type is not defined.');
            }

            $rules = [$rules];
        }

        $this->result = new Result();

        foreach ($rules as $key => $rule) {

            // if rule was 'key' => 'type'
            if (is_string($rule)) {
                // Converts 'key' => 'type' to 'key' => ['type' => 'type']
                if (in_array(mb_strtolower($rule), array_keys(self::$supported_types))) {
                    $rule = ['type' => $rule];
                }
            }

            if (!isset($rule['type'])) {
                throw new InvalidOperationException('Type is not defined.');
            }

            if (!is_numeric($key) && is_array($this->data) && !array_key_exists($key, $this->data)) {
                throw new InvalidOperationException(sprintf(
                    "This '%s' key does not exist in your data.",
                    $key
                ));
            }

            $data = $this->data;
            if (is_array($this->data)) {
                $data = is_numeric($key) ? $this->data : $this->data[$key];
            }

            $this->validateRules($rule, $data);
        }

        if ($this->options['throw_exception'] && !$this->result->isValid()) {
            throw new ValidatorException(sprintf(
                "The given data is not valid. Errors count: %s",
                count($this->result->getErrors())
            ));
        }

        return $this->result;
    }

    /**
     * @param array $rules
     * @param mixed $data
     * @return void
     */
    private function validateRules(array $rules, mixed $data): void
    {
        $type = mb_strtolower($rules['type'] ?? 'string');
        if (!in_array($type, array_keys(self::$supported_types))) {
            throw new InvalidOperationException(sprintf(
                "The type '%s' is not supported.",
                $type
            ));
        }

        $constraint = new self::$supported_types[$type]($data);
        $this->validate($constraint, $rules);
    }

    /**
     * Validate the data with the given constraint.
     *
     * @param Constraint $constraint The constraint to validate the data with.
     * @param array $rules The rules to validate the data with.
     * @return void
     */
    private function validate(Constraint $constraint, array $rules): void
    {
        foreach ($rules as $rule => $value) {
            if (in_array($rule, ['type', 'errorCode', 'errorMessage'])) {
                continue;
            }

            $refClass = new \ReflectionClass($constraint);

            try {
                $refMethod = $refClass->getMethod($rule);
            } catch (\ReflectionException $e) {
                throw new InvalidOperationException(sprintf(
                    "Given rule '%s' is not supported in '%s'.",
                    $rule,
                    explode('\\', get_class($constraint))[count(explode('\\', get_class($constraint))) - 1]
                ));
            }

            if (!$this->callMethod($constraint, $refMethod, $value)) {
                $annotations = $this->getMethodAnnotations($refMethod, $value);
                $this->result->addError(
                    $rules['errorCode'] ?? $annotations['error_code'] ?? "INVALID",
                    $rules['errorMessage'] ?? $annotations['error_message'] ?? "The given data is not valid."
                );
            }

        }
    }

    /**
     * Call a method with the given arguments.
     *
     * @param Constraint $constraint
     * @param ReflectionMethod $refMethod
     * @param mixed $args
     * @return mixed
     */
    private function callMethod(Constraint $constraint, ReflectionMethod $refMethod, mixed $args): mixed
    {
        $params = $refMethod->getParameters();
        $method = $refMethod->getName();

        if (count($params) == 0) {
            return $constraint->$method();
        }

        if (count($params) == 1) {
            return $constraint->$method($args);
        }

        return $constraint->$method(...$args);
    }

    /**
     * Get method annotations.
     *
     * @param ReflectionMethod $refMethod
     * @param mixed $args
     * @return array {error_message: string, type: string}
     */
    private function getMethodAnnotations(ReflectionMethod $refMethod, mixed $args): array
    {
        $res = [];
        $docs = $refMethod->getDocComment();

        if (preg_match('/@error_message\s+(.*)/', $docs, $matches)) {
            if (gettype($args) !== 'array') {
                $args = [$args];
            }

            $res['error_message'] = $this->parseErrorMessage($matches[1], $args);
        }

        if (preg_match('/@error_code\s+(.*)/', $docs, $matches)) {
            $res['error_code'] = $matches[1];
        }

        return $res;
    }

    /**
     * Parse error message.
     *
     * @param string $message e.g. "The value must be greater than {[0]}."
     * @param array $args e.g. [0 => 10]
     * @return string
     */
    private function parseErrorMessage(string $message, array $args): string
    {
        $res = $message;
        foreach ($args as $key => $value) {
            $res = str_replace("{[$key]}", (string)$value, $res);
        }

        return $res;
    }

}