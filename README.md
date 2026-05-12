# 🥗 Sistem MBG - Makanan Bergizi Gratis
## Project Based Learning (PJBL) | Laravel 12 + PHP 8.3

---

## 📋 DAFTAR ISI
1. [Persyaratan Sistem](#persyaratan)
2. [Cara Install di Laragon](#install-laragon)
3. [Konfigurasi Pusher (Realtime)](#pusher)
4. [Struktur Folder](#struktur)
5. [API Endpoints](#api)
6. [Akun Demo](#akun)
7. [Troubleshooting](#troubleshoot)

---

## ✅ Persyaratan Sistem {#persyaratan}

| Komponen | Versi |
|----------|-------|
| PHP | 8.3+ |
| Laravel | 12.x |
| MySQL | 8.0+ |
| Composer | 2.x |
| Node.js | 18+ |
| Laragon | 6.0+ |

---

## 🚀 Cara Install di Laragon {#install-laragon}

### Langkah 1: Persiapan Laragon
```
1. Buka Laragon
2. Pastikan Apache/Nginx dan MySQL sudah RUNNING (lampu hijau)
3. PHP versi 8.3 sudah terpilih di menu Laragon > PHP
```

### Langkah 2: Letakkan Project
```
Salin folder "mbg-project" ke:
C:\laragon\www\mbg-project\
```

### Langkah 3: Import Database via phpMyAdmin
```
1. Buka: http://localhost/phpmyadmin
2. Klik "New" di sidebar kiri → Buat database baru
3. Nama database: sistem_mbg
4. Collation: utf8mb4_unicode_ci → Klik "Create"
5. Klik database "sistem_mbg" yang baru dibuat
6. Klik tab "Import" di menu atas
7. Klik "Choose File" → Pilih file: database/sistem_mbg.sql
8. Klik "Go" / "Import"
9. Tunggu sampai muncul pesan hijau "Import has been successfully finished"
```

### Langkah 4: Konfigurasi .env
```bash
# Di terminal Laragon atau CMD, masuk ke folder project:
cd C:\laragon\www\mbg-project

# Salin file .env
copy .env.example .env
```

Edit file `.env` sesuai konfigurasi Anda:
```env
APP_NAME="Sistem MBG"
APP_URL=http://mbg-project.test

DB_DATABASE=sistem_mbg
DB_USERNAME=root
DB_PASSWORD=          # kosongkan jika default Laragon

# Pusher (wajib untuk realtime tracking)
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=ap1
```

### Langkah 5: Install Dependensi
```bash
# Di terminal, dari folder project:
composer install
php artisan key:generate
```

### Langkah 6: Jalankan Migration + Seeder (OPSIONAL)
> **Catatan:** Jika sudah import SQL di Langkah 3, lewati ini.
> Gunakan ini hanya jika ingin reset database dari awal.

```bash
php artisan migrate:fresh --seed
```

### Langkah 7: Install Sanctum & Publish Config
```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

### Langkah 8: Jalankan Aplikasi
```bash
# Cara 1: Pakai Laragon virtual host (direkomendasikan)
# Buka Laragon → klik kanan tray icon → Sites → mbg-project
# Akses: http://mbg-project.test

# Cara 2: Artisan serve
php artisan serve
# Akses: http://localhost:8000
```

### Langkah 9 (Opsional): Queue Worker
```bash
# Untuk notifikasi realtime via queue
php artisan queue:work
```

---

## 📡 Konfigurasi Pusher {#pusher}

Pusher diperlukan untuk fitur **live tracking** dan **notifikasi realtime**.

### Cara Mendapatkan Pusher Credentials (Gratis):
```
1. Daftar di https://pusher.com (akun gratis: 200k pesan/hari)
2. Buat "App" baru
3. Pilih Cluster: ap1 (Singapore - terdekat dari Indonesia)
4. Salin: App ID, Key, Secret, Cluster
5. Paste ke file .env
```

### Alternatif Tanpa Pusher (Mode Offline):
Jika belum punya Pusher, fitur tracking tetap berjalan tapi **tidak realtime**.
Data tracking tetap tersimpan dan bisa dilihat di peta saat refresh halaman.

---

## 📁 Struktur Folder {#struktur}

```
mbg-project/
├── app/
│   ├── Events/
│   │   ├── DeliveryStatusUpdated.php   ← Event broadcast status
│   │   └── LocationUpdated.php         ← Event broadcast GPS
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/LoginController.php
│   │   │   ├── Api/TrackingController.php  ← REST API tracking
│   │   │   ├── PetugasController.php
│   │   │   ├── GuruController.php
│   │   │   └── OrangTuaController.php
│   │   └── Middleware/
│   │       └── RoleMiddleware.php      ← Role-based access
│   ├── Models/
│   │   ├── User.php
│   │   ├── School.php
│   │   ├── Student.php
│   │   ├── Delivery.php
│   │   ├── DeliveryTracking.php
│   │   ├── Confirmation.php
│   │   └── ActivityLog.php
│   └── Notifications/
│       └── MakananDiterimaNofification.php
├── database/
│   ├── migrations/                     ← Schema database
│   ├── seeders/DatabaseSeeder.php      ← Data dummy
│   └── sistem_mbg.sql                  ← ⭐ IMPORT INI KE PHPMYADMIN
├── resources/views/
│   ├── auth/login.blade.php            ← Halaman login
│   ├── layouts/app.blade.php           ← Template utama
│   ├── petugas/                        ← UI Kurir
│   ├── guru/                           ← UI Guru
│   └── orangtua/                       ← UI Orang Tua
├── routes/
│   ├── web.php                         ← Route web
│   ├── api.php                         ← REST API
│   └── channels.php                    ← WebSocket channels
└── .env.example                        ← Salin ke .env
```

---

## 🔌 API Endpoints {#api}

Base URL: `http://mbg-project.test/api`

> Semua endpoint API menggunakan **Bearer Token** (Sanctum).
> Token didapat saat login melalui `auth()->user()->createToken('name')->plainTextToken`

| Method | Endpoint | Deskripsi | Role |
|--------|----------|-----------|------|
| GET | `/api/user` | Info user login | All |
| POST | `/api/tracking/update` | Update lokasi GPS | Petugas |
| GET | `/api/tracking/{delivery_id}` | Lokasi terbaru | All |
| GET | `/api/tracking/{delivery_id}/history` | Riwayat tracking | All |
| GET | `/api/deliveries/active` | Pengiriman aktif hari ini | All |
| GET | `/api/status/{kode}` | Cek status by kode (public) | Public |

### Contoh Request Update Lokasi:
```bash
curl -X POST http://mbg-project.test/api/tracking/update \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "delivery_id": 1,
    "latitude": 0.533200,
    "longitude": 101.447400,
    "accuracy": 5.0
  }'
```

### Contoh Response:
```json
{
  "success": true,
  "tracking": {
    "id": 42,
    "latitude": 0.5332,
    "longitude": 101.4474,
    "recorded_at": "2024-01-15T08:30:00.000000Z"
  }
}
```

---

## 👤 Akun Demo {#akun}

| Role | Email | Password |
|------|-------|----------|
| 🚚 Petugas/Kurir | petugas@mbg.test | password |
| 🚚 Petugas/Kurir 2 | petugas2@mbg.test | password |
| 👩‍🏫 Guru | guru@mbg.test | password |
| 👨‍🏫 Guru 2 | guru2@mbg.test | password |
| 👨‍👩‍👦 Orang Tua | orangtua@mbg.test | password |
| 👩‍👧 Orang Tua 2 | orangtua2@mbg.test | password |
| 👨‍👦 Orang Tua 3 | orangtua3@mbg.test | password |

---

## 🔄 Alur Penggunaan Sistem

```
1. PETUGAS login → Buat Pengiriman Baru → Pilih sekolah
2. PETUGAS update status: Dimasak → Dikemas → Dalam Perjalanan
3. Saat "Dalam Perjalanan" → GPS aktif otomatis → Live tracking mulai
4. GURU & ORANG TUA melihat live tracking di peta
5. PETUGAS update: Dalam Perjalanan → Sudah Sampai
6. GURU konfirmasi penerimaan → Notifikasi ke semua orang tua
7. ORANG TUA konfirmasi anak sudah makan
8. Status otomatis → Selesai
```

---

## 🐛 Troubleshooting {#troubleshoot}

### Error: "Class not found"
```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

### Error: Database connection failed
```
1. Pastikan MySQL Laragon running
2. Cek DB_DATABASE=sistem_mbg di .env
3. Cek DB_USERNAME=root, DB_PASSWORD= (kosong untuk Laragon default)
```

### Error: "No application encryption key"
```bash
php artisan key:generate
```

### Tracking tidak realtime
```
1. Pastikan credentials Pusher sudah benar di .env
2. Cek BROADCAST_CONNECTION=pusher di .env
3. Pastikan browser mengizinkan akses GPS (https atau localhost)
4. Jalankan: php artisan queue:work (untuk notifikasi)
```

### Map tidak muncul
```
Leaflet menggunakan OpenStreetMap (gratis, tidak perlu API key).
Pastikan koneksi internet tersedia saat mengakses peta.
```

---

## 🛠️ Commands Berguna

```bash
# Clear semua cache
php artisan optimize:clear

# Reset database + seeder
php artisan migrate:fresh --seed

# Jalankan queue worker
php artisan queue:work --queue=default

# Lihat semua routes
php artisan route:list

# Generate API token untuk testing
php artisan tinker
>>> App\Models\User::first()->createToken('test')->plainTextToken
```

---

## 📝 Teknologi yang Digunakan

| Teknologi | Kegunaan |
|-----------|----------|
| Laravel 12 | Framework PHP backend |
| PHP 8.3 | Runtime bahasa |
| MySQL | Database |
| TailwindCSS (CDN) | Styling UI |
| Leaflet.js | Peta interaktif (OpenStreetMap) |
| Pusher | WebSocket realtime |
| Laravel Echo | Integrasi WebSocket |
| Laravel Sanctum | API authentication |
| Blade Templates | Template engine |

---

**© 2024 Sistem MBG - Project Based Learning**
*Dibuat untuk mendukung program Makanan Bergizi Gratis Indonesia*
