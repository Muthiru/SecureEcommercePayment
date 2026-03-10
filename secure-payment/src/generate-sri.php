#!/usr/bin/env php
<?php

function computeSRI(string $content, string $algorithm = 'sha384'): string
{
    $hash = hash($algorithm, $content, true);
    return $algorithm . '-' . base64_encode($hash);
}

function fetchContent(string $source): ?string
{
    if (filter_var($source, FILTER_VALIDATE_URL)) {
        $context = stream_context_create([
            'http' => ['timeout' => 15, 'user_agent' => 'SRI-Generator/1.0']
        ]);
        $content = @file_get_contents($source, false, $context);
        return $content !== false ? $content : null;
    }

    if (!file_exists($source)) {
        return null;
    }
    return file_get_contents($source);
}

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This script must be run from the command line.');
}

$source = $argv[1] ?? null;

if (!$source) {
    echo "\nUsage: php generate-sri.php <file_path_or_url>\n\n";
    echo "Examples:\n";
    echo "  php generate-sri.php public/assets/style.css\n";
    echo "  php generate-sri.php https://cdn.example.com/library.js\n\n";
    exit(0);
}

echo "\n[SRI Generator] Processing: {$source}\n";

$content = fetchContent($source);
if ($content === null) {
    echo "[ERROR] Could not read file or URL: {$source}\n";
    exit(1);
}

$sri = computeSRI($content, 'sha384');
$ext = strtolower(pathinfo(parse_url($source, PHP_URL_PATH), PATHINFO_EXTENSION));

echo "[OK] SHA-384 Hash computed.\n";
echo "\n--- Copy this into your HTML ---\n\n";

if ($ext === 'css') {
    echo '<link rel="stylesheet"' . "\n";
    echo '      href="' . $source . '"' . "\n";
    echo '      integrity="' . $sri . '"' . "\n";
    echo '      crossorigin="anonymous">' . "\n";
} else {
    echo '<script' . "\n";
    echo '  src="' . $source . '"' . "\n";
    echo '  integrity="' . $sri . '"' . "\n";
    echo '  crossorigin="anonymous"></script>' . "\n";
}

echo "\n--- Raw integrity value ---\n";
echo $sri . "\n\n";

echo "--- Verification ---\n";
echo "Algorithm : SHA-384\n";
echo "Bytes     : " . strlen($content) . "\n";
echo "Hash (hex): " . hash('sha384', $content) . "\n\n";
