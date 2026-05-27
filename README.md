# Campus EventHub

## 1. Project Overview

Campus EventHub is a PHP and MySQL web application for campus events, ticket requests, announcements, PDF tickets, and responsible AI assistance. It is built with an MVC-lite structure and focuses on secure, practical workflows for event organisers and students.

The platform supports three user groups: public visitors, standard users, and administrators. Public visitors can explore published content, standard users can request and track tickets, and administrators can manage events, tickets, announcements, and AI-supported drafting with human review.

## 2. Assessment Context

This project is submitted for:

| Item | Value |
|------|-------|
| Unit | ICT203 Web Application Development |
| Assessment | Assessment 3 |
| Assessment Type | Full-Stack Web Application with Responsible AI Integration |

## 3. Team Members

| Name | Student ID | Responsibility |
|------|------------|----------------|
| Khaled Hasan | CIHE250337 | Team Lead / Scrum Master |
| Sunir | CIHE240723 | Front-End Lead |
| Salman Shoshe | CIHE231333 | Back-End and Data/QA Lead |

## 4. Main Features

| Feature | Description |
|---------|-------------|
| Authentication | User registration, login, logout, session-based access control |
| Role-based access | Admin and standard user permissions with protected admin routes |
| Events management | Public event listing and full admin CRUD |
| Event image upload | Admin upload from device with validation and secure storage |
| Ticket request workflow | User request flow with admin review and status updates |
| PDF ticket download | Approved ticket PDF generation with ownership checks |
| Announcements | Admin CRUD and public announcements page |
| Smart Help Assistant | FAQ/knowledge-base based assistant with optional AI polishing |
| AI Event Draft Assistant | Admin-only draft generation with required human review |
| Activity logs and AI logs | Auditable records for operational actions and AI interactions |
| Responsive UI | Mobile-friendly public pages and dashboard drawer layout |

## 5. Technology Stack

| Technology | Purpose |
|------------|---------|
| PHP 8+ | Server-side logic and MVC-lite application structure |
| MySQL | Relational database for users, events, tickets, announcements, and logs |
| PDO | Secure database access with prepared statements |
| HTML/CSS/JavaScript | User interface, styling, and client-side interaction |
| Sessions | Authentication state, flash messages, and security flow support |
| FPDF | On-demand approved ticket PDF generation |
| Optional AI API | Responsible AI features for help and event draft support |
| XAMPP/Apache | Local development runtime and live hosting compatibility |

## 6. Folder Structure

```text
app/
  config/
  core/
  controllers/
  models/
  services/
  views/
public/
  assets/
database/
docs/
tests/
```

## 7. Environment Setup

Copy `.env.example` to `.env`, then set local values for your environment.

```env
APP_NAME="Campus EventHub"
APP_ENV=local
APP_DEBUG=true
APP_URL=
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=campus_eventhub
DB_USER=root
DB_PASS=
SESSION_LIFETIME=120
LOGIN_MAX_ATTEMPTS=5
LOGIN_LOCK_MINUTES=5
AI_PROVIDER=api
AI_FALLBACK=true
AI_API_KEY=
AI_API_URL=
AI_MODEL=
```

The codebase currently includes optional provider-specific variables such as `LONGCAT_API_KEY`, `LONGCAT_API_URL`, and `LONGCAT_MODEL`. Keep all API variables private and never commit real values.

## 8. XAMPP Installation Steps

1. Copy the project folder to `htdocs`.
2. Start Apache and MySQL in XAMPP.
3. Create a database named `campus_eventhub`.
4. Import `database/schema.sql`.
5. Import `database/seed.sql`.
6. Copy `.env.example` to `.env` and update DB details.
7. Open the project in the browser.

Example URLs:

| URL | Note |
|-----|------|
| `http://localhost/campusEventHub/` | Root access |
| `http://localhost/campusEventHub/public/` | Direct public folder access |

## 9. Database Setup

| File | Purpose |
|------|---------|
| `database/schema.sql` | Creates tables and relationships |
| `database/seed.sql` | Inserts demo data |
| `database/README.md` | Additional setup notes |

Import order: schema first, seed second.

## 10. Demo Accounts

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@eventhub.test | Admin@123 |
| User | user@eventhub.test | User@123 |

## 11. Responsible AI Use Statement

AI-generated content requires human review before saving or publishing.

AI is used for Smart Help Assistant and AI Event Draft Assistant. AI output is not automatically published. Sensitive data such as passwords and ticket attendee details is not sent to AI services. If AI API is unavailable, fallback responses are used.

## 12. Security Features

| Security Control | Implementation |
|------------------|----------------|
| Password hashing | `password_hash` and `password_verify` |
| PDO prepared statements | Query parameter binding across models |
| CSRF protection | Token validation on POST requests |
| Output escaping | Centralized escaping helper in views |
| Role-based access | Admin/user guards in auth layer and controllers |
| File upload validation | Extension, MIME, and size validation for event images |
| PDF ownership checks | User can download only own approved ticket; admin can download approved tickets |
| Activity logs | Records key workflow actions |
| AI logs | Records AI inputs, outputs, and reviewed acceptance states |

## 13. Documentation Pack

| Document | Purpose |
|----------|---------|
| Project Proposal | Project scope, users, and implementation plan |
| System Design | Architecture, modules, and data flow |
| Security and Risk Register | Security controls and identified risks |
| Test Evidence | Manual test coverage and result records |
| AI Governance Appendix | Responsible AI behavior, limits, and review rules |

## 14. Testing

Testing resources are in the `tests` folder and related docs files. The project includes a manual test checklist, a test results template, positive and negative test cases, and coverage for major workflows such as auth, events, tickets, PDF download, announcements, and AI flows.

## 15. Deployment Notes

Set `APP_DEBUG=false` on live hosting. Use strong database credentials. Keep `.env` private. Ensure the image upload folder is writable. Use HTTPS if available. Do not commit real API keys.

## 16. Known Limitations

| Limitation | Current Status |
|------------|----------------|
| AI fallback mode | AI can run in fallback mode if API is not configured |
| Email notifications | Not implemented |
| Online payment system | Not implemented |
| Live QR scanner | PDF uses ticket code but not a live QR scanner |
| Public announcements complexity | Public announcements page is intentionally simple for current data volume |

## 17. Git Workflow

The project follows meaningful commits, feature-branch development, and pull request review. README and documentation files are maintained as assessment evidence.

## 18. Final Summary

Campus EventHub demonstrates full-stack web development, secure coding, database-driven workflows, responsive UI, responsible AI integration, and documentation required for the assessment.
