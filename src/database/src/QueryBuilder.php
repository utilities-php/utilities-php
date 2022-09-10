<?php
declare(strict_types=1);

namespace Utilities\Database;

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
        self::checkDataIsset($data, ['table', 'columns', 'where']);
        $columns = self::combineUpdateColumns(array_keys($data['columns']), array_values($data['columns']), $pdo);
        $where = self::combineWhere($data['where'], $pdo);
        return trim("UPDATE `{$data['table']}` SET $columns WHERE $where");
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
        $where = self::combineWhere($data['where'], $pdo);
        return trim("DELETE FROM `{$data['table']}` WHERE $where");
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
        self::checkDataIsset($data, ['table', 'columns']);
        $columns = self::combineColumns($data['columns']);
        $where = self::combineWhere($data['where'], $pdo);
        $order = self::combineOrder($data['order'] ?? []);
        $limit = self::combineLimit($data['limit'] ?? []);
        return trim("SELECT $columns FROM `{$data['table']}` WHERE $where $order $limit");
    }

}