<?php
require_once 'config.php';

$id = isset($_GET['id']) ? $_GET['id'] : '';
$file = getFileById($id);

if (!$file) {
    header('HTTP/1.1 404 Not Found');
    $error = true;
} else {
    $error = false;
}

include 'header.php';
?>

<section class="view-section">
    <?php if ($error): ?>
    <div class="error-card">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <h2>File Tidak Ditemukan</h2>
        <p>File yang Anda cari mungkin sudah dihapus (expired) atau link-nya salah.</p>
        <a href="index.php" class="btn-primary"><i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda</a>
    </div>
    <?php else: ?>
    <div class="view-card">
        <div class="view-header">
            <a href="index.php" class="view-back"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
            <h1><i class="fa-solid <?php echo $SUPPORTED_FORMATS[$file['category']]['icon']; ?>"></i> <?php echo $file['name']; ?></h1>
        </div>

        <div class="view-content">
            <?php if ($file['category'] === 'image'): ?>
            <div class="view-media image-view">
                <img src="<?php echo $file['url']; ?>" alt="<?php echo $file['name']; ?>">
            </div>
            <?php elseif ($file['category'] === 'video'): ?>
            <div class="view-media video-view">
                <video controls preload="metadata">
                    <source src="<?php echo $file['url']; ?>" type="<?php echo getMimeType($file['path']); ?>">
                    Browser Anda tidak mendukung pemutaran video.
                </video>
            </div>
            <?php elseif ($file['category'] === 'audio'): ?>
            <div class="view-media audio-view">
                <div class="audio-poster">
                    <i class="fa-solid fa-music"></i>
                </div>
                <audio controls>
                    <source src="<?php echo $file['url']; ?>" type="<?php echo getMimeType($file['path']); ?>">
                    Browser Anda tidak mendukung pemutaran audio.
                </audio>
            </div>
            <?php else: ?>
            <div class="view-media file-view">
                <i class="fa-solid fa-file"></i>
                <p>File ini tidak dapat ditampilkan secara langsung.</p>
            </div>
            <?php endif; ?>
        </div>

        <div class="view-details">
            <div class="detail-grid">
                <div class="detail-item">
                    <span class="detail-label"><i class="fa-solid fa-file"></i> Nama File</span>
                    <span class="detail-value"><?php echo $file['name']; ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="fa-solid fa-tag"></i> Tipe</span>
                    <span class="detail-value"><?php echo $SUPPORTED_FORMATS[$file['category']]['label']; ?> (<?php echo strtoupper($file['extension']); ?>)</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="fa-solid fa-weight-hanging"></i> Ukuran</span>
                    <span class="detail-value"><?php echo $file['size_formatted']; ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="fa-solid fa-calendar"></i> Diupload</span>
                    <span class="detail-value"><?php echo date('d F Y H:i', strtotime($file['uploaded_at'])); ?></span>
                </div>
                <div class="detail-item detail-expire">
                    <span class="detail-label"><i class="fa-solid fa-hourglass-half"></i> Kadaluarsa</span>
                    <span class="detail-value expire-value">
                        <span class="expire-pill"><?php echo $file['expires_text']; ?></span>
                        <span class="expire-date"><?php echo date('d F Y H:i', strtotime($file['expires_at'])); ?></span>
                    </span>
                </div>
            </div>

            <div class="view-actions">
                <a href="<?php echo $file['url']; ?>" download class="btn-primary">
                    <i class="fa-solid fa-download"></i> Download
                </a>
                <button class="btn-secondary" onclick="copyLink('<?php echo BASE_URL . '/v/' . $file['id']; ?>')">
                    <i class="fa-solid fa-link"></i> Salin Link
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>
</section>

<?php include 'footer.php'; ?>
