# Campus EventHub

A university club and local events organiser web application built as a student assessment project. Admins manage events, tickets, and announcements; students browse events and request tickets. Optional **Longcat AI** assists with event drafts and help text — always with **human review** before anything is saved or published.

---

## Overview

Campus EventHub provides:

- Secure authentication with admin and standard user roles
- Full **Events** and **Announcements** CRUD (admin)
- Ticket request workflow with approval and capacity checks
- Search, filter, and pagination on main lists
- Activity audit logs and AI usage logs
- **Smart Help Assistant** (FAQ-based)
- **AI Event Draft Assistant** (admin, human-in-the-loop)

The app runs on **XAMPP** without Composer or Laravel. It also deploys to shared hosting using the included `.htaccess` rules.

---

## Features

| Area | Details |
|------|---------|
| Auth | Register (users only), login, logout, rate limiting, CSRF |
| Events | Public listing; admin CRUD; slug URLs; **image upload** (JPG/PNG/WEBP, max 2MB) |
| Tickets | Request, duplicate block, admin approve/reject, capacity check, **PDF download** for approved tickets |
| Announcements | Admin CRUD; public published list |
| Dashboards | Admin stats + shortcuts; user tickets + upcoming events |
| AI | Event draft + help assistant with fallback when API empty |
| Security | PDO, XSS escaping, role checks, audit logs |

---

## Tech stack

- PHP 8+
- MySQL (InnoDB, utf8mb4)
- PDO (prepared statements)
- HTML / CSS / JavaScript
- Apache mod_rewrite
- Optional Longcat API (cURL)

---

## Setup (XAMPP)

### 1. Copy project

Place the folder in `htdocs` (any folder name works, e.g. `campus-eventhub`).

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

Leave `LONGCAT_API_KEY` and `LONGCAT_API_URL` empty to use **offline fallback** (recommended for local testing).

### 3. Database

1. Start **Apache** and **MySQL** in XAMPP.
2. Open phpMyAdmin: `http://localhost/phpmyadmin`
3. Import **`database/schema.sql`**
4. Import **`database/seed.sql`**

See `database/README.md` for CLI commands.

### 4. Apache rewrite

Enable `mod_rewrite` and set `AllowOverride All` for your htdocs directory.

### 5. Open the site

`http://localhost/your-folder-name/`

URLs adapt automatically — no folder name is hardcoded in code.

### Images

Static files live under `public/assets/images/`. Views use `asset()` — never hardcode `/assets/...` paths.

| File | Purpose |
|------|---------|
| `logo.png` | Logo on all pages |
| `hero.jpg`, `events-bg.jpg`, `community.jpg` | Landing page |
| `placeholder.jpg` | Global fallback |
| `events/` | Uploaded event images + `placeholder-event.jpg` |

### Event image upload (admin)

- Form: **Create/Edit Event** → file input `image`
- Allowed: JPG, PNG, WEBP — **max 2MB**
- Saved to `public/assets/images/events/` with a unique filename in the database
- Validation: extension, MIME type (`finfo`), `move_uploaded_file()`

### Ticket PDF download

- Route: `GET /tickets/{id}/download` (via `url()`)
- **Users:** approved tickets only; must own the ticket
- **Admins:** any approved ticket from admin ticket detail
- Library: **FPDF** in `app/lib/fpdf/` (no Composer)
- Filename: `ticket_CEH-{ticket_id}-{event_id}-{user_id}.pdf`

---

## Demo credentials

| Role | Email | Password |
|------|-------|----------|
| **Admin** | admin@eventhub.test | Admin@123 |
| **User** | user@eventhub.test | User@123 |

---

## Folder structure

```text
app/
  config/       # .env loader, database, longcat
  core/         # Router, Auth, CSRF, Validator, Helpers
  controllers/  # HTTP actions
  models/       # Database access
  services/     # LongcatService, EventImageUpload, TicketPdfService
  lib/fpdf/     # FPDF (no Composer) for ticket PDFs
  views/        # PHP templates
public/
  index.php     # Front controller
  assets/       # CSS, JS, images
database/
  schema.sql, seed.sql
docs/           # Proposal, design, security, tests, AI governance
tests/          # Manual test checklist and results template
```

---

## Deployment notes

1. Upload all files to hosting.
2. Point the site to the project root (root `.htaccess` forwards to `public/`) **or** set document root to `public/`.
3. Copy `.env` with production values; set `APP_DEBUG=false`.
4. Import database on the host.
5. Confirm `app/`, `database/`, and `docs/` are not publicly accessible.

---

## AI Use Statement

This project includes **optional** AI features powered by Longcat when API credentials are configured in `.env`. If credentials are missing, **local fallback** responses are used so the site works fully offline.

AI is used for:

1. **Event draft suggestions** (admin) — not auto-published
2. **Help answer polishing** (FAQ text only) — not for general chat

---

## Responsible AI statement

- AI output is **never** saved to events or announcements automatically.
- Admins must review and click **Accept Reviewed Draft** before `final_text` is stored in `ai_logs`.
- This disclaimer appears on every AI screen and response:

  **"AI-generated content requires human review before saving or publishing."**

- Ticket personal details (attendee names, emails) are **never** sent to AI.
- All AI suggestions are logged in `ai_logs` for accountability.

Full policy: `docs/ai-governance.md`

---

## Known limitations

- WEBP event images may use a placeholder in PDFs if PHP GD cannot convert them.
- Help assistant only answers Campus EventHub usage questions from FAQ data.
- Anonymous help users are not stored in `ai_logs` (logged-in users only).
- No email notifications for ticket status changes.

---

## Team roles (example for group projects)

| Role | Responsibilities |
|------|------------------|
| Backend developer | Models, controllers, security, database |
| Frontend developer | Views, CSS, client validation |
| AI / documentation | LongcatService, governance docs, test cases |
| QA / DevOps | XAMPP setup, deployment, manual testing |

Adjust names in your submission cover sheet.

---

## Git workflow (recommended)

```text
main          → stable submission branch
develop       → integration
feature/*     → one feature per branch (e.g. feature/ai-draft)
```

1. Create a branch per assessment task.
2. Commit with clear messages (e.g. `feat: add AI draft human review`).
3. Merge to `develop`, test on XAMPP, then merge to `main`.
4. Do not commit `.env` — only `.env.example`.

---

## Testing

| Resource | Location |
|----------|----------|
| Test case specifications | `docs/test-cases.md` (12+ cases) |
| Manual checklist | `tests/manual-test-checklist.md` |
| Results template | `tests/test-results-template.md` |

---

## Screenshots (placeholder)

_Add screenshots here for your report:_

1. Home page  
2. Events listing with filters  
3. Admin dashboard  
4. AI Event Draft — review step  
5. Help assistant answer with disclaimer  
6. Activity logs  

---

## Licence

Educational assessment use.
