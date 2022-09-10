<?php
declare(strict_types=1);

use Utilities\Common\Common;
use Utilities\Database\DB;

$database = new DB();

$database->setConnection([
    'host' => 'localhost',
    'user' => 'root',
    'pass' => '',
    'db' => 'test'
]);

$secret = Common::makeUUID();

if ($database->saveConnection($secret)) {
    echo 'Connection saved. Secret: ' . $secret;
}