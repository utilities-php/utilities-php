<?php
declare(strict_types=1);

namespace UtilitiesTests\Router;

use PHPUnit\Framework\TestCase;
use Utilities\Router\AnonymousController;
use Utilities\Router\Attributes\Route;
use Utilities\Router\Request;
use Utilities\Router\Router;

class RouterTest extends TestCase
{

    public static function setRequest(array $extra): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REMOTE_ADDR'] = '0.0.0.0';
        $_SERVER['HTTP_ORIGIN'] = 'example.com';
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['SERVER_PORT'] = '80';
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $_SERVER['REQUEST_TIME'] = time();
        $_SERVER['REQUEST_TIME_FLOAT'] = microtime(true);
        $_SERVER = array_merge($_SERVER, $extra);
    }

    public function test_get_uri()
    {
        RouterTest::setRequest(['REQUEST_URI' => '/test/test2']);
        Router::createRequest();

        $this->assertEquals('/test/test2', Request::getUri());
    }

    public function test_get_route()
    {
        RouterTest::setRequest(['REQUEST_URI' => '/']);
        Router::get('/', function () {
            $this->assertEquals('/', Request::getUri());
            $this->assertEquals(['test' => 'test', 'test2' => 'test2'], Request::getQueryString());
        });

        RouterTest::setRequest(['REQUEST_URI' => '/test/hi']);
        Router::get('/test/{query}', function ($query) {
            $this->assertNotEquals('test', $query);
        });
    }

    public function test_find_uri()
    {
        Router::get('/test/me', function () {
            $this->assertTrue(true);
        });

        RouterTest::setRequest(['REQUEST_URI' => '/test/me']);
        Router::resolve();

        Router::get('/hello/{name}', function ($name) {
            $this->assertEquals('world', $name);
        });

        RouterTest::setRequest(['REQUEST_URI' => '/hello/world']);
    }

    public function test_controller_route()
    {
        RouterTest::setRequest(['REQUEST_URI' => '/api/james']);
        Router::controller('james', '/api/james', new class($this) extends AnonymousController {

            public function __process(Request $request, TestCase $testCase): void
            {
                $testCase->assertTrue(true);
            }

        });
    }

    public function test_controller_route_with_params()
    {
        RouterTest::setRequest(['REQUEST_URI' => '/api/hello/james']);
        Router::controller('Hello', '/api/hello', new class($this) extends AnonymousController {

            private TestCase $testCase;

            public function __process(Request $request, TestCase $testCase): void
            {
                $this->testCase = $testCase;
            }

            #[Route('GET', '/{name}')]
            public function test(array $params): void
            {
                $this->testCase->assertEquals('james', $params['name']);
            }

        });
    }

    public function test_public_resource()
    {
//        todo: Add public resource test
//        RouterTest::setRequest(['REQUEST_URI' => '/public/logo.png']);
//        Router::resource('/public', __DIR__ . '/../../../docs');
//        Router::resolve();

        $this->assertTrue(true);
    }

}