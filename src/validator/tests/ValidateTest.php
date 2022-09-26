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

class ValidateTest extends TestCase
{

    public function testConstraint()
    {
        $this->assertInstanceOf(StringConstraint::class, validate('test')->string());
        $this->assertInstanceOf(NumberConstraint::class, validate(1)->number());
        $this->assertInstanceOf(ArrayConstraint::class, validate([])->array());
        $this->assertInstanceOf(UrlConstraint::class, validate('https://www.google.com')->url());
        $this->assertInstanceOf(IpConstraint::class, validate('1.1.1.1')->ip());
        $this->assertInstanceOf(DateConstraint::class, validate('2021-01-01')->date());

        echo json_encode(validate('Some')->getOperators(), JSON_PRETTY_PRINT);
    }

    public function testWithInnerValidators(){
        $this->assertTrue(
            validate('test')
                ->match('/test/')
                ->isNotEmpty()
                ->isValid()
        );

        $this->assertFalse(
            validate('test')
                ->doesNotContains('te')
                ->isContains('ties')
                ->isValid()
        );
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

        $res = validate($user)->withRule([
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

        if (!$res->isValid()) {
            echo Common::prettyJson($res->getErrors()) . PHP_EOL;
        }

        $this->assertTrue($res->isValid());
    }

    public function testSingleRuleValidation(): void
    {
        $this->assertTrue(validate('test')->withRule([
            'type' => Type::STRING,
            'isLengthEqual' => 4,
        ])->isValid());

        $this->assertFalse(validate('test')->withRule([
            'type' => Type::STRING,
            'isLengthEqual' => 5,
        ])->isValid());
    }

    public function testRegexRuleValidation(): void
    {
        $this->assertTrue(validate('Some text')->withRule([
            'type' => Type::STRING,
            'match' => '/^Some/',
        ])->isValid());

        $this->assertFalse(validate('test')->withRule([
            'type' => Type::STRING,
            'match' => '/^test2$/',
        ])->isValid());
    }

    public function testCommonOperators(): void
    {
        $this->assertTrue(validate('test')->withRule([
            'type' => Type::STRING,
            'isIn' => ['test', 'test2'],
        ])->isValid());
    }

    public function testErrorPrinting(): void
    {
        $res = validate(['SAD' => "test2"])->withRule([
            'SAD' => [
                'type' => Type::STRING,
                'errorCode' => 'TEST_ERROR',
                'errorMessage' => 'Test error message',
                'isContains' => 'test2',
            ]
        ]);

        $this->assertTrue($res->isValid());

        $res = validate(['SAD'])->withRule([
            'type' => Type::ARRAY,
            'errorCode' => 'TEST_ERROR',
            'errorMessage' => 'Test error message',
            'isContains' => 'test2',
        ]);

        $this->assertFalse($res->isValid());

        $res = validate('test')->withRule([
            'type' => Type::STRING,
            'errorCode' => 'TEST_ERROR',
            'errorMessage' => 'Test error message',
            'isContains' => 'test2',
        ]);

        $this->assertFalse($res->isValid());

        echo Common::prettyJson($res->getErrors()) . PHP_EOL;

        $error = $res->getFirstError();
        $this->assertEquals('TEST_ERROR', $error->code);
        $this->assertEquals('Test error message', $error->message);
    }

}
