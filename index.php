<?php
require_once 'config.php';

$message = '';
$messageType = '';

// Handle upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['media'])) {
    $file = $_FILES['media'];
    $expireDays = isset($_POST['expire_days']) ? (int)$_POST['expire_days'] : 3;
    if ($expireDays < 1) $expireDays = 1;
    if ($expireDays > 5) $expireDays = 5;

    if ($file['error'] === UPLOAD_ERR_OK) {
        if ($file['size'] > MAX_FILE_SIZE) {
            $message = 'File terlalu besar! Maksimal 100MB.';
            $messageType = 'error';
        } else {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $mime = getMimeType($file['tmp_name']);
            $category = getFileCategory($mime, $ext);

            if ($category) {
                $newName = generateUniqueName($ext);
                $uploadPath = UPLOAD_DIR . $SUPPORTED_FORMATS[$category]['path'] . $newName;

                if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                    $fileId = pathinfo($newName, PATHINFO_FILENAME);

                    // Save metadata with expire info
                    saveFileMeta($fileId, $file['name'], $category, $ext, $file['size'], $expireDays);

                    $fileUrl = BASE_URL . '/m/' . $fileId;
                    $expDate = date('d M Y H:i', time() + ($expireDays * 86400));
                    $detailUrl = BASE_URL . '/v/' . $fileId;
                    $message = 'Upload berhasil! Media siap dibuka: <a href="' . $fileUrl . '" target="_blank">' . $fileUrl . '</a><br><small><i class="fa-solid fa-clock"></i> File akan otomatis terhapus pada ' . $expDate . ' (' . $expireDays . ' hari) · <a href="' . $detailUrl . '">Lihat detail</a></small>';
                    $messageType = 'success';
                } else {
                    $message = 'Gagal mengupload file. Coba lagi.';
                    $messageType = 'error';
                }
            } else {
                $message = 'Format file tidak didukung!';
                $messageType = 'error';
            }
        }
    } else {
        $message = 'Terjadi kesalahan saat upload. Coba lagi.';
        $messageType = 'error';
    }
}

// Keep automatic expiration active even though the homepage no longer lists files.
cleanExpiredFiles();

include 'header.php';
?>

<section class="hero">
    <div class="hero-content">
        <h1 class="hero-title">Unggah & Bagikan Media Anda</h1>
        <p class="hero-subtitle">Tanpa login, tanpa ribet. Upload foto, video, musik, dan file lainnya secara gratis. File otomatis terhapus setelah 1-5 hari.</p>
    </div>
</section>

<section class="upload-section">
    <div class="upload-card">
        <div class="upload-area" id="uploadArea">
            <input type="file" name="media" id="fileInput" form="uploadForm" hidden>
            <div class="upload-icon">
                <i class="fa-solid fa-cloud-arrow-up"></i>
            </div>
            <h3>Drag & Drop file di sini</h3>
            <p>atau <span class="upload-browse">klik untuk memilih</span></p>
            <p class="upload-formats">Maksimal 100MB</p>
        </div>

        <form id="uploadForm" method="POST" action="api/upload" enctype="multipart/form-data" class="upload-form">
            <div class="file-preview" id="filePreview" style="display:none;">
                <div class="preview-content">
                    <i class="fa-solid fa-file" id="previewIcon"></i>
                    <div class="preview-info">
                        <span class="preview-name" id="previewName"></span>
                        <span class="preview-size" id="previewSize"></span>
                    </div>
                    <button type="button" class="preview-remove" id="previewRemove">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>

            <div class="expire-selector">
                <div class="expire-heading">
                    <label for="expire_days"><i class="fa-solid fa-clock"></i> Masa aktif file</label>
                    <output id="expireValue" for="expire_days">3 hari</output>
                </div>
                <div class="expire-range-wrap">
                    <span>1 hari</span>
                    <input type="range" id="expire_days" name="expire_days" min="1" max="5" value="3" step="1" aria-label="Masa aktif file dalam hari">
                    <span>5 hari</span>
                </div>
                <div class="expire-scale" aria-hidden="true">
                    <span>Singkat</span>
                    <span>Lebih lama</span>
                </div>
            </div>

            <button type="submit" class="upload-btn" id="uploadBtn" disabled>
                <i class="fa-solid fa-upload"></i> Upload Sekarang
            </button>
            <div class="upload-progress" id="uploadProgress" hidden aria-live="polite">
                <div class="progress-heading">
                    <span id="progressLabel">Menyiapkan upload...</span>
                    <strong id="progressPercent">0%</strong>
                </div>
                <div class="progress-track" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" id="progressTrack">
                    <span id="progressBar"></span>
                </div>
                <span class="progress-detail" id="progressDetail">Jangan tutup halaman ini.</span>
            </div>
        </form>

        <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?>">
            <i class="fa-solid <?php echo $messageType === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'; ?>"></i>
            <span><?php echo $message; ?></span>
        </div>
        <?php endif; ?>
    </div>
</section>

<section class="donation-section donation-home-section">
    <div class="donation-home-heading">
        <span class="section-eyebrow">Dukung oma</span>
        <h2>Suka dengan oma?</h2>
        <p>Dukunganmu membantu kami menjaga server dan terus mengembangkan layanan ini.</p>
    </div>
    <div class="donation-grid">
        <a class="donation-card saweria-card" href="<?php echo SAWERIA_URL; ?>" target="_blank" rel="noopener noreferrer">
            <div class="donation-card-top">
                <div class="donation-brand-mark" aria-label="Ikon Saweria"><i class="fa-solid fa-hand-holding-heart"></i></div>
                <span class="donation-external"><i class="fa-solid fa-arrow-up-right-from-square"></i></span>
            </div>
            <span class="donation-label">Dukung lewat</span>
            <h2>Saweria</h2>
            <span class="donation-action">Buka Saweria <i class="fa-solid fa-arrow-right"></i></span>
        </a>
        <a class="donation-card trakteer-card" href="<?php echo TRAKTEER_URL; ?>" target="_blank" rel="noopener noreferrer">
            <div class="donation-card-top">
                <div class="donation-brand-mark" aria-label="Ikon Trakteer"><i class="fa-solid fa-mug-hot"></i></div>
                <span class="donation-external"><i class="fa-solid fa-arrow-up-right-from-square"></i></span>
            </div>
            <span class="donation-label">Dukung lewat</span>
            <h2>Trakteer</h2>
            <span class="donation-action">Buka Trakteer <i class="fa-solid fa-arrow-right"></i></span>
        </a>
    </div>
</section>

<?php include 'footer.php'; ?>
