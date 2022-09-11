<?php
declare(strict_types=1);

namespace Utilities\Auth;

use Utilities\Common\Common;
use Utilities\Common\Time;
use Utilities\Common\UUID;

/**
 * User class
 *
 * @link    https://github.com/utilities-php/router
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/router/blob/master/LICENSE (MIT License)
 */
class User
{

    /**
     * @var array
     */
    private array $data;

    /**
     * User constructor.
     *
     * @param array $extend_data
     */
    public function __construct(array $extend_data = [])
    {
        $this->data = array_merge($extend_data, [
            'id' => UUID::generate(),
            'created_at' => Time::getMillisecond(),
            'updated_at' => Time::getMillisecond(),
        ]);
    }

    /**
     * put data to user container
     *
     * @param string $key
     * @param mixed $value
     * @return array
     */
    public function put(string $key, mixed $value): array
    {
        $this->data['container'][$key] = $value;
        $this->data['updated_at'] = Time::getMillisecond();
        return $this->data;
    }

    /**
     * get data from user container
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key): mixed
    {
        return $this->data['container'][$key] ?? null;
    }

    /**
     * get the timestamp of the user creation
     *
     * @return int
     */
    public function getCreatedAt(): int
    {
        return $this->data['created_at'];
    }

    /**
     * get the timestamp of the user last update
     *
     * @return int
     */
    public function getUpdatedAt(): int
    {
        return $this->data['updated_at'];
    }

}