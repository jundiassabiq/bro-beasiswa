<?php
require_once __DIR__ . '/sesi.php';
function awalHalaman(string $title): void { ?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?= e($title) ?> · BroBeasiswa</title><meta name="theme-color" content="#eafaff"><link rel="icon" href="assets/favicon.svg" type="image/svg+xml"><link rel="stylesheet" href="assets/style.css?v=bro-20261011-1"><script src="assets/app.js?v=bro-20261011-1" defer></script></head><body>
<a class="skip" href="#konten">Lewati ke konten</a>
<header class="site-header container"><nav class="navbar" aria-label="Navigasi utama"><a class="brand" href="index.php"><span class="brand-mark" aria-hidden="true">↗</span><span>BroBeasiswa<span class="brand-dot">.</span></span></a><div class="nav-links">
<?php if (loginAktif()): ?><a class="nav-link" href="daftar.php">Informasi</a><?php if (adminAktif()): ?><a class="button small" href="tambah.php">Tambah <span aria-hidden="true">+</span></a><?php endif; ?><span class="user-label"><?= e($_SESSION['nama']) ?><span class="role-label"><?= e($_SESSION['peran']) ?></span></span><form action="logout.php" method="post"><?php csrfField(); ?><button class="nav-link logout" type="submit">Keluar <span aria-hidden="true">↗</span></button></form>
<?php else: ?><a class="nav-link" href="login.php">Masuk</a><a class="button small" href="registrasi.php">Buat akun <span aria-hidden="true">↗</span></a><?php endif; ?></div></nav></header>
<?php }
function akhirHalaman(): void { ?><footer class="footer container"><a class="brand footer-brand" href="index.php"><span class="brand-mark" aria-hidden="true">↗</span>BroBeasiswa.</a><p>Info beasiswa dan lomba.</p><span>BroBeasiswa © 2026</span></footer></body></html><?php }
function flash(): void { if ($message = ambilFlash()) echo '<div class="flash" role="status"><span aria-hidden="true">✓</span> ' . e($message) . '</div>'; }
function fieldError(array $errors, string $name): void { if (isset($errors[$name])) echo '<p class="field-error" id="error-' . e($name) . '">' . e($errors[$name]) . '</p>'; }
function errorAttrs(array $errors, string $name): string { return isset($errors[$name]) ? ' aria-invalid="true" aria-describedby="error-' . e($name) . '"' : ''; }
function errorSummary(array $errors): void { if ($errors): ?><div class="error-summary" role="alert"><strong>Periksa kembali isianmu.</strong><ul><?php foreach ($errors as $key => $message): ?><li><a href="#<?= e($key) ?>"><?= e($message) ?></a></li><?php endforeach; ?></ul></div><?php endif; }
function tanggal(string $value): string { $months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; $d = new DateTimeImmutable($value); return $d->format('j') . ' ' . $months[(int)$d->format('n') - 1] . ' ' . $d->format('Y'); }
function masihBuka(string $deadline, ?string $today = null): bool { return $deadline >= ($today ?? date('Y-m-d')); }
function statusBadge(string $deadline, ?string $mulai = null): void {
    $open = masihBuka($deadline); $soon = $open && $mulai && $mulai > date('Y-m-d');
    ?><span class="status <?= $soon ? 'soon' : ($open ? 'open' : 'closed') ?>"><span aria-hidden="true">●</span> <?= $soon ? 'Segera dibuka' : ($open ? 'Dibuka' : 'Ditutup') ?></span><?php
}
function biayaLabel($amount): string { return $amount === null ? 'Belum tercantum di sumber' : uang((int)$amount); }
function posterAman(?string $url): ?string {
    // URL dari impor tetap dibatasi ke lokasi poster yang teramati di sumber.
    return $url && preg_match('~^https://luarkampus\.id/storage/event/[A-Za-z0-9._/-]+$~D', $url) ? $url : null;
}
function uang(int $amount): string { return $amount === 0 ? 'Gratis' : 'Rp' . number_format($amount, 0, ',', '.'); }

