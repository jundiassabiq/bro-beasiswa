<?php
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/validasi.php';
require_once __DIR__ . '/tampilan.php';
if (loginAktif()) redirect('daftar.php');
$username = ''; $errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    cekCsrf();
    $username = strtolower(input('username'));
    $sandi = isset($_POST['sandi']) && is_string($_POST['sandi']) ? $_POST['sandi'] : '';
    if ($username === '') $errors['username'] = 'Username wajib diisi.';
    if ($sandi === '') $errors['sandi'] = 'Kata sandi wajib diisi.';
    if (!$errors) {
        try {
            $stmt = $db->prepare('SELECT id, nama, peran, hash_sandi FROM pengguna WHERE username = ?');
            $stmt->bind_param('s', $username); $stmt->execute();
            $akun = $stmt->get_result()->fetch_assoc();
            // Pesan sama untuk akun tidak ada maupun sandi salah.
            if (!$akun || !password_verify($sandi, $akun['hash_sandi'])) $errors['username'] = 'Username atau kata sandi salah.';
            else {
                // Ganti ID sesi sebelum menyimpan identitas yang telah diverifikasi.
                session_regenerate_id(true);
                $_SESSION['id'] = (int) $akun['id']; $_SESSION['nama'] = $akun['nama']; $_SESSION['peran'] = $akun['peran'];
                $_SESSION['csrf'] = bin2hex(random_bytes(32));
                setFlash('Selamat datang, ' . $akun['nama'] . '!');
                redirect('daftar.php');
            }
        } catch (mysqli_sql_exception $ex) { gagalDatabase($ex); }
    }
}
awalHalaman('Masuk'); ?>
<main id="konten" class="auth-layout container"><section class="auth-intro"><span class="eyebrow"><span aria-hidden="true">✦</span> BROBEASISWA</span><h1>Cari beasiswa.<br>Ikut <span class="blue-word">lomba.</span></h1><p>Cek jadwal pendaftaran dan baca syaratnya sebelum daftar.</p><div class="intro-chips"><span>↗ Beasiswa</span><span>✦ Lomba</span></div><div class="orbit-art" aria-hidden="true"><div class="orbit-ring"></div><div class="orbit-center">↗</div><span class="orbit-label one">Info beasiswa & lomba</span><span class="orbit-label two">Catat deadline-nya</span></div><p class="micro-copy">Info yang kamu cari ada di sini.</p></section>
<section class="form-panel auth-panel" aria-labelledby="login-title"><span class="eyebrow">SELAMAT DATANG KEMBALI</span><h2 id="login-title">Masuk ke akunmu<span class="blue-word">.</span></h2><p class="muted">Masukkan username dan kata sandi untuk lanjut.</p><?php flash(); errorSummary($errors); ?>
<form method="post" action="login.php" novalidate><?php csrfField(); ?><div class="field"><label for="username">Username</label><input id="username" name="username" autocomplete="username" maxlength="30" value="<?= e($username) ?>" placeholder="Masukkan username" required<?= errorAttrs($errors, 'username') ?>><?php fieldError($errors, 'username'); ?></div><div class="field"><label for="sandi">Kata sandi</label><input id="sandi" name="sandi" type="password" autocomplete="current-password" placeholder="Masukkan kata sandi" required<?= errorAttrs($errors, 'sandi') ?>><?php fieldError($errors, 'sandi'); ?></div><button class="button full" type="submit">Masuk <span aria-hidden="true">↗</span></button></form><p class="form-switch">Belum punya akun? <a href="registrasi.php">Buat akun anggota</a></p><p class="panel-note">Akses informasi beasiswa dan lomba dalam satu portal.</p></section></main>
<?php akhirHalaman(); ?>
