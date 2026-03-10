<?php

// Namespace for the Vault store core components
namespace Vault;

// Generates and validates cryptographically secure nonces for CSP headers.
class NonceGenerator
{
    // Returns a URL-safe Base64 nonce from random_bytes()
    public static function generate(int $bytes = NONCE_BYTES): string
    {
        $randomBytes = random_bytes($bytes);
        return rtrim(base64_encode($randomBytes), '=');
    }

    // Checks that a nonce is long enough and contains only Base64 characters.
    public static function validate(string $nonce): bool
    {
        return strlen($nonce) >= 22 && preg_match('/^[A-Za-z0-9+\/=]+$/', $nonce);
    }
}
