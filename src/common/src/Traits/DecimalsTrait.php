<?php
declare(strict_types=1);

namespace Utilities\Common\Traits;

/**
 * NumberTrait class
 *
 * @link    https://github.com/utilities-php/common
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/common/blob/master/LICENSE (MIT License)
 */
trait DecimalsTrait
{

    /**
     * Fixed decimal
     *
     * @param float $number
     * @param int $decimal
     * @return float
     */
    public static function fixedDecimal(float $number, int $decimal = 2): float
    {
        return (float)number_format($number, $decimal, '.', '');
    }

    /**
     * Decimals Count
     *
     * @param float $number
     * @return int
     */
    public static function decimalsCount(float $number): int
    {
        $parts = explode('.', (string)$number);
        return count($parts) > 1 ? strlen($parts[1]) : 0;
    }

    /**
     * Round up
     *
     * @param float $number
     * @param int $decimal
     * @return float
     */
    public static function roundUp(float $number, int $decimal = 2): float
    {
        return self::fixedDecimal(ceil($number * pow(10, $decimal)) / pow(10, $decimal), $decimal);
    }

    /**
     * Max Decimals
     *
     * For example 0.00123456 has 6 decimals, and we want round it to 4 decimals,
     * so we use this function and return 0.00123
     *
     * @param float $number
     * @param int $decimal
     * @return float
     */
    public static function maxDecimals(float $number, int $decimal = 2): float
    {
        $parts = explode('.', (string)$number);
        return count($parts) > 1 ? self::fixedDecimal((float)($parts[0] . '.' . substr($parts[1], 0, $decimal)), $decimal) : $number;
    }

    /**
     * Non zero decimal count
     *
     * @param float $number
     * @return int
     */
    public static function nonZeroDecimalCount(float $number): int
    {
        $parts = explode('.', (string)$number);

        if (count($parts) > 1) {
            $decimals = strlen($parts[1]);
            $count = 0;

            for ($i = 0; $i < $decimals; $i++) {
                if ($parts[1][$i] != '0') {
                    $count += 1;
                }
            }

        }

        return $count ?? 0;
    }


}