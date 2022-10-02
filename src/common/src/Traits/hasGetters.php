<?php declare(strict_types=1);

namespace Utilities\Common\Traits;

/**
 * This is part of the Utilities package.
 *
 * @link https://github.com/utilities-php/utilities-php
 * @author Shahrad Elahi <shahrad@litehex.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
trait hasGetters
{

    /**
     * Get data from the model using array.
     *
     * @param array $data
     * @return array
     */
    public function get(array $data): array
    {
        $result = [];

        foreach ($data as $key) {
            $result[$key] = $this->__doGet($key);
        }

        return $result;
    }

    /**
     * Get data from the model.
     *
     * @param string $key
     * @return mixed
     */
    private function __doGet(string $key): mixed
    {
        if (property_exists($this, 'ASSOCIATIVE_STORAGE')) {
            return $this->ASSOCIATIVE_STORAGE[$key];
        }

        if (property_exists($this, $key)) {
            return $this->$key;
        }

        throw new \InvalidArgumentException(sprintf(
            "The property '%s' does not exist in the class '%s'.",
            $key,
            get_class($this)
        ));
    }

    /**
     * Convert the entity to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        if (property_exists($this, 'ASSOCIATIVE_STORAGE')) {
            return $this->ASSOCIATIVE_STORAGE;
        }

        return get_object_vars($this);
    }

}