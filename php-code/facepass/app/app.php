<?php

require __DIR__ . '/../vendor/autoload.php';

$c = require __DIR__ . '/settings.php';
$app = new \Slim\App($c);

require __DIR__ . '/functions.php';
require __DIR__ . '/dependencies.php';
require __DIR__ . '/cors.php';

require __DIR__ . '/routes.php';
session_start();
$app->run();
