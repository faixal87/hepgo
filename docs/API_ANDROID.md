# Dokumentasi API Android / Flutter

Dokumen ini disediakan untuk pembangun Android atau Flutter yang akan menggunakan API Portal Rumah Sewa HEP.

## 1. API Base URL

```text
/api/v1
```

Untuk local development, contoh penuh mungkin seperti:

```text
http://localhost:8000/api/v1
```

## 2. Kaedah Authentication

API menggunakan Laravel Sanctum dengan Bearer token.

Selepas berjaya log masuk, simpan token dengan selamat dan hantar pada request yang memerlukan login:

```http
Authorization: Bearer TOKEN
Accept: application/json
```

## 3. Public Endpoints

```text
GET  /api/v1/app-config
GET  /api/v1/properties
GET  /api/v1/properties/{id}
GET  /api/v1/areas
GET  /api/v1/categories
GET  /api/v1/facilities
POST /api/v1/reports
```

## 4. Auth Endpoints

```text
POST /api/v1/login
POST /api/v1/logout
GET  /api/v1/profile
```

`logout` dan `profile` memerlukan Bearer token.

## 5. Bookmark Endpoints

```text
GET    /api/v1/bookmarks
POST   /api/v1/bookmarks/{property}
DELETE /api/v1/bookmarks/{property}
```

Semua endpoint bookmark memerlukan Bearer token.

## 6. Contoh Login Request

```http
POST /api/v1/login
Accept: application/json
Content-Type: application/json
```

```json
{
  "email": "admin@hep.test",
  "password": "password"
}
```

Contoh respons:

```json
{
  "success": true,
  "message": "Log masuk berjaya.",
  "data": {
    "token": "TOKEN",
    "token_type": "Bearer",
    "user": {
      "id": 1,
      "name": "Pentadbir Sistem",
      "email": "admin@hep.test",
      "phone": null,
      "status": "active",
      "status_label": "Aktif",
      "roles": ["super_admin"],
      "permissions": ["view dashboard"]
    },
    "roles": ["super_admin"],
    "permissions": ["view dashboard"]
  },
  "meta": {}
}
```

## 7. Contoh Property List Response

```http
GET /api/v1/properties?search=jitra&sort=price_low&per_page=10
```

```json
{
  "success": true,
  "message": "Senarai data berjaya dipaparkan.",
  "data": [
    {
      "id": 1,
      "tajuk": "Rumah Sewa Taman Siswa",
      "slug": "rumah-sewa-taman-siswa",
      "kawasan": "Taman Siswa",
      "kategori": "Rumah Penuh",
      "harga": 650,
      "harga_label": "RM650 sebulan",
      "deposit": 650,
      "deposit_label": "RM650",
      "status": "available",
      "status_label": "Masih Kosong",
      "jarak_km": 1.5,
      "jarak_label": "Jarak anggaran: 1.5 km dari POLIMAS",
      "keutamaan_penyewa": "Perempuan",
      "thumbnail": "http://localhost:8000/storage/demo-properties/rumah-sewa-taman-siswa.svg",
      "maps_url": "https://www.google.com/maps/search/?api=1&query=...",
      "direction_url": "https://www.google.com/maps/dir/?api=1&origin=...&destination=POLIMAS%2C%20Jitra%2C%20Kedah%2C%20Malaysia&travelmode=driving",
      "whatsapp_url": "https://wa.me/60123456789?text=...",
      "ringkasan": "Rumah lengkap untuk pelajar...",
      "kemudahan_ringkas": ["WiFi", "Parking", "Dapur"],
      "created_at_label": "14/06/2026"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 10,
    "total": 1
  }
}
```

## 8. Contoh Property Detail Response

```http
GET /api/v1/properties/1
```

```json
{
  "success": true,
  "message": "Berjaya",
  "data": {
    "id": 1,
    "tajuk": "Rumah Sewa Taman Siswa",
    "penerangan": "Rumah lengkap untuk pelajar...",
    "alamat": "Taman Siswa, Jitra, Kedah",
    "kawasan": "Taman Siswa",
    "kategori": "Rumah Penuh",
    "harga": 650,
    "harga_label": "RM650 sebulan",
    "deposit": 650,
    "deposit_label": "RM650",
    "status": "Masih Kosong",
    "status_label": "Masih Kosong",
    "status_pengesahan": "Disahkan",
    "jarak_km": 1.5,
    "jarak_label": "Jarak anggaran: 1.5 km dari POLIMAS",
    "keutamaan_penyewa": "Perempuan",
    "bilangan_bilik": 3,
    "bilangan_bilik_air": 2,
    "maksimum_penghuni": 6,
    "kemudahan": [],
    "images": [],
    "thumbnail": "http://localhost:8000/storage/demo-properties/rumah-sewa-taman-siswa.svg",
    "maps_url": "https://www.google.com/maps/search/?api=1&query=...",
    "direction_url": "https://www.google.com/maps/dir/?api=1&origin=...&destination=POLIMAS%2C%20Jitra%2C%20Kedah%2C%20Malaysia&travelmode=driving",
    "whatsapp_url": "https://wa.me/60123456789?text=...",
    "maklumat_pemilik_public": {
      "nama": "Pemilik Demo 01",
      "telefon": "0111002001",
      "whatsapp_number": "60111002001"
    },
    "nota_keselamatan": "Nota: HEP menyediakan maklumat ini sebagai rujukan. Sila semak sendiri keadaan rumah dan persetujuan sewaan sebelum membuat sebarang bayaran."
  },
  "meta": {}
}
```

## 9. Contoh Report Submission

```http
POST /api/v1/reports
Accept: application/json
Content-Type: application/json
```

```json
{
  "property_id": 1,
  "reporter_name": "Pelajar Ujian",
  "reporter_phone": "0123456789",
  "reporter_email": "pelajar@example.test",
  "report_type": "wrong_location",
  "message": "Lokasi rumah sewa ini kelihatan tidak tepat."
}
```

Contoh respons:

```json
{
  "success": true,
  "message": "Aduan anda berjaya dihantar. Pihak HEP akan menyemak maklumat ini.",
  "data": {
    "id": 1,
    "jenis_aduan": "Lokasi salah",
    "status_aduan": "Baharu"
  },
  "meta": {}
}
```

## 10. Penggunaan Google Maps

- `maps_url` membuka lokasi rumah.
- `direction_url` membuka laluan dari rumah ke POLIMAS.
- Aplikasi Android atau Flutter boleh membuka `direction_url` menggunakan external intent atau browser.
- Jika `latitude` dan `longitude` wujud, server akan menggunakan koordinat sebagai origin.
- Jika koordinat tiada, server akan menggunakan alamat rumah sebagai origin.
- Destination sentiasa `POLIMAS, Jitra, Kedah, Malaysia`.

## 11. Nota Untuk Flutter / Android

- Simpan token secara selamat, contohnya menggunakan secure storage.
- Gunakan header `Authorization: Bearer TOKEN` untuk endpoint login-protected.
- Gunakan `meta.current_page`, `meta.last_page`, `meta.per_page` dan `meta.total` untuk pagination.
- Jangan hardcode label status dalam app jika API sudah menghantar `status_label`.
- Gunakan `thumbnail` untuk kad senarai rumah.
- Gunakan `images` untuk galeri halaman detail.
- Gunakan `whatsapp_url`, `maps_url` dan `direction_url` secara terus untuk aksi cepat.
