<?php
$SimpleUrls = new \Utilities\Router\URLs();

$URL = '/blockchain/tron/GenerateAccount';
$Pattern = '/{sector}/{subsector}/{method}/';

$Array = $SimpleUrls::parseURL($URL, $Pattern);
\Utilities\Common\Printer::printPrettyJson($Array);