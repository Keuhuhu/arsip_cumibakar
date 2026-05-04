<?php

// 1. Tentukan rute folder sementara di /tmp (satu-satunya area yang bisa ditulisi di Vercel)
$compiledViewPath = '/tmp/storage/framework/views';

// 2. Buat foldernya secara otomatis jika belum ada
if (!is_dir($compiledViewPath)) {
    mkdir($compiledViewPath, 0777, true);
}

// 3. Paksa Laravel untuk menggunakan folder sementara ini sebagai tempat pemrosesan View/Blade
$_ENV['VIEW_COMPILED_PATH'] = $compiledViewPath;
putenv('VIEW_COMPILED_PATH=' . $compiledViewPath);

// 4. Lanjutkan memuat aplikasi utama Laravel
require __DIR__ . '/../public/index.php';