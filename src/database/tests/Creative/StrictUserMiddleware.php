<?php
declare(strict_types=1);

namespace UtilitiesTests\Database\Creative;

use Utilities\Database\Constraints\hasTimestamps;
use Utilities\Database\Constraints\strictData;
use Utilities\Database\Constraints\strictUpdate;

class StrictUserMiddleware extends \Utilities\Database\Middleware
{

    use strictData, strictUpdate, hasTimestamps;

    protected static string $TABLE_NAME = 'Users';

    protected static string $PRIMARY_KEY = 'id';

    protected static array $COLUMNS_TYPE = [
        'id' => 'int',
        'name' => 'string',
        'email' => [
            'type' => 'email',
            'domain' => 'example.com'
        ],
        'password' => [
            'type' => 'string',
            'regex' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/'
        ],
        'created_at' => 'int',
        'updated_at' => 'int',
    ];

    protected static array $UPDATABLE_COLUMNS = [
        'name',
        'email',
        'password',
        'updated_at'
    ];

    protected static string $TIMESTAMP_FORMAT = 'milliseconds';

}