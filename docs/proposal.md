# Project Proposal — Campus EventHub

## Problem statement

University clubs and local event organisers need a single, secure place to publish events, manage ticket interest, and communicate with students. Scattered tools (email, social media, spreadsheets) make it hard to track requests and maintain accountability.

## Target users

| User | Needs |
|------|-------|
| **Administrator** | Create events, approve tickets, publish announcements, review AI drafts, view audit logs |
| **Standard student** | Browse events, request tickets, track status, read announcements, get help |

## Project features

- Secure login and registration (standard users only)
- Role-based dashboards (admin vs user)
- Events CRUD with search, filters, pagination
- Ticket request workflow with capacity checks
- Announcements CRUD and public feed
- Activity audit log
- AI Event Draft Assistant (human review required)
- Smart Help Assistant (FAQ-based, optional Longcat polish)
- Responsive, accessible web UI

## Tech stack

| Layer | Technology |
|-------|------------|
| Backend | PHP 8+ (MVC-lite, no Laravel) |
| Database | MySQL, InnoDB, utf8mb4 |
| Data access | PDO prepared statements |
| Frontend | HTML, CSS, JavaScript |
| Server | Apache (XAMPP) with mod_rewrite |
| AI (optional) | Longcat API via cURL |

## Roles

- **admin** — full management access
- **user** — browse and request tickets only

## Milestones

| Week | Deliverable |
|------|-------------|
| 1 | Database schema, authentication, routing |
| 2 | Events, tickets, announcements CRUD |
| 3 | AI assistants, dashboards, UI polish |
| 4 | Documentation, testing, deployment package |
