<?php

// Namespace for the Vault store core components
namespace Vault;

use RuntimeException;
// Custom exception for security header related errors
class InvalidNonceException extends RuntimeException {}

// Applies all HTTP security headers for every response.
// Must be called before any output is sent.
class SecurityHeaders
{
    // Sends CSP, X-Frame-Options, and the full security header suite.
    public static function apply(string $nonce): void
    {
        if (!NonceGenerator::validate($nonce)) {
            throw new InvalidNonceException('Invalid nonce provided to SecurityHeaders::apply()');
        }

        header(
            "Content-Security-Policy: " .
            "default-src 'none'; " .
            "script-src 'nonce-{$nonce}' https://js.stripe.com; " .
            "frame-src https://js.stripe.com; " .
            "connect-src 'self' https://api.stripe.com; " .
            "form-action 'self'; " .
            "frame-ancestors 'none'; " .
            "style-src 'nonce-{$nonce}' https://fonts.googleapis.com; " .
            "font-src https://fonts.gstatic.com; " .
            "img-src 'self' data:; " .
            "base-uri 'self'; " .
            "upgrade-insecure-requests;"
        );

        self::applyBasic();
    }
    //Adds security headers, but no strict CSP — safe for pages with inline styles or third-party fonts.
    public static function applyBasic(): void
    {
        header('X-Frame-Options: DENY');
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: strict-origin');
        header('Permissions-Policy: payment=(self), camera=(), microphone=(), geolocation=()');

        if (defined('APP_ENV') && APP_ENV === 'production') {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
        }

        header_remove('X-Powered-By');
        header_remove('Server');
    }

    public static function getSummary(): array
    {
        return [
            'csp'              => 'Active (nonce-based)',
            'x_frame_options'  => 'DENY',
            'x_content_type'   => 'nosniff',
            'referrer_policy'  => 'strict-origin',
            'permissions'      => 'payment=(self)',
            'hsts'             => (defined('APP_ENV') && APP_ENV === 'production') ? 'max-age=31536000' : 'dev-disabled',
        ];
    }
}
