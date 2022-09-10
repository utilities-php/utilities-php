<?php
declare(strict_types=1);

namespace UtilitiesTests\Router;

use PHPUnit\Framework\TestCase;
use Utilities\Auth\Session;

class SessionTest extends TestCase
{

    public function test_session(): void
    {
        Session::start();
        Session::set('Test', 'hi');

        $this->assertEquals(Session::get('Test'), 'hi');
    }

}