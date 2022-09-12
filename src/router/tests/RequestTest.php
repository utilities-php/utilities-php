<?php
declare(strict_types=1);

namespace UtilitiesTests\Router;

use Utilities\Router\Request;
use Utilities\Router\Router;

class RequestTest extends \PHPUnit\Framework\TestCase
{

    public function test_get_request_method()
    {
        RouterTest::setRequest([
            'REQUEST_URI' => '/test/hello/world',
            'REQUEST_METHOD' => 'GET',
        ]);

        Router::createRequest();

        $this->assertEquals('/test/hello/world', Request::getUri());
        $this->assertEquals('GET', Request::getMethod());
    }

    public function test_get_request_uri()
    {
        RouterTest::setRequest([
            'REQUEST_URI' => '/test/hello/world',
        ]);

        Router::createRequest();

        $this->assertEquals('/test/hello/world', Request::getUri());
    }

    public function test_get_request_query_string()
    {
        RouterTest::setRequest([
            'QUERY_STRING' => 'test=test&test2=test2',
        ]);

        Router::createRequest();

        $this->assertEquals(['test' => 'test', 'test2' => 'test2'], Request::getQueryString());
    }

}