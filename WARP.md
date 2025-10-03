# WARP.md

This file provides guidance to WARP (warp.dev) when working with code in this repository.

Project overview
- Stack: Laravel 11 (PHP 8.2+), Blade, Vite, TailwindCSS, Fortify (auth), Reverb (real-time), DomPDF, Maatwebsite Excel, SweetAlert.
- Package managers: Composer (PHP) and npm (frontend).
- Key entry points:
  - HTTP: routes/web.php
  - Frontend assets: resources/css/app.css, resources/js/app.js (Vite)
  - Tests: tests/Unit, tests/Feature (configured in phpunit.xml)

Common commands
Setup
- Install dependencies
  - composer install
  - npm install
- Initialize environment
  - cp .env.example .env
  - php artisan key:generate
- Database (local)
  - php artisan migrate --seed

Run (development)
- All-in-one dev (concurrently runs app server, queue listener, log viewer, and Vite):
  - composer run dev
- Manual (if you prefer separate terminals):
  - php artisan serve
  - php artisan queue:listen --tries=1
  - php artisan pail --timeout=0
  - npm run dev

Build (frontend)
- Production build
  - npm run build

Lint/format
- PHP (Laravel Pint)
  - ./vendor/bin/pint
  - Check mode (no changes): ./vendor/bin/pint --test
- JavaScript
  - Lint: npm run lint
  - Format: npm run format

Tests
- Run all tests
  - php artisan test
- With coverage
  - php artisan test --coverage
- Run a single test file
  - php artisan test tests/Feature/ApotekControllerTest.php
- Filter by test case or method
  - php artisan test --filter ApotekControllerTest
  - php artisan test tests/Feature/ApotekControllerTest.php --filter test_creates_recipe

Production-oriented tasks (from README highlights)
- Build and optimize (summarized)
  - composer install --no-dev --optimize-autoloader
  - php artisan config:cache && php artisan route:cache && php artisan view:cache
  - npm run build
  - php artisan migrate --force

High-level architecture
Backend (Laravel 11)
- MVC foundation with additional domain-oriented folders:
  - Controllers: app/Http/Controllers handle modules such as pendaftaran (registration), kunjungan (visits/observasi), apotek (pharmacy), loket, kasir, keuangan, masterdata, berita, etc. Route groups in routes/web.php reflect these modules with auth middleware and prefixes (e.g., masterdata, pendaftaran, kunjungan, apotek, loket, kasir, keuangan).
  - Models: app/Models for Eloquent ORM entities.
  - Repositories: app/Repositories indicates a data access abstraction in use for more complex queries or cross-aggregate operations.
  - Domain utilities: app/Helpers (e.g., FormatPrice), plus dedicated folders for Exports and Imports (Excel/PDF flows).
- Authentication & authorization
  - Fortify provides authentication scaffolding and flows.
- Real-time & async
  - Reverb configured for real-time features.
  - Queue workers are used in development (composer run dev starts queue:listen). Configure QUEUE_CONNECTION in .env as needed.
- Configuration
  - Centralized under config/*. Notable: config/services.php, config/queue.php, config/reverb.php, config/sweetalert.php, config/mail.php.
- Routing
  - routes/web.php defines the web interface, redirects root to login, and organizes modules under prefixes with middleware('auth').
  - Examples include data master (wilayah, jaminan, etnis, pendidikan, agama, pekerjaan, spesialis, ruangan, category, tindakan), clinical flows (pendaftaran, encounter, observasi, resep, icd10), pharmacy (products, categories, stock, penyiapan resep), cashier (kasir), finance (keuangan: gaji, insentif, operasional, pendapatan-lain), and reporting/exports.

Frontend (Blade + Vite + Tailwind)
- Vite configuration: vite.config.js with laravel-vite-plugin; inputs are resources/css/app.css and resources/js/app.js. Dev server with hot refresh is enabled (refresh: true).
- Tailwind: tailwind.config.js scans Blade templates, compiled views, and JS/Vue files. PostCSS configured with tailwindcss and autoprefixer.
- Typical dev flow: npm run dev for local HMR; npm run build for production assets consumed by Blade templates.

Testing
- PHPUnit configuration in phpunit.xml defines separate Unit and Feature test suites and a testing environment (array-based cache/session, sync queue). You can run tests via php artisan test, with support for coverage and granular filters.

Notes from README.md
- The README contains module descriptions, tech stack, setup, and deployment steps. For default development accounts and role access, refer to the README’s “Akun Default” section.

Tips specific to this codebase
- Use composer run dev for a cohesive local development experience; it synchronizes Laravel server, queue processing, logs, and Vite.
- When tracking down a flow, start from routes/web.php to locate the controller and then navigate to related models/repositories.
- For heavy or long-running tasks, prefer dispatching to queues; the project already integrates queue listeners in dev.

Indexing suggestions for Warp (optional)
- If indexing is slow or noisy, consider adding a .warpindexingignore with vendor/, node_modules/, storage/, public/build/ to speed up code understanding.
