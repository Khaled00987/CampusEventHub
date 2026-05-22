# Campus EventHub

A university club and campus events web application submitted as a student assessment project. Administrators manage events, tickets, and announcements. Students browse events and request tickets. Optional **Longcat AI** helps with event drafts and help text. All AI output requires **human review** before anything is saved or published.

## Overview

Campus EventHub includes:

- Secure authentication with **admin** and **user** roles
- Full **Events** and **Announcements** CRUD for administrators
- Ticket request workflow with approval and capacity checks
- Search, filter, and pagination on main lists
- Activity audit logs and AI usage logs
- **Smart Help Assistant** (FAQ-based)
- **AI Event Draft Assistant** (admin, human-in-the-loop)

The application runs on **XAMPP** without Composer or Laravel. It can also deploy to shared hosting using the included `.htaccess` rules.

## Features

| Area | Details |
|------|---------|
| Auth | Register (users only), login, logout, rate limiting, CSRF |
| Events | Public listing; admin CRUD; slug URLs; image upload (JPG, PNG, WEBP, max 2 MB) |
| Tickets | Request, duplicate block, admin approve/reject, capacity check, PDF download for approved tickets |
| Announcements | Admin CRUD; public published list |
| Dashboards | Admin stats and shortcuts; user tickets and upcoming events |
| AI | Event draft and help assistant with local fallback when API keys are empty |
| Security | PDO prepared statements, XSS escaping, role checks, audit logs |

## Tech stack

- PHP 8+
- MySQL (InnoDB, utf8mb4)
- PDO (prepared statements)
- HTML, CSS, JavaScript
- Apache `mod_rewrite`
- Optional Longcat API (cURL)
- FPDF (bundled in `app/lib/fpdf/` for ticket PDFs)

## Setup (XAMPP)

### 1. Copy the project

Place the folder in `htdocs` (any folder name works, for example `CampusEventHub`).

### 2. Environment file

```text
copy .env.example .env
```

Edit `.env` if your MySQL password is not empty:

```env
DB_HOST=127.0.0.1
DB_NAME=campus_eventhub
DB_USER=root
DB_PASS=
```

Leave `LONGCAT_API_KEY` and `LONGCAT_API_URL` empty to use **local fallback** (recommended for local testing).

### 3. Database

1. Start **Apache** and **MySQL** in XAMPP.
2. Open phpMyAdmin: `http://localhost/phpmyadmin`
3. Import `database/schema.sql`
4. Import `database/seed.sql`

See `database/README.md` for CLI commands and migration notes.

### 4. Apache rewrite

Enable `mod_rewrite` and set `AllowOverride All` for your htdocs directory.

### 5. Open the site

`http://localhost/your-folder-name/`

The app detects the folder name automatically. No hardcoded path is required in PHP code.

### Static images

Marketing images live under `public/assets/images/`. Views use the `asset()` and `campus_static_image()` helpers. Do not hardcode `/assets/...` paths in templates.

See `public/assets/images/README.md` for file names and landing page usage.

| File / folder | Purpose |
|---------------|---------|
| `logo.png` | Header, footer, auth pages, dashboard, PDF tickets |
| `hero-right.jpg`, `EVENT-MANAGEMENT.jpg`, etc. | Landing page sections (see images README) |
| `events/` | Admin-uploaded event images |

### Event image upload (admin)

- Form: **Create/Edit Event**, field name `image`
- Allowed: JPG, PNG, WEBP, **max 2 MB**
- Saved under `public/assets/images/events/` with a unique filename stored in the database
- Validation: extension allow-list, MIME check via `finfo`, `move_uploaded_file()` only

Implemented in `app/services/EventImageUpload.php`.

### Ticket PDF download

- Route: `GET /tickets/{id}/download` (use `url()` in views)
- **Users:** approved tickets only; must own the ticket
- **Admins:** any approved ticket from the admin ticket detail page
- Library: FPDF in `app/lib/fpdf/` (no Composer)
- Filename pattern: `ticket_CEH-{ticket_id}-{event_id}-{user_id}.pdf`

## Demo credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@eventhub.test | Admin@123 |
| User | user@eventhub.test | User@123 |

## Folder structure

```text
app/
  config/       Environment, database, Longcat settings
  core/         Router, Auth, CSRF, Validator, Session, Helpers
  controllers/  HTTP actions
  models/       Database access (PDO)
  services/     LongcatService, EventImageUpload, TicketPdfService
  lib/fpdf/     FPDF library for ticket PDFs
  views/        PHP templates
public/
  index.php     Front controller
  assets/       CSS, JS, images
database/
  schema.sql, seed.sql, migrate-local.php
docs/           Proposal, design, security, tests, AI governance
tests/          Manual checklist and results template
```

## Deployment notes

1. Upload all project files to hosting.
2. Point the site to the project root (root `.htaccess` forwards to `public/`) **or** set the document root to `public/`.
3. Copy `.env` with production values; set `APP_DEBUG=false`.
4. Import the database on the host.
5. Confirm `app/`, `database/`, and `docs/` are not publicly accessible (`.htaccess` blocks direct access).

## AI use statement

This project includes **optional** AI features powered by Longcat when API credentials are set in `.env`. If credentials are missing, **local fallback** responses are used so the site works fully offline.

AI is used for:

1. **Event draft suggestions** (admin only). Not auto-published to events.
2. **Help answer polishing** (FAQ text only). Not for general chat.

## Responsible AI statement

- AI output is **never** saved to events or announcements automatically.
- Admins must review and click **Accept Reviewed Draft** before `final_text` is stored in `ai_logs`.
- This disclaimer appears on AI screens and responses:

  **"AI-generated content requires human review before saving or publishing."**

- Ticket personal details (attendee names, emails) are **never** sent to AI.
- AI suggestions are logged in `ai_logs` for accountability.

Full policy: `docs/ai-governance.md`

## Known limitations

- WEBP event images may use a placeholder in PDFs if PHP GD cannot convert them.
- Help assistant only answers Campus EventHub usage questions from FAQ data.
- Anonymous help users are not stored in `ai_logs` (logged-in users only).
- No email notifications for ticket status changes.

## Team roles (example for group projects)

| Role | Responsibilities |
|------|------------------|
| Backend developer | Models, controllers, security, database |
| Frontend developer | Views, CSS, client validation |
| AI / documentation | LongcatService, governance docs, test cases |
| QA / DevOps | XAMPP setup, deployment, manual testing |

Adjust names on your submission cover sheet.

## Git workflow (recommended)

```text
main          Stable submission branch
develop       Integration branch
feature/*     One feature per branch (e.g. feature/ai-draft)
```

1. Create a branch per assessment task.
2. Commit with clear messages (e.g. `feat: add AI draft human review`).
3. Merge to `develop`, test on XAMPP, then merge to `main`.
4. Do not commit `.env`. Only commit `.env.example`.

## Testing

| Resource | Location |
|----------|----------|
| Test case specifications | `docs/test-cases.md` |
| Manual checklist | `tests/manual-test-checklist.md` |
| Results template | `tests/test-results-template.md` |

## Screenshots (for your report)

Add screenshots here before submission:

1. Home page
2. Events listing with filters
3. Admin dashboard
4. AI Event Draft review step
5. Help assistant answer with disclaimer
6. Activity logs

## Licence

Educational assessment use only.
