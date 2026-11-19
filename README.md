# Courier API

REST API Laravel untuk mengelola master data kurir/courier.

Project ini berfokus pada backend API dan tidak menyediakan antarmuka administrasi.

## Requirements

- PHP 8.3 atau lebih baru
- Composer
- SQLite, MySQL, atau database lain yang didukung Laravel

## Installation

Clone repository dan masuk ke direktori project:

```bash
git clone <repository-url>
cd gradin
```

Install dependency:

```bash
composer install
```

Siapkan environment:

```bash
copy .env.example .env
php artisan key:generate
```

Atur koneksi database pada `.env`, kemudian jalankan migration:

```bash
php artisan migrate
```

Jalankan server development:

```bash
php artisan serve
```

Default base URL:

```text
http://127.0.0.1:8000/api
```

## Database

Tabel `couriers` memiliki field berikut:

| Field | Tipe | Wajib | Keterangan |
|---|---|---:|---|
| `id` | integer | otomatis | Primary key |
| `name` | string | ya | Nama kurir, 2–150 karakter |
| `phone` | string | ya | Nomor telepon unik |
| `email` | string | tidak | Email unik jika diisi |
| `address` | string | tidak | Alamat kurir |
| `level` | integer | ya | Level 1 sampai 5 |
| `joined_at` | date | ya | Tanggal mulai terdaftar |
| `is_active` | boolean | tidak | Default `true` |
| `created_at` | datetime | otomatis | Waktu record dibuat |
| `updated_at` | datetime | otomatis | Waktu record diubah |

## API Endpoints

Semua endpoint mengembalikan JSON. Untuk request dengan body JSON, gunakan header:

```text
Accept: application/json
Content-Type: application/json
```

### List couriers

```http
GET /api/couriers
```

Default sorting berdasarkan `name` ascending.

Query parameters yang tersedia:

| Parameter | Contoh | Keterangan |
|---|---|---|
| `page` | `2` | Nomor halaman |
| `per_page` | `20` | Jumlah data per halaman, default 15 |
| `search` | `budi agung` | Semua kata harus cocok pada nama kurir |
| `level` | `2,3` | Filter kurir dengan level tertentu |
| `sort` | `registered_at` | Urutkan berdasarkan waktu pendaftaran terbaru |

Contoh:

```http
GET /api/couriers?search=budi%20agung&level=2,3&per_page=10
```

Response sukses berupa object pagination Laravel:

```json
{
  "current_page": 1,
  "data": [],
  "per_page": 10,
  "total": 0
}
```

### Create courier

```http
POST /api/couriers
```

Request body:

```json
{
  "name": "Budi Santoso",
  "phone": "+62 812-0000-0001",
  "email": "budi@example.com",
  "address": "Jakarta",
  "level": 3,
  "joined_at": "2026-01-15",
  "is_active": true
}
```

Response sukses: `201 Created`.

### Show courier

```http
GET /api/couriers/{id}
```

Contoh:

```http
GET /api/couriers/1
```

Response sukses: `200 OK`.

### Update courier

```http
PUT /api/couriers/{id}
```

Request body menggunakan field yang sama seperti create courier. Contoh:

```json
{
  "name": "Budi Agung Santoso",
  "phone": "+62 812-0000-0001",
  "email": "budi@example.com",
  "address": "Jakarta Selatan",
  "level": 5,
  "joined_at": "2026-01-15",
  "is_active": true
}
```

Response sukses: `200 OK`.

### Delete courier

```http
DELETE /api/couriers/{id}
```

Response sukses: `204 No Content`.

## Validation

- `name` wajib berupa string dengan panjang 2–150 karakter.
- `phone` wajib diisi, memiliki format nomor telepon, dan harus unik.
- `email` harus berupa email valid dan unik jika diisi.
- `level` wajib berupa angka integer antara 1 dan 5.
- `joined_at` wajib berupa tanggal valid.
- `is_active` harus berupa boolean jika dikirim.
- Request yang gagal validasi menghasilkan `422 Unprocessable Entity`.
- Resource yang tidak ditemukan menghasilkan `404 Not Found`.

## Testing

Test menggunakan SQLite in-memory dan tidak memerlukan database development.

Jalankan seluruh test:

```bash
php artisan test
```

Jalankan hanya feature test Courier API:

```bash
php artisan test --filter=CourierApiTest
```

Periksa format kode:

```bash
vendor/bin/pint --test
```

Test mencakup pembuatan data, validasi, pencarian multi-kata, filter level, pagination, sorting, show, update, dan delete.

## Manual Testing dengan Postman

1. Jalankan `php artisan serve`.
2. Buat environment Postman dengan variable `base_url` bernilai `http://127.0.0.1:8000`.
3. Tambahkan header `Accept: application/json`.
4. Jalankan request CRUD berikut secara berurutan:
   - `POST {{base_url}}/api/couriers`
   - `GET {{base_url}}/api/couriers`
   - `GET {{base_url}}/api/couriers/{id}`
   - `PUT {{base_url}}/api/couriers/{id}`
   - `DELETE {{base_url}}/api/couriers/{id}`
5. Untuk menguji fitur list, gunakan:
   - `GET {{base_url}}/api/couriers?search=budi%20agung`
   - `GET {{base_url}}/api/couriers?level=2,3`
   - `GET {{base_url}}/api/couriers?sort=registered_at`
   - `GET {{base_url}}/api/couriers?per_page=2`

## Project Structure

- `app/Models/Courier.php` — model Courier dan casting data.
- `app/Http/Controllers/CourierController.php` — operasi CRUD dan query list.
- `app/Http/Requests/StoreCourierRequest.php` — validasi create.
- `app/Http/Requests/UpdateCourierRequest.php` — validasi update.
- `database/migrations/*create_couriers_table.php` — struktur tabel Courier.
- `routes/api.php` — route API Courier.
- `tests/Feature/CourierApiTest.php` — feature test endpoint Courier.
