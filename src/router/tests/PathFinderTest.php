<?php
declare(strict_types=1);

namespace UtilitiesTests\Router;

use PHPUnit\Framework\TestCase;
use Utilities\Router\PathFinder;

class PathFinderTest extends TestCase
{

    public function testPathFinder()
    {
        $this->assertEquals('application/x-php', PathFinder::getMimeType(__FILE__));
    }

}