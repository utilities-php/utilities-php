<?php
declare(strict_types=1);

$db = new \Utilities\Database\DB();

$db->setConnection([
    'host' => 'localhost',
    'user' => 'root',
    'pass' => '',
    'db' => 'test'
]);

$secret = \Utilities\Common\UUID::generate();

if ($db->saveConnection($secret)) {
    echo 'Connection saved. Secret: ' . $secret;
}