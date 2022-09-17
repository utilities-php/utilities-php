<?php
declare(strict_types=1);

namespace UtilitiesTests\Database;

use Faker\Factory;
use Utilities\Common\Common;
use Utilities\Common\Time;

class QueryTest extends \PHPUnit\Framework\TestCase
{

    public function test_create_user(): void
    {
        $db = DatabaseTest::connect();
        $fk = Factory::create();

        $name = $fk->name;
        $email = $fk->email;
        $password = Common::randomString(10);

        $db->insert([
            'table' => 'Users',
            'columns' => [
                'name' => $name,
                'email' => $email,
                'password' => $password,
            ],
        ]);

        $user = $db->select([
            'table' => 'Users',
            'where' => [
                'email' => $email,
            ],
        ]);

        $this->assertIsArray($user[0]);
        $this->assertEquals($name, $user[0]['name']);
        $this->assertEquals($email, $user[0]['email']);
    }

    public function test_clear_table(): void
    {
        $db = DatabaseTest::connect();
        $fk = Factory::create();

        $name = $fk->name;
        $email = $fk->email;
        $password = Common::randomString(10);

        $db->insert([
            'table' => 'Users',
            'columns' => [
                'name' => $name,
                'email' => $email,
                'password' => $password,
            ],
        ]);

        $db->delete([
            'table' => 'Users',
            'where' => '*'
        ]);

        $user = $db->select([
            'table' => 'Users',
            'where' => [
                'email' => $email,
            ],
        ]);

        $this->assertEmpty($user);
    }

}