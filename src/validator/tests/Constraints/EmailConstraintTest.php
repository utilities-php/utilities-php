<?php
declare(strict_types=1);

namespace UtilitiesTests\Validator\Constraints;

use Utilities\Validator\Constraint;
use Utilities\Validator\Type;
use Utilities\Validator\Validate;

class EmailConstraintTest extends \PHPUnit\Framework\TestCase
{

    public function testIsEmail()
    {
        $this->assertTrue(validate('shahrad@litehex.com')->string()->isEmail());

        $this->assertTrue(validate('shahrad@litehex.com')->typeOf(Type::EMAIL));

        $this->assertFalse(validate('shahrad@litehex')->typeOf('email'));

        $this->assertTrue(validate('shahrad@litehex')->typeOf('string'));

        $this->assertTrue(validate('shahrad@litehex.com')->email()->isDomainEqual('litehex.com'));

        $this->assertTrue(validate('shahrad@litehex.com')->withRule([
            'type' => Type::EMAIL,
            'isDomain' => 'litehex.com',
        ])->isValid());
    }

}