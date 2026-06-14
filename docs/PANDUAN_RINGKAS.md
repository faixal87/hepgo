# Panduan Ringkas Portal Rumah Sewa HEP

## 1. Nama Sistem

Portal Rumah Sewa HEP

## 2. Tujuan Sistem

Sistem ini membantu HEP mengurus dan memaparkan maklumat rumah sewa luar kampus untuk rujukan pelajar baharu dan ibu bapa.

## 3. Tech Stack

- Laravel
- MySQL / MariaDB
- Livewire
- Filament
- Tailwind CSS
- Alpine.js
- Sanctum
- Spatie Permission

## 4. Default Admin Login

```text
Email: admin@hep.test
Password: password
```

## 5. Cara Run Locally

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm run dev
php artisan serve
```

Pastikan tetapan pangkalan data dalam fail `.env` telah dikemaskini sebelum menjalankan migrasi.

## 6. Public URL

```text
/
```

## 7. Admin URL

```text
/admin
```

## 8. API Base URL

```text
/api/v1
```

## 9. Main API Endpoints

Endpoint awam:

```text
GET  /api/v1/health
GET  /api/v1/app-config
GET  /api/v1/properties
GET  /api/v1/properties/{property}
GET  /api/v1/areas
GET  /api/v1/categories
GET  /api/v1/facilities
POST /api/v1/reports
POST /api/v1/login
```

Endpoint memerlukan token Sanctum:

```text
POST /api/v1/logout
GET  /api/v1/profile
GET  /api/v1/bookmarks
POST /api/v1/bookmarks/{property}
DELETE /api/v1/bookmarks/{property}
```

Endpoint admin memerlukan token Sanctum dan kebenaran yang sesuai:

```text
GET   /api/v1/admin/properties
POST  /api/v1/admin/properties
PUT   /api/v1/admin/properties/{property}
PATCH /api/v1/admin/properties/{property}/availability
PATCH /api/v1/admin/properties/{property}/verify
GET   /api/v1/admin/reports
PATCH /api/v1/admin/reports/{report}/resolve
```

## 10. Penerangan Peranan

- Pentadbir Utama: Mengurus semua modul, pengguna, peranan dan kebenaran sistem.
- Admin HEP: Mengurus operasi utama rumah sewa, pemilik rumah, aduan dan data rujukan.
- Staf HEP: Membantu mengemaskini rekod pemilik, rumah sewa dan aduan.
- Staf Jabatan: Mengumpul dan menghantar maklumat pemilik serta rumah sewa kepada HEP untuk semakan sebelum dipaparkan di portal.
- Pemilik Rumah: Peranan untuk pemilik rumah yang akan digunakan dalam fungsi portal pemilik pada masa hadapan.
- Pelajar: Pengguna sasaran yang mencari maklumat rumah sewa luar kampus.
- Ibu Bapa / Penjaga: Pengguna sasaran yang membantu pelajar menyemak pilihan penginapan.
- Klien API: Peranan untuk integrasi aplikasi atau sistem luaran yang menggunakan API.

## 11. Ciri Google Maps

- `Peta Rumah` membuka lokasi rumah berdasarkan pautan Google Maps yang disimpan oleh admin.
- `Arah Ke POLIMAS` membuka laluan Google Maps dari rumah sewa ke POLIMAS.
- Origin menggunakan `latitude` dan `longitude` jika kedua-duanya tersedia.
- Jika koordinat tiada, origin menggunakan alamat rumah.
- Destination ditetapkan kepada `POLIMAS, Jitra, Kedah, Malaysia`.
- Mod perjalanan ditetapkan kepada pemanduan.

## 12. Nota Integrasi Android

Aplikasi Android boleh menggunakan endpoint `/api/v1` dengan respons JSON standard. Endpoint awam boleh digunakan tanpa log masuk untuk paparan senarai rumah, maklumat rumah, kawasan, kategori, kemudahan dan penghantaran aduan. Fungsi yang memerlukan pengesahan seperti profil, logout dan operasi admin perlu menggunakan token Sanctum daripada endpoint `POST /api/v1/login`.

## Integrasi Aplikasi Android / Flutter

Aplikasi Android atau Flutter perlu menggunakan API base URL `/api/v1`.

Panduan ringkas integrasi:

- Senarai rumah sewa boleh dibaca secara awam melalui `GET /api/v1/properties`.
- Maklumat rumah sewa boleh dibaca melalui `GET /api/v1/properties/{id}`.
- Log masuk menggunakan Sanctum token melalui `POST /api/v1/login`.
- Simpan token dengan selamat dan hantar sebagai `Authorization: Bearer TOKEN`.
- Bookmark rumah sewa memerlukan login melalui endpoint `/api/v1/bookmarks`.
- Aduan boleh dihantar tanpa login melalui `POST /api/v1/reports`.
- `maps_url` boleh dibuka untuk lokasi rumah.
- `direction_url` boleh dibuka terus daripada aplikasi mobile untuk laluan Google Maps ke POLIMAS.
- Gunakan `thumbnail` untuk kad senarai dan `images` untuk galeri detail.
- Gunakan `meta` untuk pagination supaya app boleh memuatkan halaman seterusnya.

Dokumentasi API mobile penuh boleh dirujuk di `docs/API_ANDROID.md`.
