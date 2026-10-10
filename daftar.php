<?php
require_once __DIR__ . '/sesi.php'; wajibLogin();
require_once __DIR__ . '/koneksi.php'; require_once __DIR__ . '/tampilan.php';
try {
    // Data simulasi lama tetap tersimpan, tetapi tidak dicampur dengan katalog publik.
    $rows = $db->query("SELECT i.*, p.nama AS penginput FROM informasi i JOIN pengguna p ON p.id = i.pengguna_id WHERE i.url_pendaftaran NOT LIKE 'https://example.com/%' ORDER BY i.deadline ASC, i.id DESC")->fetch_all(MYSQLI_ASSOC);
} catch (mysqli_sql_exception $ex) { gagalDatabase($ex); }
$today = date('Y-m-d');
$aktif = array_values(array_filter($rows, fn($r) => $r['deadline'] >= $today));
$arsip = array_values(array_filter($rows, fn($r) => $r['deadline'] < $today));
usort($arsip, fn($a, $b) => strcmp($b['deadline'], $a['deadline']) ?: ($b['id'] <=> $a['id']));
$beasiswa = count(array_filter($aktif, fn($r) => $r['jenis'] === 'beasiswa'));
$lomba = count($aktif) - $beasiswa;
$scholarships = array_values(array_filter($aktif, fn($r) => $r['jenis'] === 'beasiswa' && (!$r['mulai'] || $r['mulai'] <= $today)));
$competitions = array_values(array_filter($aktif, fn($r) => $r['jenis'] === 'lomba' && (!$r['mulai'] || $r['mulai'] <= $today)));
$featured = $scholarships[0] ?? null; $competition = $competitions[0] ?? null;
function kartuInformasi(array $list): void { ?>
<div class="card-grid"><?php foreach ($list as $row): $poster = posterAman($row['poster_url'] ?? null); ?>
<article class="opportunity-card <?= e($row['jenis']) ?>">
<a class="card-cover <?= $poster ? 'has-poster' : '' ?>" href="detail.php?id=<?= e($row['id']) ?>" tabindex="-1" aria-hidden="true"><img src="<?= e($poster ?? ('assets/banner-' . $row['jenis'] . '.svg')) ?>" alt="" data-fallback="<?= e('assets/banner-' . $row['jenis'] . '.svg') ?>" loading="lazy" decoding="async" width="480" height="220"><span class="cover-tag"><?= e($row['jenis'] === 'beasiswa' ? 'BEASISWA' : 'LOMBA') ?></span></a>
<div class="card-body"><div class="card-top"><span class="kind"><?= e(ucfirst($row['jenis'])) ?></span><?php statusBadge($row['deadline'], $row['mulai'] ?? null); ?></div>
<h3><a href="detail.php?id=<?= e($row['id']) ?>"><?= e($row['judul']) ?></a></h3>
<?php if ($row['penyelenggara'] !== 'Belum tercantum di kalender publik'): ?><p class="organizer"><?= e($row['penyelenggara']) ?></p><?php endif; ?>
<p class="card-description"><?= e($row['jenjang'] ?: mb_strimwidth($row['deskripsi'], 0, 110, '…', 'UTF-8')) ?></p>
<div class="card-meta"><span class="meta-label">BATAS PENDAFTARAN</span><strong><?= e(tanggal($row['deadline'])) ?></strong><span class="benefit"><?= e($row['lokasi'] ?: ($row['jenis'] === 'beasiswa' ? ($row['pendanaan'] ? 'Pendanaan ' . strtolower($row['pendanaan']) : 'Lihat ketentuan pendanaan di sumber') : biayaLabel($row['biaya']))) ?></span></div>
<div class="card-bottom"><span class="byline"><?= $row['sumber_url'] ? 'Sumber: <b>Luar Kampus</b>' : 'Oleh <b>' . e($row['penginput']) . '</b>' ?></span><a class="detail-link" href="detail.php?id=<?= e($row['id']) ?>" aria-label="<?= e('Baca info ' . $row['judul']) ?>">Baca info <span aria-hidden="true">↗</span></a></div></div></article>
<?php endforeach; ?></div><?php }
awalHalaman('Beasiswa & lomba'); ?>
<main id="konten">
<section class="hero container bro-hero"><h1>Cari beasiswa dan lomba<br> <span class="blue-word">yang pas buat kamu.</span></h1><p>Cek jadwal pendaftaran, baca syaratnya, lalu daftar lewat tautan sumber.</p></section>
<section class="feature-grid container" aria-label="Sorotan beasiswa dan lomba">
<?php if ($featured): ?><a class="feature feature-main" href="detail.php?id=<?= e($featured['id']) ?>"><div class="feature-text"><span class="feature-label">INFO BEASISWA</span><h2><?= e($featured['judul']) ?></h2><p><?= e($featured['jenjang'] ?: 'Cek persyaratan di halaman sumber') ?></p><span class="feature-cta">Baca detail <span aria-hidden="true">↗</span></span></div><img src="assets/banner-beasiswa.svg" alt="" width="480" height="360"><span class="feature-sticker">Daftar sampai<br><strong><?= e(tanggal($featured['deadline'])) ?></strong></span></a><?php endif; ?>
<?php if ($competition): ?><a class="feature feature-competition" href="detail.php?id=<?= e($competition['id']) ?>"><div class="feature-text"><span class="feature-label">ADA LOMBA NIH</span><h3><?= e($competition['judul']) ?></h3><span class="feature-small-date"><?= e(tanggal($competition['deadline'])) ?> <span aria-hidden="true">↗</span></span></div><img src="assets/banner-lomba.svg" alt="" width="300" height="230"></a><?php endif; ?>
<a class="feature feature-reminder" href="#informasi"><div class="feature-text"><span class="feature-label">JANGAN LEWAT DEADLINE</span><h3>Masih sempat daftar.</h3><p><?= e($beasiswa) ?> beasiswa dan <?= e($lomba) ?> lomba belum melewati batas pendaftaran.</p><span class="feature-small-date">Cek daftar di bawah <span aria-hidden="true">↓</span></span></div><img src="assets/banner-deadline.svg" alt="" width="300" height="230"></a>
</section>
<section id="informasi" class="listing container" aria-labelledby="list-title"><?php flash(); ?><div class="section-heading"><div><span class="eyebrow">BROBEASISWA</span><h2 id="list-title">Beasiswa & lomba<span class="blue-word">.</span></h2><p>Diurutkan dari batas pendaftaran terdekat.</p></div><div class="catalog-counts"><span><?= e($beasiswa) ?> beasiswa</span><span><?= e($lomba) ?> lomba</span></div></div>
<?php if (!$aktif): ?><div class="empty-state"><h3>Belum ada pendaftaran yang dibuka.</h3><p>Informasi yang sudah berakhir bisa dibaca di arsip.</p></div><?php else: kartuInformasi($aktif); endif; ?>
<?php if ($arsip): ?><details class="archive"><summary><span><strong>Arsip beasiswa & lomba</strong><small>Pendaftaran sudah berakhir. Bisa dibaca untuk referensi.</small></span><span class="count-pill"><?= e(count($arsip)) ?> info <span aria-hidden="true">+</span></span></summary><?php kartuInformasi($arsip); ?></details><?php endif; ?>
<div class="catalog-source"><p>Ringkasan dari kalender <a href="https://luarkampus.id/beasiswa" target="_blank" rel="noopener noreferrer">beasiswa</a> dan <a href="https://luarkampus.id/events" target="_blank" rel="noopener noreferrer">event Luar Kampus</a>. Ketentuan bisa berubah; cek halaman sumber sebelum mendaftar.</p></div>
</section></main>
<?php akhirHalaman(); ?>
