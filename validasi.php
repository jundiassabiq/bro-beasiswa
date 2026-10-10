<?php
const JENIS = ['beasiswa', 'lomba'];
const PENDANAAN = ['Penuh', 'Sebagian'];
const PESERTA = ['Individu', 'Tim'];
function input(string $name): string {
    // Request berbentuk array juga ditolak dengan aman, tanpa TypeError.
    $value = $_POST[$name] ?? '';
    return is_string($value) ? trim($value) : '';
}
function panjang(string $label, string $value, int $min, int $max): ?string {
    if ($value === '') return "$label wajib diisi.";
    $n = mb_strlen($value, 'UTF-8');
    return $n < $min || $n > $max ? "$label harus {$min}–{$max} karakter." : null;
}
function pilihan(string $label, string $value, array $allowed): ?string {
    // Daftar putih server menolak nilai palsu walaupun berasal dari <select>.
    return in_array($value, $allowed, true) ? null : "$label harus dipilih dari pilihan yang tersedia.";
}
function validasiInformasi(array $v, ?string $today = null): array {
    $errors = [];
    foreach (['judul' => 'Judul', 'penyelenggara' => 'Penyelenggara', 'persyaratan' => 'Persyaratan', 'deskripsi' => 'Deskripsi'] as $key => $label) {
        $error = panjang($label, $v[$key], in_array($key, ['judul', 'penyelenggara']) ? 3 : 1, in_array($key, ['judul', 'penyelenggara']) ? 150 : 2000);
        if ($error) $errors[$key] = $error;
    }
    if ($error = pilihan('Jenis informasi', $v['jenis'], JENIS)) $errors['jenis'] = $error;
    // Null byte dari request palsu juga menghasilkan error kolom, bukan exception.
    $date = !str_contains($v['deadline'], "\0") ? DateTimeImmutable::createFromFormat('!Y-m-d', $v['deadline']) : false;
    if (!$date || $date->format('Y-m-d') !== $v['deadline']) $errors['deadline'] = 'Deadline wajib berupa tanggal yang valid.';
    elseif ($v['deadline'] < ($today ?? date('Y-m-d'))) $errors['deadline'] = 'Deadline tidak boleh sebelum hari ini.';
    $scheme = strtolower((string) parse_url($v['url_pendaftaran'], PHP_URL_SCHEME));
    if (mb_strlen($v['url_pendaftaran']) > 2000 || !filter_var($v['url_pendaftaran'], FILTER_VALIDATE_URL) || !in_array($scheme, ['http', 'https'], true)) $errors['url_pendaftaran'] = 'Tautan pendaftaran wajib berupa URL HTTP atau HTTPS yang valid (maksimal 2000 karakter).';
    if ($v['jenis'] === 'beasiswa') {
        if ($error = pilihan('Cakupan pendanaan', $v['pendanaan'], PENDANAAN)) $errors['pendanaan'] = $error;
    } elseif ($v['jenis'] === 'lomba') {
        $biaya = $v['biaya'];
        if (!preg_match('/^\d{1,9}$/D', $biaya) || (int) $biaya > 100000000) $errors['biaya'] = 'Biaya harus rupiah bulat antara 0 dan 100.000.000.';
        if ($error = pilihan('Bentuk peserta', $v['peserta'], PESERTA)) $errors['peserta'] = $error;
    }
    return $errors;
}
