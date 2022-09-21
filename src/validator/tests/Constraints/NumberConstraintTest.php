<?php
declare(strict_types=1);

namespace UtilitiesTests\Validator\Constraints;

class NumberConstraintTest extends \PHPUnit\Framework\TestCase
{

    public function testNumberConstraint()
    {
        $validator = \Utilities\Validator\Validate::number(10);
        $this->assertTrue($validator->isNumber());
    }

}