# CSMS — Project Stack & Versions

## Backend
- **PHP** ^8.2
- **Laravel** ^12.0
- **Livewire** ^4.0
- **Laravel Tinker** ^2.10.1
- **barryvdh/laravel-dompdf** ^3.1
- **blade-ui-kit/blade-heroicons** ^2.6
- **google/apiclient** *
- **masbug/flysystem-google-drive-ext** *
- **predis/predis** ^3.3

## Backend (Dev)
- **fakerphp/faker** ^1.23
- **laravel/pail** ^1.2.2
- **laravel/pint** ^1.13
- **laravel/sail** ^1.41
- **mockery/mockery** ^1.6
- **nunomaduro/collision** ^8.6
- **phpunit/phpunit** ^11.5.3

## Frontend
- **Vite** ^6.0.11
- **Tailwind CSS** ^4.1.18
- **@tailwindcss/vite** ^4.1.18
- **DaisyUI** ^5.5.14
- **Alpine.js** (via laravel-precognition-alpine ^1.0.2)
- **Axios** ^1.7.4
- **Flatpickr** ^4.6.13
- **Boxicons** ^2.1.4
- **autoprefixer** ^10.4.23
- **postcss** ^8.5.6
- **concurrently** ^9.0.1
- **laravel-vite-plugin** ^1.2.0

## Database
- **MySQL** (production)
- **SQLite** (local/dev fallback)

## Session / Cache / Queue
- Driver: `database` (production) — requires `sessions`, `cache`, `jobs` tables
- Fallback: `file` driver (local/Hostinger if tables missing)
