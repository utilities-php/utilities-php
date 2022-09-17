<?php
declare(strict_types=1);

namespace UtilitiesTests\Database\Creative;

class UsersMiddleware extends \Utilities\Database\Middleware
{

    /**
     * The table name
     *
     * @var string
     */
    protected static string $TABLE_NAME = 'Users';

    /**
     * The primary of the table
     *
     * @var string
     */
    protected static string $PRIMARY_KEY = 'id';

    /**
     * @param array $columns {name, email, password}
     * @return string|false
     */
    public static function insert(array $columns): string|false
    {
        $res = static::getDatabase()->insert([
            'table' => static::$TABLE_NAME,
            'columns' => $columns,
        ]);

        if ($res === false) {
            return false;
        }

        return static::getDatabase()->lastInsertId();
    }


}