<?php
require_once __DIR__ . '/sesi.php'; wajibLogin();
require_once __DIR__ . '/koneksi.php'; require_once __DIR__ . '/tampilan.php';
require_once __DIR__ . '/algoritma.php';
try {
    // Maksimal 100 data: utamakan pendaftaran aktif dan info manual admin terbaru.
    // Setelah pemilihan katalog, algoritma PHP menangani pencarian dan urutan pilihan.
    // Data simulasi lama tetap tersimpan, tetapi tidak dicampur dengan katalog publik.
    $rows = $db->query("SELECT i.*, p.nama AS penginput FROM informasi i JOIN pengguna p ON p.id = i.pengguna_id WHERE NOT (i.id <= 5 AND i.url_pendaftaran LIKE 'https://example.com/%') ORDER BY (i.deadline >= CURDATE()) DESC, (i.sumber_url IS NULL) DESC, CASE WHEN i.sumber_url IS NULL THEN i.dibuat_pada END DESC, CASE WHEN i.sumber_url IS NULL THEN i.id END DESC, CASE WHEN i.deadline >= CURDATE() THEN i.deadline END ASC, CASE WHEN i.deadline < CURDATE() THEN i.deadline END DESC, i.id DESC LIMIT 100")->fetch_all(MYSQLI_ASSOC);
} catch (mysqli_sql_exception $ex) { gagalDatabase($ex); }
$today = date('Y-m-d');
$aktif = array_values(array_filter($rows, fn($r) => $r['deadline'] >= $today));

$beasiswa = count(array_filter($aktif, fn($r) => $r['jenis'] === 'beasiswa'));
$lomba = count($aktif) - $beasiswa;
$jumlahBeasiswa = count(array_filter($rows, fn($r) => $r['jenis'] === 'beasiswa'));
$jumlahLomba = count($rows) - $jumlahBeasiswa;
$scholarships = array_values(array_filter($aktif, fn($r) => $r['jenis'] === 'beasiswa' && (!$r['mulai'] || $r['mulai'] <= $today)));
$competitions = array_values(array_filter($aktif, fn($r) => $r['jenis'] === 'lomba' && (!$r['mulai'] || $r['mulai'] <= $today)));
$featured = $scholarships[0] ?? null; $competition = $competitions[0] ?? null;
// Parameter GET berbentuk array atau pilihan palsu tidak dipakai.
$query = isset($_GET['q']) && is_string($_GET['q']) ? mb_substr(trim($_GET['q']), 0, 100, 'UTF-8') : '';
$order = isset($_GET['urut']) && is_string($_GET['urut']) && isset(URUTAN[$_GET['urut']]) ? $_GET['urut'] : 'deadline_asc';
$hasil = urutInformasi(cariInformasi($rows, $query), $order);
$total = count($hasil); $perPage = 15;
$pages = max(1, (int)ceil($total / $perPage));
$rawPage = $_GET['halaman'] ?? '1';
$page = is_string($rawPage) ? filter_var($rawPage, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) : false;
$page = min($pages, $page === false ? 1 : $page);
$offset = ($page - 1) * $perPage;
$cards = array_slice($hasil, $offset, $perPage);

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
<?php if ($query === '' && $page === 1 && $aktif): ?><section class="feature-grid container" aria-label="Sorotan beasiswa dan lomba">
<?php if ($featured): ?><a class="feature feature-main" href="detail.php?id=<?= e($featured['id']) ?>"><div class="feature-text"><span class="feature-label">INFO BEASISWA</span><h2><?= e($featured['judul']) ?></h2><p><?= e($featured['jenjang'] ?: 'Cek persyaratan di halaman sumber') ?></p><span class="feature-cta">Baca detail <span aria-hidden="true">↗</span></span></div><img src="assets/banner-beasiswa.svg" alt="" width="480" height="360"><span class="feature-sticker">Daftar sampai<br><strong><?= e(tanggal($featured['deadline'])) ?></strong></span></a><?php endif; ?>
<?php if ($competition): ?><a class="feature feature-competition" href="detail.php?id=<?= e($competition['id']) ?>"><div class="feature-text"><span class="feature-label">ADA LOMBA NIH</span><h3><?= e($competition['judul']) ?></h3><span class="feature-small-date"><?= e(tanggal($competition['deadline'])) ?> <span aria-hidden="true">↗</span></span></div><img src="assets/banner-lomba.svg" alt="" width="300" height="230"></a><?php endif; ?>
<a class="feature feature-reminder" href="#informasi"><div class="feature-text"><span class="feature-label">JANGAN LEWAT DEADLINE</span><h3>Masih sempat daftar.</h3><p><?= e($beasiswa) ?> beasiswa dan <?= e($lomba) ?> lomba belum melewati batas pendaftaran.</p><span class="feature-small-date">Cek daftar di bawah <span aria-hidden="true">↓</span></span></div><img src="assets/banner-deadline.svg" alt="" width="300" height="230"></a>
</section><?php endif; ?>
<section id="informasi" class="listing container" aria-labelledby="list-title"><?php flash(); ?><div class="section-heading"><div><span class="eyebrow">BROBEASISWA</span><h2 id="list-title">Beasiswa & lomba<span class="blue-word">.</span></h2><p>Katalog dibatasi 100 info gabungan. Pilih urutan atau cari yang kamu butuhkan.</p></div><div class="catalog-counts"><span><?= e($jumlahBeasiswa) ?> beasiswa</span><span><?= e($jumlahLomba) ?> lomba</span></div></div>

