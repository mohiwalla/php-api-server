<?php

$excahngeRatesAPI = "https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies/usd.json";
$request = fetch($excahngeRatesAPI);

if ($request->status !== 200) {
    return new Response(false, "Something went wrong while fetching currency API.");
}

$response = $request->body;
$rates = $response->usd;

return new Response(true, "1 USD = $rates->inr INR");
