# WarehouseFlow Laravel

MVP aplikasi stock warehouse dengan:
- Master produk
- Master warehouse
- Master lokasi/rak
- Barang masuk
- Barang keluar
- Transfer lokasi
- QR produk dan QR lokasi
- Stock opname dengan scan QR dari kamera HP
- Approval stock opname dan adjustment otomatis

## Requirement
- PHP 8.3+
- Composer
- MySQL/MariaDB atau PostgreSQL
- Node.js + NPM
- Laravel 13.x atau Laravel 12.x

## Cara Install Project Laravel Baru

```bash
composer create-project laravel/laravel warehouseflow
cd warehouseflow
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install
npm run build
composer require simplesoftwareio/simple-qrcode
```

Salin folder `app`, `database`, `resources`, dan `routes` dari paket ini ke project Laravel Anda.

## Setup Database

Edit `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=warehouseflow
DB_USERNAME=root
DB_PASSWORD=
```

Jalankan:

```bash
php artisan migrate
php artisan db:seed --class=WarehouseFlowSeeder
php artisan serve
```

Login menggunakan user yang Anda buat melalui register Breeze, lalu akses:

```text
http://127.0.0.1:8000/dashboard
```

## Kamera HP / QR Scanner

Untuk scan kamera dari HP:
1. Jalankan Laravel di jaringan lokal.
2. Gunakan IP laptop, contoh:
   `http://192.168.1.20:8000`
3. Browser modern biasanya butuh HTTPS untuk akses kamera, kecuali localhost.
4. Untuk testing cepat, gunakan browser Chrome Android dan izinkan kamera.
5. Untuk production, wajib pakai HTTPS.

## Isi QR

QR produk:
```text
PRODUCT:PRD-0001
```

QR lokasi:
```text
LOCATION:RAK-A1
```

Scanner otomatis membaca prefix `PRODUCT:` dan `LOCATION:`.

## Catatan
Ini adalah MVP. Untuk production, tambahkan:
- Role permission detail
- Validasi lokasi stok lebih kuat
- Audit log detail
- Offline mode PWA
- Export Excel/PDF
- Multi-company
