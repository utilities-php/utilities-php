<?php
declare(strict_types=1);

namespace UtilitiesTests\Validator\Constraints;

use Utilities\Validator\Constraint;
use Utilities\Validator\Validate;

class StringConstraintTest extends \PHPUnit\Framework\TestCase
{

    private function someJson():string
    {
        return json_encode([
            'foo' => 'bar',
            'baz' => 'qux',
        ]);
    }

    public function testIsString()
    {
        $this->assertInstanceOf(Constraint::class, validate('test')->string());
        $this->assertTrue(validate($this->someJson())->typeOf('string'));
        $this->assertTrue(validate($this->someJson())->string()->isJson());
    }

    public function testIsJson()
    {
        $this->assertTrue(validate($this->someJson())->string()->isJson());
    }

    public function testIsEmail()
    {
        $this->assertTrue(validate('shahrad@litehex.com')->typeOf('email'));
        $this->assertTrue(validate('shahrad@litehex.com')->email()->isValid());
    }

}