<?php
/**
 * Form bersama untuk create dan edit.
 *
 * @var array<string, mixed> $produk
 * @var string[]             $errors
 * @var array<int, array>    $kategoriList
 * @var array<int, array>    $supplierList
 * @var string               $formAction
 * @var string               $submitLabel
 */
?>
<form method="POST" action="<?= e($formAction) ?>" novalidate>
    <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">

    <div class="form-grid">
        <div>
            <label for="kode_produk">Kode Produk</label>
            <input type="text" id="kode_produk" name="kode_produk"
                   value="<?= e($produk['kode_produk']) ?>" placeholder="Contoh: ELK-002" maxlength="20" required>
        </div>

        <div>
            <label for="nama_produk">Nama Produk</label>
            <input type="text" id="nama_produk" name="nama_produk"
                   value="<?= e($produk['nama_produk']) ?>" placeholder="Nama produk" maxlength="150" required>
        </div>

        <div>
            <label for="kategori_id">Kategori</label>
            <select id="kategori_id" name="kategori_id" required>
                <option value="">-- Pilih Kategori --</option>
                <?php foreach ($kategoriList as $k): ?>
                    <option value="<?= e($k['id']) ?>"
                        <?= ((string)($produk['kategori_id'] ?? '') === (string)$k['id']) ? 'selected' : '' ?>>
                        <?= e($k['nama_kategori']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="supplier_id">Supplier</label>
            <select id="supplier_id" name="supplier_id" required>
                <option value="">-- Pilih Supplier --</option>
                <?php foreach ($supplierList as $s): ?>
                    <option value="<?= e($s['id']) ?>"
                        <?= ((string)($produk['supplier_id'] ?? '') === (string)$s['id']) ? 'selected' : '' ?>>
                        <?= e($s['nama_supplier']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="harga">Harga (Rp)</label>
            <input type="number" id="harga" name="harga" min="1" step="1"
                   value="<?= e($produk['harga']) ?>" placeholder="Contoh: 150000" required>
        </div>

        <div>
            <label for="stok">Stok</label>
            <input type="number" id="stok" name="stok" min="0" step="1"
                   value="<?= e($produk['stok']) ?>" placeholder="Jumlah stok" required>
        </div>
    </div>

    <div class="form-actions">
        <a href="index.php" class="btn btn-light">Batal</a>
        <button type="submit" class="btn btn-primary"><?= e($submitLabel) ?></button>
    </div>
</form>