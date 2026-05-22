# Project Proposal: Campus EventHub

**Student assessment project**  
**Module:** Web application development (example title; adjust for your brief)

## Problem statement

University clubs and local event organisers need one secure place to publish events, manage ticket interest, and communicate with students. Email, social media, and spreadsheets make it hard to track requests and keep an audit trail.

## Target users

| User | Needs |
|------|-------|
| **Administrator** | Create events, approve tickets, publish announcements, review AI drafts, view audit logs |
| **Standard student** | Browse events, request tickets, track status, read announcements, use the help assistant |

## Project features

- Secure login and registration (standard users only; admins are seeded)
- Role-based dashboards (admin vs user)
- Events CRUD with search, filters, and pagination
- Ticket request workflow with capacity checks
- Announcements CRUD and public feed
- Activity audit log
- AI Event Draft Assistant (human review required)
- Smart Help Assistant (FAQ-based, optional Longcat polish)
- Event image upload and ticket PDF download
- Responsive web UI

## Tech stack

| Layer | Technology |
|-------|------------|
| Backend | PHP 8+ (MVC-lite, no Laravel) |
| Database | MySQL, InnoDB, utf8mb4 |
| Data access | PDO prepared statements |
| Frontend | HTML, CSS, JavaScript |
| Server | Apache (XAMPP) with `mod_rewrite` |
| AI (optional) | Longcat API via cURL |
| PDF | FPDF (bundled, no Composer) |

## Roles

- **admin:** full management access
- **user:** browse events and request tickets only

## Milestones

| Week | Deliverable |
|------|-------------|
| 1 | Database schema, authentication, routing |
| 2 | Events, tickets, announcements CRUD |
| 3 | AI assistants, dashboards, UI polish |
| 4 | Documentation, testing, deployment package |

## Success criteria

- All core flows work on XAMPP with seed data
- Security controls documented in `docs/security-risk-register.md`
- Manual test cases executed and recorded in `tests/test-results-template.md`
- AI features work offline via fallback when API keys are empty
