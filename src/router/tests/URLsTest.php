<?php
declare(strict_types=1);

namespace UtilitiesTests\Router;

use PHPUnit\Framework\TestCase;
use Utilities\Router\URLs;

class URLsTest extends TestCase
{

    public function testQueryString()
    {
        $queries = [
            'test' => 'test',
            'test2' => 'test2',
        ];

        $this->assertEquals($queries, URLs::QueryString([
            'test' => 'test',
            'test2' => 'test2',
        ]));
    }

    public function testFindIndexOfSegment()
    {
        $_SERVER['REQUEST_URI'] = '/test/hello/world';
        $this->assertEquals(1, URLs::findIndexOfSegment('hello', true));
    }

    public function testParseURL()
    {
        $this->assertEquals(['first' => "hello", 'second' => "world"], URLs::parseURL(
            '/test/hello/world', '/test/{first}/{second}'
        ));
    }

    public function testGetURL()
    {
        $_SERVER['REQUEST_URI'] = '/test/hello/world';
        $this->assertEquals('/test/hello/world', URLs::getURL());
    }

    public function testSegments()
    {
        $_SERVER['REQUEST_URI'] = '/test/hello/world';
        $this->assertEquals(['test', 'hello', 'world'], URLs::getSegments());
    }

}
