<?php

# starting session if not already started
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

# for development server checks
define("DEV", $_SERVER["SERVER_NAME"] === "localhost");

# error reporting enabled for development
ini_set('display_errors', DEV ? 1 : 0);
ini_set('display_startup_errors', DEV ? 1 : 0);
error_reporting(DEV ? -1 : 0);

# loading .env file
$envFilePath = __DIR__ . "/../../.env";
$env = parse_ini_file($envFilePath);

foreach ($env as $key => $value) {
    $_ENV[$key] = $value;
}
