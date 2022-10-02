<?php declare(strict_types=1);

namespace UtilitiesTests\Common;

use UtilitiesTests\Common\Creative\UserEntity;
use UtilitiesTests\Common\Creative\UserTypeStrictEntity;

class EntityTest extends \PHPUnit\Framework\TestCase
{

    public function testEntity()
    {
        $user = new UserEntity();

        $user->setFirstName('Shahrad');
        $user->setLastName('Elahi');
        $user->setAge(30);

        $this->assertEquals('Shahrad', $user->getFirstName());
        $this->assertEquals('Elahi', $user->getLastName());
        $this->assertEquals(30, $user->getAge());
    }

    public function testEntityAssoc()
    {
        $user = new UserEntity();

        $user->setFirstName('Shahrad');
        $user->setLastName('Elahi');
        $user->setAge(30);

        $this->assertEquals('Shahrad', $user->getFirstName());
        $this->assertEquals('Elahi', $user->getLastName());
        $this->assertEquals(30, $user->getAge());
    }

    public function testEntityAssocStorage()
    {
        $user = new UserEntity([
            'firstName' => 'Shahrad',
            'lastName' => 'Elahi',
            'age' => 30
        ]);

        $this->assertEquals('Shahrad', $user->getFirstName());
        $this->assertEquals('Elahi', $user->getLastName());
        $this->assertEquals(30, $user->getAge());
    }

    public function testMultiGet()
    {
        $user = new UserEntity([
            'firstName' => 'Shahrad',
            'lastName' => 'Elahi',
            'age' => 30
        ]);

        $this->assertEquals([
            'firstName' => 'Shahrad',
            'lastName' => 'Elahi',
            'age' => 30
        ], $user->get([
            'firstName',
            'lastName',
            'age'
        ]));
    }

    public function testMultiSet()
    {
        $user = new UserEntity([
            'firstName' => 'Shahrad',
            'lastName' => 'Elahi',
            'age' => 30
        ]);

        $user->set([
            'firstName' => 'Shahrad',
            'lastName' => 'Elahi',
            'age' => 30
        ]);

        $this->assertEquals([
            'firstName' => 'Shahrad',
            'lastName' => 'Elahi',
            'age' => 30
        ], $user->get([
            'firstName',
            'lastName',
            'age'
        ]));
    }

    public function testType()
    {
        $user = new UserEntity([
            'firstName' => 'Shahrad',
            'lastName' => 'Elahi',
            'age' => 30
        ]);

        $this->assertEquals('string', $user->type('firstName'));
        $this->assertEquals('string', $user->type('lastName'));
        $this->assertEquals('integer', $user->type('age'));
    }

    public function testValidate()
    {
        $user = new UserTypeStrictEntity([
            'firstName' => 'Shahrad',
            'lastName' => 'Elahi',
            'email' => 'shahrad@litehex.com',
            'age' => 30
        ]);

        $this->assertEquals('Shahrad', $user->getFirstName());
        $this->assertEquals('Elahi', $user->getLastName());
        $this->assertEquals(30, $user->getAge());

        $this->assertTrue($user->validate()->isValid());

        $user->setAge(10);
        $this->assertFalse($user->validate()->isValid());
    }

}