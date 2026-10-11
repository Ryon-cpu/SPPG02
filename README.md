# SPPG02

Web dokumentasi Program Makan Bergizi Gratis (MBG). Aplikasi ini menyediakan informasi program untuk publik dan panel admin untuk mengelola konten.

## Fitur

- Halaman publik untuk melihat ringkasan program, laporan, galeri, data penerima, dan berita.
- Panel admin untuk mengelola laporan, galeri, data penerima, dan berita.
- Autentikasi admin dengan proteksi role admin.
- Upload dan penyajian media dari penyimpanan aplikasi.

## Teknologi

- Laravel 13
- PHP 8.3 atau lebih baru
- Blade, Vite, dan Tailwind CSS
- SQLite (konfigurasi bawaan) atau MySQL

## Persiapan

Pastikan PHP, Composer, Node.js, dan npm sudah terpasang. Dari folder project, jalankan:

```powershell
composer install
Copy-Item .env.example .env
```

Konfigurasi database pada `.env`. Untuk MySQL, atur `DB_CONNECTION=mysql` beserta `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD`. Jika menggunakan SQLite bawaan, buat file database terlebih dahulu:

```powershell
New-Item -ItemType File -Path database\database.sqlite
```

Lanjutkan dengan membuat application key, menjalankan migrasi, dan membangun aset frontend:

```powershell
php artisan key:generate
php artisan migrate
npm install
npm run build
```

Jalankan server pengembangan:

```powershell
php artisan serve
```

Buka alamat yang ditampilkan oleh Artisan. Halaman publik tersedia di `/`, sedangkan halaman login admin tersedia di `/admin/login`. Akun admin harus sudah tersedia di database.

## Pengujian

```powershell
composer test
```

## Repository

Source code: [github.com/Ryon-cpu/SPPG02](https://github.com/Ryon-cpu/SPPG02)
