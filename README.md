# Portal Rumah Sewa HEP

Portal Rumah Sewa HEP ialah sistem Laravel untuk membantu HEP mengurus dan memaparkan maklumat rumah sewa luar kampus sebagai rujukan pelajar baharu dan ibu bapa.

Antara fungsi utama:

- Paparan awam senarai rumah sewa yang telah disahkan.
- Carian dan tapisan rumah sewa menggunakan Livewire.
- Panel admin Filament untuk HEP.
- Pengurusan pemilik rumah, kawasan, kategori, kemudahan, rumah sewa, gambar dan aduan.
- Integrasi Google Maps untuk `Peta Rumah` dan `Arah Ke POLIMAS`.
- REST API v1 untuk persediaan aplikasi Android.
- Pengesahan API menggunakan Sanctum.
- Kawalan peranan dan kebenaran menggunakan Spatie Permission.

## Tech Stack

- Laravel
- MySQL / MariaDB
- Livewire
- Filament
- Tailwind CSS
- Alpine.js
- Sanctum
- Spatie Permission

## Cara Setup Local

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

Pastikan konfigurasi pangkalan data dalam `.env` betul sebelum menjalankan `php artisan migrate --seed`.

## Login Admin

```text
Email: admin@hep.test
Password: password
```

## URL Sistem

```text
Public URL: /
Admin URL: /admin
API Base URL: /api/v1
```

## Endpoint API Utama

```text
GET  /api/v1/health
GET  /api/v1/properties
GET  /api/v1/properties/{property}
GET  /api/v1/areas
GET  /api/v1/categories
GET  /api/v1/facilities
POST /api/v1/reports
POST /api/v1/login
POST /api/v1/logout
GET  /api/v1/profile
```

Endpoint admin API:

```text
GET   /api/v1/admin/properties
POST  /api/v1/admin/properties
PUT   /api/v1/admin/properties/{property}
PATCH /api/v1/admin/properties/{property}/availability
PATCH /api/v1/admin/properties/{property}/verify
GET   /api/v1/admin/reports
PATCH /api/v1/admin/reports/{report}/resolve
```

## Dokumentasi

- Panduan ringkas: `docs/PANDUAN_RINGKAS.md`
- Senarai semak ujian: `docs/UJIAN_SISTEM.md`

## Peringatan Workflow GitHub

Sebelum mula kerja baharu:

```bash
git status
git pull origin main
```

Jika ada perubahan belum commit, buat backup dahulu:

```bash
git add .
git commit -m "backup sebelum kerja baharu"
git push
```

Selepas siap perubahan:

```bash
php artisan test
npm run build
git status
git add .
git commit -m "mesej commit"
git push
```
