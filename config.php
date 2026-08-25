<?php
/**
 * oma - Free Media Hosting
 * Configuration File
 */

$forwardedProto = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '';
$scheme = $forwardedProto ?: ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http');
$scriptDirectory = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/\\');
define('BASE_URL', $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . ($scriptDirectory === '.' ? '' : $scriptDirectory));

define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('META_DIR', UPLOAD_DIR . 'meta/');
define('MAX_FILE_SIZE', 100 * 1024 * 1024);

$SUPPORTED_FORMATS = [
    'image' => [
        'label' => 'Images',
        'extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico'],
        'mime' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml', 'image/bmp', 'image/x-icon'],
        'icon' => 'fa-image',
        'path' => 'images/'
    ],
    'video' => [
        'label' => 'Videos',
        'extensions' => ['mp4', 'webm', 'ogg', 'mov', 'mkv', 'avi'],
        'mime' => ['video/mp4', 'video/webm', 'video/ogg', 'video/quicktime', 'video/x-matroska', 'video/x-msvideo'],
        'icon' => 'fa-video',
        'path' => 'videos/'
    ],
    'audio' => [
        'label' => 'Audio',
        'extensions' => ['mp3', 'wav', 'ogg', 'flac', 'aac', 'm4a', 'wma'],
        'mime' => ['audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/flac', 'audio/aac', 'audio/mp4', 'audio/x-ms-wma'],
        'icon' => 'fa-music',
        'path' => 'audio/'
    ],
    'file' => [
        'label' => 'Files',
        'extensions' => ['pdf', 'doc', 'docx', 'txt', 'zip', 'rar', '7z', 'csv', 'json', 'xml'],
        'mime' => ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain', 'application/zip', 'application/x-rar-compressed', 'application/x-7z-compressed', 'text/csv', 'application/json', 'application/xml'],
        'icon' => 'fa-file',
        'path' => 'files/'
    ]
];

/**
 * Ensure local storage exists before handling an upload.
 */
function ensureStorageDirectories() {
    global $SUPPORTED_FORMATS;

    $directories = [UPLOAD_DIR, META_DIR];
    foreach ($SUPPORTED_FORMATS as $info) {
        $directories[] = UPLOAD_DIR . $info['path'];
    }

    foreach (array_unique($directories) as $directory) {
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            throw new RuntimeException('Storage directory could not be created: ' . $directory);
        }
    }
}

ensureStorageDirectories();

// MIME type mapping fallback (jika fileinfo extension tidak aktif)
$MIME_MAP = [
    'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png',
    'gif' => 'image/gif', 'webp' => 'image/webp', 'svg' => 'image/svg+xml',
    'bmp' => 'image/bmp', 'ico' => 'image/x-icon',
    'mp4' => 'video/mp4', 'webm' => 'video/webm', 'ogg' => 'video/ogg',
    'mov' => 'video/quicktime', 'mkv' => 'video/x-matroska', 'avi' => 'video/x-msvideo',
    'mp3' => 'audio/mpeg', 'wav' => 'audio/wav', 'flac' => 'audio/flac',
    'aac' => 'audio/aac', 'm4a' => 'audio/mp4', 'wma' => 'audio/x-ms-wma',
    'pdf' => 'application/pdf', 'doc' => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'txt' => 'text/plain', 'zip' => 'application/zip',
    'rar' => 'application/x-rar-compressed', '7z' => 'application/x-7z-compressed',
    'csv' => 'text/csv', 'json' => 'application/json', 'xml' => 'application/xml'
];

// App info
define('APP_NAME', 'oma');
define('APP_VERSION', '1.1.0');
define('APP_DESCRIPTION', 'Free media hosting platform - Upload and share your media instantly without login. Files auto-expire after 1-5 days.');
define('SAWERIA_URL', 'https://saweria.co/fareldev');
define('TRAKTEER_URL', 'https://trakteer.id/farel_alfarez/gift');

/**
 * Get MIME type (fallback jika mime_content_type tidak tersedia)
 */
function getMimeType($filepath) {
    if (function_exists('mime_content_type')) {
        $mime = @mime_content_type($filepath);
        if ($mime && $mime !== 'application/octet-stream') {
            return $mime;
        }
    }
    global $MIME_MAP;
    $ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
    if (isset($MIME_MAP[$ext])) {
        return $MIME_MAP[$ext];
    }
    return 'application/octet-stream';
}

/**
 * Get file type category
 */
function getFileCategory($mimeType, $extension) {
    global $SUPPORTED_FORMATS;
    foreach ($SUPPORTED_FORMATS as $category => $info) {
        if (in_array($mimeType, $info['mime']) || in_array(strtolower($extension), $info['extensions'])) {
            return $category;
        }
    }
    return null;
}

/**
 * Generate unique filename
 */
function generateUniqueName($extension) {
    return bin2hex(random_bytes(8)) . '_' . time() . '.' . strtolower($extension);
}

/**
 * Format file size
 */
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) return round($bytes / 1073741824, 2) . ' GB';
    if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
    if ($bytes >= 1024) return round($bytes / 1024, 2) . ' KB';
    return $bytes . ' B';
}

