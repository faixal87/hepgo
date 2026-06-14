# Ujian Sistem Portal Rumah Sewa HEP

Dokumen ini ialah senarai semak ringkas untuk demo, ujian penerimaan pengguna dan semakan API Portal Rumah Sewa HEP.

## Senarai Semak Ujian

- [ ] 1. Log masuk sebagai admin
- [ ] 2. Semak Papan Pemuka HEP
- [ ] 3. Tambah pemilik rumah
- [ ] 4. Sahkan pemilik rumah
- [ ] 5. Tambah rumah sewa
- [ ] 6. Upload gambar rumah
- [ ] 7. Tetapkan gambar utama
- [ ] 8. Sahkan rumah sewa
- [ ] 9. Tukar status rumah kepada Masih Kosong
- [ ] 10. Tukar status rumah kepada Telah Penuh
- [ ] 11. Semak log status rumah
- [ ] 12. Buka laman public
- [ ] 13. Semak senarai rumah sewa
- [ ] 14. Uji carian rumah
- [ ] 15. Uji filter kawasan
- [ ] 16. Uji filter kategori
- [ ] 17. Uji filter harga
- [ ] 18. Uji filter status
- [ ] 19. Klik butang WhatsApp
- [ ] 20. Klik butang Peta Rumah
- [ ] 21. Klik butang Arah Ke POLIMAS
- [ ] 22. Pastikan Google Maps papar laluan dari rumah ke POLIMAS
- [ ] 23. Buka halaman maklumat rumah
- [ ] 24. Hantar aduan public
- [ ] 25. Semak aduan dalam admin
- [ ] 26. Tandakan aduan Dalam Semakan
- [ ] 27. Tandakan aduan Selesai
- [ ] 28. Test API senarai rumah
- [ ] 29. Test API detail rumah
- [ ] 30. Test API login
- [ ] 31. Test API profile dengan token
- [ ] 32. Test protected admin API
- [ ] 33. Pastikan public tidak nampak rumah belum disahkan
- [ ] 34. Pastikan data sensitif pemilik tidak keluar di API public

## Senarai Semak Sprint 7

- [ ] 35. Test GET /api/v1/app-config
- [ ] 36. Test property list API dengan search
- [ ] 37. Test property list API dengan filter kawasan
- [ ] 38. Test property list API dengan filter harga
- [ ] 39. Test property list API dengan sort price_low
- [ ] 40. Test property detail API
- [ ] 41. Confirm direction_url muncul dalam respons API
- [ ] 42. Confirm maps_url muncul dalam respons API
- [ ] 43. Test login API
- [ ] 44. Test profile API dengan Bearer token
- [ ] 45. Test logout API
- [ ] 46. Test report API
- [ ] 47. Test bookmark add
- [ ] 48. Test bookmark list
- [ ] 49. Test bookmark delete
- [ ] 50. Confirm public API tidak expose owner IC number
- [ ] 51. Confirm public API tidak expose admin remarks

## Senarai Semak Optimasi Gambar Rumah

- [ ] 52. Upload gambar JPG.
- [ ] 53. Upload gambar PNG.
- [ ] 54. Upload gambar WebP.
- [ ] 55. Pastikan sistem generate fail thumbnail WebP.
- [ ] 56. Pastikan sistem generate fail medium WebP.
- [ ] 57. Pastikan sistem generate fail large WebP.
- [ ] 58. Pastikan senarai rumah load thumbnail.
- [ ] 59. Pastikan detail rumah load medium/large.
- [ ] 60. Pastikan gambar tidak pecah dan tidak terlalu blur.
- [ ] 61. Pastikan fail lebih 5MB ditolak.
- [ ] 62. Pastikan SVG ditolak.
- [ ] 63. Pastikan gambar boleh dipadam.
- [ ] 64. Pastikan semua versi gambar dipadam apabila rekod gambar dipadam.
- [ ] 65. Pastikan hanya satu gambar utama untuk setiap rumah.
- [ ] 66. Pastikan gambar lama masih boleh dipaparkan melalui fallback.

## Arahan Ujian Ringkas

Jalankan migrasi dan seed data demo:

```bash
php artisan migrate
php artisan db:seed
php artisan storage:link
```

Semak laman awam:

```text
/
/rumah-sewa
/aduan
```

Semak panel admin:

```text
/admin
```

Akaun demo:

```text
Emel: admin@hep.test
Kata laluan: password
```

Semak API awam:

```bash
curl http://localhost/api/v1/properties
curl http://localhost/api/v1/areas
curl http://localhost/api/v1/categories
curl http://localhost/api/v1/facilities
```

Semak API log masuk:

```bash
curl -X POST http://localhost/api/v1/login \
  -H "Accept: application/json" \
  -d "email=admin@hep.test" \
  -d "password=password"
```

Pastikan respons API menggunakan format standard:

```json
{
  "success": true,
  "message": "Berjaya",
  "data": {}
}
```

## Semakan Google Maps

Pastikan butang `Peta Rumah` hanya muncul jika `Pautan Google Maps` diisi. Pastikan butang `Arah Ke POLIMAS` membuka Google Maps dengan destinasi tetap:

```text
POLIMAS, Jitra, Kedah, Malaysia
```

Laluan mesti menggunakan mod pemanduan dan origin mesti datang daripada `latitude,longitude` jika ada, atau `address` jika koordinat tiada.
