<?php
// ===== Fungsi bantu: session, flash, CSRF, validasi, query dropdown =====
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

/* ---------- Koneksi ---------- */
function db(): PDO
{
    return Database::getInstance()->getConnection();
}

/* ---------- Output aman (htmlspecialchars) ---------- */
function e($text): string
{
    return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8');
}

function formatRupiah($angka): string
{
    return 'Rp ' . number_format((float)$angka, 0, ',', '.');
}

/* ---------- Redirect & Flash message ---------- */
function redirect(string $url): void
{
    header("Location: $url");
    exit;
}

function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/* ---------- CSRF ---------- */
function csrfToken(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function verifyCsrf($token): bool
{
    return isset($_SESSION['csrf']) && is_string($token) && hash_equals($_SESSION['csrf'], $token);
}

/* ---------- Query (semua pakai prepared statement) ---------- */
function getKategoriList(): array
{
    $stmt = db()->prepare('SELECT id, nama_kategori FROM kategori ORDER BY nama_kategori ASC');
    $stmt->execute();
    return $stmt->fetchAll();
}

function getSupplierList(): array
{
    $stmt = db()->prepare('SELECT id, nama_supplier FROM supplier ORDER BY nama_supplier ASC');
    $stmt->execute();
    return $stmt->fetchAll();
}

function getProdukById(int $id): ?array
{
    $sql = 'SELECT p.*, k.nama_kategori, s.nama_supplier
            FROM produk p
            INNER JOIN kategori k ON p.kategori_id = k.id
            INNER JOIN supplier s ON p.supplier_id = s.id
            WHERE p.id = :id';
    $stmt = db()->prepare($sql);
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

/* ---------- Validasi ---------- */
// Memastikan parameter ID di URL benar-benar angka bulat positif
function validateIdParam($value): ?int
{
    $id = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    return $id === false ? null : $id;
}

// Dipakai bersama oleh create.php dan edit.php
// Mengembalikan ['errors' => [...], 'data' => [...]]
function validateProduk(array $input, array $kategoriList, array $supplierList, ?int $ignoreId = null): array
{
    $get = fn(string $key): string => is_string($input[$key] ?? null) ? trim($input[$key]) : '';

    $data = [
        'kode_produk' => strtoupper($get('kode_produk')),
        'nama_produk' => $get('nama_produk'),
        'kategori_id' => $get('kategori_id'),
        'supplier_id' => $get('supplier_id'),
        'harga'       => $get('harga'),
        'stok'        => $get('stok'),
    ];
    $errors = [];

    // Kode produk
    if ($data['kode_produk'] === '') {
        $errors[] = 'Kode produk wajib diisi.';
    } elseif (!preg_match('/^[A-Z0-9\-]{3,20}$/', $data['kode_produk'])) {
        $errors[] = 'Kode produk 3-20 karakter: huruf, angka, atau tanda hubung (-).';
    } else {
        $sql = 'SELECT COUNT(*) FROM produk WHERE kode_produk = :kode';
        $params = [':kode' => $data['kode_produk']];
        if ($ignoreId !== null) {
            $sql .= ' AND id <> :id';
            $params[':id'] = $ignoreId;
        }
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        if ((int)$stmt->fetchColumn() > 0) {
            $errors[] = 'Kode produk sudah dipakai produk lain.';
        }
    }

    // Nama produk
    if ($data['nama_produk'] === '') {
        $errors[] = 'Nama produk wajib diisi.';
    } elseif (mb_strlen($data['nama_produk']) < 3 || mb_strlen($data['nama_produk']) > 150) {
        $errors[] = 'Nama produk harus 3 sampai 150 karakter.';
    }

    // Kategori (cek benar-benar ada di database)
    $kategoriIds = array_map('intval', array_column($kategoriList, 'id'));
    if ($data['kategori_id'] === '') {
        $errors[] = 'Kategori wajib dipilih.';
    } elseif (!in_array((int)$data['kategori_id'], $kategoriIds, true)) {
        $errors[] = 'Kategori yang dipilih tidak valid.';
    }

    // Supplier
    $supplierIds = array_map('intval', array_column($supplierList, 'id'));
    if ($data['supplier_id'] === '') {
        $errors[] = 'Supplier wajib dipilih.';
    } elseif (!in_array((int)$data['supplier_id'], $supplierIds, true)) {
        $errors[] = 'Supplier yang dipilih tidak valid.';
    }

    // Harga
    if ($data['harga'] === '') {
        $errors[] = 'Harga wajib diisi.';
    } elseif (!preg_match('/^\d{1,12}$/', $data['harga'])) {
        $errors[] = 'Harga harus angka bulat tanpa titik/koma (maksimal 12 digit).';
    } elseif ((int)$data['harga'] < 1) {
        $errors[] = 'Harga harus lebih dari 0.';
    }

    // Stok
    if ($data['stok'] === '') {
        $errors[] = 'Stok wajib diisi.';
    } elseif (!preg_match('/^\d{1,9}$/', $data['stok'])) {
        $errors[] = 'Stok harus angka bulat 0 atau lebih.';
    }

    return ['errors' => $errors, 'data' => $data];
}