# Titik Temu Kopi POS

Aplikasi Point of Sale berbasis Laravel 12 untuk operasional kedai kopi. UI menggunakan Blade, Bootstrap 5 melalui CDN, dan Bootstrap Icons.

## Fitur

- Login dan logout dengan role admin dan kasir.
- Dashboard berisi ringkasan omzet, pesanan lunas, menu terlaris, stok rendah, grafik omzet 7 hari, dan transaksi terbaru.
- Manajemen kategori dan produk untuk admin.
- Transaksi POS multi-item dengan tipe dine-in atau takeaway, diskon, pajak 10%, dan metode pembayaran tunai, QRIS, transfer bank, atau debit.
- Struk transaksi yang dapat dicetak dari browser.
- Laporan penjualan dengan ekspor CSV untuk admin.
- Tabel resep dan bahan baku tersedia sebagai fondasi; UI resep dan pengurangan bahan otomatis belum diterapkan.

## Persyaratan

- PHP 8.2 atau lebih baru dan Composer.
- Database MySQL atau SQLite (default).
- Node.js dan npm hanya diperlukan bila ingin membangun aset frontend Vite. Halaman aplikasi saat ini memuat Bootstrap dari CDN.

## Instalasi

Clone repository, lalu jalankan dari folder project:

```bash
composer install
```

Salin `.env.example` menjadi `.env`, lalu buat application key:

```bash
php artisan key:generate
```

### SQLite (default)

Pastikan file database tersedia. Di PowerShell:

```powershell
New-Item -ItemType File -Force database/database.sqlite
```

Biarkan konfigurasi `DB_CONNECTION=sqlite` pada `.env`, lalu jalankan migrasi dan data demo:

```bash
php artisan migrate --seed
php artisan serve
```

Buka http://127.0.0.1:8000.

### MySQL (opsional)

Buat database `titik_temu_pos`, lalu atur nilai berikut di `.env`:

```env
APP_NAME="Titik Temu Kopi POS"
APP_URL=http://127.0.0.1:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=titik_temu_pos
DB_USERNAME=root
DB_PASSWORD=
```

Kemudian jalankan `php artisan migrate --seed` dan `php artisan serve`.

## Akun demo

- Admin: `admin@titiktemu.test` / `password`
- Kasir: `kasir@titiktemu.test` / `password`

Seeder membuat atau memperbarui akun serta kategori dan produk demo. Ganti password demo sebelum aplikasi digunakan sungguhan.

## Alur penggunaan

1. Masuk sebagai admin untuk melihat dashboard serta mengelola kategori dan produk.
2. Masuk sebagai kasir untuk membuat transaksi.
3. Pilih tipe pesanan, tambahkan produk dan jumlahnya, lalu tentukan diskon serta metode pembayaran.
4. Setelah transaksi tersimpan, struk dapat dicetak dari halaman struk.
5. Admin dapat membuka laporan dan mengunduh CSV.

## Struktur penting

- `routes/web.php`: route dan middleware role.
- `app/Http/Controllers`: autentikasi, dashboard, kategori, produk, POS, dan laporan.
- `app/Models`: model dan relasi data.
- `database/migrations`: skema database.
- `database/seeders/DatabaseSeeder.php`: akun serta data demo.
- `resources/views`: tampilan Blade.

## Integritas transaksi dan batasan

- Harga dan total dihitung ulang di server; nilai dari browser tidak dipercaya.
- Pembuatan order, detail transaksi, dan pengurangan stok berlangsung di dalam `DB::transaction()`.
- Stok produk dikunci dengan `lockForUpdate()` dan diperiksa sebelum dikurangi.
- Invoice mengikuti pola `INV-POS-YYYYMMDD-XXXX`, dengan `XXXX` berupa 4 karakter acak alfanumerik dan constraint unik di database.
- Stok yang dikelola adalah stok produk/menu. Modul resep dan bahan baku masih berupa fondasi.
- Aplikasi ini belum menangani printer thermal langsung, audit trail, void/refund dengan approval, rate limiting khusus, atau aturan pajak daerah yang kompleks.

## Pengujian

Jalankan test suite dengan:

```bash
php artisan test
```

## Aset frontend

Untuk menjalankan Vite saat mengembangkan aset:

```bash
npm install
npm run dev
```

Untuk membuat build produksi:

```bash
npm run build
```
