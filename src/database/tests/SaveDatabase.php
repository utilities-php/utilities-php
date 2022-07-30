<?php
$Database = new \Utilities\Database\DB();

$Database->setConnection([
    'host' => 'localhost',
    'user' => 'root',
    'pass' => '',
    'db' => 'test'
]);

$Secret = \Utilities\Common\Common::makeUUID();
$Save = $Database->saveConnection($Secret);

if ($Save) {
    echo 'Connection saved. Secret: ' . $Secret;
}