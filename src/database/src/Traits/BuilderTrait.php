<?php
declare(strict_types=1);

namespace Utilities\Database\Traits;

use Utilities\Database\Exceptions\QueryException;

/**
 * BuilderTrait class
 *
 * @link    https://github.com/utilities-php/database
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/database/blob/master/LICENSE (MIT License)
 */
trait BuilderTrait
{

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
                throw new QueryException(sprintf(
                    "Column '%s' is not set", $column
                ));
            }
        }
    }

}