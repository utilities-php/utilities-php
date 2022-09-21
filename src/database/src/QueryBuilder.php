<?php
declare(strict_types=1);

namespace Utilities\Database;

use Utilities\Database\Exceptions\QueryException;
use Utilities\Database\Traits\BuilderTrait;
use Utilities\Database\Traits\QueryCombinationTrait;

/**
 * QueryBuilder class
 *
 * @link    https://github.com/utilities-php/database
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/database/blob/master/LICENSE (MIT License)
 */
class QueryBuilder
{

    use QueryCombinationTrait;
    use BuilderTrait;

    /**
     * Convert array to sql insert query
     *
     * @param array $data {table, columns}
     * @param bool $pdo If true, it will return a PDO template to be used in prepared statements
     * @return string
     */
    public static function insert(array $data, bool $pdo = false): string
    {
        self::checkDataIsset($data, ['table', 'columns']);
        $columns = self::combineColumns(array_keys($data['columns']));
        $values = self::combineInsertValues($data['columns'], $pdo);
        return trim("INSERT INTO `{$data['table']}` ($columns) VALUES ($values)");
    }

    /**
     * Convert array to sql update query
     *
     * @param array $data {table, columns, where}
     * @param bool $pdo If true, it will return a PDO template to be used in prepared statements
     * @return string
     */
    public static function update(array $data, bool $pdo = false): string
    {
        self::checkDataIsset($data, ['table', 'columns']);
        $columns = self::combineUpdateColumns(array_keys($data['columns']), array_values($data['columns']), $pdo);
        $where = self::combineWhere($data['where'] ?? [], $pdo);
        return trim("UPDATE `{$data['table']}` SET $columns$where");
    }

    /**
     * Convert array to sql delete query
     *
     * @param array $data {table, where}
     * @param bool $pdo If true, it will return a PDO template to be used in prepared statements
     * @return string
     */
    public static function delete(array $data, bool $pdo = false): string
    {
        self::checkDataIsset($data, ['table', 'where']);

        if ($data['where'] === '*') {
            return trim("DELETE FROM `{$data['table']}`");
        }

        if (!is_array($data['where'])) {
            throw new QueryException(sprintf(
                "Invalid where clause for delete query: %s",
                json_encode($data)
            ));
        }

        $where = self::combineWhere($data['where'] ?? [], $pdo);

        return trim("DELETE FROM `{$data['table']}`$where");
    }

    /**
     * Convert array to sql select query
     *
     * @param array $data {table, columns, where, order, limit}
     * @param bool $pdo If true, it will return a PDO template to be used in prepared statements
     * @return string
     */
    public static function select(array $data, bool $pdo = false): string
    {
        self::checkDataIsset($data, ['table']);
        $columns = self::combineColumns($data['columns'] ?? '*');
        $where = self::combineWhere($data['where'] ?? [], $pdo);
        $order = self::combineOrder($data['order'] ?? []);
        $limit = self::combineLimit($data['limit'] ?? []);
        return trim("SELECT $columns FROM `{$data['table']}`$where$order$limit");
    }

    /**
     * Convert array to sql insert or update query (upsert)
     *
     * @param array $data {table, columns, update}
     * @param bool $pdo If true, it will return a PDO template to be used in prepared statements
     * @return string
     */
    public static function upsert(array $data, bool $pdo = false): string
    {
        self::checkDataIsset($data, ['table', 'columns', 'update']);
        $columns = self::combineColumns(array_keys($data['columns']));
        $values = self::combineInsertValues($data['columns'], $pdo);
        $update = self::combineUpdateColumns(array_keys($data['update']), array_values($data['update']), $pdo);
        return trim("INSERT INTO `{$data['table']}` ($columns) VALUES ($values) ON DUPLICATE KEY UPDATE $update");
    }

}