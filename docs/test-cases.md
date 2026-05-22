# Test Cases — Campus EventHub

Use with `tests/manual-test-checklist.md` and `tests/test-results-template.md`.

---

## TC-AUTH-01 — Login success (admin)

| Field | Value |
|-------|-------|
| **ID** | TC-AUTH-01 |
| **Feature** | Authentication |
| **Steps** | 1. Open `/login`. 2. Enter `admin@eventhub.test` / `Admin@123`. 3. Submit. |
| **Expected result** | Redirect to admin dashboard; session shows admin role. |
| **Actual result** | |
| **Pass/Fail** | |

---

## TC-AUTH-02 — Login success (user)

| Field | Value |
|-------|-------|
| **ID** | TC-AUTH-02 |
| **Feature** | Authentication |
| **Steps** | 1. Logout. 2. Login as `user@eventhub.test` / `User@123`. |
| **Expected result** | Redirect to user dashboard; no admin menu items. |
| **Actual result** | |
| **Pass/Fail** | |

---

## TC-AUTH-03 — Login failure

| Field | Value |
|-------|-------|
| **ID** | TC-AUTH-03 |
| **Feature** | Authentication |
| **Steps** | 1. Login with wrong password 1–2 times. |
| **Expected result** | Error message; account not logged in. After 5 failures, lockout message. |
| **Actual result** | |
| **Pass/Fail** | |

---

## TC-REG-01 — Register validation

| Field | Value |
|-------|-------|
| **ID** | TC-REG-01 |
| **Feature** | Registration |
| **Steps** | 1. Open `/register`. 2. Submit weak password `abc`. 3. Submit valid data. |
| **Expected result** | Weak password rejected; valid registration redirects to login with success flash. |
| **Actual result** | |
| **Pass/Fail** | |

---

## TC-EVT-01 — Admin create event

| Field | Value |
|-------|-------|
| **ID** | TC-EVT-01 |
| **Feature** | Events CRUD |
| **Steps** | 1. Admin → Create event. 2. Fill required fields. 3. Set status Published. 4. Save. |
| **Expected result** | Event appears in admin list and public `/events`. Activity log `event_created`. |
| **Actual result** | |
| **Pass/Fail** | |

---

## TC-EVT-02 — Admin edit event

| Field | Value |
|-------|-------|
| **ID** | TC-EVT-02 |
| **Feature** | Events CRUD |
| **Steps** | 1. Edit an event title. 2. Save. |
| **Expected result** | Updated title shown; `updated_at` changes; activity log `event_updated`. |
| **Actual result** | |
| **Pass/Fail** | |

---

## TC-EVT-03 — Event search and filter

| Field | Value |
|-------|-------|
| **ID** | TC-EVT-03 |
| **Feature** | Events listing |
| **Steps** | 1. Open `/events`. 2. Search partial title. 3. Filter category and Upcoming. |
| **Expected result** | Only matching published events shown; pagination works if >10 results. |
| **Actual result** | |
| **Pass/Fail** | |

---

## TC-TKT-01 — Ticket request success

| Field | Value |
|-------|-------|
| **ID** | TC-TKT-01 |
| **Feature** | Tickets |
| **Steps** | 1. Login as user. 2. Open published event. 3. Submit ticket form. |
| **Expected result** | Redirect to My Tickets; status `pending`; activity `ticket_created`. |
| **Actual result** | |
| **Pass/Fail** | |

---

## TC-TKT-02 — Duplicate ticket request blocked

| Field | Value |
|-------|-------|
| **ID** | TC-TKT-02 |
| **Feature** | Tickets |
| **Steps** | 1. Request ticket for same event again while pending/approved. |
| **Expected result** | Error message; second request not created. |
| **Actual result** | |
| **Pass/Fail** | |

---

## TC-TKT-03 — Capacity check

| Field | Value |
|-------|-------|
| **ID** | TC-TKT-03 |
| **Feature** | Tickets |
| **Steps** | 1. Admin approves tickets until capacity full. 2. Try approve one more. |
| **Expected result** | Approval blocked with capacity error. |
| **Actual result** | |
| **Pass/Fail** | |

---

## TC-TKT-04 — Admin approve ticket

