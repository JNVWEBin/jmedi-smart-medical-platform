<?php
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($uri === '/' || $uri === '/index.php') {
    require __DIR__ . '/public/index.php';
    return;
}

$filePath = __DIR__ . $uri;
if (preg_match('/\.(css|js|png|jpg|jpeg|gif|webp|ico|svg|woff|woff2|ttf|eot)$/i', $uri)) {
    if (file_exists($filePath)) {
        return false;
    }
    http_response_code(404);
    return;
}

if (is_dir($filePath)) {
    $indexPath = rtrim($filePath, '/') . '/index.php';
    if (file_exists($indexPath)) {
        require $indexPath;
        return;
    }
}

if (file_exists($filePath) && pathinfo($filePath, PATHINFO_EXTENSION) === 'php') {
    require $filePath;
    return;
}

http_response_code(404);
echo '<!DOCTYPE html><html><head><title>404</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"></head><body class="d-flex align-items-center justify-content-center" style="min-height:100vh;"><div class="text-center"><h1 class="display-1 text-primary">404</h1><p class="lead">Page not found</p><a href="/" class="btn btn-primary">Go Home</a></div></body></html>';
