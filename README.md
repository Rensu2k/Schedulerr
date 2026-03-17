# Schedule Management System

A Laravel-based meeting and schedule management application with calendar view, event CRUD, export, and admin features.

## Requirements

- PHP 8.2+
- Composer
- Node.js & npm
- MySQL/SQLite/PostgreSQL

## Setup

1. **Clone and install dependencies**

    ```bash
    composer install
    npm install
    ```

2. **Environment**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

3. **Database**
    - Configure `DB_*` in `.env`

    ```bash
    php artisan migrate
    php artisan db:seed
    ```

4. **Storage link**

    ```bash
    php artisan storage:link
    ```

5. **Build assets**

    ```bash
    npm run build
    ```

6. **Run**
    ```bash
    php artisan serve
    ```
    Visit http://localhost:8000

## Scheduler (Email Reminders)

For daily event reminders at 8:00 AM, add to crontab:

```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

Configure mail in `.env` (SMTP, etc.) for reminders to work.

## Features

- Login / session auth
- Calendar dashboard with event management
- Recurring events (daily, weekly, monthly)
- Event attachments
- Excel export (Create Summary)
- iCal export (`/api/events/ical`)
- Email reminders (scheduled)
- Admin dashboard (admin users only)
- User management (admin)
- Audit trail for event changes

## Default Admin

After seeding: `admin@example.com` / `admin123`
