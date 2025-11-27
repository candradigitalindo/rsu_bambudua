# Bambudua - Sistem Informasi Klinik

Sistem informasi klinik yang dibangun dengan Laravel 11 untuk mengelola operasional klinik secara terintegrasi, mulai dari pendaftaran pasien, rekam medis, apotek, hingga laporan keuangan.

## Fitur Utama

### 👥 Manajemen User & Role

-   **Owner/Admin**: Dashboard keuangan, laporan, pengaturan sistem
-   **Dokter**: Rekam medis, diagnosis, resep
-   **Perawat**: Asuhan keperawatan, vital signs
-   **Pendaftaran**: Registrasi pasien, antrian
-   **Apotek**: Penyiapan resep, stok obat
-   **Kasir**: Pembayaran, billing
-   **Keuangan**: Laporan keuangan, operasional

### 🏥 Modul Klinik

-   **Rawat Jalan**: Antrian, encounter, billing
-   **Rawat Inap**: Admission, daily medication, discharge
-   **IGD**: Emergency care, triase
-   **Apotek**: Inventory management, FIFO stock rotation
-   **Laboratorium**: Pemeriksaan penunjang
-   **Keuangan**: Pendapatan, pengeluaran, gaji, insentif

### 📊 Dashboard & Laporan

-   Grafik pendapatan bulanan/tahunan
-   Analisis laba rugi
-   Laporan kunjungan pasien
-   Export Excel/PDF
-   Real-time monitoring stok

## Tech Stack

-   **Backend**: Laravel 11, PHP 8.2+
-   **Frontend**: Blade Templates, TailwindCSS, Vite
-   **Database**: MySQL/PostgreSQL
-   **Authentication**: Laravel Fortify
-   **Real-time**: Laravel Reverb
-   **Export**: DomPDF, Maatwebsite Excel
-   **UI**: SweetAlert2

## Instalasi

### Prerequisites

-   PHP 8.2+
-   Composer
-   Node.js & npm
-   MySQL/PostgreSQL

### Setup Development

1. **Clone repository**

    ```bash
    git clone <repository-url>
    cd bambudua
    ```

2. **Install dependencies**

    ```bash
    composer install
    npm install
    ```

3. **Environment setup**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. **Database configuration**
   Edit `.env`:

    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=bambudua
    DB_USERNAME=your_username
    DB_PASSWORD=your_password
    ```

5. **Database migration & seeding**

    ```bash
    php artisan migrate --seed
    ```

6. **Build assets**
    ```bash
    npm run build
    ```

### Development Server

```bash
# Jalankan semua service development
composer run dev

# Atau jalankan manual:
php artisan serve          # Laravel server
npm run dev               # Vite dev server
php artisan queue:work    # Queue worker
```

## Akun Default

Setelah seeding, gunakan akun berikut:

| Role   | Username | Password | Akses                        |
| ------ | -------- | -------- | ---------------------------- |
| Owner  | owner    | password | Dashboard utama, semua modul |
| Admin  | admin    | password | Manajemen sistem             |
| Dokter | dokter   | password | Rekam medis, diagnosis       |
| Apotek | apotek   | password | Manajemen stok, resep        |
| Kasir  | kasir    | password | Billing, pembayaran          |

## Struktur Project

```
app/
├── Enums/              # Enum definitions (UserRole, etc.)
├── Events/             # Event classes
├── Exports/            # Excel export classes
├── Helpers/            # Helper functions
├── Http/Controllers/   # Controllers
├── Imports/            # Excel import classes
├── Models/             # Eloquent models
└── Repositories/       # Data access layer

database/
├── migrations/         # Database schema
├── seeders/           # Sample data
└── factories/         # Model factories

