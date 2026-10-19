# Open Case

Open Case is a Laravel, Inertia, Vue, and Tailwind SPA for Philippine business owners who need a preliminary tax-readiness and business-closure review before working with an internal accountant.

Open Case is not an official BIR website, does not collect BIR taxes or penalties, and does not guarantee exact penalties or business closure.

## MVP Coverage

- Business owner registration, login, Google login hook, password reset, and role redirects
- Guided business profile and case intake
- Private document upload and document-request fulfillment
- Rule-based preliminary result, risk score, recommendations, and checklist
- Service package selection and manual payment reference workflow
- Admin dashboard, payment verification, accountant creation, package creation, and case assignment
- Accountant dashboard, assigned-case review, checklist updates, client/internal notes, document requests, and completion status
- Role-based access control, encrypted TIN storage, private file downloads, and audit logs

## Local Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
npm run build
```

For development:

```bash
php artisan serve --host=127.0.0.1 --port=8000
npm run dev -- --host 127.0.0.1 --port 5173
```

Open `http://127.0.0.1:8000`.

## Seeded Accounts

- Admin: `admin@opencase.local` / `password`
- Accountant: `accountant@opencase.local` / `password`
- Business owners can register from the app.

## Verification

```bash
php artisan test
npm run build
vendor/bin/pint
```

## Pull request checks

The [PHP CI workflow](.github/workflows/php.yml) runs on every pull request,
pushes to master, and manual dispatch. It exposes two independent checks:
**PHP tests** and **PHP formatting**.

Both jobs use PHP 8.5 on Ubuntu 24.04 and install the versions in composer.lock.
The test job generates a temporary application key and uses phpunit.xml's
in-memory SQLite database and array-backed test services. No repository secrets,
external database, frontend build or production environment file is required.
The formatting job checks the whole PHP codebase with Pint without modifying it.

To reproduce locally after installing dependencies, provide a local application
key (or generate one in your local .env), then run:

```bash
composer test
vendor/bin/pint --test
```

To fix formatting locally, run `vendor/bin/pint` and review the diff.
Branch protection is configured separately in GitHub; this workflow does not
make its checks mandatory for merging.
