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
* **Label Product (Print Label)**
Print Label Produk untuk di tempel.
* **Barcode Product**
Barcode agar lebih mudah mengenali Product.
* **Fitur Produk Potong**
Fitur ini untuk produk yang pembeliannya bisa sebagian, misal kaca, 10 x 20 cm sebanyak 1 pcs, maka dibulatkan menjadi 0,5 untuk harganya, sedangkan stoknya tetap berkurang 1.





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

![Image](https://github.com/user-attachments/assets/3f42b5ae-5b2e-4473-8d2c-4ef27a6ab85e)

![Image](https://github.com/user-attachments/assets/86e9ad55-e411-40d9-a685-e622446f2338)

![Image](https://github.com/user-attachments/assets/c656e2fd-feed-4c9a-bf71-c7342e0a97a7)

![Image](https://github.com/user-attachments/assets/260f2078-0868-4425-81c8-aaa2bee6c699)

![Image](https://github.com/user-attachments/assets/79c24bc2-e074-4ec4-9775-47c857c56e2e)

![Image](https://github.com/user-attachments/assets/c3c55c19-cbbf-4d44-a58b-7efa08adc82b)

![Image](https://github.com/user-attachments/assets/51a600f1-9781-4646-9979-252e4cbafa29)

![Image](https://github.com/user-attachments/assets/af0981fe-4069-4d65-b693-7c41c6339ed6)

![Image](https://github.com/user-attachments/assets/909d2c7d-3433-464f-8c56-ba568f7538a7)

![Image](https://github.com/user-attachments/assets/05e69c40-a166-4ab8-ae11-a4e33d22f227)

![Image](https://github.com/user-attachments/assets/685f8857-ad08-43d9-9436-be38da454230)

![Image](https://github.com/user-attachments/assets/ca354843-e06a-4cfa-9833-51abbe7118b9)

![Image](https://github.com/user-attachments/assets/c5402e57-f7ab-4aa7-ab91-db60e46f3c46)

![Image](https://github.com/user-attachments/assets/4d14923c-f2b2-41b0-aa82-09ec934b278e)

![Image](https://github.com/user-attachments/assets/55705406-f378-4465-b1f0-761751f2add4)

![Image](https://github.com/user-attachments/assets/e35960b8-71f2-4563-9cfe-c9d0ac82e4cf)

![Image](https://github.com/user-attachments/assets/478a55a2-ebc7-4b0d-865a-d26983650ce3)

![Image](https://github.com/user-attachments/assets/d3fda8cf-90d6-44e5-b936-91ff03da655f)

![Image](https://github.com/user-attachments/assets/a0378b21-b3b9-4caa-98d1-56788eccec4d)

![Image](https://github.com/user-attachments/assets/7f3a6c8d-a36e-4df2-9fe3-d12bae92aa0a)

![Image](https://github.com/user-attachments/assets/c7a1e192-59b8-4855-b481-3bde7f08e236)

![Image](https://github.com/user-attachments/assets/1c33bea0-d240-4dbd-b257-db576976da51)

![Image](https://github.com/user-attachments/assets/16679cff-2d6d-4d79-81cf-adc907bb0b0f)

![Image](https://github.com/user-attachments/assets/32f01c42-4d54-4136-9320-af088d49f7ef)

![Image](https://github.com/user-attachments/assets/a51176de-8106-451e-963a-54bbe0fa8a65)
