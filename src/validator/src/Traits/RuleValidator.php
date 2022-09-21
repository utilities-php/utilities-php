<?php
declare(strict_types=1);

namespace Utilities\Validator\Traits;

use ReflectionMethod;
use Utilities\Validator\Constraint;
use Utilities\Validator\Exception\InvalidOperationException;
use Utilities\Validator\Exception\ValidatorException;

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

    /**
     * Validate an associative array with the given rules.
     *
     * @param array $rules The rules to validate the data with.
     * @return bool
     */
    public function withRule(array $rules): bool
    {
        if (isset($rules['type'])) {
            $rules = [$rules];
        }

        foreach ($rules as $key => $rule) {

            if (is_string($rule)) {
                if (in_array(mb_strtolower($rule), array_keys(self::$supported_types))) {
                    $rule = [
                        'type' => $rule,
                    ];
                }
            }

            if (!is_numeric($key) && !array_key_exists($key, $this->data)) {
                throw new InvalidOperationException(sprintf(
                    "The key '%s' does not exist in the data.",
                    $key
                ));
            }

            $data = is_numeric($key) ? $this->data : $this->data[$key];
            $this->validateRules($rule, $data);
        }

        if ($this->options['throw_exception'] && $this->hasError()) {
            throw new ValidatorException(sprintf(
                "The given data is not valid. Errors count: %s",
                count($this->getErrors())
            ));
        }

        return $this->hasError() !== true;
    }

    /**
     * @param array $rules
     * @param mixed $data
     * @return void
     */
    private function validateRules(array $rules, mixed $data): void
    {
        $type = mb_strtolower($rules['type']);
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
     * @return array If returns an empty array, it means there is no error.
     */
    private function validate(Constraint $constraint, array $rules): array
    {
        $errors = [];

        foreach ($rules as $rule => $value) {
            if ($rule === 'type' || $rule === 'error_message') {
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
                $this->addError($rules['error_message'] ?? $annotations['error_message'] ?? sprintf(
                    "Given value '%s' is not valid for '%s'.",
                    $value,
                    $rule
                ));
            }

        }

        return $errors;
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
            $res = str_replace("{[$key]}", $value, $res);
        }

        return $res;
    }

}