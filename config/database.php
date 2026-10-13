<?php
// ===== Koneksi PDO dengan Singleton Pattern =====
// Singleton: class ini hanya boleh punya SATU objek koneksi untuk seluruh aplikasi.
class Database
{
    private const DB_HOST = '127.0.0.1;port=3307';
    private const DB_NAME = 'inventaris_db';
    private const DB_USER = 'root';
    private const DB_PASS = '';          // XAMPP default: tanpa password

    private static ?Database $instance = null;
    private PDO $pdo;

    // private: tidak bisa dibuat dengan "new Database()" dari luar
    private function __construct()
    {
        $dsn = 'mysql:host=' . self::DB_HOST . ';dbname=' . self::DB_NAME . ';charset=utf8mb4';

        try {
            $this->pdo = new PDO($dsn, self::DB_USER, self::DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,   // error jadi exception
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,                    // prepared statement asli
            ]);
        } catch (PDOException $e) {
            error_log('Koneksi database gagal: ' . $e->getMessage());
            http_response_code(500);
            die('<h2 style="font-family:sans-serif">Koneksi database gagal</h2>'
              . '<p style="font-family:sans-serif">Pastikan MySQL sudah Start di XAMPP dan '
              . 'file <code>database/schema.sql</code> sudah diimport.</p>'
              . '<pre>' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</pre>');
        }
    }

    private function __clone() {}

    public function __wakeup()
    {
        throw new Exception('Singleton tidak boleh di-unserialize.');
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}