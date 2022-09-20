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
        $this->assertTrue(Validate::string('shahrad@litehex.com')->isEmail());

        $this->assertFalse(Validate::string('shahrad@litehex')->isEmail());

        $this->assertTrue((new Validate('shahrad@litehex.com'))->withRule([
            'type' => Type::EMAIL,
            'isDomain' => 'litehex.com',
        ]));
    }



}