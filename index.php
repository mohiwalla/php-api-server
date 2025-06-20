<?php

$rawRoute = rtrim($_SERVER['PATH_INFO'] ?? "", "/");
$route = $rawRoute ?: "/";

require __DIR__ . "/src/shared/fetch.php";
require __DIR__ . "/src/shared/response.php";
require __DIR__ . "/src/shared/display.php";
require __DIR__ . '/src/utils/get-ip.php';
require __DIR__ . "/src/shared/config.php";
require __DIR__ . "/src/shared/zod.php";
require __DIR__ . "/src/shared/db.php";

$filePath = __DIR__ . '/src' . $route . '.php';

if (!file_exists($filePath)) {
    return new Response(false, "404", 404);
}

require $filePath;
