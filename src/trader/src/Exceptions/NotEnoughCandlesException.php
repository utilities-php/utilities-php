<?php
declare(strict_types=1);

namespace Utilities\Trader\Exceptions;

/**
 * NotEnoughCandlesException class
 *
 * @link    https://github.com/utilities-php/trader
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/trader/blob/master/LICENSE (MIT License)
 */
class NotEnoughCandlesException extends \RuntimeException
{

    protected $message = "Not enough candles to calculate the indicator";

}