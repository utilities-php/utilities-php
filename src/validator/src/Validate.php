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
use Utilities\Validator\Traits\ErrorHolder;
use Utilities\Validator\Traits\RuleValidator;

/**
 * Entry point for the Validator package.
 *
 * @method static StringConstraint string(mixed $data)
 * @method static NumberConstraint number(mixed $data)
 * @method static ArrayConstraint array(mixed $data)
 * @method static UrlConstraint url(mixed $data)
 * @method static PhoneConstraint phone(mixed $data)
 * @method static EmailConstraint email(mixed $data)
 * @method static IpConstraint ip(mixed $data)
 * @method static UuidConstraint uuid(mixed $data)
 * @method static DateConstraint date(mixed $data)
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

    use Operations, RuleValidator, ErrorHolder;

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
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return Constraint
     */
    public static function __callStatic(string $name, array $arguments): Constraint
    {
        if (array_key_exists($name, self::$supported_types)) {
            return new self::$supported_types[$name]($arguments[0]);
        }

        if (method_exists(Validate::class, $name)) {
            return Validate::$name(...$arguments);
        }

        throw new \BadMethodCallException(sprintf(
            'Method %s does not exist in %s',
            $name,
            __CLASS__
        ));
    }
}