<?php
require_once __DIR__ . '/sesi.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Allow: POST'); halamanError(405, 'Gunakan tombol keluar', 'Keluar dari akun melalui tombol Keluar pada navigasi.'); }
cekCsrf();
$_SESSION = [];
$cookie = session_get_cookie_params();
setcookie(session_name(), '', ['expires' => time() - 42000, 'path' => $cookie['path'], 'domain' => $cookie['domain'], 'secure' => $cookie['secure'], 'httponly' => true, 'samesite' => 'Lax']);
session_destroy();
redirect('login.php');
