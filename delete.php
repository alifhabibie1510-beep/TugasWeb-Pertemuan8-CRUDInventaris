<?php
require_once 'includes/functions.php';

$id = validateIdParam($_GET['id'] ?? null);
if ($id === null) {
    setFlash('error', 'ID produk tidak valid.');
    redirect('index.php');
}

$produk = getProdukById($id);
if ($produk === null) {
    setFlash('error', 'Produk tidak ditemukan atau sudah dihapus.');
    redirect('index.php');
}

// Penghapusan hanya terjadi lewat POST (setelah user menekan tombol konfirmasi)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf'] ?? '')) {
        setFlash('error', 'Sesi tidak valid. Penghapusan dibatalkan.');
        redirect('index.php');
    }

    try {
        $stmt = db()->prepare('DELETE FROM produk WHERE id = :id');
        $stmt->execute([':id' => $id]);
        setFlash('success', 'Produk "' . $produk['nama_produk'] . '" berhasil dihapus.');
    } catch (PDOException $ex) {
        error_log('Gagal DELETE produk: ' . $ex->getMessage());
        setFlash('error', 'Gagal menghapus produk. Silakan coba lagi.');
    }
    redirect('index.php');
}

$pageTitle = 'Hapus Produk';
include 'includes/header.php';
?>

<div class="card card-form">
    <h1>Hapus Produk?</h1>
    <p class="subtitle">Data yang dihapus tidak bisa dikembalikan.</p>

    <div class="detail">
        <div><span>Kode</span><strong><?= e($produk['kode_produk']) ?></strong></div>
        <div><span>Nama</span><strong><?= e($produk['nama_produk']) ?></strong></div>
        <div><span>Kategori</span><strong><?= e($produk['nama_kategori']) ?></strong></div>
        <div><span>Supplier</span><strong><?= e($produk['nama_supplier']) ?></strong></div>
        <div><span>Harga</span><strong><?= e(formatRupiah($produk['harga'])) ?></strong></div>
        <div><span>Stok</span><strong><?= e($produk['stok']) ?></strong></div>
    </div>

    <form method="POST" action="delete.php?id=<?= e($id) ?>" class="form-actions">
        <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
        <a href="index.php" class="btn btn-light">Batal</a>
        <button type="submit" class="btn btn-danger">Ya, Hapus Produk</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>