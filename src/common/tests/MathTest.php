<?php
declare(strict_types=1);

namespace UtilitiesTests\Common;

use PHPUnit\Framework\TestCase;
use Utilities\Common\Math;

class MathTest extends TestCase
{

    public function test_prime(): void
    {
        $this->assertTrue(Math::isPrime(2));
        $this->assertTrue(Math::isPrime(3));
        $this->assertTrue(Math::isPrime(5));
        $this->assertTrue(Math::isPrime(7));
        $this->assertTrue(Math::isPrime(11));
        $this->assertTrue(Math::isPrime(13));
    }

}