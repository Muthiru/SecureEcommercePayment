<?php

namespace {
    $configFile = __DIR__ . '/../config/config.php';
    if (is_file($configFile)) {
        require_once $configFile;
    } else {
        $defineIfMissing = static function (string $name, mixed $value): void {
            if (!defined($name)) {
                define($name, $value);
            }
        };

        $defineIfMissing('STRIPE_PUBLIC_KEY', getenv('STRIPE_PUBLIC_KEY') ?: '');
        $defineIfMissing('STRIPE_SECRET_KEY', getenv('STRIPE_SECRET_KEY') ?: '');
        $defineIfMissing('NONCE_BYTES', (int) (getenv('NONCE_BYTES') ?: 16));
        $defineIfMissing('APP_ENV', getenv('APP_ENV') ?: 'production');
        $defineIfMissing('APP_NAME', getenv('APP_NAME') ?: 'VAULT');

        spl_autoload_register(function ($class) {
            if (strpos($class, 'Vault\\') === 0) {
                $name = str_replace('Vault\\', '', $class);
                $file = __DIR__ . '/../src/' . str_replace('\\', '/', $name) . '.php';
                if (file_exists($file)) {
                    include_once $file;
                }
            }
        });
    }

    if (!defined('STRIPE_PUBLIC_KEY') || !defined('STRIPE_SECRET_KEY')) {
        throw new \RuntimeException('Bootstrap could not load payment configuration.');
    }

    $productsFile = __DIR__ . '/../config/products.php';
    if (!is_file($productsFile)) {
        throw new \RuntimeException('Missing required bootstrap file: ' . $productsFile);
    }

    require_once $productsFile;

    if (!defined('NONCE_BYTES')) {
        define('NONCE_BYTES', 16);
    }
}