/**
 * Save file metadata (including expire info)
 */
function saveFileMeta($fileId, $originalName, $category, $extension, $size, $expireDays) {
    $metaPath = META_DIR . $fileId . '.json';
    $uploadedAt = time();
    $expiresAt = $uploadedAt + ($expireDays * 86400);

    $meta = [
        'id' => $fileId,
        'original_name' => $originalName,
        'category' => $category,
        'extension' => $extension,
        'size' => $size,
        'expire_days' => (int)$expireDays,
        'uploaded_at' => $uploadedAt,
        'expires_at' => $expiresAt,
        'expires_at_formatted' => date('Y-m-d H:i:s', $expiresAt)
    ];

    file_put_contents($metaPath, json_encode($meta, JSON_PRETTY_PRINT));
    return $meta;
}

/**
 * Get file metadata
 */
function getFileMeta($fileId) {
    $metaPath = META_DIR . $fileId . '.json';
    if (file_exists($metaPath)) {
        $meta = json_decode(file_get_contents($metaPath), true);
        if ($meta) return $meta;
    }
    return null;
}

/**
 * Delete file and its metadata
 */
function deleteFileAndMeta($file) {
    $deleted = false;
    if (isset($file['path']) && file_exists($file['path'])) {
        $deleted = unlink($file['path']);
    }
    $metaPath = META_DIR . $file['id'] . '.json';
    if (file_exists($metaPath)) {
        unlink($metaPath);
    }
    return $deleted;
}

/**
 * Check and clean expired files
 */
function cleanExpiredFiles() {
    global $SUPPORTED_FORMATS;
    $now = time();
    $cleaned = 0;

    foreach ($SUPPORTED_FORMATS as $category => $info) {
        $dir = UPLOAD_DIR . $info['path'];
        if (!is_dir($dir)) continue;

        foreach (glob($dir . '*') as $file) {
            if (!is_file($file)) continue;

            $fileId = pathinfo(basename($file), PATHINFO_FILENAME);
            $meta = getFileMeta($fileId);

            if ($meta && isset($meta['expires_at']) && $meta['expires_at'] < $now) {
                // File expired, delete it
                $fileData = [
                    'id' => $fileId,
                    'path' => $file
                ];
                deleteFileAndMeta($fileData);
                $cleaned++;
            }
        }
    }

    return $cleaned;
}

/**
 * Get all uploaded files (non-expired only)
 */
function getAllFiles() {
    global $SUPPORTED_FORMATS;

    // Clean expired files first
    cleanExpiredFiles();

    $files = [];
    $now = time();

    foreach ($SUPPORTED_FORMATS as $category => $info) {
        $dir = UPLOAD_DIR . $info['path'];
        if (!is_dir($dir)) continue;

        foreach (glob($dir . '*') as $file) {
            if (!is_file($file)) continue;

            $filename = basename($file);
            $fileId = pathinfo($filename, PATHINFO_FILENAME);
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $meta = getFileMeta($fileId);

            // Skip if no metadata (orphan file)
            if (!$meta) continue;

            // Skip if expired (double check)
            if (isset($meta['expires_at']) && $meta['expires_at'] < $now) {
                deleteFileAndMeta(['id' => $fileId, 'path' => $file]);
                continue;
            }

            $remaining = isset($meta['expires_at']) ? $meta['expires_at'] - $now : 0;
            $remainingDays = ceil($remaining / 86400);
            $remainingHours = ceil($remaining / 3600);

            if ($remainingDays > 1) {
                $expiresText = $remainingDays . ' hari lagi';
            } elseif ($remainingHours > 1) {
                $expiresText = $remainingHours . ' jam lagi';
            } else {
                $expiresText = '< 1 jam lagi';
            }

            $files[] = [
                'id' => $fileId,
                'name' => $filename,
                'original_name' => $meta['original_name'] ?? $filename,
                'category' => $category,
                'extension' => $ext,
                'size' => filesize($file),
                'size_formatted' => formatFileSize(filesize($file)),
                'url' => BASE_URL . '/uploads/' . $info['path'] . $filename,
                'path' => $file,
                'uploaded_at' => date('Y-m-d H:i:s', filemtime($file)),
                'expires_at' => isset($meta['expires_at']) ? date('Y-m-d H:i:s', $meta['expires_at']) : 'Unknown',
                'expires_at_timestamp' => $meta['expires_at'] ?? 0,
                'expire_days' => $meta['expire_days'] ?? 1,
                'expires_text' => $expiresText,
                'remaining_seconds' => $remaining
            ];
        }
    }

    // Sort by upload time (newest first)
    usort($files, function($a, $b) {
        return strtotime($b['uploaded_at']) - strtotime($a['uploaded_at']);
    });

    return $files;
}

/**
 * Get file by ID
 */
function getFileById($id) {
    $files = getAllFiles();
    foreach ($files as $file) {
        if ($file['id'] === $id) {
            return $file;
        }
    }
    return null;
}
?>
