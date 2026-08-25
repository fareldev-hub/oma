<?php
require_once 'config.php';
$base = BASE_URL;
include 'header.php';
?>

<section class="api-hero">
    <div class="api-hero-content">
        
        <div class="api-kicker"><i class="fa-solid fa-terminal"></i> Developer docs</div>
        <h1><?php echo APP_NAME; ?> API</h1>
        <p>Upload, kelola, dan bagikan media melalui endpoint sederhana dalam format JSON.</p>
        <div class="api-hero-meta">
            <div class="api-version">v<?php echo APP_VERSION; ?></div>
            <span><i class="fa-solid fa-circle-check"></i> Base URL siap digunakan</span>
        </div>
    </div>
</section>

<section class="api-section">
    <div class="api-container">
        <div class="api-overview">
            <div>
                <span class="section-eyebrow">Mulai dalam hitungan menit</span>
                <h2>API yang simpel untuk berbagi media</h2>
                <p>Gunakan <code><?php echo $base; ?></code> sebagai base URL. Semua endpoint mengembalikan JSON dan tidak membutuhkan autentikasi.</p>
            </div>
            <div class="api-overview-note"><i class="fa-solid fa-shield-halved"></i><span>Maks. 100MB<br><small>per file</small></span></div>
        </div>

        <div class="api-endpoint">
            <div class="endpoint-header">
                <span class="method method-post">POST</span>
                <code class="endpoint-url"><?php echo $base; ?>/api/upload</code>
            </div>
            <div class="endpoint-body">
                <h3>Upload File</h3>
                <p>Mengupload file media ke server <?php echo APP_NAME; ?>. File akan otomatis terhapus setelah masa aktif berakhir (1-5 hari).</p>

                <h4>Parameters</h4>
                <div class="api-table-wrap"><table class="api-table">
                    <tr>
                        <th>Parameter</th>
                        <th>Type</th>
                        <th>Required</th>
                        <th>Description</th>
                    </tr>
                    <tr>
                        <td><code>media</code></td>
                        <td>File</td>
                        <td>Yes</td>
                        <td>File yang akan diupload (max 100MB)</td>
                    </tr>
                    <tr>
                        <td><code>expire_days</code></td>
                        <td>Integer (1-5)</td>
                        <td>No</td>
                        <td>Masa aktif file dalam hari. Default: 3 hari</td>
                    </tr>
                </table></div>

                <h4>Response</h4>
                <pre class="code-block"><code class="language-json">{
  "success": true,
  "message": "Upload successful",
  "data": {
    "id": "a1b2c3d4_1699123456",
    "name": "a1b2c3d4_1699123456.jpg",
    "url": "<?php echo $base; ?>/v/a1b2c3d4_1699123456",
      "raw_url": "<?php echo $base; ?>/m/a1b2c3d4_1699123456",
    "direct_url": "<?php echo $base; ?>/uploads/images/a1b2c3d4_1699123456.jpg",
    "category": "image",
    "extension": "jpg",
    "size": 204800,
    "size_formatted": "200 KB",
    "expire_days": 3,
    "expires_at": "2024-01-18 10:30:00"
  }
}</code></pre>
            </div>
        </div>

        <div class="api-endpoint">
            <div class="endpoint-header">
                <span class="method method-get">GET</span>
                <code class="endpoint-url"><?php echo $base; ?>/api/files</code>
            </div>
            <div class="endpoint-body">
                <h3>List All Files</h3>
                <p>Mendapatkan daftar semua file yang aktif (belum expired). File yang sudah expired otomatis terhapus.</p>

                <h4>Response</h4>
                <pre class="code-block"><code class="language-json">{
  "success": true,
  "count": 5,
  "data": [
    {
      "id": "a1b2c3d4_1699123456",
      "name": "a1b2c3d4_1699123456.jpg",
      "category": "image",
      "extension": "jpg",
      "size": 204800,
      "size_formatted": "200 KB",
      "url": "<?php echo $base; ?>/uploads/images/a1b2c3d4_1699123456.jpg",
      "view_url": "<?php echo $base; ?>/v/a1b2c3d4_1699123456",
      "uploaded_at": "2024-01-15 10:30:00",
      "expires_at": "2024-01-18 10:30:00",
      "expires_text": "3 hari lagi",
      "expire_days": 3
    }
  ]
}</code></pre>
            </div>
        </div>

        <div class="api-endpoint">
            <div class="endpoint-header">
                <span class="method method-get">GET</span>
                <code class="endpoint-url"><?php echo $base; ?>/api/files/{id}</code>
            </div>
            <div class="endpoint-body">
                <h3>Get File Detail</h3>
                <p>Mendapatkan detail file berdasarkan ID. Jika file sudah expired, akan return error.</p>

                <h4>Response</h4>
                <pre class="code-block"><code class="language-json">{
  "success": true,
  "data": {
    "id": "a1b2c3d4_1699123456",
    "name": "a1b2c3d4_1699123456.jpg",
    "category": "image",
    "extension": "jpg",
    "size": 204800,
    "size_formatted": "200 KB",
    "url": "<?php echo $base; ?>/uploads/images/a1b2c3d4_1699123456.jpg",
    "view_url": "<?php echo $base; ?>/v/a1b2c3d4_1699123456",
    "uploaded_at": "2024-01-15 10:30:00",
    "expires_at": "2024-01-18 10:30:00",
    "expires_text": "3 hari lagi",
    "expire_days": 3
  }
}</code></pre>
            </div>
        </div>

        <div class="api-endpoint">
            <div class="endpoint-header">
                <span class="method method-delete">DELETE</span>
                <code class="endpoint-url"><?php echo $base; ?>/api/delete/{id}</code>
            </div>
            <div class="endpoint-body">
                <h3>Delete File</h3>
                <p>Menghapus file berdasarkan ID (termasuk metadata-nya).</p>

                <h4>Response</h4>
                <pre class="code-block"><code class="language-json">{
  "success": true,
  "message": "File deleted successfully."
}</code></pre>
            </div>
        </div>

        <div class="api-note">
            <h3><i class="fa-solid fa-lightbulb"></i> Tips Penggunaan</h3>
            <ul>
                <li>Semua response dalam format JSON</li>
                <li>Content-Type untuk upload harus <code>multipart/form-data</code></li>
                <li>File ID dapat ditemukan di URL view: <code>/v/{id}</code></li>
                <li>Maksimal ukuran file adalah 100MB</li>
                <li>File otomatis terhapus setelah <code>expire_days</code> (1-5 hari)</li>
                <li>File yang sudah expired tidak bisa diakses via API maupun web</li>
            </ul>
        </div>

    </div>
</section>

<?php include 'footer.php'; ?>
