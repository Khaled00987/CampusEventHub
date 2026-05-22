# System Design — Campus EventHub

## Client–server–database architecture

```
[ Browser ]
    │  HTTP (GET/POST + CSRF token)
    ▼
[ Apache + .htaccess ] → public/index.php
    ▼
[ Router ] → [ Controller ] → [ Model / Service ] → [ MySQL ]
                │
                ▼
            [ PHP Views + HTML/CSS/JS ]
```

The browser never talks to MySQL directly. All data access goes through PHP models using PDO.

## MVC-lite structure

| Folder | Role |
|--------|------|
| `app/config/` | Environment, database, AI settings |
| `app/core/` | Router, Auth, CSRF, Validator, Session, Helpers |
| `app/controllers/` | HTTP actions per feature |
| `app/models/` | SQL queries (prepared statements) |
| `app/services/` | LongcatService (external AI) |
| `app/views/` | HTML templates |
| `public/` | Entry point and static assets |

## ERD (text description)

- **users** 1—* **events** (created_by, updated_by)
- **users** 1—* **ticket_requests** *—1 **events**
- **users** 1—* **announcements**
- **users** 1—* **activity_logs** (nullable user_id)
- **users** 1—* **ai_logs**
- **faq_items** — standalone reference for help assistant

Deleting an event with tickets is blocked in application logic (FK CASCADE exists but UI prevents delete).

## Tables

| Table | Purpose |
|-------|---------|
| users | Accounts and roles |
| events | Campus events |
| ticket_requests | Ticket workflow |
| announcements | News posts |
| activity_logs | Audit trail |
| ai_logs | AI governance |
| faq_items | Help knowledge base |

## Route list

| Method | Path | Controller action |
|--------|------|-------------------|
| GET | / | AdminController@home |
| GET/POST | /login, /register, /logout | AuthController |
| GET | /dashboard | DashboardController@index |
| GET | /events, /events/{slug} | EventController |
| POST | /events/{id}/request-ticket | EventController |
| GET | /my-tickets | TicketController |
| GET | /announcements | AnnouncementController |
| GET/POST | /help-assistant, /help-assistant/ask | AiController |
| GET/POST | /admin/events/* | AdminController |
| GET/POST | /admin/tickets/* | TicketController |
| GET/POST | /admin/announcements/* | AnnouncementController |
| GET/POST | /admin/ai-draft/* | AiController |
| GET | /admin/ai-logs, /admin/activity-logs | AiController, AdminController |

## Security design

- **Authentication:** bcrypt passwords, session regeneration on login, idle timeout.
- **Authorization:** `Auth::requireAdmin()` on admin routes.
- **Input:** Server `Validator` + client `validation.js`.
- **Output:** `e()` escaping in all views.
- **CSRF:** Token on every POST form.
- **SQL:** Prepared statements only in models.
- **AI:** No auto-publish; FAQ-only help; no ticket PII to API.
- **Files:** Block web access to `app/`, `database/`, `docs/` via `.htaccess`.

## Universal URLs

`BASE_URL` is computed from `$_SERVER['SCRIPT_NAME']` so the app runs in any htdocs subfolder or live hosting path without code changes.
