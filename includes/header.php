<?php
// $pageTitle diisi dari masing-masing halaman
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Inventaris') ?> | TR8 Inventaris</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="topbar">
    <div class="topbar-inner">
        <a class="brand" href="index.php">📦 Inventaris</a>
        <nav>
            <a href="index.php">Daftar Produk</a>
            <a href="create.php" class="btn btn-primary btn-sm">+ Tambah Produk</a>
        </nav>
    </div>
</header>
<main class="container">
<?php if ($flash): ?>
    <div class="alert alert-<?= e($flash['type']) ?>">
        <?= e($flash['message']) ?>
    </div>
<?php endif; ?>