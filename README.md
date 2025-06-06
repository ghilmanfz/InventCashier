# InventCashier

![PHP Version](https://img.shields.io/badge/php-8.1%2B-blue) ![Laravel Version](https://img.shields.io/badge/laravel-10.x-red) ![License: MIT](https://img.shields.io/badge/license-MIT-green)

**InventCashier** adalah aplikasi berbasis Laravel untuk mengelola stok barang dan sistem kasir (Point of Sale) dengan antarmuka admin modern menggunakan Filament.

## 🎯 Fitur Utama

* **Manajemen Kategori Produk**
  Tambahkan, edit, dan hapus kategori barang yang Anda jual.
* **Manajemen Produk**
  Kelola data produk lengkap (nama, SKU unik, deskripsi, harga, stok, gambar).
* **Penyesuaian Stok (Stock Adjustment)**
  Catat perubahan stok beserta alasan (restock, retur, kerusakan, dsb.).
* **Manajemen Pelanggan**
  Simpan data pelanggan (nama, email, no. telepon, alamat).
* **Pencatatan Pesanan (Orders)**
  Buat pesanan dengan detail barang, diskon, total, metode pembayaran, dan status.
* **Detail Pesanan (Order Details)**
  Lihat riwayat item per pesanan beserta subtotal dan harga per item.
* **Laporan & PDF Export**
  Cetak laporan penjualan ke PDF (menggunakan DOMPDF).
* **Dashboard Statistik**
  Dashboard visual untuk memantau tren penjualan dan stok (menggunakan Laravel Trend).
* **Autentikasi & Otorisasi**
  Login via Laravel Sanctum, proteksi rute dan hak akses granular dengan Filament Shield.
* **Responsive & Modern UI**
  Dibangun di atas Filament Admin Panel untuk pengalaman pengguna yang bersih dan cepat.

## 🚀 Instalasi

1. **Clone repository**

   ```bash
   git clone https://github.com/ghilmanfz/InventCashier.git
   cd InventCashier
   ```
   
2. **Salin dan konfig `.env`**

   ```bash
   cp .env.example .env
   ```
   
3. **Install dependency**

   ```bash
   composer install && npm i && npm run build
   ```

4. **APP_KEY dalam file . env**

   ```bash
   php artisan key:generate
   ```

5. **Migrasi & Seeder database**

   ```bash
   php artisan migrate:fresh --seed && php artisan shield:install --fresh --minimal # (opsional, jika ingin menggunakan data dummy, nanti pilih user login di Terminal)
   ```
   Atau
   ```bash
   php artisan migrate:fresh && php artisan shield:install --fresh --minimal # (opsional, jika tidak ingin menggunakan data dummy, nanti bikin user untuk login di Terminal)
   ```

   
6. **Jalankan server lokal**

   ```bash
   php artisan serve
   ```

   Akses aplikasi di `http://localhost:8000` dan panel admin di `http://localhost:8000/admin`.

## ⚙️ Konfigurasi

* **Database**: atur koneksi di file `.env` (`DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).
* **Email**: sesuaikan konfigurasi mail di `.env` jika ingin fitur notifikasi.
* **Storage**: publish asset storage jika diperlukan:

  ```bash
  php artisan storage:link
  ```

## 🗂️ Struktur Direktori

```
app/              # Kode backend (Models, Controllers, Filament Resources)
config/           # Konfigurasi aplikasi
database/         # Migrations & Seeders
public/           # Aset publik (JS, CSS, images)
resources/        # Views, Filament pages & components
routes/           # Deklarasi rute
tests/            # Unit & feature tests
```

## 🛠️ Teknologi & Package

* **Framework**: Laravel 10
* **Admin Panel**: Filament v3
* **Otorisasi**: Filament Shield
* **API & Auth**: Laravel Sanctum
* **PDF Generation**: barryvdh/laravel-dompdf
* **Dashboard Trends**: flowframe/laravel-trend
* **Gravatar Integration**: awcodes/filament-gravatar
* **HTTP Client**: Guzzle

Lihat `composer.json` untuk daftar lengkap dependency.

## 🤝 Kontribusi

1. Fork project ini
2. Buat branch fitur: `git checkout -b feature/NamaFitur`
3. Commit perubahan: `git commit -m "Tambah fitur X"`
4. Push ke branch: `git push origin feature/NamaFitur`
5. Buka Pull Request

## 📄 Lisensi

Di bawah lisensi [MIT](https://opensource.org/licenses/MIT).

---

*Made with ❤️ by ghilmanfz*


![Image](https://github.com/user-attachments/assets/b88006d3-bfed-4ee8-a86f-1b9bc901489f)

![Image](https://github.com/user-attachments/assets/d1e1cd79-0e32-4696-8c45-18dc87ce7ec8)

![Image](https://github.com/user-attachments/assets/59a1f982-6789-4f74-af27-cfea43148fc8)

![Image](https://github.com/user-attachments/assets/61632613-f4a3-429b-8365-87a0e64763aa)

![Image](https://github.com/user-attachments/assets/19d46f64-48f6-49f8-a62b-a3b5aaeedfdc)

![Image](https://github.com/user-attachments/assets/afa77d58-08db-4712-ad33-40c2bd8ef059)

![Image](https://github.com/user-attachments/assets/41481135-3e68-4091-bd0b-d6e6e7ab9a7a)

![Image](https://github.com/user-attachments/assets/3957c57f-7141-4a07-8346-03c316869a3d)

![Image](https://github.com/user-attachments/assets/c8f6bee2-0bae-4097-bb4d-c65cf4fea2b4)





