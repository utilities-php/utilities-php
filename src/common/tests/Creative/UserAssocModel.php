<?php declare(strict_types=1);

namespace UtilitiesTests\Common\Creative;

use Utilities\Common\Traits\hasAssocStorage;
use Utilities\Common\Traits\hasGetters;
use Utilities\Common\Traits\hasSetters;

/**
 * The UserModel class.
 *
 * @method UserModel setFirstName(string $firstName)
 * @method UserModel setLastName(string $lastName)
 * @method UserModel setAge(int $age)
 * @method UserModel setGender(string $gender)
 * @method UserModel setCountry(string $country)
 *
 * @method string getFirstName()
 * @method string getLastName()
 * @method int getAge()
 * @method string getGender()
 * @method string getCountry()
 */
class UserAssocModel extends \Utilities\Common\Model
{

    use hasGetters, hasSetters, hasAssocStorage;

}