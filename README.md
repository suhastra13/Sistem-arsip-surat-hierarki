````md
# Sistem Arsip Surat Hierarki (Laravel + MySQL)

Aplikasi **E-Arsip Surat** untuk mengelola **Surat Masuk** dan **Surat Keluar** dengan alur kerja **hierarki jabatan** (Kabid → Kasi → Staff), fitur **disposisi berjenjang/multi-target**, dan **monitoring status posisi surat** (“di meja siapa”).

---

## Daftar Isi

-   [Gambaran Umum](#gambaran-umum)
-   [Fitur Utama](#fitur-utama)
-   [Role & Hak Akses](#role--hak-akses)
-   [Alur Proses](#alur-proses)
-   [Teknologi](#teknologi)
-   [Instalasi & Menjalankan (Windows/XAMPP)](#instalasi--menjalankan-windowsxampp)
-   [Seeder: Akun Demo Default](#seeder-akun-demo-default)
-   [Screenshot](#screenshot)
-   [Catatan Keamanan Repository](#catatan-keamanan-repository)
-   [Lisensi](#lisensi)

---

## Gambaran Umum

Sistem ini dirancang untuk kebutuhan pengarsipan surat di lingkungan instansi, dengan penekanan pada:

-   **Hierarki atasan–bawahan** agar alur disposisi realistis dan terkontrol.
-   **Tracking progres**: surat dapat dipantau statusnya hingga selesai/diarsipkan.
-   **Validasi berjenjang** khususnya pada surat keluar hingga **ACC final**.

---

## Fitur Utama

-   **Manajemen Surat Masuk**

    -   Input metadata surat + upload dokumen
    -   Disposisi berjenjang: Kabid → Kasi → Staff
    -   Monitoring status surat: menunggu disposisi / di meja Kabid/Kasi/Staff / selesai

-   **Manajemen Surat Keluar**

    -   Upload draft dari Staff → review Kasi → review Kabid (ACC final)
    -   Opsi keputusan (sesuaikan implementasi project): ACC / revisi / tolak

-   **Role-Based Access Control**

    -   Halaman, aksi, dan data yang terlihat menyesuaikan role

-   **Hierarki User (parent_id)**

    -   Kabid membawahi Kasi
    -   Kasi membawahi Staff
    -   Membatasi list penerima disposisi agar sesuai struktur organisasi

-   **Pencarian & Filter** _(jika sudah ada di project)_

    -   Berdasarkan judul/nomor/kategori/tahun/bulan

-   **Notifikasi Internal** _(jika sudah ada di project)_
    -   Notifikasi cukup di dalam aplikasi (tanpa WA/email)

---

## Role & Hak Akses

| Role      | Hak Akses Inti                                                                          |
| --------- | --------------------------------------------------------------------------------------- |
| **Admin** | Input surat masuk, upload dokumen, manajemen akun/role, monitoring proses               |
| **Kabid** | Disposisi surat masuk ke Kasi, validasi/ACC final surat keluar                          |
| **Kasi**  | Terima disposisi Kabid, disposisi ke Staff bawahannya, validasi surat keluar dari Staff |
| **Staff** | Terima disposisi, tindaklanjut, upload draft surat keluar                               |

---

## Alur Proses

### 1) Surat Masuk (Top-Down)

1. **Admin** input surat masuk + upload dokumen
2. Surat masuk ke **Kabid** → Kabid **disposisi ke Kasi**
3. **Kasi** menerima → **disposisi ke Staff** (hanya Staff bawahannya)
4. **Staff** tindaklanjut → status surat bisa dipantau

### 2) Surat Keluar (Bottom-Up)

1. **Staff** upload draft surat keluar
2. **Kasi** review (ACC/revisi/tolak) → jika ACC diteruskan ke Kabid
3. **Kabid** review → **ACC final**
4. Surat menjadi **Selesai/Arsip** _(tergantung implementasi di project)_

> Catatan: detail tombol/validasi mengikuti implementasi di project kamu (misalnya “harus dilihat dulu sebelum disposisi”).

---

## Teknologi

-   Laravel
-   MySQL / MariaDB
-   Node.js + NPM (untuk asset build jika menggunakan Vite)

---

## Instalasi & Menjalankan (Windows/XAMPP)

### 1) Clone repository

```bash
git clone https://github.com/suhastra13/Sistem-arsip-surat-hierarki.git
cd Sistem-arsip-surat-hierarki
```
````

### 2) Install dependency

```bash
composer install
npm install
```

### 3) Setup environment

```bash
copy .env.example .env
php artisan key:generate
```

Atur DB di `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=arsip_surat
DB_USERNAME=root
DB_PASSWORD=
```

> Buat dulu database `arsip_surat` via phpMyAdmin.

### 4) Migrasi + Seeder

Jika `DatabaseSeeder` sudah memanggil `UserSeeder`:

```bash
php artisan migrate --seed
```

Atau jalankan seeder tertentu:

```bash
php artisan db:seed --class=UserSeeder
```

### 5) Jalankan aplikasi

Terminal 1:

```bash
php artisan serve
```

Terminal 2 (opsional, jika pakai Vite):

```bash
npm run dev
```

Buka:

-   [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## Seeder: Akun Demo Default

Seeder `UserSeeder` membuat akun demo berikut (password semuanya: `password`):

| Role  | Email                                       | Password | Keterangan          |
| ----- | ------------------------------------------- | -------- | ------------------- |
| Admin | [admin@dishut.com](mailto:admin@dishut.com) | password | Administrator       |
| Kabid | [kabid@dishut.com](mailto:kabid@dishut.com) | password | Atasan Kasi         |
| Kasi  | [kasi@dishut.com](mailto:kasi@dishut.com)   | password | `parent_id` = Kabid |
| Staff | [staf@dishut.com](mailto:staf@dishut.com)   | password | `parent_id` = Kasi  |
| Staff | [staf2@dishut.com](mailto:staf2@dishut.com) | password | `parent_id` = Kasi  |

> ⚠️ Untuk produksi, **wajib** ganti password dan jangan gunakan akun demo.

---

#### Login

![Login](screenshots/login.png)

#### Dashboard

![Dashboard Admin](screenshots/dashboard_admin.png)

#### Surat Masuk

![List Surat Masuk](screenshots/suratmasuk_list.png)
![Monitor Proses Surat oleh admin](screenshots/Proses_surat.png)

#### Surat Keluar

![Monitor Surat Keluar Oleh Staff](screenshots/suratkeluar_monitor.png)
![Tolak Surat Oleh Kabid](screenshots/suratkeluar_kabid.png)

---

---

## Lisensi

Untuk kebutuhan akademik dan demo.

```

```
