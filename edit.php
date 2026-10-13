<?php
require_once 'includes/functions.php';

$id = validateIdParam($_GET['id'] ?? null);
if ($id === null) {
    setFlash('error', 'ID produk tidak valid.');
    redirect('index.php');
}

$produk = getProdukById($id);
if ($produk === null) {
    setFlash('error', 'Produk tidak ditemukan.');
    redirect('index.php');
}

$kategoriList = getKategoriList();
$supplierList = getSupplierList();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf'] ?? '')) {
        $errors[] = 'Sesi form tidak valid. Silakan muat ulang halaman.';
    }

    $result = validateProduk($_POST, $kategoriList, $supplierList, $id);
    $errors = array_merge($errors, $result['errors']);
    $produk = array_merge($produk, $result['data']);   // tampilkan input terbaru

    if (empty($errors)) {
        try {
            $sql = 'UPDATE produk
                    SET kode_produk = :kode, nama_produk = :nama,
                        kategori_id = :kategori, supplier_id = :supplier,
                        harga = :harga, stok = :stok
                    WHERE id = :id';
            $stmt = db()->prepare($sql);
            $stmt->execute([
                ':kode'     => $produk['kode_produk'],
                ':nama'     => $produk['nama_produk'],
                ':kategori' => (int)$produk['kategori_id'],
                ':supplier' => (int)$produk['supplier_id'],
                ':harga'    => $produk['harga'],
                ':stok'     => (int)$produk['stok'],
                ':id'       => $id,
            ]);

            setFlash('success', 'Produk "' . $produk['nama_produk'] . '" berhasil diperbarui.');
            redirect('index.php');
        } catch (PDOException $ex) {
            error_log('Gagal UPDATE produk: ' . $ex->getMessage());
            $errors[] = 'Gagal memperbarui data. Silakan coba lagi.';
        }
    }
}

$formAction  = 'edit.php?id=' . $id;
$submitLabel = 'Simpan Perubahan';
$pageTitle   = 'Edit Produk';
include 'includes/header.php';
?>

<div class="card card-form">
    <h1>Edit Produk</h1>
    <p class="subtitle">Ubah data produk <strong><?= e($produk['nama_produk']) ?></strong>.</p>
    <?php include 'includes/form_produk.php'; ?>
</div>

<?php include 'includes/footer.php'; ?>