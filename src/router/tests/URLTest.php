<?php
declare(strict_types=1);

namespace UtilitiesTests\Router;

use PHPUnit\Framework\TestCase;
use Utilities\Router\URL;

class URLTest extends TestCase
{

    public function test_query_string()
    {
        RouterTest::setRequest([
            'QUERY_STRING' => 'test=test&test2=test2',
        ]);

        $this->assertEquals(URL::QueryString(), [
            'test' => 'test',
            'test2' => 'test2',
        ]);
    }

    public function test_find_index_segment()
    {
        $_SERVER['REQUEST_URI'] = '/test/hello/world';
        $this->assertEquals(1, URL::findIndexOfSegment('hello', true));
    }

    public function test_parse_url()
    {
        $this->assertEquals(['first' => "hello", 'second' => "world"], URL::parseURL(
            '/test/hello/world', '/test/{first}/{second}'
        ));
    }

    public function test_get_url()
    {
        $_SERVER['REQUEST_URI'] = '/test/hello/world';
        $this->assertEquals('/test/hello/world', URL::getURL());
    }

    public function test_segments()
    {
        $_SERVER['REQUEST_URI'] = '/test/hello/world';
        $this->assertEquals(['test', 'hello', 'world'], URL::getSegments());
    }

    public function test_parse_params()
    {
        $_SERVER['REQUEST_URI'] = '/test/hello/world';
        $this->assertEquals(['first' => 'hello', 'second' => 'world'], URL::parseParams('/test/{first}/{second}'));
    }

}
