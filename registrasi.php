<?php
require_once __DIR__ . '/koneksi.php'; require_once __DIR__ . '/validasi.php'; require_once __DIR__ . '/tampilan.php';
if (loginAktif()) redirect('daftar.php');
$v = ['username' => '', 'nama' => '']; $errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    cekCsrf(); $v['username'] = strtolower(input('username')); $v['nama'] = input('nama');
    $sandi = isset($_POST['sandi']) && is_string($_POST['sandi']) ? $_POST['sandi'] : '';
    $konfirmasi = isset($_POST['konfirmasi']) && is_string($_POST['konfirmasi']) ? $_POST['konfirmasi'] : '';
    if ($error = panjang('Username', $v['username'], 3, 30)) $errors['username'] = $error;
    elseif (!preg_match('/^[a-z0-9_]+$/D', $v['username'])) $errors['username'] = 'Username hanya boleh berisi huruf kecil, angka, dan garis bawah.';
    if ($error = panjang('Nama', $v['nama'], 3, 100)) $errors['nama'] = $error;
    // Bcrypt membatasi 72 bita; menolak input lebih panjang mencegah pemotongan diam-diam.
    if (mb_strlen($sandi) < 8 || strlen($sandi) > 72 || str_contains($sandi, "\0")) $errors['sandi'] = 'Kata sandi minimal 8 karakter dan maksimal 72 bita.';
    if ($konfirmasi === '' || $konfirmasi !== $sandi) $errors['konfirmasi'] = 'Konfirmasi kata sandi harus sama.';
    if (!$errors) {
        try {
            $hash = password_hash($sandi, PASSWORD_DEFAULT);
            // Peran ditentukan server. Pendaftar tidak dapat membuat dirinya admin.
            $stmt = $db->prepare("INSERT INTO pengguna (username, nama, peran, hash_sandi) VALUES (?, ?, 'anggota', ?)");
            $stmt->bind_param('sss', $v['username'], $v['nama'], $hash); $stmt->execute();
            setFlash('Akun berhasil dibuat. Silakan masuk.'); redirect('login.php');
        } catch (mysqli_sql_exception $ex) {
            // UNIQUE menjaga keunikan bahkan jika dua pendaftaran terjadi bersamaan.
            if ($ex->getCode() === 1062) $errors['username'] = 'Username sudah dipakai. Pilih username lain.';
            else gagalDatabase($ex);
        }
    }
}
awalHalaman('Buat akun'); ?>
<main id="konten" class="auth-layout container"><section class="auth-intro"><span class="eyebrow">✦ GABUNG BROBEASISWA</span><h1>Lagi cari<br><span class="blue-word">beasiswa?</span></h1><p>Buat akun untuk membaca informasi beasiswa dan lomba, lengkap dengan persyaratan dan deadline.</p><div class="intro-chips"><span>Beasiswa & lomba</span><span>Jadwal pendaftaran</span></div><p class="micro-copy">Akun baru mendapat akses sebagai anggota.</p></section><section class="form-panel auth-panel" aria-labelledby="register-title"><span class="eyebrow">MULAI DI SINI</span><h2 id="register-title">Buat akun baru<span class="blue-word">.</span></h2><p class="muted">Isi data di bawah untuk membuat akun.</p><?php errorSummary($errors); ?><form method="post" action="registrasi.php" novalidate><?php csrfField(); ?>
<?php foreach (['nama' => ['Nama lengkap', 'Nama lengkapmu', 100], 'username' => ['Username', 'Contoh: mahasiswa_01', 30]] as $key => $meta): ?><div class="field"><label for="<?= e($key) ?>"><?= e($meta[0]) ?></label><input id="<?= e($key) ?>" name="<?= e($key) ?>" autocomplete="<?= $key === 'nama' ? 'name' : 'username' ?>" maxlength="<?= e($meta[2]) ?>" placeholder="<?= e($meta[1]) ?>" value="<?= e($v[$key]) ?>" required<?= errorAttrs($errors, $key) ?>><?php fieldError($errors, $key); ?></div><?php endforeach; ?>
<div class="field"><label for="sandi">Kata sandi</label><input id="sandi" name="sandi" type="password" autocomplete="new-password" placeholder="Minimal 8 karakter" required<?= errorAttrs($errors, 'sandi') ?>><?php fieldError($errors, 'sandi'); ?></div><div class="field"><label for="konfirmasi">Konfirmasi kata sandi</label><input id="konfirmasi" name="konfirmasi" type="password" autocomplete="new-password" placeholder="Ulangi kata sandi" required<?= errorAttrs($errors, 'konfirmasi') ?>><?php fieldError($errors, 'konfirmasi'); ?></div><button type="submit" class="button full">Buat akun <span aria-hidden="true">↗</span></button></form><p class="form-switch">Sudah punya akun? <a href="login.php">Masuk sekarang</a></p></section></main>
<?php akhirHalaman(); ?>
