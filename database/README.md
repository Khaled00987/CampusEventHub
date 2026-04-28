# Database Setup

## Files

| File | Purpose |
|------|---------|
| `schema.sql` | Creates database, tables, indexes, and foreign keys |
| `seed.sql` | Sample users, events, announcements, FAQs, and tickets |

## Installation (XAMPP)

1. Start **Apache** and **MySQL** in XAMPP Control Panel.
2. Open phpMyAdmin: `http://localhost/phpmyadmin`
3. Import `schema.sql`, then `seed.sql`.
4. Copy `.env.example` to `.env` in the project root and set `DB_*` values if needed.

## Default accounts

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

## Update an existing local database (without re-import)

If your DB was created before newer features (event images, AI logs, FAQ, activity logs), run:

```bash
php database/migrate-local.php
```

Or in browser (localhost only): `http://localhost/Campus-EventHub/database/migrate-local.php`

**Ticket PDF download** does not require new columns — ticket codes are built in PHP from existing `ticket_requests` rows.
