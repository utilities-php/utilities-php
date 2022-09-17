<?php
declare(strict_types=1);

namespace UtilitiesTests\Database;

use Dotenv\Dotenv;
use Utilities\Database\DB;

class DatabaseTest
{

    public static function loadENV(): void
    {
        $dotenv = Dotenv::createImmutable($_SERVER['PWD']);
        if (file_exists($_SERVER['PWD'] . '/.env')) {
            $dotenv->load();
        }
    }

    public static function connect(): DB
    {
        self::loadENV();
        return DB::connect([
            'db' => $_ENV['DATABASE_NAME'],
            'host' => $_ENV['DATABASE_HOST'],
            'user' => $_ENV['DATABASE_USERNAME'],
            'pass' => $_ENV['DATABASE_PASSWORD'],
        ]);
    }

}