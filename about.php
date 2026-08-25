<?php
require_once 'config.php';
include 'header.php';
?>

<section class="about-hero">
    <div class="about-hero-content">
        <div class="about-icon">
            <i class="fa-solid fa-cloud-arrow-up"></i>
        </div>
        <h1>Tentang <?php echo APP_NAME; ?></h1>
        <p>Platform hosting media gratis tanpa login. Sederhana, cepat, dan modern. File otomatis terhapus setelah 1-5 hari.</p>
    </div>
</section>

<!--
<section class="about-section">
    <div class="about-grid">
        <div class="about-card">
            <div class="about-card-icon">
                <i class="fa-solid fa-bolt"></i>
            </div>
            <h3>Cepat & Mudah</h3>
            <p>Tidak perlu membuat akun atau login. Cukup upload file Anda dan langsung dapatkan link untuk dibagikan.</p>
        </div>
        <div class="about-card">
            <div class="about-card-icon">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <h3>Gratis & Aman</h3>
            <p>Semua layanan gratis untuk digunakan. File Anda tersimpan aman di server lokal.</p>
        </div>
        <div class="about-card">
            <div class="about-card-icon">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <h3>Auto Expire</h3>
            <p>File otomatis terhapus setelah 1-5 hari sesuai pilihan Anda. Tidak ada file yang menumpuk selamanya.</p>
        </div>
        <div class="about-card">
            <div class="about-card-icon">
                <i class="fa-solid fa-code"></i>
            </div>
            <h3>API Tersedia</h3>
            <p>Developer dapat mengintegrasikan <?php echo APP_NAME; ?> ke aplikasi mereka melalui API yang tersedia.</p>
        </div>
    </div>
</section>
-->

<section class="formats-section">
    <div class="section-header">
        <h2>Format yang Didukung</h2>
        <p><?php echo APP_NAME; ?> mendukung berbagai format file populer</p>
    </div>

    <div class="formats-grid">
        <div class="format-card">
            <div class="format-header">
                <i class="fa-solid fa-image"></i>
                <h3>Gambar</h3>
            </div>
            <div class="format-tags">
                <span class="format-tag">JPG</span>
                <span class="format-tag">JPEG</span>
                <span class="format-tag">PNG</span>
                <span class="format-tag">GIF</span>
                <span class="format-tag">WebP</span>
                <span class="format-tag">SVG</span>
                <span class="format-tag">BMP</span>
                <span class="format-tag">ICO</span>
            </div>
        </div>

        <div class="format-card">
            <div class="format-header">
                <i class="fa-solid fa-video"></i>
                <h3>Video</h3>
            </div>
            <div class="format-tags">
                <span class="format-tag">MP4</span>
                <span class="format-tag">WebM</span>
                <span class="format-tag">OGG</span>
                <span class="format-tag">MOV</span>
                <span class="format-tag">MKV</span>
                <span class="format-tag">AVI</span>
            </div>
        </div>

        <div class="format-card">
            <div class="format-header">
                <i class="fa-solid fa-music"></i>
                <h3>Audio</h3>
            </div>
            <div class="format-tags">
                <span class="format-tag">MP3</span>
                <span class="format-tag">WAV</span>
                <span class="format-tag">OGG</span>
                <span class="format-tag">FLAC</span>
                <span class="format-tag">AAC</span>
                <span class="format-tag">M4A</span>
                <span class="format-tag">WMA</span>
            </div>
        </div>

        <div class="format-card">
            <div class="format-header">
                <i class="fa-solid fa-file-lines"></i>
                <h3>File</h3>
            </div>
            <div class="format-tags">
                <span class="format-tag">PDF</span>
                <span class="format-tag">DOC</span>
                <span class="format-tag">DOCX</span>
                <span class="format-tag">TXT</span>
                <span class="format-tag">ZIP</span>
                <span class="format-tag">RAR</span>
                <span class="format-tag">7Z</span>
                <span class="format-tag">CSV</span>
                <span class="format-tag">JSON</span>
                <span class="format-tag">XML</span>
            </div>
        </div>
    </div>
</section>

<section class="creator-section">
    <div class="creator-card">
        <div class="creator-avatar">
            <i class="fa-solid fa-user-astronaut"></i>
        </div>
        <div class="creator-info">
            <h2>Farel Alfareza</h2>
            <p class="creator-role">Developer & Creator</p>
            <p class="creator-desc">Pembuat <?php echo APP_NAME; ?>. Seorang developer yang passionate dalam membangun aplikasi web yang bermanfaat dan mudah digunakan.</p>
            <a href="https://farelsite.pages.dev" target="_blank" class="creator-portfolio">
                <i class="fa-solid fa-globe"></i> farelsite.pages.dev
            </a>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
