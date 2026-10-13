<?php
require_once 'includes/functions.php';

$kategoriList = getKategoriList();
$supplierList = getSupplierList();

$errors = [];
$produk = [
    'kode_produk' => '', 'nama_produk' => '',
    'kategori_id' => '', 'supplier_id' => '',
    'harga' => '', 'stok' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf'] ?? '')) {
        $errors[] = 'Sesi form tidak valid. Silakan muat ulang halaman.';
    }

    $result = validateProduk($_POST, $kategoriList, $supplierList);
    $errors = array_merge($errors, $result['errors']);
    $produk = $result['data'];   // simpan input agar form tidak kosong lagi

    if (empty($errors)) {
        try {
            $sql = 'INSERT INTO produk (kode_produk, nama_produk, kategori_id, supplier_id, harga, stok)
                    VALUES (:kode, :nama, :kategori, :supplier, :harga, :stok)';
            $stmt = db()->prepare($sql);
            $stmt->execute([
                ':kode'     => $produk['kode_produk'],
                ':nama'     => $produk['nama_produk'],
                ':kategori' => (int)$produk['kategori_id'],
                ':supplier' => (int)$produk['supplier_id'],
                ':harga'    => $produk['harga'],
                ':stok'     => (int)$produk['stok'],
            ]);

            setFlash('success', 'Produk "' . $produk['nama_produk'] . '" berhasil ditambahkan.');
            redirect('index.php');   // redirect pattern: cegah submit ganda saat refresh
        } catch (PDOException $ex) {
            error_log('Gagal INSERT produk: ' . $ex->getMessage());
            $errors[] = 'Gagal menyimpan data ke database. Silakan coba lagi.';
        }
    }
}

$formAction  = 'create.php';
$submitLabel = 'Simpan Produk';
$pageTitle   = 'Tambah Produk';
include 'includes/header.php';
?>

<div class="card card-form">
    <h1>Tambah Produk</h1>
    <p class="subtitle">Isi data produk baru dengan lengkap.</p>
    <?php include 'includes/form_produk.php'; ?>
</div>

<?php include 'includes/footer.php'; ?>