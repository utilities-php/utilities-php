<?php declare(strict_types=1);

namespace UtilitiesTests\Common;

use UtilitiesTests\Common\Creative\UserModel;

class ModelTest extends \PHPUnit\Framework\TestCase
{

    public function testModel()
    {
        $user = new UserModel();

        $user->setFirstName('Shahrad');
        $user->setLastName('Elahi');
        $user->setAge(30);

        $this->assertEquals('Shahrad', $user->getFirstName());
        $this->assertEquals('Elahi', $user->getLastName());
        $this->assertEquals(30, $user->getAge());
    }

    public function testModelAssoc()
    {
        $user = new UserModel();

        $user->setFirstName('Shahrad');
        $user->setLastName('Elahi');
        $user->setAge(30);

        $this->assertEquals('Shahrad', $user->getFirstName());
        $this->assertEquals('Elahi', $user->getLastName());
        $this->assertEquals(30, $user->getAge());
    }

    public function testModelAssocStorage()
    {
        $user = new UserModel([
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
        $user = new UserModel([
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

}