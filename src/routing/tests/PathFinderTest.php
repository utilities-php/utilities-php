<?php
declare(strict_types=1);

namespace UtilitiesTests\Routing;

use PHPUnit\Framework\TestCase;
use Utilities\Routing\PathFinder;

class PathFinderTest extends TestCase
{

    public function testPathFinder()
    {
        $this->assertEquals('application/x-php', PathFinder::getMimeType(__FILE__));
    }

}