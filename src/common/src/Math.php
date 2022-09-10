<?php
declare(strict_types=1);

namespace Utilities\Common;

use Exception;
use NXP\MathExecutor;
use RuntimeException;
use Utilities\Common\Traits\DecimalsTrait;

/**
 * Math class
 *
 * @link    https://github.com/utilities-php/common
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/common/blob/master/LICENSE (MIT License)
 */
class Math
{

    use DecimalsTrait;

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
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
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
     * Exponentiation
     *
     * @param int $base
     * @param int $exponent
     * @return int
     */
    public static function exponentiation(int $base, int $exponent): int
    {
        return $exponent === 0 ? 1 : $base * self::exponentiation($base, $exponent - 1);
    }

    /**
     * is prime
     *
     * @param int $number
     * @return bool
     */
    public static function isPrime(int $number): bool
    {
        if ($number < 2) {
            return false;
        }
        if ($number === 2) {
            return true;
        }
        if ($number % 2 === 0) {
            return false;
        }
        for ($i = 3; $i <= (int)sqrt($number); $i += 2) {
            if ($number % $i === 0) {
                return false;
            }
        }
        return true;
    }

    /**
     * Is even
     *
     * @param int $number
     * @return bool
     */
    public static function isEven(int $number): bool
    {
        return $number % 2 == 0;
    }

    /**
     * Is odd
     *
     * @param int $number
     * @return bool
     */
    public static function isOdd(int $number): bool
    {
        return !self::isEven($number);
    }

}