<form class="catalog-tools" method="get" action="daftar.php#informasi" role="search" aria-label="Cari beasiswa dan lomba">
<div class="search-field"><label for="q">Cari info</label><div class="search-input-wrap"><span aria-hidden="true">⌕</span><input id="q" name="q" type="search" maxlength="100" value="<?= e($query) ?>" placeholder="Nama beasiswa, lomba, kampus…"></div></div>
<div class="sort-field"><label for="urut">Urutkan</label><select id="urut" name="urut"><?php foreach (URUTAN as $key => $label): ?><option value="<?= e($key) ?>"<?= $order === $key ? ' selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?></select></div>
<button class="button" type="submit">Tampilkan <span aria-hidden="true">↗</span></button>
</form>
<div class="results-heading"><p role="status"><?php if ($total): ?>Menampilkan <strong><?= e($offset + 1) ?>–<?= e(min($offset + $perPage, $total)) ?></strong> dari <strong><?= e($total) ?></strong> info<?php else: ?><strong>0 info ditemukan</strong><?php endif; ?><?php if ($query !== ''): ?> untuk “<?= e($query) ?>”<?php endif; ?>.</p><?php if ($query !== '' || $order !== 'deadline_asc'): ?><a class="reset-link" href="daftar.php#informasi">Reset pencarian</a><?php endif; ?></div>
<?php if (!$cards): ?><div class="empty-state"><h3>Belum ketemu.</h3><p>Coba kata lain, misalnya nama kampus atau “beasiswa”.</p><a class="button secondary" href="daftar.php#informasi">Lihat semua info</a></div><?php else: kartuInformasi($cards); endif; ?>
<?php if ($pages > 1): ?><nav class="pagination" aria-label="Halaman katalog">
<?php if ($page > 1): ?><a class="page-nav" href="<?= e(tautanHalaman($page - 1, $query, $order)) ?>">← Sebelumnya</a><?php else: ?><span class="page-nav disabled" aria-disabled="true">← Sebelumnya</span><?php endif; ?>
<div class="page-numbers"><?php for ($n = 1; $n <= $pages; $n++): ?><?php if ($n === $page): ?><span class="page-number current" aria-current="page" aria-label="Halaman <?= e($n) ?>, aktif"><?= e($n) ?></span><?php else: ?><a class="page-number" href="<?= e(tautanHalaman($n, $query, $order)) ?>" aria-label="Halaman <?= e($n) ?>"><?= e($n) ?></a><?php endif; ?><?php endfor; ?></div>
<?php if ($page < $pages): ?><a class="page-nav" href="<?= e(tautanHalaman($page + 1, $query, $order)) ?>">Berikutnya →</a><?php else: ?><span class="page-nav disabled" aria-disabled="true">Berikutnya →</span><?php endif; ?>
</nav><?php endif; ?>
<div class="catalog-source"><p>Ringkasan dari kalender <a href="https://luarkampus.id/beasiswa" target="_blank" rel="noopener noreferrer">beasiswa</a> dan <a href="https://luarkampus.id/events" target="_blank" rel="noopener noreferrer">event Luar Kampus</a>. Ketentuan bisa berubah; cek halaman sumber sebelum mendaftar.</p></div>
</section></main>
<?php akhirHalaman(); ?>
