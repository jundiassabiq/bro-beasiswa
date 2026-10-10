<?php
// Algoritma dipisah dari tampilan agar mudah dipelajari dan diuji.
const URUTAN = [
    'deadline_asc' => 'Deadline terdekat',
    'deadline_desc' => 'Deadline terjauh',
    'judul_asc' => 'Judul A–Z',
    'judul_desc' => 'Judul Z–A',
];

function teksPencarian(string $value): string {
    return mb_strtolower(preg_replace('/\s+/u', ' ', trim($value)) ?? '', 'UTF-8');
}

// Linear search: periksa setiap informasi satu kali, O(n).
function cariInformasi(array $rows, string $query): array {
    $needle = teksPencarian($query);
    if ($needle === '') return $rows;
    $found = [];
    foreach ($rows as $row) {
        $text = implode(' ', [$row['judul'], $row['penyelenggara'], $row['jenis'], $row['lokasi'] ?? '', $row['jenjang'] ?? '']);
        if (mb_strpos(teksPencarian($text), $needle, 0, 'UTF-8') !== false) $found[] = $row;
    }
    return $found;
}

function bandingInformasi(array $a, array $b, string $order): int {
    $field = str_starts_with($order, 'judul_') ? 'judul' : 'deadline';
    $cmp = strcmp(teksPencarian($a[$field]), teksPencarian($b[$field]));
    return str_ends_with($order, '_desc') ? -$cmp : $cmp;
}

// Merge sort: bagi array menjadi dua, urutkan, lalu gabungkan, O(n log n).
// Nilai yang sama diambil dari sisi kiri lebih dulu agar urutan tetap stabil.
function urutInformasi(array $rows, string $order): array {
    if (count($rows) < 2) return $rows;
    if (!isset(URUTAN[$order])) $order = 'deadline_asc';
    $mid = intdiv(count($rows), 2);
    $left = urutInformasi(array_slice($rows, 0, $mid), $order);
    $right = urutInformasi(array_slice($rows, $mid), $order);
    $result = []; $i = 0; $j = 0;
    while ($i < count($left) && $j < count($right)) {
        if (bandingInformasi($left[$i], $right[$j], $order) <= 0) $result[] = $left[$i++];
        else $result[] = $right[$j++];
    }
    while ($i < count($left)) $result[] = $left[$i++];
    while ($j < count($right)) $result[] = $right[$j++];
    return $result;
}

function tautanHalaman(int $page, string $query, string $order): string {
    return 'daftar.php?' . http_build_query(['q' => $query, 'urut' => $order, 'halaman' => $page]) . '#informasi';
}
