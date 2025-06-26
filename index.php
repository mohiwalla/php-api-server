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

header("Access-Control-Allow-Origin: {$_ENV["FRONTEND"]}");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$filePath = __DIR__ . '/src/api' . $route . '.php';

if (!file_exists($filePath)) {
    $filePath = $filePath = __DIR__ . '/src/api' . $route . "/index.php";

    if (!file_exists($filePath)) {
        return new Response(false, "404", 404);
    }
}

require $filePath;