| Field | Value |
|-------|-------|
| **ID** | TC-TKT-04 |
| **Feature** | Tickets |
| **Steps** | 1. Admin → Tickets → pending request → Approve. |
| **Expected result** | Status `approved`; activity `ticket_approved`. |
| **Actual result** | |
| **Pass/Fail** | |

---

## TC-AI-01 — AI draft generated but not auto-saved

| Field | Value |
|-------|-------|
| **ID** | TC-AI-01 |
| **Feature** | AI Event Draft |
| **Steps** | 1. Admin → AI Draft. 2. Generate draft. 3. Edit fields. 4. Accept Reviewed Draft. 5. Check `events` table. |
| **Expected result** | `ai_logs` has row with `accepted=1` and `final_text`; **no** new event row auto-created. Disclaimer visible. |
| **Actual result** | |
| **Pass/Fail** | |

---

## TC-AI-02 — Help assistant fallback

| Field | Value |
|-------|-------|
| **ID** | TC-AI-02 |
| **Feature** | Smart Help Assistant |
| **Steps** | 1. Leave Longcat keys empty in `.env`. 2. Ask "How do I request tickets?" |
| **Expected result** | FAQ-based answer returned; disclaimer shown. |
| **Actual result** | |
| **Pass/Fail** | |

---

## TC-AI-03 — Help assistant no match

| Field | Value |
|-------|-------|
| **ID** | TC-AI-03 |
| **Feature** | Smart Help Assistant |
| **Steps** | 1. Ask unrelated question e.g. "What is the weather?" |
| **Expected result** | Exact message: "Sorry, I could not find an exact answer. Please contact an administrator." |
| **Actual result** | |
| **Pass/Fail** | |

---

## TC-IMG-01 — Admin uploads valid JPG event image

| Field | Value |
|-------|-------|
| **ID** | TC-IMG-01 |
| **Feature** | Event image upload |
| **Steps** | 1. Log in as admin. 2. Create event with JPG under 2MB. 3. Save. 4. View event on public site. |
| **Expected result** | Image saved in `public/assets/images/events/`; displays on listing and detail via `event_image()`. |
| **Actual result** | |
| **Pass/Fail** | |

---

## TC-IMG-02 — Admin uploads invalid file type

| Field | Value |
|-------|-------|
| **ID** | TC-IMG-02 |
| **Feature** | Event image upload validation |
| **Steps** | 1. Log in as admin. 2. Try to upload `.pdf` or `.exe` as event image. |
| **Expected result** | Friendly validation error; event not saved with bad file. |
| **Actual result** | |
| **Pass/Fail** | |

---

## TC-PDF-01 — User cannot download pending ticket PDF

| Field | Value |
|-------|-------|
| **ID** | TC-PDF-01 |
| **Feature** | Ticket PDF access control |
| **Steps** | 1. Log in as user with a **pending** ticket. 2. Open `/tickets/{id}/download` directly. |
| **Expected result** | Redirect with error; no PDF file. My Tickets shows "PDF available after approval". |
| **Actual result** | |
| **Pass/Fail** | |

---

## TC-PDF-02 — User downloads approved ticket PDF

| Field | Value |
|-------|-------|
| **ID** | TC-PDF-02 |
| **Feature** | Ticket PDF download |
| **Steps** | 1. Log in as `user@eventhub.test`. 2. My Tickets → approved row → Download Ticket PDF. |
| **Expected result** | PDF downloads as `ticket_CEH-{id}-{event}-{user}.pdf`; shows event details, attendee, ticket code, disclaimer. |
| **Actual result** | |
| **Pass/Fail** | |

---

## TC-PDF-03 — User cannot download another user's ticket

| Field | Value |
|-------|-------|
| **ID** | TC-PDF-03 |
| **Feature** | Ticket PDF authorization |
| **Steps** | 1. Log in as user A. 2. Request URL for user B's ticket ID. |
| **Expected result** | HTTP 403 Forbidden. |
| **Actual result** | |
| **Pass/Fail** | |

---

## TC-PDF-04 — PDF content and missing image safety

| Field | Value |
|-------|-------|
| **ID** | TC-PDF-04 |
| **Feature** | Ticket PDF layout |
| **Steps** | 1. Download approved ticket PDF. 2. Repeat for event with no image file. |
| **Expected result** | PDF includes logo, title, date, location, attendee name, ticket code; no overlapping text; generates without fatal error if image missing. |
| **Actual result** | |
| **Pass/Fail** | |
