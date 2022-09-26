<?php
declare(strict_types=1);

namespace Utilities\Validator;

use Utilities\Validator\Operators\CommonOperators;

/**
 * The base class for all validators.
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
class Constraint
{

    use CommonOperators;

    /**
     * The error message.
     *
     * @var string
     */
    protected string $message = 'This value is not valid.';

    /**
     * The error code.
     *
     * @var string
     */
    protected string $code = 'INVALID';

    /**
     * The error message parameters.
     *
     * @var array
     */
    protected array $parameters = [];

    /**
     * The error message parameters.
     *
     * @var array
     */
    protected array $options = [];

    /**
     * @param mixed $data
     */
    public function __construct(protected mixed $data)
    {
        $this->isValid();
    }

    /**
     * Is according to the given callback.
     *
     * @param callable $callback
     * @return bool
     */
    public function isAccordingTo(callable $callback): bool
    {
        return $callback($this->data);
    }

    /**
     * Get Type.
     *
     * @return string
     */
    public function getType(): string
    {
        $refClass = new \ReflectionClass($this);
        $name = $refClass->getShortName();
        return str_replace('Constraint', '', $name);
    }

    /**
     * Check the given data is valid with the given constraint.
     *
     * @return bool
     */
    public function isValid(): bool
    {
        $name = "is" . $this->getType();
        return $this->$name();
    }

}