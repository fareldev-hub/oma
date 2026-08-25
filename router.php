<?php
/**
 * Router for PHP's built-in server. Apache uses .htaccess instead.
 */
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$path = '/' . ltrim($path, '/');

$routes = [
    '#^/$#' => ['index.php', null],
    '#^/api/upload/?$#' => ['api.php', 'upload'],
    '#^/api/files/?$#' => ['api.php', 'list'],
    '#^/api/files/([A-Za-z0-9_-]+)/?$#' => ['api.php', 'get'],
    '#^/api/delete/([A-Za-z0-9_-]+)/?$#' => ['api.php', 'delete'],
    '#^/v/([A-Za-z0-9_-]+)/?$#' => ['view.php', null],
    '#^/m/([A-Za-z0-9_-]+)/?$#' => ['raw.php', null],
    '#^/about/?$#' => ['about.php', null],
    '#^/api/?$#' => ['api-docs.php', null],
];

foreach ($routes as $pattern => [$script, $action]) {
    if (preg_match($pattern, $path, $matches)) {
        if ($action !== null) {
            $_GET['action'] = $action;
        }
        if ($action === 'get' || $action === 'delete') {
            $_GET['id'] = $matches[1];
        } elseif ($script === 'view.php') {
            $_GET['id'] = $matches[1];
        } elseif ($script === 'raw.php') {
            $_GET['id'] = $matches[1];
        }
        require __DIR__ . '/' . $script;
        return true;
    }
}

if (is_file(__DIR__ . $path)) {
    return false;
}

http_response_code(404);
echo 'Not found';