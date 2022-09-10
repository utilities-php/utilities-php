<?php
declare(strict_types=1);

namespace UtilitiesTests\Common;

use PHPUnit\Framework\TestCase;
use Utilities\Common\Validator;

class ValidatorTest extends TestCase
{

    public function testValidateType()
    {
        $arr = [
            'name' => 'shahrad',
            'age' => 25,
            'is_admin' => true,
        ];

        $this->assertTrue(Validator::validateType($arr, [
            'name' => 'string',
            'age' => 'integer|string',
            'is_admin' => 'boolean',
        ]));

        $this->assertFalse(Validator::validateType($arr, [
            'name' => 'string',
            'age' => 'string',
            'is_admin' => 'boolean',
        ]));
    }

}
