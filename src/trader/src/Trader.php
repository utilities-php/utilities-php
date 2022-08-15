<?php

namespace Utilities\Trader;

use Utilities\Common\Math;

/**
 * Trader Class
 *
 * @link    https://github.com/utilities-php/trader
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/trader/blob/master/LICENSE (MIT License)
 * @version v1.0
 */
class Trader
{

    /**
     * This function calculates the profit and loss of a trade.
     *
     * @param string $direction ["LONG" | "SHORT"]
     * @param int|float $volume
     * @param int|float $entry
     * @param int|float $close
     * @return float
     */
    public static function CalculatePL(string $direction, int|float $volume, int|float $entry, int|float $close): float
    {
        $difference = Math::percentageDifference($entry, $close);
        $balanceChange = (($volume / 100) * $difference);
        if ($direction == "SHORT") $balanceChange *= -1;
        return $balanceChange;
    }

}