<?php

namespace Utilities\Database;

/**
 * Collector class
 *
 * @link    https://github.com/utilities-php/database
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/database/blob/master/LICENSE (MIT License)
 */
class Collector
{

    /**
     * @var string
     */
    protected static string $table_name;

    /**
     * @var string
     */
    protected static string $primary_key;

    /**
     * @var string
     */
    protected static string $database_secret;

    /**
     * @var ?DB
     */
    private static ?DB $database = null;

    /**
     * Get Database
     *
     * @return DB
     */
    protected static function getDatabase(): DB
    {
        if (static::$database === null) {
            static::$database = new DB(
                static::$database_secret
            );
        }

        return static::$database;
    }

    /**
     * Find data with specified column and value
     *
     * @param string $column
     * @param mixed $value
     * @return array|bool
     */
    public static function where(string $column, mixed $value): array|bool
    {
        return self::get([$column => $value])[0] ?? false;
    }

    /**
     * Get rows with specified conditions
     *
     * @param array|string $conditions e.g. [{'column' => 'value'}, ...] or single line string
     * @return array
     */
    public static function get(array|string $conditions): array
    {
        return self::getDatabase()->select([
            'table' => static::$table_name,
            'where' => $conditions
        ]);
    }

    /**
     * Update data
     *
     * @param mixed $primary The primary key of the row to update
     * @param array $data The data to update
     * @return bool
     */
    public static function update(mixed $primary, array $data): bool
    {
        if (static::$primary_key === '') {
            throw new \RuntimeException("You must set the primary key of the table");
        }

        return self::getDatabase()->update([
            'table' => static::$table_name,
            'where' => [static::$primary_key => $primary],
            'columns' => $data
        ]);
    }

    /**
     * Delete data
     *
     * @param array $conditions e.g. [{'column' => 'value'}, ...]
     * @return bool
     */
    public static function delete(array $conditions): bool
    {
        if (static::$primary_key === '') {
            return false;
        }

        return self::getDatabase()->delete([
            'table' => static::$table_name,
            'where' => $conditions
        ]);
    }

    /**
     * Insert data
     *
     * @param array $data The data to insert
     * @return mixed
     */
    public static function insert(array $data): mixed
    {
        return self::getDatabase()->insert([
            'table' => static::$table_name,
            'columns' => $data
        ]);
    }

    /**
     * Check data does exist
     *
     * @param array $conditions e.g. [{'column' => 'value'}, ...]
     * @return bool
     */
    public static function exists(array $conditions): bool
    {
        return self::get($conditions)[0] !== false;
    }

}