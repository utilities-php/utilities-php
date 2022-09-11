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
     * The table name
     *
     * @var string
     */
    protected static string $TABLE_NAME;

    /**
     * The primary of the table
     *
     * @var string
     */
    protected static string $PRIMARY_KEY;

    /**
     * The database secret
     *
     * @var string
     */
    protected static string $DATABASE_SECRET;

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
        if (isset($data['table'])){
            throw new \RuntimeException('Table name is not allowed in data');
        }

        $data['columns'] = $data['columns'] ?? '*';
        $data['where'] = $data['where'] ?? [];
        $data['order'] = $data['order'] ?? [];
        $data['limit'] = $data['limit'] ?? null;
        $data['offset'] = $data['offset'] ?? null;

        return self::getDatabase()->select([
            'table' => static::$TABLE_NAME,
            ...$data,
        ]);
    }

    /**
     * Insert data
     *
     * @param array $columns
     * @return bool
     */
    public static function insert(array $columns): bool
    {
        return self::getDatabase()->insert([
            'table' => static::$TABLE_NAME,
            'columns' => $columns
        ]);
    }

    /**
     * Update data
     *
     * @param array $columns
     * @param array $where
     * @return bool
     */
    public static function update(array $columns, array $where): bool
    {
        return self::getDatabase()->update([
            'table' => static::$TABLE_NAME,
            'columns' => $columns,
            'where' => $where
        ]);
    }

    /**
     * Upsert data
     *
     * @param array $columns
     * @param array $where
     * @return bool
     */
    public static function upsert(array $columns, array $where): bool
    {
        return self::getDatabase()->upsert([
            'table' => static::$TABLE_NAME,
            'columns' => $columns,
            'where' => $where
        ]);
    }

    /**
     * Delete data
     *
     * @param array $where
     * @return bool
     */
    public static function delete(array $where): bool
    {
        return self::getDatabase()->delete([
            'table' => static::$TABLE_NAME,
            'where' => $where
        ]);
    }

}