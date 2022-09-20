<?php
declare(strict_types=1);

namespace Utilities\Database\Constraints;

use Utilities\Common\Time;

/**
 * The hasTimestamps trait.
 *
 *
 * This is part of the Utilities package.
 *
 * @link https://github.com/utilities-php/utilities-php
 * @author Shahrad Elahi <shahrad@litehex.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
trait hasTimestamps
{

    /**
     * Timestamps format
     *
     * @var string 'Y-m-d H:i:s' | 'milliseconds' | 'seconds'
     */
    protected static string $TIMESTAMP_FORMAT = 'milliseconds';

    /**
     * Valid timestamp formats
     *
     * @var array
     */
    private static array $TIMESTAMP_FORMATS = ['Y-m-d H:i:s', 'milliseconds', 'seconds'];

    /**
     * Get the current timestamp in their format
     *
     * @return string
     */
    private function __getTimestamp(): string
    {
        return match (self::$TIMESTAMP_FORMAT) {
            'Y-m-d H:i:s' => date('Y-m-d H:i:s', time()),
            'milliseconds' => Time::getMillisecond(),
            'seconds' => time(),
        };
    }

}