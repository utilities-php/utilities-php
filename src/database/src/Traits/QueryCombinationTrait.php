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
     * @return mixed
     */
    private static function combineValue(mixed $value): mixed
    {
        return match (gettype($value)) {
            'string' => "'$value'",
            'integer', 'double' => $value,
            'boolean' => $value ? 1 : 0,
            'NULL' => 'NULL',
            default => throw new QueryException("Invalid value type for combination: " . gettype($value)),
        };
    }

    /**
     * Combine insert values
     *
     * @param array $values The values to be inserted, by default it is all values
     * @param bool $pdo If true, it will return PDO values
     * @return string
     */
    private static function combineInsertValues(array $values, bool $pdo = false): string
    {
        $index = 0;
        $combined = "";
        foreach ($values as $value) {
            $more = count($values) - 1 == $index ? ' ' : ', ';
            $combined .= !$pdo
                ? self::combineValue($value) . $more
                : "':$value'";
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
                : "`$column` = :$column$more";
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
        if (isset($where[0])) {
            foreach ($where as $item) {
                $more = count($where) - 1 == $index ? ' ' : ' AND ';
                $combined .= !$pdo
                    ? "`{$item['column']}` {$item['operator']} " . self::combineValue($item['value']) . $more
                    : "`{$item['column']}` {$item['operator']} :{$item['column']}$more";
                $index++;
            }
        } else {
            foreach ($where as $column => $value) {
                $more = count($where) - 1 == $index ? ' ' : ' AND ';
                $combined .= !$pdo
                    ? "`$column` = " . self::combineValue($value) . $more
                    : "`$column` = :$column$more";
                $index++;
            }
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

    /**
     * Filter quotation. convert " and ' to &quot; and &#039;
     *
     * @param mixed $value The value to be filtered
     * @return mixed
     */
    private static function filterQuotation(mixed $value): mixed
    {
        if (is_string($value)) {
            return str_replace(["'", '"'], ["&#039;", "&quot;"], $value);
        }

        return $value;
    }

    /**
     * Check the data contains with given columns, On the false, it will throw an exception
     *
     * @param array $data The data to be checked
     * @param array $columns The columns to be used
     * @return void
     */
    private static function checkDataIsset(array $data, array $columns): void
    {
        foreach ($columns as $column) {
            if (!isset($data[$column])) {
                throw new QueryException(sprintf("Column '%s' is not set", $column));
            }
        }
    }

}