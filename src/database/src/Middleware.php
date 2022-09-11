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
     * Get Database
     *
     * @return DB
     */
    protected static function getDatabase(): DB
    {
        if (!isset(static::$TABLE_NAME, static::$PRIMARY_KEY, static::$DATABASE_SECRET)) {
            throw new RuntimeException('The {$table_name, $primary_key, $database_secret} properties must be set.');
        }

        return static::$DB ?: (static::$DB = new DB(static::$DATABASE_SECRET));
    }

}