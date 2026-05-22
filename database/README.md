# Database Setup

## Files

| File | Purpose |
|------|---------|
| `schema.sql` | Creates database, tables, indexes, and foreign keys |
| `seed.sql` | Sample users, events, announcements, FAQs, and tickets |
| `migrate-local.php` | Adds missing columns on an older local database (localhost only) |

## Installation (XAMPP)

1. Start **Apache** and **MySQL** in the XAMPP Control Panel.
2. Open phpMyAdmin: `http://localhost/phpmyadmin`
3. Import `schema.sql`, then `seed.sql`.
4. Copy `.env.example` to `.env` in the project root and set `DB_*` values if needed.

Default database name in schema: `campus_eventhub`.

## Demo accounts

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@eventhub.test | Admin@123 |
| User | user@eventhub.test | User@123 |

Passwords are stored as **bcrypt** hashes compatible with PHP `password_verify()`.

## CLI alternative

```bash
mysql -u root -p < database/schema.sql
mysql -u root -p < database/seed.sql
```

## Update an existing local database

If your database was created before newer features (event images, AI logs, FAQ, activity logs), run:

```bash
php database/migrate-local.php
```

Or in the browser (localhost only):

`http://localhost/CampusEventHub/database/migrate-local.php`

## Ticket PDF notes

Ticket PDF download does not need extra database columns. Ticket codes are built in PHP from existing `ticket_requests` rows in `TicketPdfService.php`.
