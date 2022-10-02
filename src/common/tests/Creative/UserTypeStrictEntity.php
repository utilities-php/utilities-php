<?php declare(strict_types=1);

namespace UtilitiesTests\Common\Creative;

use Utilities\Common\Traits\hasAssocStorage;
use Utilities\Common\Traits\hasValidator;
use Utilities\Validator\Type;

/**
 * The UserModel class.
 *
 * @method UserEntity setFirstName(string $firstName)
 * @method UserEntity setLastName(string $lastName)
 * @method UserEntity setEmail(string $email)
 * @method UserEntity setAge(int $age)
 * @method UserEntity setGender(string $gender)
 * @method UserEntity setCountry(string $country)
 *
 * @method string getFirstName()
 * @method string getLastName()
 * @method string getEmail()
 * @method int getAge()
 * @method string getGender()
 * @method string getCountry()
 */
class UserTypeStrictEntity extends \Utilities\Common\Entity
{

    use hasAssocStorage, hasValidator;

    protected array $VALIDATION_RULES = [
        'firstName' => [
            'type' => Type::STRING,
            'isLengthBetween' => [2, 50],
        ],
        'lastName' => [
            'type' => Type::STRING,
            'isLengthBetween' => [2, 50],
        ],
        'email' => [
            'type' => Type::EMAIL,
            'isDomainEqual' => 'litehex.com',
        ],
        'age' => [
            'type' => Type::NUMBER,
            'isBetween' => [18, 100],
        ]
    ];

}