<?php

namespace {
    $sharedFiles = [
        __DIR__ . '/../config/config.php',
        __DIR__ . '/../config/products.php',
        __DIR__ . '/../src/SecurityHeaders.php',
        __DIR__ . '/../src/NonceGenerator.php',
    ];

    foreach ($sharedFiles as $sharedFile) {
        if (!is_file($sharedFile)) {
            throw new \RuntimeException('Missing required bootstrap file: ' . $sharedFile);
        }

        require_once $sharedFile;
    }
}
