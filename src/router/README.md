<p align="center">
  <a href="https://github.com/utilities-php/utilities-php" target="_blank" rel="noopener noreferrer"><img width="140" height="auto" src="https://cdn.jsdelivr.net/gh/utilities-php/utilities-php/logo.png" alt="Utilities PHP" /></a>
</p>
<p align="center">
  <a href="https://github.com/utilities-php/utilities-php/actions"><img src="https://github.com/utilities-php/utilities-php/workflows/PHPUnit%20Test/badge.svg" alt="Build Status" /></a>
  <a href="https://coveralls.io/r/utilities-php/router?branch=master"><img src="https://coveralls.io/repos/utilities-php/router/badge.png?branch=master" alt="Coverage Status" /></a> 
  <a href="https://scrutinizer-ci.com/g/utilities-php/router/?branch=master"><img src="https://img.shields.io/scrutinizer/g/utilities-php/router/master.svg?style=flat" alt="Code Quality" /></a>
  <a href="https://travis-ci.com/utilities-php/router"><img src="https://travis-ci.com/utilities-php/router.svg?branch=master" alt="Build Status" /></a>
</p>
<p align="center">
  <a href="https://packagist.org/packages/utilities-php/router"><img src="https://img.shields.io/packagist/v/utilities-php/router.svg?cacheSeconds=3600" alt="Latest Stable Version" /></a>
  <a href="https://packagist.org/packages/utilities-php/router"><img src="https://img.shields.io/github/languages/code-size/utilities-php/router?cacheSeconds=3600" alt="Code Size" /></a>
  <a href="https://packagist.org/packages/utilities-php/router"><img src="https://img.shields.io/packagist/dt/utilities-php/router?cacheSeconds=3600" alt="Downloads" /></a>
  <a href="https://packagist.org/packages/utilities-php/router"><img src="https://img.shields.io/github/license/utilities-php/router?cacheSeconds=3600" alt="License" /></a>
</p>

# Router Utilities - PHP

## Introduction

A day will come when I will write documentation for this library. Until then, you can use this library to create routes
for your application.

### Installation

```bash
composer require utilities-php/router
```

### Getting Started

```php
<?php
require 'vendor/autoload.php';

use Utilities\Router\Router;

Router::get('/', function () {
    return 'Hello World!';
});
```

<br/>

## Documentation

Some documentation will be here.

* [Router](#router)
    * [Create simple routes](#create-simple-routes)
    * [Create dynamic routes](#create-dynamic-routes)
* [Controller](#controller)
    * [Routing](#route-attribute)
    * [Rate Limiting](#rate-limiting)
    * [Authentication](#authentication)
    * [Anonymous Controllers](#anonymous-controllers)
* [Application](#application)
    * [Create a simple application](#create-a-simple-application)
* [Redirection](#redirection)

### Examples

* [Simple Todo List](/examples/todo-list)

To add another example, just add the link to the documentation.

<br/>

## Router

Some documentation will be here.

##### Create simple routes

```php
use Utilities\Router\Response;
use Utilities\Router\Router;
use Utilities\Router\Utils\StatusCode;

Router::post('/', function () {
    echo 'Hello World!';
});
```

##### Create dynamic routes

```php
use Utilities\Router\Response;
use Utilities\Router\Router;
use Utilities\Router\Utils\StatusCode;

Router::post('/hello/{name}', function ($name) {
    Response::send(StatusCode::OK, [
        'message' => "Hello {$name}",
    ]);
});
```

<br/>

## Controller

Some documentation will be here.

##### Create a simple controller

```php
use Utilities\Router\Response;
use Utilities\Router\Utils\StatusCode;

class HomeController extends \Utilities\Router\Controller
{

    public function index(): void
    {
        Response::send(StatusCode::OK, [
            'description' => "You are on the index page",
        ]);
    }

}
```

##### Create a controller with dynamic routes

```php
use Utilities\Router\Attributes\Route;
use Utilities\Router\Response;
use Utilities\Router\Attributes\RateLimit;
use Utilities\Router\Utils\StatusCode;

class HelloController extends \Utilities\Router\Controller
{

    #[RateLimit(500, 1)]
    #[Route('GET', '/hello/{name}')]
    public function print(array $params): void
    {
        Response::send(StatusCode::OK, [
            'result' => [
                'name' => $params['name'],
            ],
        ]);
    }

}
```

##### Create a controller with secure routes

Please note that you have to implement the `__isAuthorized()` method into your controller class, and also you can
rewrite the unauthorized message by implementing the `__unauthorized()` method.

```php
use Utilities\Router\Attributes\Route;
use Utilities\Router\Response;
use Utilities\Router\Utils\StatusCode;

class PaymentController extends \Utilities\Router\Controller
{

    private string $secret = "SOMETHING";

    public function __isAuthorized(): bool
    {
        if ($this->secret === "SOMETHING") {
            return true;
        }

        return false;
    }

    public function __unauthorized(): void
    {
        Response::send(StatusCode::UNAUTHORIZED,[
            'message'=> "Unauthorized: Sorry, your request could not be processed"
        ]);
    }

    // NOTE: The third parameter of the `Route` attribute is for defining whether the route is secure or not.
    #[Route('POST', '/user/payment', true)]
    public function pay(array $params): void
    {
        Response::send(StatusCode::OK, [
            'result' => [
                'name' => $params['name'],
            ],
        ]);
    }

}
```

##### Anonymous controllers

```php
use Utilities\Router\Controller;
use Utilities\Router\Response;
use Utilities\Router\Router;

Router::controller('Hello', '/api/hello/{name}', new class extends Controller {

    public function index(array $params): void
    {
        Response::send(200, [
            'description' => "You are on the index page",
        ]);
    }

});
```

##### Anonymous controllers with passing extra parameters

```php
use Utilities\Router\AnonymousController;
use Utilities\Router\Response;
use Utilities\Router\Router;
use Utilities\Router\Utils\StatusCode;

Router::controller('Hello', '/api/passing', new class($something) extends AnonymousController {

    public function __process($something): void
    {
        Response::send(StatusCode::OK, [
            'result' => $something,
        ]);
    }

});
```

<br/>

## Application

Some documentation will be here.

##### Create a simple application

```php
use Utilities\Router\Controller;
use Utilities\Router\Request;
use Utilities\Router\Response;
use Utilities\Router\Utils\StatusCode;

class App extends \Utilities\Router\Application
{
    
    public function __process(Request $request): void
    {
        self::addController([
            Controller::__create('/api/todo', TodoController::class),
            Controller::__create('/api/users', UsersController::class)
        ]);
    }

    public function __exception(\Throwable $throwable): void
    {
        Response::send(StatusCode::INTERNAL_SERVER_ERROR,[
            'description' => "Internal Server Error",
        ]);
    }

}
```

<br/>

## Redirection

```
Some documentation will be here.
```

<br/>

## License

```
MIT License

Copyright (c) 2022 LiteHex

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

```