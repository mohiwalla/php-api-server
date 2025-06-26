<?php

function RandomString(int $length = 16, ?string $customAlphabet = null): string {
    if ($length <= 0) {
        throw new InvalidArgumentException("Length must be a positive integer.");
    }

    $alphabet = $customAlphabet ?? '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz_-';
    $alphabetLength = strlen($alphabet);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $alphabet[rand(0, $alphabetLength - 1)];
    }

    return $randomString;
}
