<?php

$zod = new zod();

$zod->field("id", $zod->number(), "`id` must be a number.", "Please add `id` to URL query.");
$result = $zod->parse($_GET);

if (!$result->ok) {
    return new Response(false, $result->error);
}

$data = $result->data;

new Response(true, "OK", 200, [
    "id" => $data->id
]);
