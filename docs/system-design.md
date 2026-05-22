# System Design: Campus EventHub

## Client, server, and database

```
[ Browser ]
    |  HTTP (GET/POST + CSRF token)
    v
[ Apache + .htaccess ] -> public/index.php
    v
[ Router ] -> [ Controller ] -> [ Model / Service ] -> [ MySQL ]
                |
                v
            [ PHP views + HTML/CSS/JS ]
```

The browser does not connect to MySQL directly. All data access goes through PHP models using PDO.

## MVC-lite structure

| Folder | Role |
|--------|------|
| `app/config/` | Environment, database, AI settings |
| `app/core/` | Router, Auth, CSRF, Validator, Session, Helpers |
| `app/controllers/` | HTTP actions per feature |
| `app/models/` | SQL queries (prepared statements) |
| `app/services/` | LongcatService, EventImageUpload, TicketPdfService |
| `app/views/` | HTML templates |
| `public/` | Entry point and static assets |

## Entity relationships (text)

- **users** one-to-many **events** (`created_by`, `updated_by`)
- **users** one-to-many **ticket_requests**, each ticket many-to-one **events**
- **users** one-to-many **announcements**
- **users** one-to-many **activity_logs** (`user_id` nullable for system actions)
- **users** one-to-many **ai_logs**
- **faq_items** standalone table for the help assistant

Deleting an event that still has tickets is blocked in application logic.

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

Defined in `public/index.php`.

| Method | Path | Controller action |
|--------|------|-------------------|
| GET | `/` | AdminController@home |
| GET/POST | `/login`, `/register`, `/logout` | AuthController |
| GET | `/dashboard` | DashboardController@index |
| GET | `/events`, `/events/{slug}` | EventController |
| POST | `/events/{id}/request-ticket` | EventController@requestTicket |
| GET | `/my-tickets` | TicketController@myTickets |
| GET | `/tickets/{id}/download` | TicketController@download |
| GET | `/announcements` | AnnouncementController@index |
| GET | `/about` | AdminController@about |
| GET/POST | `/help-assistant`, `/help-assistant/ask` | AiController |
| GET/POST | `/admin/events/*` | AdminController |
| GET/POST | `/admin/tickets/*` | TicketController |
| GET/POST | `/admin/announcements/*` | AnnouncementController |
| GET/POST | `/admin/ai-draft/*` | AiController |
| GET | `/admin/ai-logs` | AiController@aiLogs |
| GET | `/admin/activity-logs` | AdminController@activityLogs |

## Security design

- **Authentication:** bcrypt passwords, session regeneration on login, idle timeout.
- **Authorization:** `Auth::requireAdmin()` on admin routes.
- **Input:** server `Validator` plus client `validation.js`.
- **Output:** `e()` escaping in views.
- **CSRF:** token on every POST form.
- **SQL:** prepared statements only in models.
- **AI:** no auto-publish; FAQ-only help; no ticket PII to API.
- **Files:** `.htaccess` blocks web access to `app/`, `database/`, and `docs/`.

## Universal URLs

`BASE_URL` is computed from `$_SERVER['SCRIPT_NAME']` so the app runs in any htdocs subfolder or live hosting path without code changes.
