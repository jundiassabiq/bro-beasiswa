INFOKESEMPATAN
Bank Informasi Beasiswa dan Lomba — Mini Project Pemrograman Web I

MENJALANKAN DARI AWAL
1. Gunakan XAMPP dengan PHP 8.1+ dan MySQL/MariaDB. Aktifkan Apache dan MySQL.
2. Letakkan folder bank-informasi di C:\xampp\htdocs\bank-informasi.
3. Buka http://localhost/phpmyadmin/ lalu pilih Import dan impor app.sql.
   SQL membuat database infokesempatan beserta tabel dan data awal.
   Impor sekali pada database baru; tidak ada DROP atau penghapusan data.
4. Buka http://localhost/bank-informasi/.
5. Jika kredensial database berbeda, sesuaikan config.php.
   Default XAMPP lokal: host 127.0.0.1, port 3306, user root, sandi kosong.
   Ekstensi PHP mysqli dan mbstring harus aktif.

AKUN UJI LOKAL
Admin   : username admin   | kata sandi Admin2026!
Anggota : username anggota | kata sandi Anggota2026!
Akun dari registrasi selalu menjadi anggota. Peran tidak dapat dipilih lewat form.
Kata sandi di database disimpan sebagai hash; README memuat sandi akun demo saja.

DATA CONTOH
Tersedia tiga beasiswa dan dua lomba. Semua program, penyelenggara, serta tautan
example.com adalah simulasi untuk praktikum, bukan peluang yang benar-benar dibuka.
Deadline data contoh dihitung relatif terhadap tanggal impor.

ALUR APLIKASI
- Registrasi, login, dan logout.
- Anggota/admin membaca seluruh daftar dan halaman detail.
- Admin menambah informasi beasiswa atau lomba.
- Pendaftaran dilakukan di situs penyelenggara melalui tautan eksternal.
- Status Dibuka sampai tanggal deadline (inklusif); setelahnya menjadi Ditutup.
- Informasi kedaluwarsa tetap terlihat sebagai arsip. Zona waktu Asia/Jakarta.
- Tidak ada fitur edit, hapus, pencarian, filter, pagination, atau transaksi.

STRUKTUR
config.php                  : kredensial database dan zona waktu.
koneksi.php                 : MySQLi, utf8mb4, penanganan kegagalan database.
sesi.php                    : session, e(), penjaga akses, CSRF, flash, redirect.
validasi.php                : pembacaan input dan validasi server.
tampilan.php                : navigasi, footer, helper tampilan dan pesan error.
index.php                   : mengarahkan ke login atau daftar.
registrasi.php / login.php   : pendaftaran dan autentikasi.
logout.php                  : logout via POST.
daftar.php / detail.php      : Read dengan JOIN nama penginput.
tambah.php                  : Create khusus admin dengan PRG.
assets/                     : CSS, JavaScript ringan, dan favicon buatan sendiri.
app.sql                     : skema relasi dan data awal.
PENGUJIAN.docx              : skenario, hasil aktual, dan screenshot.

VALIDASI
Judul dan penyelenggara 3–150 karakter. Persyaratan/deskripsi wajib dan maksimal
2.000 karakter per kolom. Deadline wajib valid dan tidak sebelum hari penginputan.
URL wajib HTTP/HTTPS maksimal 2.000 karakter. Pilihan jenis/pendanaan/peserta
divalidasi whitelist. Biaya lomba bilangan rupiah bulat 0–100.000.000.
Username 3–30 huruf kecil/angka/garis bawah. Nama 3–100 karakter.
Kata sandi minimal 8 karakter, maksimal 72 bita karena batas bcrypt PHP 8.2.
Isian kata sandi tidak dicetak kembali. Kolom jenis lain disimpan NULL.

KEAMANAN DAN LOG
Input SQL memakai prepared statement. Semua data HTML memakai e().
ID penginput dari session; session_regenerate_id(true) setelah login.
Username UNIQUE ditangani dengan kode 1062; akses admin dijaga untuk GET/POST.
Form POST menggunakan token CSRF. Script/style hanya dimuat dari aplikasi sendiri.
Pesan teknis database masuk log PHP/Apache (C:\xampp\apache\logs\error.log).
Konfigurasi ini ditujukan untuk praktikum lokal, bukan layanan publik.

DESAIN
Arah visual mengacu https://meetingiq.shadcn.io/: biru muda, judul tebal,
navigasi dan tombol membulat, kartu putih, serta bayangan tegas.
Implementasi HTML/CSS sendiri; logo, copy, dan aset MeetingIQ tidak disalin.
Font sistem Segoe UI/Arial. Tidak membutuhkan npm, CDN, framework, atau internet
untuk memuat tampilan. CSS mendukung reduced motion dan navigasi keyboard.

PENGUMPULAN
Isi folder ini dapat dikompres menjadi NIM_NamaLengkap_MiniProject.zip.
Sesuaikan nama arsip dengan identitas mahasiswa dan format laporan dengan Eldiru.
Pelajari komentar pada alur autentikasi, whitelist, prepared statement, JOIN,
escaping, dan PRG agar mampu menjelaskan logika program saat penilaian.

PEMBARUAN WEBSITE 11 OKTOBER 2026
Nama aplikasi: BroBeasiswa.
Kalender publik Luar Kampus 2026: 583 beasiswa dan 371 lomba unik.
Program kedaluwarsa muncul dalam arsip; data simulasi lama disembunyikan dari katalog.
Data publik tidak memuat biaya, bentuk peserta, dan cakupan pendanaan lengkap. Kolom yang belum diketahui bernilai NULL dan ditampilkan secara jujur. Detail sumber meminta login ke Luar Kampus.
Poster lomba menggunakan URL sumber dengan fallback ilustrasi lokal. Banner utama dibuat khusus dalam SVG.
Snapshot seluruh data tersedia dalam app.sql. Validasi formulir admin tetap mewajibkan kolom sesuai jenis.
Laporan pengujian dan ZIP terdahulu belum diperbarui karena fokus saat ini adalah website.
