# Security Risk Register: Campus EventHub

Top risks for this assessment project and how they are mitigated in the codebase.

## R1: SQL injection

| Field | Detail |
|-------|--------|
| **Likelihood** | Medium if raw SQL were used |
| **Impact** | High (data breach, account takeover) |
| **Mitigation** | PDO prepared statements in all models; `ATTR_EMULATE_PREPARES => false` |
| **Residual risk** | Low |

## R2: Cross-site scripting (XSS)

| Field | Detail |
|-------|--------|
| **Likelihood** | Medium |
| **Impact** | High (session theft, defacement) |
| **Mitigation** | `e()` helper on view output; no raw echo of user input |
| **Residual risk** | Low |

## R3: Weak passwords and brute force

| Field | Detail |
|-------|--------|
| **Likelihood** | Medium |
| **Impact** | Medium (account compromise) |
| **Mitigation** | Password rules on register; bcrypt storage; login attempt counter and lockout in session |
| **Residual risk** | Low |

## R4: Unauthorised admin access

| Field | Detail |
|-------|--------|
| **Likelihood** | Medium |
| **Impact** | High (data modification, ticket fraud) |
| **Mitigation** | `Auth::requireAdmin()` on admin routes; role stored server-side in session; 403 page |
| **Residual risk** | Low |

## R5: AI misuse or unreviewed AI content

| Field | Detail |
|-------|--------|
| **Likelihood** | Medium |
| **Impact** | Medium (incorrect events, misleading help, data sent to third party) |
| **Mitigation** | Human accept step; disclaimer; `ai_logs`; no auto-publish; FAQ-only help; no ticket PII to AI |
| **Residual risk** | Medium (admin must still read content) |

## R6: Unsafe file upload

| Field | Detail |
|-------|--------|
| **Likelihood** | Medium without validation |
| **Impact** | High (malicious files, storage abuse) |
| **Mitigation** | 2 MB limit; allow-list extensions (jpg, jpeg, png, webp); MIME check via `finfo_file()`; unique filenames; `basename()` on stored names; `move_uploaded_file()` only to `public/assets/images/events/`; never trust client filename |
| **Residual risk** | Low |

## Additional controls

- CSRF tokens on POST requests
- Blocked user status check at login
- Directory deny rules for sensitive folders
- Friendly error pages when `APP_DEBUG=false`
- Ticket PDF: owner or admin only; approved status required
