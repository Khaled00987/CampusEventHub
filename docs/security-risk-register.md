# Security Risk Register — Campus EventHub

Top five risks for this assessment project:

---

## R1 — SQL injection

| | |
|---|---|
| **Likelihood** | Medium (if raw SQL used) |
| **Impact** | High (data breach, account takeover) |
| **Mitigation** | PDO prepared statements in all models; `ATTR_EMULATE_PREPARES => false` |
| **Residual risk** | Low |

---

## R2 — Cross-site scripting (XSS)

| | |
|---|---|
| **Likelihood** | Medium |
| **Impact** | High (session theft, defacement) |
| **Mitigation** | `e()` helper on all view output; no raw `echo` of user input |
| **Residual risk** | Low |

---

## R3 — Weak passwords / brute force

| | |
|---|---|
| **Likelihood** | Medium |
| **Impact** | Medium (account compromise) |
| **Mitigation** | Password rules on register; bcrypt storage; session login attempt counter and lockout |
| **Residual risk** | Low |

---

## R4 — Unauthorised admin access

| | |
|---|---|
| **Likelihood** | Medium |
| **Impact** | High (data modification, ticket fraud) |
| **Mitigation** | `Auth::requireAdmin()` on admin routes; role stored server-side in session; 403 page |
| **Residual risk** | Low |

---

## R5 — AI misuse / unreviewed AI content

| | |
|---|---|
| **Likelihood** | Medium |
| **Impact** | Medium (incorrect events, misleading help, data sent to third party) |
| **Mitigation** | Human accept step; disclaimer; `ai_logs`; no auto-publish; FAQ-only help; no ticket PII to AI |
| **Residual risk** | Medium (admin must still read content) |

---

## R6 — Unsafe file upload

| | |
|---|---|
| **Likelihood** | Medium (if uploads enabled without checks) |
| **Impact** | High (malicious file execution, storage abuse) |
| **Mitigation** | 2MB size limit; allow-list extensions (jpg, jpeg, png, webp); MIME validation via `finfo_file()`; unique generated filenames; `basename()` on stored names; `move_uploaded_file()` only to `public/assets/images/events/`; never trust client filename; no PHP uploads |
| **Residual risk** | Low |

---

## Additional controls

- CSRF tokens on POST requests
- Blocked user status check at login
- Directory deny rules for sensitive folders
- Friendly error pages when `APP_DEBUG=false`
- Ticket PDF: owner or admin only; approved status required
