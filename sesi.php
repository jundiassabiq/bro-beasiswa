<?php
require_once __DIR__ . '/config.php';
ini_set('session.use_strict_mode', '1');
session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax', 'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off']);
session_start();
// Menyelaraskan sesi demo yang dibuat sebelum nama aplikasi berubah.
if (($_SESSION['nama'] ?? '') === 'Admin InfoKesempatan') $_SESSION['nama'] = 'Admin BroBeasiswa';
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'self'; style-src 'self'; script-src 'self'; img-src 'self' data: https://luarkampus.id; base-uri 'self'; form-action 'self'; frame-ancestors 'none'");
header('Cache-Control: no-store');

// Escaping dilakukan saat keluar ke HTML, bukan saat menyimpan ke database.
function e($value): string { return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function redirect(string $path): never { header('Location: ' . $path, true, 303); exit; }
function setFlash(string $message): void { $_SESSION['flash'] = $message; }
function ambilFlash(): ?string { $message = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $message; }
function loginAktif(): bool { return isset($_SESSION['id']); }
function adminAktif(): bool { return loginAktif() && ($_SESSION['peran'] ?? '') === 'admin'; }
function wajibLogin(): void { if (!loginAktif()) redirect('login.php'); }
function wajibAdmin(): void {
    wajibLogin();
    // Menyembunyikan tombol saja tidak cukup: GET maupun POST dijaga server.
    if (!adminAktif()) halamanError(403, 'Akses khusus admin', 'Akun anggota dapat membaca informasi. Penambahan informasi hanya tersedia untuk admin.');
}
function csrfToken(): string { return $_SESSION['csrf'] ??= bin2hex(random_bytes(32)); }
function csrfValid(): bool { return isset($_POST['csrf']) && is_string($_POST['csrf']) && hash_equals(csrfToken(), $_POST['csrf']); }
function csrfField(): void { echo '<input type="hidden" name="csrf" value="' . e(csrfToken()) . '">'; }
function cekCsrf(): void { if (!csrfValid()) halamanError(403, 'Formulir kedaluwarsa', 'Muat ulang halaman lalu kirim formulir kembali.'); }
function halamanError(int $code, string $title, string $message): never {
    http_response_code($code);
    require_once __DIR__ . '/tampilan.php';
    awalHalaman($title);
    echo '<main id="konten" class="error-page container"><span class="eyebrow">HTTP ' . e($code) . '</span><h1>' . e($title) . '</h1><p>' . e($message) . '</p><a class="button" href="daftar.php">Kembali ke informasi <span aria-hidden="true">↗</span></a></main>';
    akhirHalaman(); exit;
}
