<?php

function fetch(string $url, array $options = []) {
    $ch = curl_init();

    $defaults = [
        'method' => 'GET',
        'headers' => [],
        'body' => '',
        'timeout' => 30,
        'ssl_verify' => true,
    ];

    $options = array_merge($defaults, $options);
    $method = strtoupper($options['method']);

    if (!in_array($method, ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD'])) {
        throw new InvalidArgumentException('Invalid method specified.');
    }

    $headers = [];
    foreach ($options['headers'] as $key => $value) {
        $headers[] = "$key: $value";
    }

    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => $options['timeout'],
        CURLOPT_SSL_VERIFYPEER => $options['ssl_verify'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => true,
    ]);

    if ($method !== 'GET') {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $options['body']);
    }

    $response = curl_exec($ch);
    if ($response === false) {
        throw new RuntimeException('cURL error: ' . curl_error($ch));
    }

    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerStr = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);

    curl_close($ch);

    $headers = [];
    $lines = explode("\r\n", trim($headerStr));

    foreach ($lines as $i => $line) {
        if ($i === 0) {
            $statusText = $line;
            continue;
        }

        if (strpos($line, ':') !== false) {
            [$key, $value] = explode(':', $line, 2);
            $headers[trim($key)] = trim($value);
        }
    }

    $decoded = json_decode($body);
    $body = json_last_error() === JSON_ERROR_NONE ? $decoded : $body;

    return (object) [
        'status' => $statusCode,
        'statusText' => $statusText ?? '',
        'headers' => $headers,
        'body' => $body,
    ];
}
