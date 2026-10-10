<?php
require_once __DIR__ . '/sesi.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    $db->set_charset('utf8mb4');
    $db->query("SET time_zone = '+07:00'");
} catch (mysqli_sql_exception $ex) {
    // Detail teknis hanya masuk log Apache; pengguna menerima pesan umum.
    error_log('[BroBeasiswa koneksi] ' . $ex->getMessage());
    halamanError(503, 'Layanan sementara tidak tersedia', 'Koneksi data belum tersedia. Pastikan MySQL aktif dan app.sql sudah diimpor.');
}
function gagalDatabase(mysqli_sql_exception $ex): never {
    error_log('[BroBeasiswa kueri] ' . $ex->getMessage());
    halamanError(503, 'Data belum dapat dimuat', 'Terjadi kendala saat memproses data. Silakan coba kembali.');
}