resources/
├── views/             # Blade templates
└── js/                # Frontend assets
```

## Development Guidelines

### Code Style

-   Follow PSR-12 coding standard
-   Use Laravel Pint: `./vendor/bin/pint`
-   TypeScript untuk frontend yang kompleks

### Database

-   Gunakan migration untuk schema changes
-   Foreign key constraints wajib
-   Index pada kolom yang sering diquery

### Performance

-   Gunakan eager loading untuk relasi
-   whereBetween untuk filter tanggal (bukan whereMonth/whereYear)
-   Cache query yang berat
-   Queue untuk proses yang lama

### Security

-   Validasi input pada semua endpoint
-   Authorization menggunakan Policy/Gate
-   Sanitize data sebelum export
-   Log aktivitas sensitif

## Testing

```bash
# Unit & Feature tests
php artisan test

# Coverage report
php artisan test --coverage

# Specific test
php artisan test tests/Feature/ApotekControllerTest.php
```

## Deployment

### Production Setup

1. **Server requirements**

    - PHP 8.2+ dengan extensions: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML
    - Composer
    - Web server (Nginx/Apache)
    - Database server
    - Redis (optional, untuk cache/session)

2. **Deploy steps**

    ```bash
    composer install --no-dev --optimize-autoloader
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    npm run build
    php artisan migrate --force
    ```

3. **Environment variables**

    ```env
    APP_ENV=production
    APP_DEBUG=false
    APP_URL=https://your-domain.com

    # Database
    DB_CONNECTION=mysql
    DB_HOST=your-db-host

    # Cache & Session
    CACHE_DRIVER=redis
    SESSION_DRIVER=redis
    QUEUE_CONNECTION=redis

    # Mail configuration untuk notifikasi
    MAIL_MAILER=smtp
    ```

## Support

Untuk bantuan teknis atau pertanyaan pengembangan:

-   **Documentation**: Lihat file ini dan inline comments
-   **Issues**: Buat issue di repository untuk bug report
-   **Development**: Follow Laravel best practices dan project conventions

## Changelog & Bug Fixes

### Kasir Calculation Fix (27 Nov 2025)

**Problem:** Perhitungan total di kasir/pembayaran tidak akurat

-   E-25112705: DB menunjukkan Rp 513k (seharusnya Rp 363k)
-   E-25111801: DB menunjukkan Rp 210k (seharusnya Rp 213k)

**Root Cause:**

1. `total_bayar_tindakan` tidak terupdate dengan benar
2. `total_resep` dan `total_bayar_resep` tidak ter-calculate saat tambah/hapus resep
3. Missing function `updateEncounterTotalResep()`

**Solution:**

-   Created `updateEncounterTotalResep()` function di `ObservasiRepository`
-   Updated `postResepDetail()` dan `deleteResepDetail()` untuk auto-update totals
-   Fixed historical data dengan recalculation

**Key Learnings:**

-   Jangan gunakan master table price untuk perhitungan, gunakan transaction table
-   Field mapping: `TindakanEncounter.total_harga`, `LabRequestItem.price`, `ResepDetail.total_harga`

### Fee Obat Fix (27 Nov 2025)

**Problem:** Fee obat terbuat untuk dokter yang salah

-   Bug: `practitioner()->with('user')->first()->user->id` returns null karena `practitioners.user_id` tidak exist
-   Actual field: `practitioners.id_petugas`

**Solution:**

-   Added `dpjp_id` field ke encounters table
-   Updated KasirController untuk prioritas: `dpjp_id` → fallback ke `id_petugas`
-   Updated ObservasiRepository (3 locations) untuk gunakan `dpjp_id`
-   Backfilled dpjp_id untuk existing encounters
-   Integrated dpjp_id ke PendaftaranRepository (2 locations)

**Result:** Fee obat sekarang terbuat untuk DPJP yang benar

### Other Features & Fixes

-   **Vital Signs**: Real-time monitoring vital signs di dashboard perawat
-   **Nurse Incentive**: Sistem perhitungan fee perawat berdasarkan shift
-   **Lab/Radiologi Modal**: Custom fields untuk hasil pemeriksaan
-   **Pendaftaran Protection**: Prevent delete pasien yang sudah punya encounter
-   **Dashboard Dokter**: History encounter dan incentive summary
-   **Kasir Split Payment**: Support multiple payment methods per transaction

## License

Project ini menggunakan [MIT license](https://opensource.org/licenses/MIT).
