<?php
declare(strict_types=1);

namespace Utilities\Database;

use RuntimeException;
use Utilities\Database\Traits\CommonConnectionTrait;

/**
 * Middleware class
 *
 * @link    https://github.com/utilities-php/database
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/database/blob/master/LICENSE (MIT License)
 */
class Middleware
{

    use CommonConnectionTrait;

    /**
     * @var ?DB
     */
    protected static ?DB $DB = null;

    /**
     * The table name (must be overridden)
     *
     * @var string
     */
    protected static string $TABLE_NAME;

    /**
     * The primary of the table (must be overridden)
     *
     * @var string
     */
    protected static string $PRIMARY_KEY;

    /**
     * Get Database
     *
     * @return DB
     */
    protected static function getDatabase(): DB
    {
        if (!isset(static::$TABLE_NAME, static::$PRIMARY_KEY)) {
            throw new RuntimeException(sprintf(
                'The %s class must have the TABLE_NAME and PRIMARY_KEY constants defined.',
                static::class
            ));
        }

        if (!isset($_ENV['DATABASE_SECRET_KEY'])) {
            throw new RuntimeException(
                'The DATABASE_SECRET_KEY environment variable is not set.'
            );
        }

        return static::$DB ?: (static::$DB = new DB());
    }

}