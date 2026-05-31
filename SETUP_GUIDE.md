# Open Case Setup Guide

Final corrected setup guide for **Open Case** using:

- Laravel 12
- PHP 8.5
- Inertia.js v3
- Vue 3
- Vue Options API where practical
- Tailwind CSS 4
- shadcn-vue
- npm only
- Laravel Socialite for Google login
- Pest 4 with browser testing
- Playwright
- oxlint and oxfmt

## 1. Final Setup Decision

This setup uses **npm only** for frontend package management.

Do not use:

```bash
pnpm
yarn
bun
```

For consistency, commit only:

```txt
package-lock.json
```

Do not commit:

```txt
pnpm-lock.yaml
yarn.lock
bun.lockb
```

## 2. Why npm Only

npm is already included with Node.js, Laravel documentation commonly uses npm, and it is easier to onboard other developers with fewer setup decisions.

pnpm is still good, but for this project, npm is enough. Less tool drama, more shipping. Ancient wisdom.

## 3. Important Inertia v3 Correction

For Inertia v3, use:

```bash
npm install @inertiajs/vue3 @inertiajs/vite
```

The key correction is the `@inertiajs/vite` plugin.

Inertia v3 can automatically handle page resolution and mounting when the Vite plugin is used. That means this is now valid:

```js
import { createInertiaApp } from '@inertiajs/vue3'

createInertiaApp()
```

Avoid using the older manual `resolve` and `setup` approach unless you specifically need it. Manual setup still works, but it skips some automatic behavior from the Inertia v3 Vite plugin.

## 4. Prerequisites

Install these first:

1. PHP 8.5
2. Composer
3. MySQL or PostgreSQL
4. Node.js with npm
5. Git
6. Chrome or Chromium for browser testing
7. Google Cloud OAuth credentials for Google login

Check versions:

```bash
php -v
composer -V
node -v
npm -v
git --version
```

## 5. Create Laravel Project

Create the Laravel app:

```bash
composer create-project laravel/laravel open-case
cd open-case
```

Create `.env`:

```bash
cp .env.example .env
php artisan key:generate
```

## 6. Configure Environment

Update `.env`:

```env
APP_NAME="Open Case"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=open_case
DB_USERNAME=root
DB_PASSWORD=
```

Create the database manually:

```sql
CREATE DATABASE open_case;
```

Run migrations:

```bash
php artisan migrate
```

## 7. Install Inertia Laravel Server Adapter

Install the Laravel adapter:

```bash
composer require inertiajs/inertia-laravel
```

Publish the Inertia middleware:

```bash
php artisan inertia:middleware
```

## 8. Register Inertia Middleware

Open:

```txt
bootstrap/app.php
```

Update the middleware section:

```php
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
```

## 9. Create Inertia Root Template

Create the root template:

```bash
mkdir -p resources/views
touch resources/views/app.blade.php
```

Add this to `resources/views/app.blade.php`:

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title inertia>{{ config('app.name', 'Open Case') }}</title>
    @vite('resources/js/app.js')
    <x-inertia::head />
</head>
<body>
    <x-inertia::app />
</body>
</html>
```

This is the correct Inertia v3 style for the root Blade file.

## 10. Install Frontend Dependencies With npm

Install the existing Laravel frontend dependencies first:

```bash
npm install
```

Install Vue and the Vue Vite plugin:

```bash
npm install vue @vitejs/plugin-vue
```

Install Inertia v3 packages:

```bash
npm install @inertiajs/vue3 @inertiajs/vite
```

Install Tailwind CSS 4 Vite plugin:

```bash
npm install tailwindcss @tailwindcss/vite
```

## 11. Configure Vite

Update `vite.config.js`:

```js
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import inertia from '@inertiajs/vite'
import tailwindcss from '@tailwindcss/vite'
import { fileURLToPath, URL } from 'node:url'

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/js/app.js'],
      refresh: true,
    }),
    vue(),
    inertia(),
    tailwindcss(),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./resources/js', import.meta.url)),
    },
  },
})
```

Notes:

1. `@inertiajs/vite` is required for the cleaner Inertia v3 setup.
2. `@vitejs/plugin-vue` is required because the frontend uses Vue.
3. `@tailwindcss/vite` is the Tailwind CSS 4 Vite plugin.
4. The `@` alias helps with cleaner imports, especially when using shadcn-vue.

## 12. Configure Tailwind CSS 4

Open:

```txt
resources/css/app.css
```

Replace the content with:

```css
@import "tailwindcss";
```

Since the Vite input is only `resources/js/app.js`, import the CSS inside the JavaScript entry file.

## 13. Create Inertia App Entry

Update `resources/js/app.js`:

```js
import '../css/app.css'
import { createInertiaApp } from '@inertiajs/vue3'

