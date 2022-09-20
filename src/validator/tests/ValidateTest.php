<?php
declare(strict_types=1);

namespace UtilitiesTests\Validator;

use PHPUnit\Framework\TestCase;
use Utilities\Common\Common;
use Utilities\Common\Time;
use Utilities\Validator\Constraints\ArrayConstraint;
use Utilities\Validator\Constraints\DateConstraint;
use Utilities\Validator\Constraints\IpConstraint;
use Utilities\Validator\Constraints\NumberConstraint;
use Utilities\Validator\Constraints\StringConstraint;
use Utilities\Validator\Constraints\UrlConstraint;
use Utilities\Validator\Type;
use Utilities\Validator\Validate;

class ValidateTest extends TestCase
{

    public function testStringConstraint()
    {
        $this->assertInstanceOf(StringConstraint::class, Validate::string('test'));
    }

    public function testNumberConstraint()
    {
        $this->assertInstanceOf(NumberConstraint::class, Validate::number(1));
    }

    public function testArrayConstraint()
    {
        $this->assertInstanceOf(ArrayConstraint::class, Validate::array([]));
    }

    public function testUrlConstraint()
    {
        $this->assertInstanceOf(UrlConstraint::class, Validate::url('https://www.google.com'));
    }

    public function testIpConstraint()
    {
        $this->assertInstanceOf(IpConstraint::class, Validate::ip('1.1.1.1'));

    }

    public function testDateConstraint()
    {
        $this->assertInstanceOf(DateConstraint::class, Validate::date('2019-01-01'));
    }

    public function testValidationWithRules(): void
    {
        $user = [
            'name' => 'Shahrad',
            'email' => 'shahrad@litehex.com',
            'phone' => '1234567890',
            'address' => '1234 Main St',
            'created_at' => Time::getMillisecond(),
            'updated_at' => Time::getMillisecond(),
        ];

        $res = ($validator = new Validate($user))->withRule([
            'name' => 'string',
            'email' => [
                'type' => Type::EMAIL,
                'isDomain' => 'litehex.com',
            ],
            'phone' => 'PHONE',
            'address' => 'string',
            'created_at' => [
                'type' => Type::TIMESTAMP,
                'isBetween' => [
                    Time::getMillisecond('-10 seconds'),
                    Time::getMillisecond('+10 seconds'),
                ],
            ],
            'updated_at' => [
                'type' => Type::TIMESTAMP,
                'isGreaterOrEqual' => Time::getMillisecond('-10 seconds'),
            ],
        ]);

        if ($validator->hasError()) {
            echo Common::prettyJson($validator->getErrors()) . PHP_EOL;
        }

        $this->assertTrue($res);
    }

    public function testSingleRuleValidation(): void
    {
        $this->assertTrue((new Validate('test'))->withRule([
            'type' => Type::STRING,
            'isLengthEqual' => 4,
        ]));

        $this->assertFalse((new Validate('test'))->withRule([
            'type' => Type::STRING,
            'isLengthEqual' => 5,
        ]));
    }

    public function testRegexRuleValidation(): void
    {
        $this->assertTrue((new Validate('Some text'))->withRule([
            'type' => Type::STRING,
            'regex' => '/^Some/',
        ]));

        $this->assertFalse((new Validate('test'))->withRule([
            'type' => Type::STRING,
            'regex' => '/^test2$/',
        ]));
    }

    public function testCommonOperators(): void
    {
        $this->assertTrue((new Validate('test'))->withRule([
            'type' => Type::STRING,
            'isIn' => ['test', 'test2'],
        ]));
    }

}
