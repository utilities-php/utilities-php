<?php
declare(strict_types=1);

namespace UtilitiesTests\Validator\Constraints;

class NumberConstraintTest extends \PHPUnit\Framework\TestCase
{

    public function testNumberConstraint()
    {
        $this->assertTrue(validate(10)->number()->is(10));
        $this->assertTrue(validate(10)->number()->isGreaterThan(9));
    }

}