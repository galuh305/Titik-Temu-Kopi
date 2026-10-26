# Titik Temu Kopi POS — Laravel Starter

Starter project untuk tes MED-WEB-01. Stack: Laravel 12, PHP 8.2+, MySQL, Blade, Bootstrap CDN.
Fitur pada paket ini: login/logout, role admin/kasir, dashboard KPI, master kategori & produk, POS multi-item, pembayaran, struk print, laporan dan CSV.
Skema resep/bahan baku disediakan sebagai fondasi (belum ada UI resep dan pengurangan bahan resep otomatis).

## 1. Buat project Laravel
```bash
composer create-project laravel/laravel titik-temu-pos "^12.0"
cd titik-temu-pos
```
Ekstrak ZIP starter ini ke root project Laravel (gabungkan folder `app`, `database`, `resources`, `routes`).

## 2. Database
Buat database MySQL `titik_temu_pos`, lalu atur `.env`:
```env
APP_NAME="Titik Temu POS"
APP_URL=http://127.0.0.1:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=titik_temu_pos
DB_USERNAME=root
DB_PASSWORD=
```
Jalankan:
```bash
php artisan key:generate
php artisan migrate --seed
php artisan serve
```
Buka http://127.0.0.1:8000

## 3. Akun demo
- Admin: `admin@titiktemu.test` / `password`
- Kasir: `kasir@titiktemu.test` / `password`
Ganti password demo sebelum digunakan sungguhan.

## 4. Folder penting
- `routes/web.php`: route & proteksi role
- `app/Http/Controllers`: auth, dashboard, master, POS, laporan
- `app/Models`: relasi data
- `database/migrations`: struktur data
- `database/seeders/DatabaseSeeder.php`: akun + data demo
- `resources/views`: Blade UI

## 5. Alur demo
1. Login sebagai admin, buka Dashboard, Kategori, Produk.
2. Logout, login sebagai kasir.
3. Buat transaksi, pilih tipe pesanan dan produk, qty, diskon, metode pembayaran.
4. Pembayaran tunai harus >= tagihan. Struk dapat dicetak.
5. Buka laporan dan ekspor CSV.

## Catatan integritas dan batasan
- Nominal dihitung ulang di server; harga dari browser tidak dipercaya.
- Header dan detail transaksi disimpan dalam `DB::transaction()`.
- Stok produk dikunci dengan `lockForUpdate()` lalu diperiksa dan dikurangi dalam transaksi yang sama.
- Invoice menggunakan tabel sequence sederhana? Implementasi saat ini memakai UUID pendek acak setelah tanggal agar aman dari collision, dengan pola `INV-POS-YYYYMMDD-XXXX` (XXXX alfanumerik).
- Implementasi stok adalah stok produk/menu, sesuai skema dasar proyek. Modul recipes/ingredients adalah fondasi dan perlu disempurnakan bila waktu cukup.
- Ini starter ujian, bukan sistem POS produksi: belum menangani printer thermal langsung, audit trail, void/refund dengan approval, rate limiting khusus, atau pajak/aturan daerah yang kompleks.
