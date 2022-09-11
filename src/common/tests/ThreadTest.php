<?php
declare(strict_types=1);

namespace UtilitiesTests\Common;

use Utilities\Common\Thread;

class ThreadTest extends \PHPUnit\Framework\TestCase
{

    public function testDelay()
    {
        $start = microtime(true);
        Thread::delay(1000, function () use ($start) {
            $this->assertGreaterThanOrEqual(1, microtime(true) - $start);
        });
    }

}