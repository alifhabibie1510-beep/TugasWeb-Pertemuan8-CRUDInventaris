# CRUD Inventaris - Tugas Rutin 8

Aplikasi manajemen inventaris barang berbasis **PHP Native**, **PDO (Singleton Pattern)**, dan **MySQL/MariaDB**. Dibuat untuk mata kuliah Pemrograman Web.

## Fitur

- Create, Read, Update, Delete (CRUD) data produk
- Relasi 3 tabel: `produk`, `kategori`, `supplier` (Foreign Key)
- Halaman daftar produk menggunakan JOIN (menampilkan nama kategori dan supplier)
- Dropdown kategori dan supplier pada form tambah/edit
- Form edit terisi otomatis (pre-filled) dengan data lama
- Konfirmasi sebelum menghapus data
- Semua query memakai prepared statements (aman dari SQL Injection)
- Output memakai `htmlspecialchars()` lewat fungsi `e()` (aman dari XSS)
- Token CSRF pada form
- Flash message sukses/gagal dengan redirect pattern
- Bonus: pencarian dan pagination

## Dokumentasi CRUD dan Dashboard
1. List produk
![alt text](<Screenshot 2026-10-13 141645.png>)

2. Tambah produk
![alt text](<WhatsApp Image 2026-10-13 at 14.13.55.jpeg>)

3. Edit produk
![alt text](<WhatsApp Image 2026-10-13 at 14.14.27.jpeg>)

4. Hapus produk
![alt text](<WhatsApp Image 2026-10-13 at 14.15.01.jpeg>)

## Teknologi

| Komponen | Keterangan |
|---|---|
| Bahasa | PHP 8.2 |
| Database | MariaDB 10.4 (XAMPP) |
| Koneksi | PDO + Singleton Pattern |
| Server | Apache (XAMPP) |
| Tampilan | HTML + CSS (`assets/style.css`) |

## Struktur Folder

```
TR8_CRUDInventaris/
├── assets/
│   └── style.css          # Tampilan aplikasi
├── config/
│   └── database.php       # Koneksi PDO (Singleton)
├── database/
│   └── schema.sql         # Struktur tabel + data seed
├── includes/
│   ├── footer.php         # Bagian bawah halaman
│   ├── form_produk.php    # Form bersama untuk create dan edit
│   ├── functions.php      # Fungsi bantu: e(), flash, redirect, CSRF, validasi
│   └── header.php         # Bagian atas halaman
├── create.php             # Tambah produk
├── delete.php             # Hapus produk
├── edit.php               # Ubah produk
├── index.php              # Daftar produk
└── README.md
```

## Kebutuhan Sistem

- XAMPP (Apache, MySQL/MariaDB, PHP 8.x)
- Browser (Chrome, Edge, atau Firefox)
- Editor kode, disarankan Visual Studio Code

## Instalasi

### 1. Letakkan proyek di folder htdocs

Salin folder `TR8_CRUDInventaris` ke:

```
C:\xampp\htdocs\
```

Hasil akhirnya: `C:\xampp\htdocs\TR8_CRUDInventaris\`

> Jika proyek berada di drive lain, buat junction lewat Command Prompt (Run as administrator):
> ```
> mklink /J "C:\xampp\htdocs\TR8_CRUDInventaris" "D:\Tugas pemroweb\TR8_CRUDInventaris"
> ```

### 2. Jalankan Apache dan MySQL

1. Buka **XAMPP Control Panel**.
2. Klik **Start** pada **Apache**.
3. Klik **Start** pada **MySQL**.
4. Pastikan kedua modul berwarna **hijau**.

### 3. Sesuaikan port MySQL (jika bukan 3306)

Pada pengembangan proyek ini, MySQL XAMPP berjalan di **port 3307** karena port 3306 dipakai program lain. Jika port MySQL kamu **3306 (default)**, ubah `config/database.php`:

```php
private const DB_HOST = '127.0.0.1;port=3307';
```

Jika kamu memakai port lain, ganti angkanya sesuai port MySQL kamu. Agar phpMyAdmin ikut terhubung, tambahkan baris berikut di `C:\xampp\phpMyAdmin\config.inc.php` (di bawah baris `host`):

```php
$cfg['Servers'][$i]['port'] = '3307';
```

### 4. Import database

1. Buka phpMyAdmin di **http://localhost/phpmyadmin/**
2. Klik tab **Import**.
3. Klik **Choose File**, pilih `database/schema.sql`.
4. Klik **Import** di bagian bawah halaman.
5. Database **`inventaris_db`** beserta tabel dan data seed akan terbentuk otomatis.

Alternatif lewat terminal (sesuaikan port):

```powershell
cmd /c "C:\xampp\mysql\bin\mysql.exe -u root -h 127.0.0.1 -P 3307 < database\schema.sql"
```

## Cara Membuka phpMyAdmin

1. Pastikan **Apache** dan **MySQL** hijau di XAMPP.
2. Buka browser, lalu akses:
```
   http://localhost/phpmyadmin/
```
   (Atau klik tombol **Admin** pada baris MySQL di XAMPP Control Panel.)
3. Login otomatis sebagai `root` tanpa password.
4. Klik **inventaris_db** di panel kiri untuk melihat tabel `kategori`, `produk`, dan `supplier`.
5. Klik nama tabel, lalu tab **Browse** untuk melihat isi datanya.

## Cara Menjalankan Aplikasi

Buka di browser:

```
http://localhost/TR8_CRUDInventaris/
```

Halaman utama menampilkan