<?php

namespace Utilities\Database;

/**
 * SQLQueryBuilder class
 *
 * @link    https://github.com/utilities-php/database
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/database/blob/master/LICENSE (MIT License)
 */
class SQLQueryBuilder
{

    /**
     * Convert array to sql insert query
     *
     * @param array $data ["table", "columns"]
     * @return string
     */
    public static function insert(array $data): string
    {
        $i = 0;
        $more = ', ';
        $columns['col'] = $columns['val'] = "";
        foreach ($data['columns'] as $key => $value) {
            if (count($data['columns']) - 1 == $i) $more = ' ';
            $columns['col'] .= "`$key`$more";
            if ($value != null) {
                $value = self::filterQuotation($value);
                $columns['val'] .= "'$value'$more";
            } else {
                $columns['val'] .= "NULL$more";
            }
            $i++;
        }

        return "INSERT INTO `{$data['table']}` (" . trim($columns['col']) . ") VALUES (" . trim($columns['val']) . ")";
    }

    /**
     * This function is used to convert " and ' to &quot; and &apos;
     *
     * @param $value
     * @return string
     */
    public static function filterQuotation($value): string
    {
        return str_replace(["'", '"'], ["&apos;", "&quot;"], $value);
    }

    /**
     * Convert array to sql update query
     *
     * @param array $data ["table", "where", "columns"]
     * @return string
     */
    public static function update(array $data): string
    {
        $i = 0;
        $more = ', ';
        $columns = "";
        foreach ($data['columns'] as $key => $value) {
            if (count($data['columns']) - 1 == $i) $more = ' ';
            if ($value != null) {
                $value = self::filterQuotation($value);
                if (gettype($value) == 'integer' || gettype($value) == 'double') {
                    $columns .= "`$key` = $value$more";
                } else {
                    $columns .= "`$key` = '$value'$more";
                }
            } else {
                $columns .= "`$key` = NULL$more";
            }
            $i++;
        }
        $columns = trim($columns, ', ');
        return "UPDATE `{$data['table']}` SET $columns" . self::fetch_where($data['where'] ?? []);
    }

    /**
     * Key known to be column name and value known to be value
     *
     * @param array|string $data [{"column", "operator", "value"}] or ["column"=> "value", "column"=> "value"]
     * @return string
     */
    private static function fetch_where(array|string $data): string
    {
        $i = 0;
        $more = ' AND ';
        $where = "";
        if (is_string($data)) return " WHERE $data";
        if (isset($data[0]['column'])) {
            foreach ($data as $Key => $value) {
                if (count($data) - 1 == $i) $more = ' ';
                $where .= "`{$value['column']}` {$value['operator']} '{$value['value']}'$more";
                $i++;
            }
        } else {
            foreach ($data as $Key => $value) {
                if (count($data) - 1 == $i) $more = ' ';
                if (gettype($value) == 'integer' || gettype($value) == 'double') {
                    $where .= "`$Key` = $value$more";
                } else {
                    $where .= "`$Key` = '$value'$more";
                }
                $i++;
            }
        }
        if ($where == '') return "";
        return " WHERE " . trim($where);
    }

    /**
     * @param array $data ["table", "where"]
     * @return string
     */
    public static function delete(array $data): string
    {
        return "DELETE FROM `{$data['table']}`" . self::fetch_where($data['where'] ?? []);
    }

    /**
     * @param array $data ["table", "columns", "order","extra", "where"]
     * @return string
     */
    public static function select(array $data): string
    {
        $Columns = self::fetch_columns($data['columns'] ?? []);
        $Order = self::fetch_order($data['order'] ?? []);
        $Where = self::fetch_where($data['where'] ?? []);
        $Extra = self::make_extra($data['extra'] ?? []);

        $more = $Where . $Order . $Extra;

        return "SELECT " . $Columns . " FROM `{$data['table']}`" . $more;
    }

    /**
     * Convert array to columns
     *
     * @param array $data [key, key, ...] or [key => value, key => value, ...]
     * @return string
     */
    private static function fetch_columns(array $data): string
    {
        $i = 0;
        $more = ', ';
        $str = "";

        if ($data == [] || empty($data)) {
            return "*";
        }

        if (array_keys($data) === range(0, count($data) - 1)) {

            foreach ($data as $value) {
                if (count($data) - 1 == $i) $more = ' ';
                $str .= "`$value`$more";
                $i++;
            }

        } else {

            foreach ($data as $key => $value) {
                if (count($data) - 1 == $i) $more = ' ';
                $str .= "`$key`$more";
                $i++;
            }

        }

        return trim($str);
    }

    /**
     * Convert array of order to string
     *
     * @param array $data e.g. [{"column", "order"}] or ["column"=> "order"]
     * @return string
     */
    private static function fetch_order(array $data): string
    {
        $i = 0;
        $more = ', ';
        $str = "";
        if ($data == [] || empty($data)) {
            return "";
        }
        if (array_keys($data) === range(0, count($data) - 1)) {
            foreach ($data as $value) {
                if (count($data) - 1 == $i) $more = ' ';
                $str .= "`{$value['column']}` {$value['order']}$more";
                $i++;
            }
        } else {
            foreach ($data as $key => $value) {
                if (count($data) - 1 == $i) $more = ' ';
                $str .= "`$key` $value$more";
                $i++;
            }
        }
        return trim($str);
    }

    /**
     * @param array|string $data
     * @return string
     */
    private static function make_extra(array|string $data): string
    {
        $result = "";
        if (empty($data)) return '';
        if (is_string($data)) return ' ' . $data;
        foreach ($data as $value) {
            $result .= " $value";
        }
        return $result;
    }

}