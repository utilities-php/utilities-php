<?php
declare(strict_types=1);

namespace UtilitiesTests\Database;

use Utilities\Common\UUID;
use Utilities\Database\DB;

class ConnectionTest extends \PHPUnit\Framework\TestCase
{

    public function test_connect_to_remote(): void
    {
        $db = DatabaseTest::connect();

        $this->assertInstanceOf(DB::class, $db);
        $this->assertInstanceOf(\PDO::class, $db->getConnection());
        $this->assertIsString($db->getServerVersion());
    }

    public function test_create_secret()
    {
        $db = DatabaseTest::connect();

        `rm -rf {$_SERVER['PWD']}/.database`;
        $secret = $_ENV['DATABASE_SECRET_KEY'] ?? UUID::generate();
        $this->assertTrue($db->saveConnection($secret));

        $fileId = DB::getFileId($secret);
        $this->assertFileExists("{$_SERVER['PWD']}/.database/{$fileId}");
    }

    public function test_connect_with_secret(): void
    {
        DatabaseTest::loadENV();
        $secret = $_ENV['DATABASE_SECRET_KEY'];
        $db = DB::connectWithSecret($secret);

        $this->assertInstanceOf(DB::class, $db);
        $this->assertInstanceOf(\PDO::class, $db->getConnection());
        $this->assertIsString($db->getServerVersion());
    }

    public function test_connection_without_manual_env(): void
    {
        $db = new DB();

        $this->assertInstanceOf(DB::class, $db);
        $this->assertInstanceOf(\PDO::class, $db->getConnection());
        $this->assertIsString($db->getServerVersion());
    }

}