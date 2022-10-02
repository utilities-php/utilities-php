<?php declare(strict_types=1);

namespace Utilities\Common;

use Utilities\Common\Traits\hasGetters;
use Utilities\Common\Traits\hasSetters;

/**
 * This is part of the Utilities package.
 *
 * @link https://github.com/utilities-php/utilities-php
 * @author Shahrad Elahi <shahrad@litehex.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Entity
{

    use hasGetters, hasSetters;

    /**
     * Validation rules. (Used for hasValidator trait)
     *
     * @var array
     */
    protected array $VALIDATION_RULES = [];

    /**
     * Entity constructor.
     *
     * @param array $data The data to initialize the model
     */
    public function __construct(array $data = [])
    {
        $this->set($data);
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments): mixed
    {
        $usedTraits = $this->__getTraits();

        if (in_array(hasSetters::class, $usedTraits)) {
            if (preg_match('/^set([A-Z][a-zA-Z0-9]*)$/', $name, $matches)) {
                $property = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', substr($name, 3)))));
                return $this->__doSet($property, $arguments[0]);
            }
        }

        if (in_array(hasGetters::class, $usedTraits)) {
            if (preg_match('/^get([A-Z][a-zA-Z0-9]*)$/', $name, $matches)) {
                $property = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', substr($name, 3)))));
                return $this->__doGet($property);
            }
        }

        throw new \BadMethodCallException(sprintf(
            "The method '%s' does not exist in the class '%s'.",
            $name,
            get_class($this)
        ));
    }

    /**
     * Get get implemented traits.
     *
     * @return array
     */
    private function __getTraits(): array
    {
        $traits = [];
        $class = get_class($this);

        do {
            foreach (class_uses($class) as $trait) {
                $traits[] = $trait;
            }
        } while ($class = get_parent_class($class));

        return $traits;
    }

    /**
     * Find the key in the model.
     *
     * @param string $key
     * @return bool
     */
    public function hasProperty(string $key): bool
    {
        if (property_exists($this, 'ASSOCIATIVE_STORAGE')) {
            return array_key_exists($key, $this->ASSOCIATIVE_STORAGE);
        }

        if (property_exists($this, $key)) {
            return true;
        }

        return false;
    }

    /**
     * Get type of the property.
     *
     * @param string $key
     * @return string
     */
    public function type(string $key): string
    {
        if (property_exists($this, 'ASSOCIATIVE_STORAGE')) {
            return gettype($this->ASSOCIATIVE_STORAGE[$key]);
        }

        if (property_exists($this, $key)) {
            return gettype($this->$key);
        }

        throw new \InvalidArgumentException(sprintf(
            "The property '%s' does not exist in the class '%s'.",
            $key,
            get_class($this)
        ));
    }

}