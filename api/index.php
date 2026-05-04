<?php

// 1. Definisikan folder sementara yang bisa ditulis di Vercel
$storagePath = '/tmp/storage';
$viewPath = $storagePath . '/framework/views';

// 2. Buat struktur folder secara paksa
if (!is_dir($viewPath)) {
    mkdir($viewPath, 0777, true);
}

// 3. Override folder storage Laravel secara global
// Ini akan memindahkan views, sessions, dan cache ke /tmp
putenv("APP_STORAGE=$storagePath");
putenv("VIEW_COMPILED_PATH=$viewPath");

// 4. Pastikan Laravel tidak menggunakan file cache lama dari build
putenv("APP_CONFIG_CACHE=/tmp/config.php");
putenv("APP_ROUTES_CACHE=/tmp/routes.php");
putenv("APP_SERVICES_CACHE=/tmp/services.php");
putenv("APP_PACKAGES_CACHE=/tmp/packages.php");

// 5. Muat aplikasi utama
require __DIR__ . '/../public/index.php';