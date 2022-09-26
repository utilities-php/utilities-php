<?php
declare(strict_types=1);

namespace Utilities\Validator;

use Utilities\Validator\Constraints\ArrayConstraint;
use Utilities\Validator\Constraints\DateConstraint;
use Utilities\Validator\Constraints\EmailConstraint;
use Utilities\Validator\Constraints\IpConstraint;
use Utilities\Validator\Constraints\NumberConstraint;
use Utilities\Validator\Constraints\PhoneConstraint;
use Utilities\Validator\Constraints\StringConstraint;
use Utilities\Validator\Constraints\TimestampConstraint;
use Utilities\Validator\Constraints\UrlConstraint;
use Utilities\Validator\Constraints\UuidConstraint;
use Utilities\Validator\Operators\ClassicValidator;
use Utilities\Validator\Traits\RuleValidator;

/**
 * Entry point for the Validator package.
 *
 * @method StringConstraint string()
 * @method NumberConstraint number()
 * @method ArrayConstraint array()
 * @method UrlConstraint url()
 * @method PhoneConstraint phone()
 * @method EmailConstraint email()
 * @method IpConstraint ip()
 * @method UuidConstraint uuid()
 * @method DateConstraint date()
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
class Validate
{

    use Operations, RuleValidator, ClassicValidator;

    /**
     * @var array|string[]
     */
    protected static array $supported_types = [
        Type::STRING => StringConstraint::class,
        Type::NUMBER => NumberConstraint::class,
        Type::ARRAY => ArrayConstraint::class,
        Type::URL => UrlConstraint::class,
        Type::PHONE => PhoneConstraint::class,
        Type::EMAIL => EmailConstraint::class,
        Type::IP => IpConstraint::class,
        Type::UUID => UuidConstraint::class,
        Type::DATE => DateConstraint::class,
        Type::TIMESTAMP => TimestampConstraint::class,
    ];

    /**
     * The validator options.
     *
     * @var array
     */
    protected array $options = [
        'throw_exception' => false,
    ];

    /**
     * @param mixed $data The data to store for validation.
     * @param array $options [optional] Validator options.
     */
    public function __construct(protected mixed $data, array $options = [])
    {
        $this->options = array_merge($this->options, $options);
        $this->result = new Result();
    }

    /**
     * Is type of
     *
     * @param string $type
     * @return bool
     */
    public function typeOf(string $type): bool
    {
        $type = mb_strtolower($type);

        if (!isset(self::$supported_types[$type])) {
            throw new \InvalidArgumentException("The type '$type' is not supported.");
        }

        foreach (self::$supported_types as $supported_type => $constraint) {
            if ((new $constraint($this->data))->isValid()) {
                if ($supported_type === $type) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return Constraint
     */
    public function __call(string $name, array $arguments): Constraint
    {
        if (array_key_exists($name, self::$supported_types)) {
            return new self::$supported_types[$name]($this->data);
        }

        if (method_exists($this, $name)) {
            return $this->$name(...$arguments);
        }

        throw new \BadMethodCallException(sprintf(
            'Method %s does not exist in %s',
            $name,
            __CLASS__
        ));
    }

}