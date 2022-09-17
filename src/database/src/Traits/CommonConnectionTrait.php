<?php
declare(strict_types=1);

namespace Utilities\Database\Traits;

/**
 * CommonConnectionTrait class
 *
 * @link    https://github.com/utilities-php/database
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/database/blob/master/LICENSE (MIT License)
 */
trait CommonConnectionTrait
{

    /**
     * Find data with specified column and value
     *
     * @param string $column
     * @param mixed $value
     * @return array|bool
     */
    public static function where(string $column, mixed $value): array|bool
    {
        return self::get(['where' => [$column => $value]])[0] ?? false;
    }

    /**
     * Get all data
     *
     * @return array
     */
    public static function all(): array
    {
        return self::get();
    }

    /**
     * Get data
     *
     * @param array $data [optional] {columns, where, order, limit, offset}
     * @return array
     */
    public static function get(array $data): array
    {
        if (isset($data['table'])) {
            throw new \RuntimeException('Table name is not allowed in data');
        }

        return self::getDatabase()->select([
            'table' => static::$TABLE_NAME,
            ...$data,
        ]);
    }

    /**
     * Insert data
     *
     * @param array $columns
     * @return mixed
     */
    public static function insert(array $columns): mixed
    {
        return self::getDatabase()->insert([
            'table' => static::$TABLE_NAME,
            'columns' => $columns
        ]);
    }

    /**
     * Update data
     *
     * @param mixed $primary
     * @param array $columns
     * @return mixed
     */
    public static function update(mixed $primary, array $columns): mixed
    {
        return self::getDatabase()->update([
            'table' => static::$TABLE_NAME,
            'columns' => $columns,
            'where' => [static::$PRIMARY_KEY => $primary]
        ]);
    }

    /**
     * Upsert data
     *
     * @param mixed $primary
     * @param array $columns
     * @return mixed
     */
    public static function upsert(mixed $primary, array $columns): mixed
    {
        return self::getDatabase()->upsert([
            'table' => static::$TABLE_NAME,
            'columns' => $columns,
            'where' => [static::$PRIMARY_KEY => $primary]
        ]);
    }

    /**
     * Delete data
     *
     * @param array $where
     * @return mixed
     */
    public static function delete(array $where): mixed
    {
        return self::getDatabase()->delete([
            'table' => static::$TABLE_NAME,
            'where' => $where
        ]);
    }

    /**
     * Exists data
     *
     * @param array $where
     * @return bool
     */
    public static function exists(array $where): bool
    {
        return self::getDatabase()->exists([
            'table' => static::$TABLE_NAME,
            'where' => $where
        ]);
    }

}