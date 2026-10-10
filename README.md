# BroBeasiswa

Aplikasi bank informasi beasiswa dan lomba untuk mini project Pemrograman Web I. Dibuat dengan PHP native dan MySQLi, HTML, CSS, serta JavaScript ringan. Tidak membutuhkan Composer atau npm.

## Fitur

- Registrasi anggota, login, dan logout.
- Anggota dapat membaca daftar dan detail informasi.
- Admin dapat menambahkan beasiswa dan lomba.
- Pencarian berdasarkan judul, penyelenggara, jenis, lokasi, dan jenjang.
- Pengurutan deadline terdekat/terjauh atau judul A–Z/Z–A.
- Maksimal 100 info gabungan dalam katalog, dengan 15 kartu per halaman.
- Status Dibuka, Segera dibuka, dan Ditutup berdasarkan tanggal di zona waktu Asia/Jakarta.
- Banner program, poster lomba, dan tampilan responsif.

Pendaftaran beasiswa atau lomba dilakukan melalui tautan eksternal. Aplikasi ini tidak menyediakan fitur edit dan hapus.

## Kebutuhan lokal

- XAMPP dengan PHP **8.1 atau lebih baru**, Apache, dan MySQL/MariaDB.
- Ekstensi PHP `mysqli` dan `mbstring` aktif. XAMPP yang digunakan saat pengujian memakai PHP 8.2.12 dan MariaDB 10.4.32.
- Browser modern. Internet diperlukan untuk poster dari Luar Kampus dan tautan eksternal; banner lokal tetap dapat ditampilkan tanpa internet.

## Instalasi di Windows dengan XAMPP

### 1. Siapkan folder aplikasi

Clone repository ini atau unduh melalui **Code → Download ZIP** di GitHub, kemudian letakkan **isi aplikasi** di:

```text
C:\xampp\htdocs\bank-informasi
```

Pastikan `index.php`, `config.php`, `app.sql`, dan folder `assets` langsung berada di folder tersebut. Hindari folder bertingkat seperti `bank-informasi\bank-informasi\index.php`.

### 2. Jalankan Apache dan MySQL

Buka **XAMPP Control Panel**, lalu klik **Start** untuk Apache dan MySQL.

### 3. Impor database