createInertiaApp()
```

This is the final Inertia v3 style.

The Inertia Vite plugin will automatically look for pages inside:

```txt
resources/js/Pages
resources/js/pages
```

For this project, use:

```txt
resources/js/Pages
```

## 14. Add Optional App Customization

If later you need to register Vue plugins, use `withApp`.

Example:

```js
import '../css/app.css'
import { createInertiaApp } from '@inertiajs/vue3'

createInertiaApp({
  withApp(app) {
    // app.use(SomePlugin)
  },
})
```

## 15. Create First Page

Create folders:

```bash
mkdir -p resources/js/Pages
```

Create:

```bash
touch resources/js/Pages/Dashboard.vue
```

Add this to `resources/js/Pages/Dashboard.vue`:

```vue
<template>
  <main class="mx-auto max-w-6xl p-6">
    <h1 class="text-2xl font-semibold">Open Case Dashboard</h1>

    <p class="mt-2 text-sm text-gray-600">
      Track your BIR case review, uploaded documents, and accountant updates.
    </p>
  </main>
</template>

<script>
export default {
  name: 'Dashboard',
}
</script>
```

Yes, you can use the **Vue Options API** with Inertia pages. Inertia does not force the Composition API.

## 16. Create Test Route

Open:

```txt
routes/web.php
```

Add:

```php
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Dashboard');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');
```

## 17. Run The App

Run Laravel:

```bash
php artisan serve
```

In a second terminal, run Vite:

```bash
npm run dev
```

Open:

```txt
http://localhost:8000
```

You should see the Open Case Dashboard page.

## 18. Install shadcn-vue

Initialize shadcn-vue:

```bash
npx shadcn-vue@latest init
```

Recommended settings:

```txt
Style: Default
Base color: Neutral
CSS file: resources/css/app.css
Component directory: resources/js/Components/ui
Utils directory: resources/js/lib/utils
Import alias: @/*
```

Add common MVP components:

```bash
npx shadcn-vue@latest add button
npx shadcn-vue@latest add card
npx shadcn-vue@latest add input
npx shadcn-vue@latest add label
npx shadcn-vue@latest add textarea
npx shadcn-vue@latest add select
npx shadcn-vue@latest add dialog
npx shadcn-vue@latest add table
npx shadcn-vue@latest add badge
npx shadcn-vue@latest add alert
npx shadcn-vue@latest add separator
npx shadcn-vue@latest add dropdown-menu
```

Note:

Many shadcn-vue examples use Vue Composition API. That is fine. You can still use Options API for your pages and wrap Composition API components where needed.

## 19. Suggested Frontend Folder Structure

Use this structure:

```txt
resources/
  css/
    app.css
  js/
    app.js
    Components/
      ui/
    Layouts/
      AppLayout.vue
      GuestLayout.vue
      AdminLayout.vue
      AccountantLayout.vue
    Pages/
      Auth/
        Login.vue
        Register.vue
      Owner/
        Dashboard.vue
        Cases/
          Index.vue
          Create.vue
          Show.vue
      Accountant/
        Cases/
          Index.vue
          Show.vue
      Admin/
        Dashboard.vue
        Accountants/
          Index.vue
          Create.vue
        Cases/
          Index.vue
          Show.vue
    lib/
      utils.js
```

## 20. Authentication Plan

Open Case should support:

1. Email and password login
2. Continue with Google
3. Password reset
4. Email verification
5. Role based redirects
6. Admin created accountant accounts
7. Two factor authentication for admin and accountant accounts

Roles:

```txt
business_owner
accountant
admin
```

Important:

Accountants must not register publicly. Admin creates accountant accounts only.

## 21. Install Laravel Socialite For Google Login

Install Socialite:

```bash
composer require laravel/socialite
```

Add to `.env`:

```env
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

Add to `config/services.php`:

```php
'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT_URI'),
],
```

## 22. Create Google Auth Controller

Create controller:

```bash
php artisan make:controller Auth/GoogleAuthController
```

Add this:

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::query()
            ->where('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            $user->update([
                'provider' => $user->provider ?: 'google',
                'provider_id' => $user->provider_id ?: $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'email_verified_at' => $user->email_verified_at ?: now(),
            ]);
        } else {
            $user = User::create([
                'first_name' => $googleUser->user['given_name'] ?? $googleUser->getName(),
                'last_name' => $googleUser->user['family_name'] ?? '',
                'email' => $googleUser->getEmail(),
                'provider' => 'google',
                'provider_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'email_verified_at' => now(),
                'role' => 'business_owner',
            ]);
        }

        Auth::login($user, true);

        return match ($user->role) {
            'admin' => redirect()->intended('/admin'),
            'accountant' => redirect()->intended('/accountant/cases'),
            default => redirect()->intended('/dashboard'),
        };
    }
}
```

## 23. Add Google Login Routes

In `routes/web.php`:

```php
use App\Http\Controllers\Auth\GoogleAuthController;

Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])
    ->name('google.redirect');

Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])
    ->name('google.callback');
```

## 24. Google Login Button

Use:

```vue
<template>
  <a
    href="/auth/google/redirect"
    class="inline-flex w-full items-center justify-center rounded-lg border px-4 py-2 text-sm font-medium"
  >
    Continue with Google
  </a>
</template>

<script>
export default {
  name: 'GoogleLoginButton',
}
</script>
```

Use this wording:

```txt
Continue with Google
```

Do not use:

```txt
Login with Gmail
```

Google login is for authentication only. Do not request Gmail, Google Drive, or Google Calendar access.

## 25. Update Users Table For Roles And Google Login

Create migration:

```bash
php artisan make:migration add_open_case_auth_fields_to_users_table
```

Example migration:

```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('id');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('phone')->nullable()->after('email');
            $table->string('role')->default('business_owner')->after('password');
            $table->string('provider')->nullable()->after('role');
            $table->string('provider_id')->nullable()->after('provider');
            $table->string('avatar')->nullable()->after('provider_id');

            $table->unique(['provider', 'provider_id']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['provider', 'provider_id']);
            $table->dropColumn([
                'first_name',
                'last_name',
                'phone',
                'role',
                'provider',
                'provider_id',
                'avatar',
            ]);
        });
    }
};
```

Run:

```bash
php artisan migrate
```

Update the `User` model fillable fields:

```php
protected $fillable = [
    'first_name',
    'last_name',
    'name',
    'email',
    'phone',
    'password',
    'role',
    'provider',
    'provider_id',
    'avatar',
    'email_verified_at',
];
```

## 26. Roles And Permissions

Install Spatie Permission:

```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

Suggested roles:

```txt
admin
accountant
business_owner
```

Suggested permissions:

```txt
view own cases
create cases
upload own documents
view assigned cases
update assigned cases
manage users
manage accountant accounts
manage service packages
manage case rules
```

## 27. File Uploads

Install Spatie Media Library:

```bash
composer require spatie/laravel-medialibrary
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-migrations"
php artisan migrate
```

Update `.env`:

```env
FILESYSTEM_DISK=local
```

Rules:

1. Do not store taxpayer files in public storage.
2. Use private storage.
3. Serve documents through authorization checked routes.
4. Use signed URLs if needed.
5. Log every document view and download.

## 28. Suggested Backend Folder Structure

```txt
app/
  Actions/
    Cases/
    Documents/
    Payments/
  Enums/
  Http/
    Controllers/
      Admin/
      Accountant/
      Auth/
      Owner/
    Middleware/
    Requests/
  Models/
  Policies/
  Services/
    CaseAssessment/
    Documents/
    Payments/
resources/
  js/
    Components/
    Layouts/
    Pages/
routes/
  web.php
tests/
  Browser/
  Feature/
  Unit/
```

## 29. Suggested Models

Create models:

```bash
php artisan make:model Business -m
php artisan make:model TaxCase -m
php artisan make:model CaseAnswer -m
php artisan make:model CaseResult -m
php artisan make:model ChecklistItem -m
php artisan make:model CaseNote -m
php artisan make:model ServicePackage -m
php artisan make:model Payment -m
php artisan make:model AuditLog -m
```

Use `TaxCase` as the Laravel model name because `Case` is not a good class name in PHP projects.

The table can still be:

```txt
cases
```

## 30. Case Assessment Service

Create:

```bash
mkdir -p app/Services/CaseAssessment
touch app/Services/CaseAssessment/AssessCase.php
```

Example:

```php
<?php

namespace App\Services\CaseAssessment;

use App\Models\TaxCase;

class AssessCase
{
    public function handle(TaxCase $case): array
    {
        $answers = $case->answers()->pluck('answer_value', 'question_key');

        $riskScore = 0;
        $recommendations = [];

        if (($answers['has_cor'] ?? null) === 'no') {
            $riskScore += 20;
            $recommendations[] = 'Upload your Certificate of Registration for accurate review.';
        }

        if (
            ($answers['stopped_operating'] ?? null) === 'yes'
            && ($answers['filed_closure'] ?? null) === 'no'
        ) {
            $riskScore += 35;
            $recommendations[] = 'Business closure review is recommended.';
        }

        if (($answers['received_bir_notice'] ?? null) === 'yes') {
            $riskScore += 40;
            $recommendations[] = 'Accountant review is strongly recommended.';
        }

        return [
            'risk_score' => min($riskScore, 100),
            'result_type' => $this->resultType($riskScore),
            'recommendations' => $recommendations,
        ];
    }

    private function resultType(int $riskScore): string
    {
        return match (true) {
            $riskScore >= 70 => 'accountant_required',
            $riskScore >= 45 => 'likely_open_case',
            $riskScore >= 25 => 'needs_review',
            default => 'low_risk',
        };
    }
}
```

## 31. Payments

For MVP, choose one payment approach:

1. Manual payment verification
2. PayMongo
3. Stripe, if available for your business setup

Payment rules:

1. Payment is for Open Case service fees only.
2. Do not label payment as BIR tax payment.
3. Track pending, paid, failed, refunded, and cancelled statuses.
4. Do not assign accountant review until payment is confirmed, unless admin overrides.

## 32. Install Pest 4

Install Pest:

```bash
composer require pestphp/pest pestphp/pest-plugin-laravel --dev --with-all-dependencies
php artisan pest:install
```

Install browser testing:

```bash
composer require pestphp/pest-plugin-browser --dev --with-all-dependencies
npm install --save-dev playwright
npx playwright install
```

Run tests:

```bash
php artisan test
./vendor/bin/pest
./vendor/bin/pest tests/Browser
```

Suggested tests:

1. Business owner can register.
2. Business owner can log in.
3. Business owner can continue with Google.
4. Business owner can create a business profile.
5. Business owner can start a case.
6. Business owner can upload a document.
7. Business owner receives a preliminary result.
8. User cannot access another user's case.
9. Accountant cannot access an unassigned case.
10. Admin can assign accountant.
11. Payment status controls accountant assignment.

## 33. Install oxlint And oxfmt

Install:

```bash
npm install --save-dev oxlint oxfmt
```

Update `package.json` scripts:

```json
{
  "scripts": {
    "dev": "vite",
    "build": "vite build",
    "lint": "oxlint resources/js",
    "format": "oxfmt --write resources/js"
  }
}
```

Run:

```bash
npm run lint
npm run format
```

## 34. Recommended package.json Scripts

Use this:

```json
{
  "private": true,
  "type": "module",
  "scripts": {
    "dev": "vite",
    "build": "vite build",
    "lint": "oxlint resources/js",
    "format": "oxfmt --write resources/js"
  },
  "dependencies": {
    "@inertiajs/vite": "latest",
    "@inertiajs/vue3": "latest",
    "@tailwindcss/vite": "latest",
    "@vitejs/plugin-vue": "latest",
    "tailwindcss": "latest",
    "vue": "latest"
  },
  "devDependencies": {
    "oxfmt": "latest",
    "oxlint": "latest",
    "playwright": "latest"
  }
}
```

Do not blindly paste this over your whole file if Laravel already generated other dependencies. Merge it carefully.

## 35. Development Commands

Run Laravel:

```bash
php artisan serve
```

Run Vite:

```bash
npm run dev
```

Run queue worker:

```bash
php artisan queue:work
```

Run migrations and seeders:

```bash
php artisan migrate:fresh --seed
```

Run PHP tests:

```bash
php artisan test
```

Run Pest:

```bash
./vendor/bin/pest
```

## 36. Suggested Seeders

Create seeders:

```bash
php artisan make:seeder RoleSeeder
php artisan make:seeder AdminUserSeeder
php artisan make:seeder AccountantUserSeeder
php artisan make:seeder ServicePackageSeeder
```

Seed roles:

```txt
admin
accountant
business_owner
```

Seed service packages:

```txt
Basic Review
Open Case Review
Business Closure Assistance
Full Compliance Cleanup
Monthly Compliance Help
```

## 37. Environment Variables Checklist

```env
APP_NAME="Open Case"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=open_case
DB_USERNAME=root
DB_PASSWORD=

FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=
MAIL_FROM_ADDRESS="support@opencase.local"
MAIL_FROM_NAME="Open Case"

GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"

PAYMENT_PROVIDER=
PAYMENT_SECRET_KEY=
PAYMENT_PUBLIC_KEY=
```

## 38. Google Cloud OAuth Setup

In Google Cloud Console:

1. Create a project.
2. Configure OAuth consent screen.
3. Create OAuth Client ID.
4. Choose Web Application.
5. Add authorized redirect URI.

Local redirect URI:

```txt
http://localhost:8000/auth/google/callback
```

Production redirect URI:

```txt
https://yourdomain.com/auth/google/callback
```

Only request basic profile and email scopes.

Do not request:

```txt
Gmail inbox access
Google Drive access
Google Calendar access
```

## 39. Production Checklist

Before launch:

1. Set `APP_ENV=production`.
2. Set `APP_DEBUG=false`.
3. Configure HTTPS.
4. Configure private file storage.
5. Configure database backups.
6. Configure queue worker.
7. Configure mail.
8. Configure payment webhooks.
9. Configure error monitoring.
10. Require two factor authentication for admin and accountant accounts.
11. Add privacy policy.
12. Add terms of service.
13. Add clear tax disclaimers.
14. Test role access.
15. Test document upload authorization.
16. Test payment flow.
17. Test Google login.
18. Test browser flows.
19. Redact all sample documents used in demos.

## 40. Final Setup Summary

Use this as the final MVP setup:

```txt
Laravel 12
PHP 8.5
Inertia.js v3
Vue 3
@inertiajs/vite
Tailwind CSS 4
shadcn-vue
npm only
Laravel Socialite
Pest 4
Playwright
oxlint
oxfmt
```

The most important changes from the older setup guide are:

1. Use npm only.
2. Add `@inertiajs/vite`.
3. Use `createInertiaApp()` with no manual resolver.
4. Use `<x-inertia::head />` and `<x-inertia::app />`.
5. Import Tailwind CSS through `resources/js/app.js`.
6. Keep accountant registration closed and admin controlled.

## 41. Official References

1. Inertia v3 server side setup: https://inertiajs.com/docs/v3/installation/server-side-setup
2. Inertia v3 client side setup: https://inertiajs.com/docs/v3/installation/client-side-setup
3. Laravel 12 Vite documentation: https://laravel.com/docs/12.x/vite
4. Tailwind CSS Vite installation: https://tailwindcss.com/docs/installation/using-vite
5. Laravel 12 Socialite documentation: https://laravel.com/docs/12.x/socialite
6. Laravel 12 starter kits: https://laravel.com/docs/12.x/starter-kits
7. Inertia v3 demo repository: https://github.com/inertiajs/demo-v3
