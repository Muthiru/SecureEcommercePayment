<?php
declare(strict_types=1);

$publicDir = realpath(__DIR__ . '/../public');

if ($publicDir === false) {
  http_response_code(500);
  echo 'Public directory not found.';
  exit;
}

$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$requestPath = rawurldecode($requestPath);
$requestPath = '/' . ltrim($requestPath, '/');

if ($requestPath === '/' || $requestPath === '') {
  $requestPath = '/index.php';
}

$relativePath = ltrim($requestPath, '/');
$relativePath = str_replace(["\0", '..'], '', $relativePath);
$targetFile = $publicDir . '/' . $relativePath;

if (is_file($targetFile)) {
  if (pathinfo($targetFile, PATHINFO_EXTENSION) === 'php') {
    require $targetFile;
  } else {
    serveFile($targetFile);
  }
  exit;
}

if (pathinfo($relativePath, PATHINFO_EXTENSION) === '') {
  $phpFile = $publicDir . '/' . trim($relativePath, '/') . '.php';
  if (is_file($phpFile)) {
    require $phpFile;
    exit;
  }
}

http_response_code(404);
echo 'Not Found';

function serveFile(string $filePath): void
{
  $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
  $contentType = match ($extension) {
    'css' => 'text/css; charset=UTF-8',
    'js' => 'application/javascript; charset=UTF-8',
    'html', 'htm' => 'text/html; charset=UTF-8',
    'svg' => 'image/svg+xml',
    'png' => 'image/png',
    'jpg', 'jpeg' => 'image/jpeg',
    'gif' => 'image/gif',
    'webp' => 'image/webp',
    'ico' => 'image/x-icon',
    'json' => 'application/json; charset=UTF-8',
    'txt' => 'text/plain; charset=UTF-8',
    default => 'application/octet-stream',
  };

  header('Content-Type: ' . $contentType);
  header('Content-Length: ' . filesize($filePath));
  readfile($filePath);
}
