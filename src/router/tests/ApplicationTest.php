<?php
declare(strict_types=1);

namespace UtilitiesTests\Router;

use PHPUnit\Framework\TestCase;
use Utilities\Router\Response;
use UtilitiesTests\Router\Creative\App;

class ApplicationTest extends TestCase
{

    public function test_james_controller()
    {
        RouterTest::setRequest([
            'QUERY_STRING' => 'test=test2',
            'REQUEST_URI' => '/james/echo/',
        ]);

        (new App())->resolve();
        $this->assertEquals(200, Response::getStatusCode());
    }

    public function test_todo_controller()
    {
        RouterTest::setRequest([
            'QUERY_STRING' => 'test=test2',
            'REQUEST_URI' => '/api/todo/',
        ]);
        (new App())->resolve();

        RouterTest::setRequest([
            'REQUEST_METHOD' => 'DELETE',
            'REQUEST_URI' => '/api/todo/2560',
        ]);
        (new App())->resolve();

        $this->assertEquals(200, Response::getStatusCode());
    }

    public function test_route_attribute()
    {
        RouterTest::setRequest([
            'QUERY_STRING' => 'test=test2',
            'REQUEST_URI' => '/james/echo/',
        ]);

        (new App())->resolve();
        $this->assertEquals(200, Response::getStatusCode());
    }

    public function test_echo()
    {
        RouterTest::setRequest([
            'QUERY_STRING' => 'test=test2',
            'REQUEST_URI' => '/echo/',
        ]);

        (new App())->resolve();
        $this->assertEquals(Response::getResponse('result'), [
            'test' => 'test2',
        ]);

        RouterTest::setRequest([
            'QUERY_STRING' => 'test3=test4',
            'REQUEST_URI' => '/james/echo/',
        ]);

        (new App())->resolve();
        $this->assertEquals(Response::getResponse('result'), [
            'test3' => 'test4',
        ]);
    }

}