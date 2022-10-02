<?php declare(strict_types=1);

namespace UtilitiesTests\Common\Creative;

use Utilities\Common\Traits\hasAssocStorage;
use Utilities\Common\Traits\hasGetters;
use Utilities\Common\Traits\hasSetters;

/**
 * The UserModel class.
 *
 * @method UserEntity setFirstName(string $firstName)
 * @method UserEntity setLastName(string $lastName)
 * @method UserEntity setAge(int $age)
 * @method UserEntity setGender(string $gender)
 * @method UserEntity setCountry(string $country)
 *
 * @method string getFirstName()
 * @method string getLastName()
 * @method int getAge()
 * @method string getGender()
 * @method string getCountry()
 */
class UserAssocEntity extends \Utilities\Common\Entity
{

    use hasGetters, hasSetters, hasAssocStorage;

}