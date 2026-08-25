<?php
require_once 'config.php';
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Free Media Hosting</title>
    <meta name="description" content="<?php echo APP_DESCRIPTION; ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/styles/atom-one-dark.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📷</text></svg>">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="nav-brand">
                <div class="brand-icon">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                </div>
                <span class="brand-text"><?php echo APP_NAME; ?></span>
            </a>
            <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation">
                <i class="fa-solid fa-bars"></i>
            </button>
            <ul class="nav-menu" id="navMenu">
                <li class="nav-item">
                    <a href="index.php" class="nav-link <?php echo $currentPage === 'index' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-house"></i> Beranda
                    </a>
                </li>
                <li class="nav-item">
                    <a href="about.php" class="nav-link <?php echo $currentPage === 'about' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-circle-info"></i> Tentang
                    </a>
                </li>
                <li class="nav-item">
                    <a href="api-docs.php" class="nav-link <?php echo $currentPage === 'api-docs' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-code"></i> API
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    <main class="main-content">
