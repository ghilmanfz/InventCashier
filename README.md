# InventCashier

![PHP Version](https://img.shields.io/badge/php-8.1%2B-blue) ![Laravel Version](https://img.shields.io/badge/laravel-10.x-red) ![License: MIT](https://img.shields.io/badge/license-MIT-green)

**InventCashier** adalah aplikasi berbasis Laravel untuk mengelola stok barang dan sistem kasir (Point of Sale) dengan antarmuka admin modern menggunakan Filament.

## 🎯 Fitur Utama

* **Manajemen Kategori Produk**
  Tambahkan, edit, dan hapus kategori barang yang Anda jual.
* **Manajemen Produk**
  Kelola data produk lengkap (nama, SKU unik, deskripsi, harga, stok, gambar).
* **Penyesuaian Stok (Stock Adjustment)**
  Catat penambahan stok beserta alasan (misal restock).
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
* **Retur Produk (Return Product)**
Catat retur stok beserta alasan (retur ke supplier atau ke costumer, dengan namanya, dan alasan nya).




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


![image](https://github.com/user-attachments/assets/c85f1365-25c2-4aab-84a6-b2162d31baa5)

![image](https://github.com/user-attachments/assets/d2757fe3-a0bd-45bc-b907-851c0487e3a4)

![image](https://github.com/user-attachments/assets/1314069e-a8bf-4618-942a-91d044c71876)

![image](https://github.com/user-attachments/assets/27394cfd-d686-4554-bc3e-e3db1a063613)

![image](https://github.com/user-attachments/assets/4a872a99-6ea6-4d47-bf76-3cb5b974e4d4)

![image](https://github.com/user-attachments/assets/3ca35d30-0c66-40e1-9f06-c95390294a70)

![image](https://github.com/user-attachments/assets/dbba0018-064e-4324-a9f4-6133981de400)

![image](https://github.com/user-attachments/assets/92ccf26b-dc47-4c27-9d26-fadb9c54d79f)

![image](https://github.com/user-attachments/assets/9a07b564-f6e9-41f5-943e-2cd986d53c12)

![image](https://github.com/user-attachments/assets/5e8641fc-2f77-4633-8f5d-487820cb13f8)

