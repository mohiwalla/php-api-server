<?php

require_once __DIR__ . "/../../utils/random-string.php";

$con = new DB();

$query = <<<SQL
-- DROP TABLE IF EXISTS visits;
CREATE TABLE IF NOT EXISTS visits (
    id VARCHAR(16) PRIMARY KEY,
    createdAt DATETIME
);
SQL;

$con->query($query);

$query = "INSERT INTO visits (id, createdAt) VALUES (?, NOW(3));";
$con->query($query, RandomString(16));

$query = "SELECT COUNT(*) `count` FROM visits;";
$result = $con->query($query);

$count = $con->fetch($result)->count;

new Response(true, "OK", 200, ["visitsCount" => $count]);
