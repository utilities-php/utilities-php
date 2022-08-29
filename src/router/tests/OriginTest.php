<?php
declare(strict_types=1);

namespace UtilitiesTests\Router;

use Utilities\Router\Origin;

class OriginTest extends \PHPUnit\Framework\TestCase
{

    public function test_is_valid()
    {
        RouterTest::setRequest([
            'HTTP_ORIGIN' => 'subdomain.example.com',
            'HTTP_HOST' => 'example.com',
            'SERVER_PORT' => '443',
            'REMOTE_ADDR' => '31.187.72.206',
        ]);

        Origin::addDomain('*.example.com', true);
        $this->assertTrue(Origin::validate());

        Origin::removeDomain('*.example.com');
        Origin::addIp('31.187.72.206');
        $this->assertTrue(Origin::validate());

        RouterTest::setRequest([
            'HTTP_ORIGIN' => 'http://localhost:3000',
        ]);
        Origin::addDomain('localhost', true);
        $this->assertTrue(Origin::validate());
    }

    public function test_not_valid()
    {
        RouterTest::setRequest([
            'HTTP_ORIGIN' => 'subdomain.examplelitehex.com',
            'HTTP_HOST' => 'example.com',
            'SERVER_PORT' => '443',
            'REMOTE_ADDR' => '68.45.72.206',
        ]);

        Origin::addDomain('*.litehex.com', true, 86400);
        $this->assertFalse(Origin::validate());

        Origin::removeDomain('example.com');
        Origin::addIp('8.8.8.8');
        $this->assertFalse(Origin::validate());
    }

    public function test_regex()
    {
        $res = preg_match_all('/^.*\.litehex\.com$/', 'subdomain.examplelitehex.com', $matches) > 0;
        $this->assertFalse($res);
    }

}