1. Buka [phpMyAdmin](http://localhost/phpmyadmin/).
2. Pilih tab **Import** dari halaman utama phpMyAdmin.
3. Pilih file `app.sql` di folder aplikasi.
4. Klik **Import/Go** dan tunggu sampai selesai.

File SQL membuat database **`infokesempatan`**, tabel `pengguna` dan `informasi`, akun demo, serta data awal. Nama database tetap `infokesempatan` meskipun nama aplikasi sekarang BroBeasiswa.

Impor dilakukan **sekali pada instalasi baru**. Jika database tersebut sudah berisi tabel aplikasi, jangan mengimpor ulang ke database yang sama karena akan muncul error tabel sudah ada. Untuk instalasi terpisah, ganti nama database pada perintah `CREATE DATABASE` dan `USE` dalam salinan `app.sql`, lalu gunakan nama yang sama di `config.php`.

### 4. Periksa konfigurasi

Konfigurasi default di `config.php`:

```php
const DB_HOST = '127.0.0.1';
const DB_PORT = 3306;
const DB_NAME = 'infokesempatan';
const DB_USER = 'root';
const DB_PASS = '';
```

Sesuaikan host, port, nama database, username, dan sandi jika konfigurasi MySQL lokal berbeda. Kredensial ini adalah default pengembangan lokal XAMPP.

### 5. Buka aplikasi

Buka [http://localhost/bank-informasi/](http://localhost/bank-informasi/). Pengunjung akan diarahkan ke halaman login.

Jika Apache berjalan pada port lain, gunakan port tersebut pada URL, misalnya `http://localhost:8080/bank-informasi/`.

## Akun demo

| Peran | Username | Kata sandi |
| --- | --- | --- |
| Admin | `admin` | `Admin2026!` |
| Anggota | `anggota` | `Anggota2026!` |

Akun dari formulir registrasi selalu menjadi anggota. Tombol **Tambah** hanya tersedia untuk admin; akses langsung anggota ke halaman tambah juga ditolak server.

## Mencoba aplikasi

1. Login sebagai anggota, buka detail program, lalu coba pencarian dan pilihan urutan.
2. Pindah halaman. Kata pencarian dan urutan tetap dipertahankan. Tanpa pencarian, 100 info terbagi menjadi 7 halaman: enam halaman berisi 15 kartu dan halaman terakhir 10 kartu.
3. Logout, kemudian login sebagai admin.
4. Buka **Tambah**, pilih Beasiswa atau Lomba, lalu isi kolom yang sesuai. Deadline tidak boleh sebelum hari penginputan.
5. Simpan dan cari judul informasi yang baru ditambahkan. Informasi manual admin terbaru diprioritaskan ketika memilih isi katalog.

Formulir dan navigasi halaman tetap bekerja tanpa JavaScript. JavaScript membantu menampilkan kolom khusus jenis informasi dan mengganti poster eksternal yang gagal dimuat dengan ilustrasi lokal.

## Data dan batas katalog

`app.sql` memuat snapshot **583 beasiswa dan 371 lomba** dari kalender publik [beasiswa](https://luarkampus.id/beasiswa) dan [event Luar Kampus](https://luarkampus.id/events), diambil pada **11 Oktober 2026**. Terdapat juga lima data simulasi praktikum yang disembunyikan dari katalog.

Snapshot tidak diperbarui otomatis. Detail Luar Kampus dapat meminta login. Biaya, cakupan pendanaan, bentuk peserta, atau persyaratan yang tidak tersedia pada kalender publik ditandai sebagai belum tercantum; biaya yang belum diketahui tidak dianggap gratis.

Batas 100 berlaku pada **katalog**, bukan jumlah baris database. Pemilihan mengutamakan pendaftaran yang belum berakhir, informasi manual admin terbaru, lalu data sumber dengan deadline terdekat. Jika perlu, katalog diisi dari arsip. Data di luar katalog tetap tersimpan. Pencarian hanya memeriksa 100 info yang terpilih.

## Algoritma

Implementasi berada di `algoritma.php`, disertai komentar penjelas:

- **Linear search**, kompleksitas O(n): memeriksa teks setiap informasi, tanpa membedakan huruf besar/kecil.
- **Merge sort**, kompleksitas O(n log n): membagi data, mengurutkan tiap bagian, lalu menggabungkannya. Urutan untuk nilai yang sama tetap stabil.
- **Pagination**: setelah pencarian dan sorting, `array_slice()` mengambil maksimal 15 data sesuai nomor halaman.

## Struktur utama

```text
bank-informasi/
├── assets/          # CSS, JavaScript, favicon, dan ilustrasi banner
├── algoritma.php    # Linear search, merge sort, dan tautan pagination
├── app.sql          # Skema dan snapshot data awal
├── config.php       # Konfigurasi database dan zona waktu
├── koneksi.php      # Koneksi MySQLi dan penanganan error
├── sesi.php         # Session, penjaga akses, CSRF, escaping, dan flash
├── validasi.php     # Validasi input di server
├── tampilan.php     # Navigasi, footer, dan helper tampilan
├── index.php        # Halaman masuk aplikasi
├── login.php
├── registrasi.php
├── logout.php
├── daftar.php       # Katalog, pencarian, sorting, dan pagination
├── detail.php
├── tambah.php       # Create khusus admin
├── .htaccess        # Pembatasan akses berkas pada Apache
└── README.md
```

## Pengujian terakhir

Pengujian dilakukan pada **11 Oktober 2026** dengan database QA terpisah, termasuk instalasi dari `app.sql` pada database baru. Data aplikasi utama tidak digunakan untuk percobaan Create.

- 39 pemeriksaan HTTP untuk autentikasi, validasi, akses admin, CSRF, escaping XSS, ID detail, deadline, logout, formulir tanpa JavaScript, dan kegagalan koneksi.
- 17 pemeriksaan HTTP untuk pencarian, sorting, pagination, parameter tidak valid, dan batas 100 info.
- 19 pemeriksaan tambahan, termasuk info baru dengan deadline jauh, akses anggota, validasi sandi, input pencarian berbahaya, dan header keamanan.
- 8 pemeriksaan Apache untuk ketersediaan halaman/aset dan pembatasan akses berkas SQL serta dokumen.
- Pemeriksaan sintaks seluruh file PHP dan pengujian stabilitas merge sort serta pencarian Unicode.

Seluruh **83 pemeriksaan HTTP** tersebut lulus. Informasi baru dengan deadline jauh yang sebelumnya tidak masuk katalog sudah diperbaiki. Tampilan dan interaksi browser diperiksa pada viewport 390, 768, dan 1280 piksel, termasuk fokus keyboard, kolom formulir sesuai jenis, penyimpanan melalui browser, dan pengganti poster yang gagal dimuat.

## Jika aplikasi belum berjalan

| Masalah | Yang perlu diperiksa |
| --- | --- |
| URL tidak terbuka | Apache sudah berjalan; port dan lokasi folder sesuai. Buka lewat `http://localhost/`, bukan dengan klik langsung file PHP. |
| Halaman 404 | `index.php` berada langsung di `htdocs\bank-informasi`, bukan di subfolder tambahan. |
| Layanan sementara tidak tersedia | MySQL aktif, `app.sql` berhasil diimpor, dan kredensial `config.php` benar. Periksa `C:\xampp\apache\logs\error.log` untuk detail. |
| Error tabel sudah ada saat impor | Gunakan database baru untuk instalasi terpisah; jangan menimpa database yang masih dipakai. |
| Fungsi `mb_*` atau `mysqli` tidak ditemukan | Aktifkan ekstensi terkait di `C:\xampp\php\php.ini`, lalu restart Apache. |
| Login akun demo gagal | Periksa username/sandi pada tabel di atas dan pastikan data akun dari `app.sql` sudah masuk. |
| Poster tidak muncul | Periksa internet. Ilustrasi lokal menjadi pengganti jika poster sumber gagal dimuat. |
| Hasil pencarian kosong | Coba nama kampus, jenis informasi, atau sebagian judul. Pencarian dibatasi isi katalog 100 info. |

Tautan eksternal dan ketentuan program mengikuti situs sumber. Selalu periksa sumber sebelum mendaftar.
