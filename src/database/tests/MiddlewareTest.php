<?php
declare(strict_types=1);

namespace UtilitiesTests\Database;

use Faker\Factory;
use Utilities\Common\Common;
use UtilitiesTests\Database\Creative\UsersMiddleware;

class MiddlewareTest extends \PHPUnit\Framework\TestCase
{

    public function test_insert(): void
    {
        DatabaseTest::loadENV();
        $fk = Factory::create();

        $name = $fk->name;
        $email = $fk->email;
        $password = Common::randomString();

        $res = UsersMiddleware::insert([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        $this->assertNotFalse($res);
    }

    public function test_update(): void
    {
        DatabaseTest::loadENV();
        $fk = Factory::create();

        $name = $fk->name;
        $email = $fk->email;
        $password = Common::randomString();

        $id = UsersMiddleware::insert([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        $this->assertNotFalse($id);

        $res = UsersMiddleware::update($id, [
            'password' => Common::randomString()
        ]);

        $this->assertTrue($res);
    }

    public function test_delete(): void
    {
        DatabaseTest::loadENV();
        $fk = Factory::create();

        $name = $fk->name;
        $email = $fk->email;
        $password = Common::randomString();

        $id = UsersMiddleware::insert([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        $this->assertNotFalse($id);

        $res = UsersMiddleware::delete([
            'id' => $id
        ]);

        $this->assertTrue($res);
        $this->assertFalse(UsersMiddleware::where('id', $id));
    }

    public function test_select(): void
    {
        DatabaseTest::loadENV();
        $fk = Factory::create();

        $name = $fk->name;
        $email = $fk->email;
        $password = Common::randomString();

        $id = UsersMiddleware::insert([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        $this->assertNotFalse($id);

        $res = UsersMiddleware::get([
            'where' => [
                'id' => $id
            ]
        ]);

        $this->assertIsArray($res);
        $this->assertEquals($name, $res[0]['name']);
        $this->assertEquals($email, $res[0]['email']);
    }

    public function test_exists(): void
    {
        DatabaseTest::loadENV();
        $fk = Factory::create();

        $name = $fk->name;
        $email = $fk->email;
        $password = Common::randomString();

        $id = UsersMiddleware::insert([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        $this->assertNotFalse($id);

        $res = UsersMiddleware::exists([
            'id' => $id
        ]);

        $this->assertTrue($res);
    }

}