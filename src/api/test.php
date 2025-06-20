<?php

$zod = new zod();
$con = new DB();

$zod->field("id", $zod->number());
$result = $zod->parse($_GET);

if (!$result->ok) {
    return new Response(false, $result->error);
}

$data = $result->data;
$res = fetch("https://dummyjson.com/users/{$data->id}");

$query = <<<SQL
CREATE TABLE IF NOT EXISTS requestHistory (
    id INT AUTO_INCREMENT,
    userID INT NOT NULL,
    createdAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(id)
);
SQL;

$con->query($query);

$query = "INSERT INTO requestHistory (userID) VALUES (?);";
$con->query($query, $data->id);

$query = "SELECT COUNT(*) `count` FROM requestHistory;";
$result = $con->query($query);

$count = $con->fetch($result)->count;

new Response(true, "OK", 200, ["data" => $res->body, "count" => $count]);
