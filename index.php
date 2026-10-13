<?php
require_once 'includes/functions.php';

$perPage = 5;
$q       = trim($_GET['q'] ?? '');
$page    = max(1, (int)($_GET['page'] ?? 1));
$like    = '%' . $q . '%';

// Bagian query yang dipakai bersama (JOIN produk + kategori + supplier)
$from  = 'FROM produk p
          INNER JOIN kategori k ON p.kategori_id = k.id
          INNER JOIN supplier s ON p.supplier_id = s.id';
$where = 'WHERE (p.nama_produk LIKE :q1 OR p.kode_produk LIKE :q2
                 OR k.nama_kategori LIKE :q3 OR s.nama_supplier LIKE :q4)';
$searchParams = [':q1' => $like, ':q2' => $like, ':q3' => $like, ':q4' => $like];

// Hitung total data untuk pagination
$countStmt = db()->prepare("SELECT COUNT(*) $from $where");
$countStmt->execute($searchParams);
$totalRows  = (int)$countStmt->fetchColumn();
$totalPages = max(1, (int)ceil($totalRows / $perPage));
$page       = min($page, $totalPages);
$offset     = ($page - 1) * $perPage;

// Ambil data halaman ini
$sql = "SELECT p.id, p.kode_produk, p.nama_produk, p.harga, p.stok,
               k.nama_kategori, s.nama_supplier
        $from $where
        ORDER BY p.id DESC
        LIMIT :limit OFFSET :offset";
$stmt = db()->prepare($sql);
foreach ($searchParams as $name => $value) {
    $stmt->bindValue($name, $value, PDO::PARAM_STR);
}
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$produkList = $stmt->fetchAll();

// Ringkasan statistik
$statStmt = db()->prepare(
    'SELECT COUNT(*) AS total_produk,
            COALESCE(SUM(stok), 0) AS total_stok,
            COALESCE(SUM(harga * stok), 0) AS nilai_total
     FROM produk'
);
$statStmt->execute();
$stat = $statStmt->fetch();

// Pembuat link pagination (mempertahankan kata kunci pencarian)
$pageUrl = function (int $p) use ($q): string {
    $params = ['page' => $p];
    if ($q !== '') {
        $params['q'] = $q;
    }
    return 'index.php?' . http_build_query($params);
};

$no = $offset + 1;
$pageTitle = 'Daftar Produk';
include 'includes/header.php';
?>

<div class="page-head">
    <div>
        <h1>Daftar Produk</h1>
        <p class="subtitle">Kelola seluruh stok barang di gudang.</p>
    </div>
    <form method="GET" action="index.php" class="search">
        <input type="text" name="q" value="<?= e($q) ?>" placeholder="Cari produk, kategori, supplier...">
        <button type="submit" class="btn btn-primary">Cari</button>
        <?php if ($q !== ''): ?>
            <a href="index.php" class="btn btn-light">Reset</a>
        <?php endif; ?>
    </form>
</div>

<div class="stats">
    <div class="stat">
        <span class="stat-label">Total Produk</span>
        <span class="stat-value"><?= e($stat['total_produk']) ?></span>
    </div>
    <div class="stat">
        <span class="stat-label">Total Stok</span>
        <span class="stat-value"><?= e(number_format((int)$stat['total_stok'], 0, ',', '.')) ?></span>
    </div>
    <div class="stat">
        <span class="stat-label">Nilai Inventaris</span>
        <span class="stat-value"><?= e(formatRupiah($stat['nilai_total'])) ?></span>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Supplier</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($produkList)): ?>
                <tr>
                    <td colspan="8" class="empty">
                        <?= $q !== '' ? 'Tidak ada produk yang cocok dengan pencarian.' : 'Belum ada data produk.' ?>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($produkList as $p): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><code><?= e($p['kode_produk']) ?></code></td>
                    <td><strong><?= e($p['nama_produk']) ?></strong></td>
                    <td><span class="badge badge-info"><?= e($p['nama_kategori']) ?></span></td>
                    <td><?= e($p['nama_supplier']) ?></td>
                    <td><?= e(formatRupiah($p['harga'])) ?></td>
                    <td>
                        <span class="badge <?= (int)$p['stok'] <= 5 ? 'badge-danger' : 'badge-success' ?>">
                            <?= e($p['stok']) ?>
                        </span>
                    </td>
                    <td class="actions">
                        <a href="edit.php?id=<?= e($p['id']) ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="delete.php?id=<?= e($p['id']) ?>" class="btn btn-danger btn-sm">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a class="page-link" href="<?= e($pageUrl($page - 1)) ?>">&laquo; Sebelumnya</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <?php if ($i === $page): ?>
                <span class="page-link active"><?= $i ?></span>
            <?php else: ?>
                <a class="page-link" href="<?= e($pageUrl($i)) ?>"><?= $i ?></a>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a class="page-link" href="<?= e($pageUrl($page + 1)) ?>">Berikutnya &raquo;</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>