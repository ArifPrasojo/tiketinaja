# TiketinAja — Platform Booking Lapangan Olahraga

> Sistem web booking lapangan olahraga berbasis Laravel yang memungkinkan pengguna mencari, memesan, dan membayar lapangan secara online, dilengkapi dashboard manajemen venue, promo, QR check-in, dan REST API.

---

## Daftar Isi

- [Fitur](#fitur)
- [Teknologi](#teknologi)
- [Persyaratan](#persyaratan)
- [Instalasi](#instalasi)
- [Konfigurasi](#konfigurasi)
- [Database & Seeder](#database--seeder)
- [Menjalankan Aplikasi](#menjalankan-aplikasi)
- [Akun Demo](#akun-demo)
- [Struktur Folder](#struktur-folder)
- [Entity Relationship Diagram](#entity-relationship-diagram)
- [Alur Sistem](#alur-sistem)
- [REST API](#rest-api)
- [Testing](#testing)
- [Scheduler](#scheduler)
- [Lisensi](#lisensi)

---

## Fitur

### Customer
| Fitur | Keterangan |
|---|---|
| 🔐 Autentikasi | Register, login, logout, reset password, verifikasi email |
| 🏟️ Cari Venue | Browse, filter kota/olahraga/nama, lihat rating & ulasan |
| 📅 Booking Lapangan | Pilih tanggal, slot jam, promo diskon, checkout |
| 💳 Pembayaran Simulasi | Konfirmasi pembayaran (simulasi — bisa diganti payment gateway) |
| ⏱️ Expiry Otomatis | Booking pending kedaluwarsa dalam 15 menit jika belum dibayar |
| 📱 QR Check-in | QR code tiket ditampilkan setelah konfirmasi, check-in mandiri |
| ⏰ Window Check-in | Check-in dibuka 10 menit sebelum jam main |
| 🏃 Live Timer | Countdown "Sedang Bermain" real-time di halaman booking |
| ⭐ Review & Rating | Ulasan 1–5 bintang setelah sesi selesai |
| 🔔 Notifikasi | Notifikasi in-app + email untuk setiap perubahan status booking |
| 🎟️ Kode Promo | Diskon persentase atau nominal, berlaku per-venue atau global |
| 📋 Riwayat Booking | Daftar semua booking dengan status real-time |

### Venue Manager
| Fitur | Keterangan |
|---|---|
| 🏟️ Kelola Venue | CRUD venue, foto, jam operasional per hari |
| 🏸 Kelola Lapangan | CRUD court, harga per jam, fasilitas, status aktif |
| 📊 Dashboard | Booking hari ini, total revenue, lapangan terpopuler, peak hours |
| 📋 Manajemen Booking | Lihat, konfirmasi, batalkan booking |
| 🎟️ Manajemen Promo | Buat kode promo khusus venue |
| 📷 Scanner QR | Scan QR customer via kamera (jsQR) atau input manual |

### Admin
| Fitur | Keterangan |
|---|---|
| 👥 Kelola Pengguna | Cari, filter, ubah role, hapus pengguna |
| 📊 Dashboard Platform | Metrik total user/venue/booking/revenue, top venues, activity log |
| 🏟️ Kelola Semua Venue | Akses ke semua venue |
| 🎟️ Promo Global | Buat promo yang berlaku di semua venue |

### Teknis
| Fitur | Keterangan |
|---|---|
| 🔁 REST API | 24 endpoint dengan autentikasi Sanctum |
| 🔒 Anti-double Booking | DB unique constraint + service layer validation |
| 📜 Activity Log | Semua perubahan status booking tercatat |
| ⚙️ Scheduler | Auto-expire (1 menit) + auto-complete (1 menit) booking |
| 🧪 Automated Tests | 94 tests, 215 assertions |

---

## Teknologi

| Layer | Stack |
|---|---|
| **Backend** | PHP 8.3, Laravel 13.x |
| **Frontend** | Blade, Tailwind CSS 3.x, Alpine.js 3.x |
| **Database** | MySQL 8.x |
| **Auth** | Laravel Breeze + Laravel Sanctum (API) |
| **QR Code** | simplesoftwareio/simple-qrcode (generate), jsQR (scan) |
| **Queue** | Database queue (notifikasi email) |
| **Testing** | PHPUnit 12 |

---

## Persyaratan

- PHP >= 8.3
- Composer >= 2.x
- Node.js >= 20.x & npm
- MySQL >= 8.0
- Laravel Scheduler (cron atau `php artisan schedule:work`)

---

## Instalasi

```bash
# 1. Clone repository
git clone https://github.com/username/tiketinaja.git
cd tiketinaja

# 2. Install PHP dependencies
composer install

# 3. Install JS dependencies
npm install

# 4. Copy file environment
cp .env.example .env

# 5. Generate application key
php artisan key:generate
```

---

## Konfigurasi

Edit `.env` sesuai environment Anda:

```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tiketinaja
DB_USERNAME=root
DB_PASSWORD=

# Mail (untuk notifikasi email)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="noreply@tiketinaja.test"
MAIL_FROM_NAME="TiketinAja"

# Queue (untuk notifikasi async)
QUEUE_CONNECTION=database

# App URL
APP_URL=http://localhost:8000
```

---

## Database & Seeder

```bash
# Buat database baru di MySQL, lalu jalankan:

# Migrasi + seed data demo lengkap
php artisan migrate:fresh --seed

# Atau hanya migrasi tanpa seed
php artisan migrate

# Atau hanya seed (jika migrasi sudah ada)
php artisan db:seed
```

### Tabel Database

| Tabel | Deskripsi |
|---|---|
| `users` | Akun pengguna (customer / manager / admin) |
| `venues` | Tempat olahraga |
| `courts` | Lapangan dalam venue |
| `sports` | Jenis olahraga (Futsal, Badminton, dll.) |
| `operating_hours` | Jam operasional venue per hari |
| `bookings` | Transaksi pemesanan |
| `booking_items` | Detail slot per booking |
| `reserved_slots` | Slot yang sudah terpesan (unique constraint anti-double) |
| `payments` | Informasi pembayaran |
| `promos` | Kode promo |
| `promo_usages` | Riwayat penggunaan promo per user |
| `reviews` | Ulasan dan rating venue |
| `notifications` | Notifikasi in-app (Laravel database notifications) |
| `activity_logs` | Log audit setiap perubahan status booking |
| `personal_access_tokens` | Token Sanctum untuk API |
| `sessions`, `cache`, `jobs` | Tabel Laravel standar |

---

## Menjalankan Aplikasi

```bash
# Build assets frontend
npm run build

# Atau development mode (watch)
npm run dev

# Jalankan development server
php artisan serve

# Jalankan queue worker (untuk notifikasi email)
php artisan queue:work

# Jalankan scheduler (untuk auto-expire & auto-complete booking)
php artisan schedule:work
```

Aplikasi dapat diakses di: **http://localhost:8000**

---

## Akun Demo

Setelah menjalankan `migrate:fresh --seed`, akun berikut tersedia:

| Role | Email | Password | Keterangan |
|---|---|---|---|
| **Admin** | `admin@tiketinaja.test` | `password` | Akses penuh platform |
| **Manager** | `manager@tiketinaja.test` | `password` | GBK Arena & Tangkas Sports |
| **Manager 2** | `manager2@tiketinaja.test` | `password` | Sportify Bekasi |
| **Customer** | `customer@tiketinaja.test` | `password` | 13 skenario booking |
| **Customer 2** | `customer2@tiketinaja.test` | `password` | Booking & review tambahan |

### Skenario Booking Demo

Login sebagai `customer@tiketinaja.test` untuk melihat:

| Status | Skenario |
|---|---|
| **Pending** | Booking besok, belum bayar |
| **Pending + Promo** | Menggunakan kode `NEWUSER25` |
| **Confirmed** | Booking besok, sudah bayar |
| **Confirmed + Promo** | Diskon dari kode `GBK20` |
| **Check-in terbuka** | Booking hari ini, ≤10 menit menjelang jam main |
| **🏃 Sedang Bermain** | Check-in aktif, timer countdown real-time |
| **Completed** | Kemarin, belum ada ulasan |
| **Completed + ⭐⭐⭐⭐⭐** | Sudah ada ulasan bintang 5 |
| **Cancelled** | Booking dibatalkan |
| **Expired** | Pembayaran kedaluwarsa |

### Kode Promo Demo

| Kode | Tipe | Nilai | Berlaku |
|---|---|---|---|
| `TIKETIN10` | Persentase | 10% (maks Rp 50.000) | Semua venue |
| `NEWUSER25` | Nominal | Rp 25.000 | Semua venue, min. Rp 100.000 |
| `GBK20` | Persentase | 20% (maks Rp 100.000) | GBK Arena |

---

## Struktur Folder

```
tiketinaja/
├── app/
│   ├── Console/Commands/
│   │   ├── ExpirePendingBookingsCommand.php   # artisan bookings:expire
│   │   └── CompleteFinishedBookingsCommand.php # artisan bookings:complete
│   ├── Enums/
│   │   ├── BookingStatus.php   # Pending|Confirmed|Cancelled|Expired|Completed
│   │   └── PaymentStatus.php   # Pending|Paid|Failed|Expired|Cancelled
│   ├── Http/Controllers/
│   │   ├── Api/                # REST API controllers
│   │   ├── Admin/              # Admin-only controllers
│   │   ├── Auth/               # Breeze auth controllers
│   │   ├── Manage/             # Manager+admin controllers
│   │   ├── BookingController.php
│   │   ├── CheckInController.php
│   │   ├── CourtController.php
│   │   ├── DashboardController.php
│   │   ├── NotificationController.php
│   │   ├── PromoValidateController.php
│   │   ├── ReviewController.php
│   │   └── VenueController.php
│   ├── Models/                 # Eloquent models
│   ├── Notifications/          # Email + database notifications
│   │   ├── BookingConfirmed.php
│   │   ├── BookingCancelled.php
│   │   ├── BookingExpired.php
│   │   ├── CheckInSuccess.php
│   │   └── SessionCompleted.php
│   ├── Policies/
│   │   ├── BookingPolicy.php
│   │   └── VenuePolicy.php
│   └── Services/
│       └── BookingService.php  # Core business logic
├── database/
│   ├── migrations/             # 20 migration files
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── SportSeeder.php
│       ├── DemoSeeder.php      # Data demo lengkap
│       └── PromoSeeder.php
├── resources/views/
│   ├── auth/                   # Login, register, dll.
│   ├── bookings/               # Customer booking views
│   ├── checkin/                # QR check-in views
│   ├── components/             # Reusable Blade components
│   ├── errors/                 # 403, 404, 419, 429, 500
│   ├── layouts/                # app, guest, guest-public
│   ├── manage/                 # Manager/admin views
│   ├── notifications/          # Halaman notifikasi
│   └── venues/                 # Public venue catalog
├── routes/
│   ├── api.php                 # 24 API endpoints
│   ├── console.php             # Scheduler
│   └── web.php                 # Web routes
└── tests/
    ├── Feature/
    │   ├── ApiNotificationTest.php
    │   ├── ApiTest.php
    │   ├── AuthorizationTest.php
    │   ├── BookingEngineTest.php
    │   ├── PromoTest.php
    │   ├── ReviewTest.php
    │   ├── SessionCompletionTest.php
    │   └── Auth/               # Breeze auth tests
    └── Unit/
        ├── BookingStatusTest.php
        └── PromoModelTest.php
```

---

## Entity Relationship Diagram

```
users
 ├─── venues (manager_id) ──── courts ──── booking_items ──── bookings
 │                         │                                      │
 │                         └── operating_hours                    ├── payments
 │                         └── promos ──── promo_usages           ├── reserved_slots
 │                         └── reviews                            ├── reviews
 │                                                                └── activity_logs
 ├─── bookings (user_id)
 ├─── reviews  (user_id)
 └─── notifications (notifiable)

sports ──── courts
```

### Relasi Utama

| Model | Relasi |
|---|---|
| `User` | hasMany: Venue, Booking, Review, Notifications |
| `Venue` | belongsTo: User(manager); hasMany: Court, OperatingHour, Booking, Review, Promo |
| `Court` | belongsTo: Venue, Sport; hasMany: BookingItem, ReservedSlot |
| `Booking` | belongsTo: User, Venue, Promo; hasMany: BookingItem, ReservedSlot; hasOne: Payment, Review |
| `Promo` | belongsTo: Venue(nullable); hasMany: PromoUsage |

---

## Alur Sistem

### Booking Flow (Happy Path)

```
Customer
  │
  ├─ Browse Venue & Lapangan
  ├─ Pilih Tanggal & Slot Jam
  ├─ (Opsional) Input Kode Promo
  ├─ Submit Booking
  │     └─ System: Create Booking(Pending) + ReservedSlots + Payment(Pending)
  │                expires_at = now + 15 menit
  │
  ├─ Bayar (dalam 15 menit)
  │     └─ System: Payment(Paid) + Booking(Confirmed)
  │                Kirim notifikasi: BookingConfirmed
  │
  ├─ Check-in (max 10 mnt sebelum jam main)
  │     └─ System: Booking.checked_in = true
  │                Kirim notifikasi: CheckInSuccess
  │
  └─ Sesi Bermain Selesai
        └─ Scheduler: Booking(Confirmed) → Booking(Completed)
                      Kirim notifikasi: SessionCompleted
                      Customer bisa memberi Review
```

### Status Booking

```
Pending ──[bayar]──► Confirmed ──[selesai]──► Completed
   │                    │
   │[expire/15mnt]       │[batalkan >24j sebelum]
   ▼                    ▼
Expired              Cancelled
```

### Status Pembayaran

```
Pending ──[proses]──► Paid
   │
   ├─[expire]──► Expired
   └─[batal]──► Cancelled
```

### Check-in Window

```
Timeline:   [jam main - 10mnt]  [jam main]  [jam main + durasi]
                    │                 │              │
                    ▼                 ▼              ▼
            Check-in DIBUKA     Waktunya Main!   Sesi SELESAI
            (tombol aktif)      (notif 🏃)       (status → Completed)
```

---

## REST API

**Base URL:** `http://localhost:8000/api`  
**Autentikasi:** Bearer Token (Laravel Sanctum)  
**Content-Type:** `application/json`

### Autentikasi

| Method | Endpoint | Auth | Deskripsi |
|---|---|---|---|
| `POST` | `/auth/login` | — | Login, return token |
| `POST` | `/auth/register` | — | Register customer baru |
| `POST` | `/auth/logout` | ✓ | Revoke token |
| `GET` | `/auth/me` | ✓ | Profil user aktif |
| `PUT` | `/auth/me` | ✓ | Update profil |

**Rate limit:** 10 request/menit untuk endpoint login & register.

**Contoh Login:**
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"customer@tiketinaja.test","password":"password"}'
```

**Response:**
```json
{
  "token": "1|abc123...",
  "user": {
    "id": 4,
    "name": "Andi Wijaya",
    "email": "customer@tiketinaja.test",
    "role": "customer"
  }
}
```

---

### Venue & Lapangan (Publik)

| Method | Endpoint | Deskripsi |
|---|---|---|
| `GET` | `/venues` | Daftar venue (filter: `city`, `sport`, `search`) |
| `GET` | `/venues/{id}` | Detail venue + courts + operating hours |
| `GET` | `/venues/{id}/reviews` | Ulasan venue (paginated) |
| `GET` | `/courts/{id}/availability?date=YYYY-MM-DD` | Slot jam tersedia |
| `GET` | `/sports` | Daftar jenis olahraga |
| `GET` | `/cities` | Daftar kota yang tersedia |

**Contoh Get Venues:**
```bash
curl "http://localhost:8000/api/venues?city=Jakarta+Pusat&sport=1"
```

**Contoh Check Availability:**
```bash
curl "http://localhost:8000/api/courts/1/availability?date=2026-09-01"
```

**Response Availability:**
```json
{
  "court": { "id": 1, "name": "Lapangan Futsal A", "price_per_hour": 150000 },
  "date": "2026-09-01",
  "slots": [
    { "start_time": "08:00", "end_time": "09:00", "available": true },
    { "start_time": "09:00", "end_time": "10:00", "available": false },
    ...
  ]
}
```

---

### Booking (Auth Required)

| Method | Endpoint | Deskripsi |
|---|---|---|
| `GET` | `/bookings` | Daftar booking user aktif |
| `POST` | `/bookings` | Buat booking baru |
| `GET` | `/bookings/{id}` | Detail booking |
| `POST` | `/bookings/{id}/pay` | Proses pembayaran (simulasi) |
| `POST` | `/bookings/{id}/cancel` | Batalkan booking |
| `POST` | `/bookings/{id}/reviews` | Kirim ulasan (hanya setelah Completed) |
| `POST` | `/bookings/{id}/checkin` | Self check-in customer |

**Contoh Create Booking:**
```bash
curl -X POST http://localhost:8000/api/bookings \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "court_id": 1,
    "date": "2026-09-01",
    "time_slots": ["09:00", "10:00"],
    "promo_code": "TIKETIN10"
  }'
```

**Response:**
```json
{
  "message": "Booking berhasil dibuat.",
  "booking": {
    "id": 15,
    "booking_code": "BK-ABCD1234",
    "status": "pending",
    "total_amount": 300000,
    "discount_amount": 30000,
    "expires_at": "2026-09-01T09:15:00.000000Z",
    "payment": { "payment_code": "PAY-XYZ789", "amount": 270000, "status": "pending" }
  }
}
```

---

### Check-in (Auth Required)

| Method | Endpoint | Deskripsi |
|---|---|---|
| `POST` | `/checkin/verify` | Manager scan QR customer |
| `POST` | `/bookings/{id}/checkin` | Customer self check-in |

**Contoh Verify QR (Manager):**
```bash
curl -X POST http://localhost:8000/api/checkin/verify \
  -H "Authorization: Bearer {manager_token}" \
  -H "Content-Type: application/json" \
  -d '{"qr_token": "uuid-dari-qr-customer"}'
```

---

### Promo

| Method | Endpoint | Deskripsi |
|---|---|---|
| `POST` | `/promos/validate` | Validasi kode promo |

**Request:**
```json
{
  "code": "GBK20",
  "amount": 300000,
  "venue_id": 1
}
```

**Response:**
```json
{
  "valid": true,
  "discount_amount": 60000,
  "final_amount": 240000,
  "description": "Diskon 20% khusus GBK Arena"
}
```

---

### Notifikasi (Auth Required)

| Method | Endpoint | Deskripsi |
|---|---|---|
| `GET` | `/notifications` | Daftar notifikasi (paginated) |
| `GET` | `/notifications/unread-count` | Jumlah notifikasi belum dibaca |
| `POST` | `/notifications/{id}/read` | Tandai satu notifikasi dibaca |
| `POST` | `/notifications/read-all` | Tandai semua notifikasi dibaca |

---

### Kode Error API

| Kode | Arti |
|---|---|
| `200` | Sukses |
| `201` | Data berhasil dibuat |
| `401` | Tidak terautentikasi (token tidak valid/tidak ada) |
| `403` | Tidak diizinkan |
| `404` | Data tidak ditemukan |
| `422` | Validasi gagal (cek field `errors`) |
| `429` | Terlalu banyak request (rate limited) |
| `500` | Server error |

---

## Testing

```bash
# Jalankan semua test
php artisan test

# Jalankan test tertentu
php artisan test --filter=BookingEngineTest

# Dengan output detail
php artisan test --ansi

# Coverage report (butuh Xdebug)
php artisan test --coverage
```

### Test Coverage

| Test Suite | Jumlah Test | Cakupan |
|---|---|---|
| `ApiNotificationTest` | 4 | API notifikasi |
| `ApiTest` | 11 | Semua endpoint API |
| `AuthorizationTest` | 10 | Otorisasi manager/admin/customer |
| `BookingEngineTest` | 12 | Core booking + promo + check-in |
| `PromoTest` | 4 | Validasi & CRUD promo |
| `ReviewTest` | 3 | Ulasan booking |
| `SessionCompletionTest` | 5 | Auto-complete sesi |
| `ProfileTest` | 5 | Update profil |
| `Auth/*` | 18 | Breeze authentication |
| `BookingStatusTest` (Unit) | 9 | Enum methods |
| `PromoModelTest` (Unit) | 10 | Kalkulasi diskon |
| **Total** | **94** | **215 assertions** |

---

## Scheduler

Dua command berjalan otomatis via `routes/console.php`:

```php
Schedule::command('bookings:expire')->everyMinute();   // Expire booking pending yang kedaluwarsa
Schedule::command('bookings:complete')->everyMinute(); // Complete booking yang waktu bermainnya sudah selesai
```

**Setup Cron di Server Linux:**
```bash
* * * * * cd /path/to/tiketinaja && php artisan schedule:run >> /dev/null 2>&1
```

**Development (tanpa cron):**
```bash
php artisan schedule:work
```

**Manual trigger:**
```bash
php artisan bookings:expire    # Cek dan expire booking pending
php artisan bookings:complete  # Cek dan complete booking yang selesai
```

---

## Business Rules

1. **Satu lapangan tidak boleh memiliki dua booking aktif pada slot yang sama** — dijaga oleh unique constraint di DB level dan service layer validation.
2. **Booking hanya bisa dilakukan pada jam operasional venue** — sistem membaca `operating_hours` sebelum memunculkan slot.
3. **Booking pending kadaluwarsa dalam 15 menit** — scheduler otomatis merelease slot.
4. **Check-in hanya dibuka 10 menit sebelum jam main** — tidak bisa check-in terlalu awal atau setelah waktu bermain habis.
5. **Pembatalan booking confirmed hanya bisa dilakukan >24 jam sebelum jam main**.
6. **Ulasan hanya bisa dikirim setelah status booking = Completed**.
7. **Kode promo hanya bisa digunakan sekali per user**.
8. **Manager hanya bisa mengelola venue, booking, dan promo miliknya sendiri**.
9. **Admin tidak bisa mengubah atau menghapus akun admin lain**.
10. **Sesi bermain dinyatakan selesai (Completed) otomatis setelah end_time lewat** — dijaga oleh scheduler.

---

## Keamanan

- **CSRF Protection** — semua form web dilindungi token CSRF
- **SQL Injection** — menggunakan Eloquent ORM & parameterized queries
- **XSS** — Blade template auto-escape output
- **Otorisasi** — Policy-based (`BookingPolicy`, `VenuePolicy`) + middleware role
- **Rate Limiting** — Login API dibatasi 10 req/menit
- **Email Verification** — Akun baru wajib verifikasi email
- **Password Hashing** — bcrypt via Laravel Hash facade
- **Sanctum Token** — Token API bisa di-revoke kapan saja

---

## Deliverables Portfolio

- [x] Source code Laravel (GitHub)
- [x] README lengkap dengan fitur, ERD, cara instalasi, API documentation
- [x] Database migrations (20 files) + Demo seeder (13 skenario booking)
- [x] Automated tests (94 tests / 215 assertions)
- [x] REST API dengan Sanctum (24 endpoints)
- [x] Custom error pages (403, 404, 419, 429, 500)
- [x] Business rules documentation
- [x] Scheduler untuk otomasi (expire + complete)

---

## Lisensi

Project ini dibuat untuk keperluan portfolio. Silakan digunakan sebagai referensi pembelajaran.

---

<div align="center">
  <p>Dibuat dengan ❤️ menggunakan Laravel</p>
  <p><strong>TiketinAja</strong> — Platform Booking Lapangan Olahraga</p>
</div>
