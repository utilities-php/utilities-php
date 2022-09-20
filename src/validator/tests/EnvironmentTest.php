<?php
declare(strict_types=1);

namespace UtilitiesTests\Validator;

class EnvironmentTest
{

    public static function load(): void
    {
        $dotenv = \Dotenv\Dotenv::createImmutable(self::rootPath());
        $dotenv->load();
    }

    private static function rootPath(): string
    {
        return $_SERVER['DOCUMENT_ROOT'] ?? $_SERVER['PWD'];
    }

}