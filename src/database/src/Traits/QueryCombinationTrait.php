<?php
declare(strict_types=1);

namespace Utilities\Database\Traits;

use Utilities\Database\Exceptions\QueryException;

/**
 * QueryCombinationTrait class
 *
 * @link    https://github.com/utilities-php/database
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/database/blob/master/LICENSE (MIT License)
 */
trait QueryCombinationTrait
{

    /**
     * Combine insert columns
     *
     * @param array|string $columns The columns to be inserted, by default it is all columns
     * @return string
     */
    private static function combineColumns(array|string $columns): string
    {
        if (is_array($columns)) {
            $index = 0;
            $combined = "";
            foreach ($columns as $column) {
                $more = count($columns) - 1 == $index ? ' ' : ', ';
                $combined .= "`$column`$more";
                $index++;
            }
            return trim($combined);
        }
        return $columns;
    }

    /**
     * Get value with their types for combination
     *
     * String: from=hello to='hello'
     * Integer: from=1 to=1
     * NULL: from=null to=NULL
     * etc.
     *
     * @param mixed $value The value to be combined
     * @param bool $pdo Whether to use PDO or not
     * @return mixed
     */
    private static function combineValue(mixed $value, bool $pdo = false): mixed
    {
        return match (gettype($value)) {
            'string' => $pdo ? ":WHERE_$value" : "'$value'",
            'integer', 'double' => $pdo ? ":WHERE_$value" : $value,
            'boolean' => $pdo ? ":WHERE_$value" : ($value ? 1 : 0),
            'NULL' => $pdo ? ":WHERE_$value" : 'NULL',
            'array' => self::combineValueArray($value, $pdo),
            default => throw new QueryException("Invalid value type for combination: " . gettype($value)),
        };
    }

    /**
     * Combine where array
     *
     * eg. ['a', 'b', 'c'] => ('a', 'b', 'c')
     *
     * @param array $array The array to be combined
     * @param bool $pdo Whether to use PDO or not
     * @return string
     */
    private static function combineValueArray(array $array, bool $pdo = false): string
    {
        $combined = "";
        foreach ($array as $item) {
            $combined .= self::combineValue($item, $pdo) . ", ";
        }
        return "(" . trim($combined, ", ") . ")";
    }

    /**
     * Combine insert values
     *
     * @param array $columns The columns to be inserted
     * @param bool $pdo If true, it will return PDO values
     * @return string
     */
    private static function combineInsertValues(array $columns, bool $pdo = false): string
    {
        $index = 0;
        $combined = "";
        foreach ($columns as $column => $value) {
            $more = count($columns) - 1 == $index ? ' ' : ', ';
            $combined .= $pdo ? ":COLUMN_$column$more" : self::combineValue($value) . $more;
            $index++;
        }
        return trim($combined);
    }

    /**
     * Combine update columns
     *
     * @param array $columns The columns to be updated
     * @param array $values The values to be updated
     * @param bool $pdo If true, it will return PDO values
     * @return string
     */
    private static function combineUpdateColumns(array $columns, array $values, bool $pdo = false): string
    {
        $index = 0;
        $combined = "";
        foreach ($columns as $column) {
            $more = count($columns) - 1 == $index ? ' ' : ', ';
            $combined .= !$pdo
                ? "`$column` = " . self::combineValue($values[$index]) . $more
                : "`$column` = :UPDATE_$column$more";
            $index++;
        }
        return trim($combined);
    }

    /**
     * Combine where
     *
     * @param array $where The where clause. [{column, operator, value}, ...] or {column => value, column => value}
     * @param bool $pdo If true, it will return PDO values
     * @return string
     */
    private static function combineWhere(array $where, bool $pdo = false): string
    {
        $index = 0;
        $combined = "";
        foreach ($where as $key => $value) {
            $more = count($where) - 1 == $index ? ' ' : ' AND ';
            if (is_array($value) && isset($value['operator'], $value['value'])) {
                if (!isset($value['column'])) {
                    $value['column'] = $key;
                }
                $combined .= !$pdo
                    ? "`{$value['column']}` {$value['operator']} " . self::combineValue($value['value']) . $more
                    : "`{$value['column']}` {$value['operator']} :WHERE_{$value['column']}$more";
            } else {
                $match = match (gettype($value)) {
                    default => $pdo ? "`$key` = :WHERE_$key" : "`$key` = " . self::combineValue($value),
                    'array' => "`$key` IN " . self::combineValueArray($value, $pdo),
                };
                $combined .= $match . $more;
            }
            $index++;
        }
        return trim($combined);
    }

    /**
     * Combine order
     *
     * @param array $order The order clause. [{column, direction}, ...] or {column => direction, column => direction}
     * @return string
     */
    private static function combineOrder(array $order): string
    {
        if (count($order) > 0) {
            $index = 0;
            $combined = "ORDER BY ";
            if (isset($order[0])) {
                foreach ($order as $item) {
                    $more = count($order) - 1 == $index ? ' ' : ', ';
                    $combined .= "`{$item['column']}` {$item['direction']}$more";
                    $index++;
                }
            } else {
                foreach ($order as $column => $direction) {
                    $more = count($order) - 1 == $index ? ' ' : ', ';
                    $combined .= "`$column` $direction$more";
                    $index++;
                }
            }
            return trim($combined);
        }
        return "";
    }

    /**
     * Combine limit
     *
     * @param array $limit The limit clause. [offset, limit]
     * @return string
     */
    private static function combineLimit(array $limit): string
    {
        if (count($limit) > 0) {
            return "LIMIT {$limit[0]}, {$limit[1]}";
        }
        return "";
    }

}