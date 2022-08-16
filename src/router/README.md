# Router Utilities - PHP

<p align="center">
  <a href="https://github.com/utilities-php/utilities-php" target="_blank" rel="noopener noreferrer"><img style="border-radius: 8%" width="140" height="auto" src="https://cdn.jsdelivr.net/gh/utilities-php/utilities-php/docs/logo-2x.png" alt="Utilities PHP" /></a>
</p>
<p align="center">
  <a href="https://github.com/utilities-php/utilities-php/actions"><img src="https://github.com/utilities-php/utilities-php/workflows/PHPUnit%20Test/badge.svg" alt="Build Status" /></a>
  <a href="https://coveralls.io/r/utilities-php/router?branch=master"><img src="https://coveralls.io/repos/utilities-php/router/badge.png?branch=master" alt="Coverage Status" /></a> 
  <a href="https://scrutinizer-ci.com/g/utilities-php/utilities-php/?branch=master"><img src="https://img.shields.io/scrutinizer/g/utilities-php/router/master.svg?style=flat" alt="Code Quality" /></a>
  <a href="https://travis-ci.com/utilities-php/utilities-php"><img src="https://travis-ci.com/utilities-php/utilities-php.svg?branch=master" alt="Build Status" /></a>
</p>
<p align="center">
  <a href="https://packagist.org/packages/utilities-php/utilities-php"><img src="https://img.shields.io/packagist/v/utilities-php/utilities-php.svg" alt="Latest Stable Version" /></a>
  <a href="https://packagist.org/packages/utilities-php/utilities-php"><img src="https://img.shields.io/github/languages/code-size/utilities-php/router" alt="Code Size" /></a>
  <a href="https://packagist.org/packages/utilities-php/utilities-php"><img src="https://img.shields.io/packagist/dt/utilities-php/router" alt="Downloads" /></a>
  <a href="https://packagist.org/packages/utilities-php/utilities-php"><img src="https://img.shields.io/github/license/utilities-php/router" alt="License" /></a>
</p>

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

```php
// Todo: Add documentation
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
            'Todo' => Controller::__instance(TodoController::class, '/api/todo'),
            'Users' => Controller::__instance(UsersController::class, '/api/users')
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