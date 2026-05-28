<?php

// Copy this file to config.php and fill in your own values.

// Stripe API keys — get them from https://dashboard.stripe.com/apikeys
define('STRIPE_PUBLIC_KEY', getenv('STRIPE_PUBLIC_KEY') ?: 'pk_test_YOUR_PUBLISHABLE_KEY_HERE');
define('STRIPE_SECRET_KEY', getenv('STRIPE_SECRET_KEY') ?: 'sk_test_YOUR_SECRET_KEY_HERE');

// Byte length for CSP nonces; 16 bytes → 22-char Base64 string
define('NONCE_BYTES', (int) (getenv('NONCE_BYTES') ?: 16));
define('APP_ENV', getenv('APP_ENV') ?: 'development'); // switch to 'production' on live server
define('APP_NAME', getenv('APP_NAME') ?: 'VAULT');

// Custom autoloader to handle namespaced classes in the src directory
spl_autoload_register(function ($class) {
    if (strpos($class, 'Vault\\') === 0) {
        $name = str_replace('Vault\\', '', $class);
        $file = __DIR__ . '/../src/' . str_replace('\\', '/', $name) . '.php';
        if (file_exists($file)) {
            include_once $file;
        }
    }
});

if (APP_ENV === 'production') {
    ini_set('display_errors', 0);
    error_reporting(0);
} else {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}
