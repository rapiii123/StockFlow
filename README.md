# StockFlow – Warehouse Inventory Management System

**StockFlow** adalah sistem manajemen inventaris dan stok barang gudang berbasis web modern, profesional, responsif, dan mudah digunakan yang dibangun menggunakan arsitektur **MVC (Model-View-Controller)** dengan **PHP 8.3+**, **Laravel 12**, **MySQL 8 / MariaDB**, serta antarmuka **Bootstrap 5.3** dan **Chart.js**.

---

## 🚀 Fitur Utama

### 1. Dashboard Interaktif & Informatif
- **Total Jenis Barang**: Jumlah katalog produk aktif di gudang.
- **Total Keseluruhan Stok**: Total fisik barang aktual yang tersimpan di seluruh rak.
- **Jumlah Barang Masuk**: Akumulasi kuantitas dan total transaksi pasokan barang masuk.
- **Jumlah Barang Keluar**: Akumulasi kuantitas dan total transaksi pengeluaran barang.
- **Informasi Stok Menipis (Low Stock Alert)**: Banner peringatan otomatis dan tabel daftar barang yang berada pada atau di bawah ambang batas minimum stok (`min_stock`) dilengkapi tombol *Restock Cepat*.
- **Grafik Aktivitas Barang Masuk & Keluar**: Visualisasi interaktif tren 14 hari terakhir menggunakan **Chart.js**.
- **Donut Chart Status Ketersediaan**: Pemantauan visual rasio stok aman, menipis, dan habis.
- **Ringkasan Transaksi Terbaru**: Tabel riwayat mutasi terkini dengan badge status, rekanan/pemasok, dan aksi cepat.

### 2. Manajemen Barang & Stok (Products)
- Katalog barang lengkap dengan filter pencarian instan (Nama, SKU, Barcode, Lokasi Rak).
- Filter status stok: *Semua*, *Stok Aman*, *Menipis (≤ Min)*, *Habis (0)*.
- Pengurutan dinamis (Terbaru, Nama A-Z, Stok Terendah/Terbanyak, Harga Tertinggi).
- Form tambah dan edit barang lengkap dengan unggah gambar, penentuan ambang batas minimum stok, dan lokasi rak gudang.
- Halaman detail produk dengan riwayat mutasi khusus produk tersebut dan modal *Restock Cepat*.

### 3. Mutasi & Transaksi Pergudangan
- **Penerimaan Barang Masuk (Stock In)**:
  - Multi-item dinamis (tambah baris barang tanpa refresh).
  - Terintegrasi dengan data pemasok (vendor).
  - Otomatis menambah kuantitas stok produk melalui Eloquent database transaction.
- **Pengeluaran Barang Keluar (Stock Out)**:
  - Multi-item dinamis dengan validasi stok ketersediaan di frontend & backend.
  - Mencegah pengeluaran barang melebihi stok yang ada.
  - Otomatis mengurangi kuantitas stok produk.
- **Riwayat Transaksi**: Log pencatatan lengkap disertai filter jenis (Masuk/Keluar) dan rentang tanggal.
- **Cetak Bukti Transaksi**: Format siap cetak (*Print-friendly*) untuk Bukti Penerimaan Barang dan Surat Jalan Pengeluaran Barang.

### 4. Stock Opname / Penyesuaian Stok
- Modul audit fisik berkala untuk mencocokkan stok sistem dengan stok fisik aktual.
- Otomatis menghitung selisih stok (*positive/negative difference*).
- Pilihan alasan penyesuaian (Selisih audit, Kerusakan fisik/afkir, Hilang, Salah input, Kadaluwarsa).

### 5. Master Data Kategori & Pemasok
- **Kategori Barang**: Pengelompokan jenis barang beserta proteksi penghapusan jika kategori masih memiliki produk aktif.
- **Data Pemasok**: Informasi vendor, kontak person, nomor telepon, email, alamat, serta riwayat pasokan.

### 6. Laporan & Ekspor Data
- Rekapitulasi mutasi dan valuasi aset gudang berdasarkan periode tanggal dan kategori.
- Ekspor laporan ke format **CSV / Excel** (dilengkapi BOM UTF-8).
- Format cetak laporan langsung dari peramban web (*Print to PDF*).

---

## 🛠️ Teknologi yang Digunakan

- **Backend**: PHP 8.3+ / Laravel 12
- **Database**: MySQL 8 / MariaDB (Database: `stockflow`)
- **ORM**: Laravel Eloquent
- **Frontend**: HTML5, CSS3, Bootstrap 5.3, JavaScript ES6
- **Charts**: Chart.js 4.4
- **Icons**: Bootstrap Icons (v1.11.3)
- **Fonts**: Plus Jakarta Sans (Google Fonts)

---

## 🔑 Akun Demo Siap Pakai

Sistem dilengkapi seeder data awal dan tombol pengisian cepat pada halaman login:

| Peran (Role) | Alamat Email | Kata Sandi |
| :--- | :--- | :--- |
| **Admin Gudang** | `admin@stockflow.test` | `password` |
| **Operator Stok (Staff)** | `staff@stockflow.test` | `password` |
| **Warehouse Manager** | `manager@stockflow.test` | `password` |

---

## 💻 Cara Menjalankan Aplikasi

1. **Pastikan MySQL / MariaDB berjalan:**
   - Database: `stockflow`
   - Port: `3306`

2. **Jalankan Migrasi & Seeder (jika diperlukan ulang):**
   ```bash
   php artisan migrate --seed
   ```

3. **Jalankan Local Development Server:**
   ```bash
   php artisan serve
   ```

4. **Buka di Browser:**
   Akses `http://localhost:8000` dan masuk menggunakan akun demo di atas.
