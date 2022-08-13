<?php
declare(strict_types=1);

namespace UtilitiesTests\Router;

class PathFinderTest extends \PHPUnit\Framework\TestCase
{

    public function testPathFinder()
    {
        $this->assertEquals(
            'text/plain',
            \Utilities\Router\PathFinder::getMimeType(__FILE__)
        );
    }

}