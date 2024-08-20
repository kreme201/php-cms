<?php declare(strict_types=1);

use Kreme\Application;

define('APP_ROOT', dirname(__DIR__));

require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/functions/mime-types.php';

$app = new Application();
$app->serve();
