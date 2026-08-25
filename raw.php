<?php
/**
 * Serve an uploaded file directly, without an HTML wrapper.
 */
require_once 'config.php';

$id = $_GET['id'] ?? '';
$file = getFileById($id);

if (!$file || !is_file($file['path'])) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Media tidak ditemukan atau sudah expired.';
    exit;
}

$mime = getMimeType($file['path']);
$size = filesize($file['path']);
$start = 0;
$end = $size - 1;

if (isset($_SERVER['HTTP_RANGE']) && preg_match('/bytes=(\d*)-(\d*)/', $_SERVER['HTTP_RANGE'], $range)) {
    if ($range[1] !== '') {
        $start = (int) $range[1];
    }
    if ($range[2] !== '') {
        $end = (int) $range[2];
    } elseif ($range[1] !== '') {
        $end = min($start + 1024 * 1024 - 1, $size - 1);
    }
    $start = max(0, $start);
    $end = min($end, $size - 1);
    if ($start > $end || $start >= $size) {
        http_response_code(416);
        header('Content-Range: bytes */' . $size);
        exit;
    }
    http_response_code(206);
}

header('Content-Type: ' . $mime);
header('Content-Length: ' . ($end - $start + 1));
header('Content-Disposition: inline; filename="' . addslashes(basename($file['name'])) . '"');
header('X-Content-Type-Options: nosniff');
header('Accept-Ranges: bytes');
if ($start !== 0 || $end !== $size - 1) {
    header('Content-Range: bytes ' . $start . '-' . $end . '/' . $size);
}

if ($_SERVER['REQUEST_METHOD'] !== 'HEAD') {
    $handle = fopen($file['path'], 'rb');
    fseek($handle, $start);
    $remaining = $end - $start + 1;
    while ($remaining > 0 && !feof($handle)) {
        $chunk = fread($handle, min(8192, $remaining));
        if ($chunk === false || $chunk === '') {
            break;
        }
        echo $chunk;
        $remaining -= strlen($chunk);
        flush();
    }
    fclose($handle);
}