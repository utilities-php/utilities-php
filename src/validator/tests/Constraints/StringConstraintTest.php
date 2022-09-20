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
        $this->assertInstanceOf(Constraint::class, Validate::string('test'));
        $this->assertTrue(Validate::string($this->someJson())->isString());
        $this->assertTrue(Validate::string($this->someJson())->isJson());
    }

    public function testIsJson()
    {
        $this->assertTrue(Validate::string($this->someJson())->isJson());
    }

    public function testIsEmail()
    {
        $this->assertTrue(Validate::string('shahrad@litehex.com')->isEmail());
    }

}