<?php
declare(strict_types=1);

namespace Utilities\Database\Traits;

/**
 * ValueExtractionTrait class
 *
 * @link    https://github.com/utilities-php/database
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/database/blob/master/LICENSE (MIT License)
 */
trait ValueExtractionTrait
{

    /**
     * Extract the column values from where clause
     *
     * @param array $where
     * @return array
     */
    private function extractWhereValues(array $where): array
    {
        $values = [];

        foreach ($where as $key => $value) {
            if (is_array($value)) {
                if (isset($value['operator'], $value['value'])) {
                    if (!isset($value['column'])) {
                        $value['column'] = $key;
                    }

                    if (is_array($value['value'])){
                        foreach ($this->extractAssocArray('WHERE', $value['value']) as $k => $v) {
                            $values[$k] = $v;
                        }
                        continue;
                    }

                    $values["WHERE_$key"] = $value['value'];
                    continue;
                }

                foreach ($value as $k) {
                    $values["WHERE_$k"] = $k;
                }

            } else {
                $values["WHERE_$key"] = $value;
            }
        }

        return $values;
    }

    /**
     * Extract a assoc array from given array
     *
     * @param string $prefix
     * @param array $array
     * @return array
     */
    private function extractAssocArray(string $prefix, array $array): array
    {
        $values = [];

        foreach ($array as $key => $value) {
            if (is_numeric($key)){
                $key = $value;
            }

            $values["{$prefix}_$key"] = $value;
        }

        return $values;
    }

    /**
     * Extract the column values from where clause
     *
     * @param array $columns
     * @return array
     */
    private function extractColumnValues(array $columns): array
    {
        $values = [];

        foreach ($columns as $key => $value) {
            $values["COLUMN_$key"] = $value;
        }

        return $values;
    }

    /**
     * Extract the update values from where clause
     *
     * @param array $update_columns
     * @return array
     */
    private function extractUpdateValues(array $update_columns): array
    {
        $values = [];

        foreach ($update_columns as $key => $value) {
            $values["UPDATE_$key"] = $value;
        }

        return $values;
    }

    /**
     * Extract the values from the given data
     *
     * @param array $data {column, where, update}
     * @return array
     */
    private function extractValues(array $data): array
    {
        return array_merge(
            $this->extractWhereValues($data['where'] ?? []),
            $this->extractColumnValues($data['columns'] ?? []),
            $this->extractUpdateValues($data['update'] ?? []),
        );
    }

}