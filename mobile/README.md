# Building Manager Mobile — Phase 1

NativePHP Mobile client for the Building Manager Laravel API. Phase 1 contains project setup, the API layer, secure token handling, and authentication only.

## Requirements

- PHP 8.3+
- Composer
- Node.js and npm
- Android Studio for Android builds
- The Laravel backend running and reachable from the device

NativePHP v3.3 is installed because the current development environment uses PHP 8.3. NativePHP v4 requires PHP 8.4.

## Configuration

Copy `.env.example` to `.env`, generate the application key, and set the backend URL:

```dotenv
API_BASE_URL=http://127.0.0.1:8000/api
API_TIMEOUT=15
NATIVEPHP_APP_ID=com.buildingmanager.mobile
```

Use the correct backend address for the target:

- Browser preview: `http://127.0.0.1:8000/api`
- Android emulator: `http://10.0.2.2:8000/api`
- Physical device: `http://YOUR_COMPUTER_LAN_IP:8000/api`
- Production: an HTTPS URL

## Install and verify

```bash
composer install
npm install
npm run build
php artisan test
```

Run a browser preview first:

```bash
php artisan serve
```

Then prepare and run the Android application:

```bash
php artisan native:install android
php artisan native:run android
```

## Test accounts

After running the backend seeder:

| Role | Mobile | Password |
|---|---|---|
| Admin | `09120000001` | `123456` |
| Manager | `09120000002` | `123456` |
| Resident | `09120000003` | `123456` |

## Phase 1 architecture

```text
Livewire Screen / ViewModel
        ↓
AuthService
        ↓
ApiClient
        ↓
Laravel REST API
```

The Sanctum token is stored through NativePHP `SecureStorage` on Android/iOS. Browser development uses the Laravel session fallback. Passwords are never persisted.
