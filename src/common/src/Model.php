<?php declare(strict_types=1);

namespace Utilities\Common;

use Utilities\Common\Traits\hasGetters;
use Utilities\Common\Traits\hasSetters;

/**
 * The Model class.
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
class Model
{

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments): mixed
    {
        $usedTraits = $this->getTraits();

        if (in_array(hasSetters::class, $usedTraits)) {
            if (preg_match('/^set([A-Z][a-zA-Z0-9]+)$/', $name, $matches)) {
                $property = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', substr($name, 3)))));

                if (property_exists($this, $property)) {
                    $this->$property = $arguments[0];
                    return $this;
                }

            }
        }

        if (in_array(hasGetters::class, $usedTraits)) {
            if (preg_match('/^get([A-Z][a-zA-Z0-9]+)$/', $name, $matches)) {
                $property = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', substr($name, 3)))));

                if (property_exists($this, $property)) {
                    return $this->$property;
                }

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
    private function getTraits(): array
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

}