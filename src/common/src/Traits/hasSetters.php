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
trait hasSetters
{

    /**
     * Set data to the model using array.
     *
     * @param array $data
     * @return static
     */
    public function set(array $data): static
    {
        foreach ($data as $key => $value) {
            $this->__doSet($key, $value);
        }

        return $this;
    }

    /**
     * Set data to the model.
     *
     * @param string $key
     * @param mixed $data
     * @return static
     */
    private function __doSet(string $key, mixed $data): static
    {
        if (property_exists($this, 'ASSOCIATIVE_STORAGE')) {
            $this->ASSOCIATIVE_STORAGE[$key] = $data;
            return $this;
        }

        if (property_exists($this, $key)) {
            $this->$key = $data;
            return $this;
        }

        throw new \InvalidArgumentException(sprintf(
            "The property '%s' does not exist in the class '%s'.",
            $key,
            get_class($this)
        ));
    }

}