# IOD Manager

Self-hosted application for a Data Protection Officer (IOD/DPO) managing multiple organizations.

## Scope of the MVP

- multiple companies/organizations with strict tenant separation,
- global IOD account with access to all assigned companies,
- company-scoped read-only administrator accounts,
- GDPR/RODO registers and register entries,
- employee/person authorizations with issue and revocation history,
- recurring and one-off compliance reminders,
- audit log of important changes,
- TOTP two-factor authentication with recovery codes,
- strong password policy and login rate limiting,
- MariaDB/MySQL database,
- Blade + Alpine.js frontend,
- Docker-based self-hosting behind your own HTTPS reverse proxy.

## Technology

- PHP 8.3+
- Laravel 13
- Laravel Fortify (authentication / TOTP 2FA)
- Blade + Alpine.js + Vite
- MariaDB 11
- Redis (optional but recommended for cache, sessions and queues)

Laravel was chosen instead of raw PHP so authentication, CSRF protection, authorization policies, validation, encryption, rate limiting and database migrations are built on well-tested framework primitives.

## Security model

The application is multi-company, but not a public SaaS. Every business record carries a `company_id`. Access is granted through `company_user` memberships. A user with `is_super_admin = true` can administer the whole installation; normal users only see explicitly assigned companies.

Roles in a company:

- `iod` – full operational access to the assigned company,
- `company_admin` – company-scoped read-only overview by default.

Important actions (authorization issue/revocation, register changes, reminder changes, company/user assignments) should be written to `audit_logs`.

### Authentication requirements

- minimum 14-character password,
- upper/lower case, number and symbol,
- login rate limiting,
- TOTP 2FA required for application access,
- recovery codes stored encrypted by Fortify,
- secure cookies enabled in production,
- HTTPS terminated by your reverse proxy (for example Nginx Proxy Manager + Let's Encrypt).

Do not commit `.env`, database dumps, uploaded documents, 2FA secrets or application keys.

## Local / server setup

This repository currently contains the application-specific foundation. Install dependencies after cloning:

```bash
cp .env.example .env
composer install
npm install
php artisan key:generate
php artisan migrate
npm run build
```

Then create the first administrator:

```bash
php artisan iod:create-admin
```

For production set at least:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://iod.example.pl
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
```

Configure the web server document root to `public/` and schedule Laravel tasks every minute:

```cron
* * * * * cd /var/www/iod-manager && php artisan schedule:run >> /dev/null 2>&1
```

## Planned modules

### Companies
Company data, active/inactive status, contact data, IOD assignment and company-scoped users.

### Registers
Generic register engine with predefined GDPR register types, including processing activities, breaches, data-subject requests, processor agreements, DPIA/risk items, training and inspections. Each entry can store structured metadata in JSON so new register types do not require a schema rewrite.

### Authorizations
Issue, expiry and revocation of authorizations. Revocation never deletes history. A later version can generate printable authorization/revocation documents from templates.

### Reminders
One-off and recurring tasks (`daily`, `weekly`, `monthly`, `quarterly`, `yearly`, custom interval), due dates, completion history and optional e-mail notifications.

### Audit
Append-only business audit trail showing who changed what, for which company and when. Application logs are separate from the business audit trail.

## Recommended next implementation steps

1. Complete Laravel/Fortify UI and enforce 2FA after first login.
2. Add CRUD for companies and company memberships.
3. Add register definitions and register-entry CRUD.
4. Add authorization issue/revoke workflow.
5. Add reminder scheduler, completion history and notifications.
6. Add company dashboard and global IOD dashboard.
7. Add export/backup (CSV/PDF where appropriate) and encrypted document attachments.
8. Add automated tests for tenant isolation and authorization policies before production use.

## Deployment note

Because this application will contain compliance records and potentially personal data, keep MariaDB and Redis on a private Docker/network interface, expose only the reverse proxy, make encrypted backups, and test restore procedures regularly.
