/**
 * oma - Main JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    if (window.hljs) {
        document.querySelectorAll('pre code').forEach((block) => window.hljs.highlightElement(block));
    }

    
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.getElementById('navMenu');

    if (navToggle && navMenu) {
        navToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            const icon = navToggle.querySelector('i');
            if (navMenu.classList.contains('active')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-xmark');
            } else {
                icon.classList.remove('fa-xmark');
                icon.classList.add('fa-bars');
            }
        });

        
        navMenu.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                navMenu.classList.remove('active');
                navToggle.querySelector('i').classList.remove('fa-xmark');
                navToggle.querySelector('i').classList.add('fa-bars');
            });
        });
    }

    // ===== File Upload =====
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('fileInput');
    const filePreview = document.getElementById('filePreview');
    const previewName = document.getElementById('previewName');
    const previewSize = document.getElementById('previewSize');
    const previewIcon = document.getElementById('previewIcon');
    const previewRemove = document.getElementById('previewRemove');
    const uploadBtn = document.getElementById('uploadBtn');
    const expireRange = document.getElementById('expire_days');
    const expireValue = document.getElementById('expireValue');
    const uploadForm = document.getElementById('uploadForm');
    const uploadProgress = document.getElementById('uploadProgress');
    const progressBar = document.getElementById('progressBar');
    const progressPercent = document.getElementById('progressPercent');
    const progressLabel = document.getElementById('progressLabel');
    const progressDetail = document.getElementById('progressDetail');
    const progressTrack = document.getElementById('progressTrack');

    if (uploadArea && fileInput) {
        if (expireRange && expireValue) {
            const updateExpireValue = function() {
                expireValue.textContent = expireRange.value + ' hari';
                expireRange.style.setProperty('--range-progress', ((expireRange.value - expireRange.min) / (expireRange.max - expireRange.min) * 100) + '%');
            };
            expireRange.addEventListener('input', updateExpireValue);
            updateExpireValue();
        }

        // Click to browse
        uploadArea.addEventListener('click', function(e) {
            if (e.target !== previewRemove && !previewRemove.contains(e.target)) {
                fileInput.click();
            }
        });

        // Drag & Drop
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            uploadArea.addEventListener(eventName, function() {
                uploadArea.classList.add('dragover');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, function() {
                uploadArea.classList.remove('dragover');
            }, false);
        });

        uploadArea.addEventListener('drop', function(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files.length > 0) {
                fileInput.files = files;
                handleFileSelect(files[0]);
            }
        }, false);

        // File input change
        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                handleFileSelect(this.files[0]);
            }
        });

        // Remove file
        if (previewRemove) {
            previewRemove.addEventListener('click', function(e) {
                e.stopPropagation();
                fileInput.value = '';
                filePreview.style.display = 'none';
                uploadArea.style.display = 'block';
                uploadBtn.disabled = true;
            });
        }

        function handleFileSelect(file) {
            if (file.size > 100 * 1024 * 1024) {
                showUploadError('File terlalu besar. Ukuran maksimal adalah 100MB.');
                fileInput.value = '';
                return;
            }

            // Update preview
            previewName.textContent = file.name;
            previewSize.textContent = formatFileSize(file.size);

            // Set icon based on file type
            const ext = file.name.split('.').pop().toLowerCase();
            const imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico'];
            const videoExts = ['mp4', 'webm', 'ogg', 'mov', 'mkv', 'avi'];
            const audioExts = ['mp3', 'wav', 'ogg', 'flac', 'aac', 'm4a', 'wma'];

            previewIcon.className = 'fa-solid ';
            if (imageExts.includes(ext)) {
                previewIcon.classList.add('fa-image');
            } else if (videoExts.includes(ext)) {
                previewIcon.classList.add('fa-video');
            } else if (audioExts.includes(ext)) {
                previewIcon.classList.add('fa-music');
            } else {
                previewIcon.classList.add('fa-file');
            }

            filePreview.style.display = 'block';
            uploadArea.style.display = 'none';
            uploadBtn.disabled = false;
        }

        if (uploadForm) {
            uploadForm.addEventListener('submit', function(e) {
                e.preventDefault();
                if (!fileInput.files.length || uploadBtn.disabled) return;

                const xhr = new XMLHttpRequest();
                const formData = new FormData(uploadForm);
                uploadBtn.disabled = true;
                uploadBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Mengupload...';
                uploadProgress.hidden = false;
                setProgress(0, 'Menghubungkan ke server...', 'Menyiapkan file Anda');

                xhr.upload.addEventListener('progress', function(event) {
                    if (!event.lengthComputable) {
                        setProgress(0, 'Mengupload file...', 'Server sedang menerima file');
                        return;
                    }
                    const percent = Math.round((event.loaded / event.total) * 100);
                    setProgress(percent, percent >= 100 ? 'Memproses file...' : 'Mengupload file...', formatFileSize(event.loaded) + ' dari ' + formatFileSize(event.total));
                });

                xhr.addEventListener('load', function() {
                    let result;
                    try {
                        result = JSON.parse(xhr.responseText);
                    } catch (error) {
                        showUploadError('Server mengirim respons yang tidak valid (' + xhr.status + ').');
                        resetUploadButton();
                        return;
                    }

                    if (xhr.status >= 200 && xhr.status < 300 && result.success) {
                        setProgress(100, 'Upload selesai!', 'Link berhasil dibuat');
                        showUploadSuccess(result.data);
                        resetUploadButton();
                    } else {
                        showUploadError(result.message || 'Upload gagal. Silakan coba lagi.');
                        resetUploadButton();
                    }
                });

                xhr.addEventListener('error', function() {
                    showUploadError('Koneksi terputus. Periksa koneksi lalu coba lagi.');
                    resetUploadButton();
                });

                xhr.open('POST', uploadForm.action || 'api/upload');
                xhr.setRequestHeader('Accept', 'application/json');
                xhr.send(formData);
            });
        }

        function setProgress(percent, label, detail) {
            progressBar.style.width = percent + '%';
            progressBar.classList.remove('progress-error');
            progressPercent.textContent = percent + '%';
            progressLabel.textContent = label;
            progressDetail.textContent = detail;
            progressTrack.setAttribute('aria-valuenow', percent);
        }

        function resetUploadButton() {
            uploadBtn.disabled = false;
            uploadBtn.innerHTML = '<i class="fa-solid fa-upload"></i> Upload Sekarang';
        }

        function showUploadError(message) {
            uploadProgress.hidden = false;
            setProgress(0, 'Upload belum berhasil', message);
            progressBar.classList.add('progress-error');
            if (window.Swal) {
                window.Swal.fire({
                    icon: 'error',
                    title: 'Upload gagal',
                    text: message,
                    confirmButtonText: 'Mengerti',
                    confirmButtonColor: '#6366f1'
                });
            }
        }

        function showUploadSuccess(data) {
            const link = data.raw_url || data.direct_url || data.url;
            const alert = document.createElement('div');
            alert.className = 'alert alert-success upload-result';
            alert.innerHTML = '<i class="fa-solid fa-circle-check"></i><span><strong>Upload berhasil!</strong> <a href="' + link + '" target="_blank" rel="noopener">Buka media langsung</a><br><code>' + link + '</code><br><small>Media akan otomatis terhapus setelah ' + data.expire_days + ' hari.</small></span>';
            const existingResult = uploadForm.parentElement.querySelector('.upload-result');
            if (existingResult) existingResult.remove();
            uploadForm.parentElement.appendChild(alert);
            showToast('Upload berhasil. Link media siap dibagikan.');
        }
    }

    function formatFileSize(bytes) {
        if (bytes >= 1073741824) return (bytes / 1073741824).toFixed(2) + ' GB';
        if (bytes >= 1048576) return (bytes / 1048576).toFixed(2) + ' MB';
        if (bytes >= 1024) return (bytes / 1024).toFixed(2) + ' KB';
        return bytes + ' B';
    }

    // ===== Copy Link =====
    window.copyLink = function(url) {
        navigator.clipboard.writeText(url).then(function() {
            showToast('Link berhasil disalin!');
        }).catch(function() {
            // Fallback
            const textarea = document.createElement('textarea');
            textarea.value = url;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            showToast('Link berhasil disalin!');
        });
    };

    // ===== Toast Notification =====
    function showToast(message, icon = 'success') {
        if (!window.Swal) return;
        window.Swal.fire({
            toast: true,
            position: 'bottom-end',
            icon: icon,
            title: message,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            background: '#1e293b',
            color: '#f1f5f9'
        });
    }

    // ===== Auto-hide alerts =====
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        if (alert.classList.contains('alert-success')) {
            setTimeout(() => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }, 8000);
        }
    });

    // ===== Navbar scroll effect =====
    let lastScroll = 0;
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.navbar');
        const currentScroll = window.pageYOffset;

        if (currentScroll > 50) {
            navbar.style.boxShadow = '0 4px 20px rgba(0,0,0,0.2)';
        } else {
            navbar.style.boxShadow = 'none';
        }

        lastScroll = currentScroll;
    });
});
