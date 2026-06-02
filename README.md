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
