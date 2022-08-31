<?php
declare(strict_types=1);

namespace UtilitiesTests\Router;

use Utilities\Router\Session;

class SessionTest extends \PHPUnit\Framework\TestCase
{

    public function test_session(): void
    {
        Session::start();
        Session::set('Test', 'hi');

        $this->assertEquals(Session::get('Test'), 'hi');
    }

}