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
     * @param string $direction ["LONG" | "SHORT"]
     * @param int $volume
     * @param float $entry
     * @param float $close
     * @return float
     */
    public static function CalculatePNL(string $direction, int $volume, float $entry, float $close): float
    {
        $difference = Math::percentageDifference($entry, $close);
        $balanceChange = (($volume / 100) * $difference);
        if ($direction == "SHORT") $balanceChange *= -1;
        return $balanceChange;
    }

}