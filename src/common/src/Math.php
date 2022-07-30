<?php
declare(strict_types=1);

namespace Utilities\Common;

use NXP\MathExecutor;

/**
 * Math class
 *
 * @link    https://github.com/utilities-php/common
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/common/blob/master/LICENSE (MIT License)
 */
class Math
{

    /**
     * Solve problem
     *
     * @param string $problem e.g. "2x + 3y = 5"
     * @return string|int|float|null
     */
    public static function solve(string $problem): string|int|null|float
    {
        try {
            return (new MathExecutor())->execute($problem);
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get difference of two numbers in percent
     *
     * @param float $first
     * @param float $new
     * @param int $decimal
     * @return float
     */
    public static function percentageDifference(float $first, float $new, int $decimal = 2): float
    {
        return self::fixedDecimal(($new - $first) / $first * 100, $decimal);
    }

    /**
     * Fixed decimal
     *
     * @param float $number
     * @param float $decimal
     * @return float
     */
    public static function fixedDecimal(float $number, float $decimal = 2): float
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
     * Get percentage of number
     *
     * Example: 10% of 256 is 25.6
     *
     * @param float|int $number
     * @param int $percentage
     * @return float
     */
    public static function percentageOf(float|int $number, int $percentage): float
    {
        return $number * $percentage / 100;
    }

    /**
     * multiply
     *
     * @param float|int $number
     * @param float|int $multiplier
     * @return float
     */
    public static function multiply(float|int $number, float|int $multiplier): float
    {
        return $number * $multiplier;
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