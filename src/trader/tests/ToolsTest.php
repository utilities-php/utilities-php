<?php
declare(strict_types=1);

namespace UtilitiesTests\Trader;

use Utilities\Trader\Trader;

class ToolsTest extends \PHPUnit\Framework\TestCase
{

    public function test_calculatePL()
    {
        $this->assertEquals(0, Trader::CalculatePL("LONG", 100, 100, 100));
        $this->assertEquals(0, Trader::CalculatePL("SHORT", 100, 100, 100));
        $this->assertEquals(-1, Trader::CalculatePL("LONG", 100, 100, 99));
    }

}