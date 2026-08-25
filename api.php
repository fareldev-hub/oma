<?php
/**
 * oma API Router
 */
require_once 'config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$action = isset($_GET['action']) ? $_GET['action'] : '';
$response = ['success' => false, 'message' => 'Invalid action'];

switch ($action) {
    case 'upload':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $response = ['success' => false, 'message' => 'Method not allowed. Use POST.'];
            break;
        }

        if (!isset($_FILES['media'])) {
            $response = ['success' => false, 'message' => 'No file uploaded. Field name must be "media".'];
            break;
        }

        $file = $_FILES['media'];
        $expireDays = isset($_POST['expire_days']) ? (int)$_POST['expire_days'] : 3;
        if ($expireDays < 1) $expireDays = 1;
        if ($expireDays > 5) $expireDays = 5;

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $uploadErrors = [
                UPLOAD_ERR_INI_SIZE => 'File melebihi batas upload server (maksimal 100MB).',
                UPLOAD_ERR_FORM_SIZE => 'File melebihi batas ukuran formulir.',
                UPLOAD_ERR_PARTIAL => 'Upload terputus sebelum selesai. Silakan coba lagi.',
                UPLOAD_ERR_NO_FILE => 'Tidak ada file yang dikirim.',
                UPLOAD_ERR_NO_TMP_DIR => 'Folder sementara server tidak tersedia.',
                UPLOAD_ERR_CANT_WRITE => 'Server tidak dapat menulis file. Periksa penyimpanan server.',
                UPLOAD_ERR_EXTENSION => 'Upload dihentikan oleh ekstensi server.'
            ];
            $response = ['success' => false, 'message' => $uploadErrors[$file['error']] ?? 'Upload gagal (kode ' . $file['error'] . ').'];
            http_response_code(400);
            break;
        }

        if ($file['size'] > MAX_FILE_SIZE) {
            $response = ['success' => false, 'message' => 'File too large. Max 100MB.'];
            break;
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $mime = getMimeType($file['tmp_name']);
        $category = getFileCategory($mime, $ext);

        if (!$category) {
            $response = ['success' => false, 'message' => 'Unsupported file format.'];
            break;
        }

        $newName = generateUniqueName($ext);
        $uploadPath = UPLOAD_DIR . $SUPPORTED_FORMATS[$category]['path'] . $newName;

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            $fileId = pathinfo($newName, PATHINFO_FILENAME);

            // Save metadata with expire info
            saveFileMeta($fileId, $file['name'], $category, $ext, $file['size'], $expireDays);

            $response = [
                'success' => true,
                'message' => 'Upload successful',
                'data' => [
                    'id' => $fileId,
                    'name' => $newName,
                    'url' => BASE_URL . '/v/' . $fileId,
                    'raw_url' => BASE_URL . '/m/' . $fileId,
                    'direct_url' => BASE_URL . '/uploads/' . $SUPPORTED_FORMATS[$category]['path'] . $newName,
                    'category' => $category,
                    'extension' => strtolower($ext),
                    'size' => $file['size'],
                    'size_formatted' => formatFileSize($file['size']),
                    'expire_days' => $expireDays,
                    'expires_at' => date('Y-m-d H:i:s', time() + ($expireDays * 86400))
                ]
            ];
        } else {
            $response = ['success' => false, 'message' => 'Failed to save file.'];
        }
        break;

    case 'list':
        $files = getAllFiles();
        $response = [
            'success' => true,
            'count' => count($files),
            'data' => array_map(function($f) {
                return [
                    'id' => $f['id'],
                    'name' => $f['name'],
                    'category' => $f['category'],
                    'extension' => $f['extension'],
                    'size' => $f['size'],
                    'size_formatted' => $f['size_formatted'],
                    'url' => $f['url'],
                    'view_url' => BASE_URL . '/v/' . $f['id'],
                    'raw_url' => BASE_URL . '/m/' . $f['id'],
                    'uploaded_at' => $f['uploaded_at'],
                    'expires_at' => $f['expires_at'],
                    'expires_text' => $f['expires_text'],
                    'expire_days' => $f['expire_days']
                ];
            }, $files)
        ];
        break;

    case 'get':
        $id = isset($_GET['id']) ? $_GET['id'] : '';
        $file = getFileById($id);

        if ($file) {
            $response = [
                'success' => true,
                'data' => [
                    'id' => $file['id'],
                    'name' => $file['name'],
                    'category' => $file['category'],
                    'extension' => $file['extension'],
                    'size' => $file['size'],
                    'size_formatted' => $file['size_formatted'],
                    'url' => $file['url'],
                    'view_url' => BASE_URL . '/v/' . $file['id'],
                    'raw_url' => BASE_URL . '/m/' . $file['id'],
                    'uploaded_at' => $file['uploaded_at'],
                    'expires_at' => $file['expires_at'],
                    'expires_text' => $file['expires_text'],
                    'expire_days' => $file['expire_days']
                ]
            ];
        } else {
            $response = ['success' => false, 'message' => 'File not found or expired.'];
        }
        break;

    case 'delete':
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $response = ['success' => false, 'message' => 'Method not allowed. Use DELETE or POST.'];
            break;
        }

        $id = isset($_GET['id']) ? $_GET['id'] : '';
        $file = getFileById($id);

        if ($file) {
            if (deleteFileAndMeta($file)) {
                $response = ['success' => true, 'message' => 'File deleted successfully.'];
            } else {
                $response = ['success' => false, 'message' => 'Failed to delete file.'];
            }
        } else {
            $response = ['success' => false, 'message' => 'File not found.'];
        }
        break;

    default:
        $response = [
            'success' => false,
            'message' => 'Invalid action. Available: upload, list, get, delete'
        ];
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>
