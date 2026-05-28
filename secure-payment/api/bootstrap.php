<?php

namespace {
    $configFile = __DIR__ . '/../config/config.php';
    if (!is_file($configFile)) {
        throw new \RuntimeException('Missing required config file: ' . $configFile);
    }

    require_once $configFile;

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
