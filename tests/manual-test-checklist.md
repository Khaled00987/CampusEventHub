# Manual Test Checklist

**Tester:** _______________  
**Date:** _______________  
**Environment:** XAMPP / Live  
**Project URL:** _______________

| # | Test ID | Pass? | Notes |
|---|---------|-------|-------|
| 1 | TC-AUTH-01 | ☐ | Admin login |
| 2 | TC-AUTH-02 | ☐ | User login |
| 3 | TC-AUTH-03 | ☐ | Login failure / lockout |
| 4 | TC-REG-01 | ☐ | Register validation |
| 5 | TC-EVT-01 | ☐ | Admin create event |
| 6 | TC-EVT-02 | ☐ | Admin edit event |
| 7 | TC-EVT-03 | ☐ | Event search and filter |
| 8 | TC-TKT-01 | ☐ | Ticket request |
| 9 | TC-TKT-02 | ☐ | Duplicate blocked |
| 10 | TC-TKT-03 | ☐ | Capacity check |
| 11 | TC-TKT-04 | ☐ | Admin approve |
| 12 | TC-AI-01 | ☐ | AI draft not auto-saved |
| 13 | TC-AI-02 | ☐ | Help assistant fallback |
| 14 | TC-AI-03 | ☐ | Help no-match message |
| 15 | TC-IMG-01 | ☐ | Valid event image upload |
| 16 | TC-IMG-02 | ☐ | Invalid file rejected |
| 17 | TC-PDF-01 | ☐ | Pending PDF blocked |
| 18 | TC-PDF-02 | ☐ | Approved PDF download |
| 19 | TC-PDF-03 | ☐ | Other user PDF blocked |
| 20 | TC-PDF-04 | ☐ | PDF layout / missing image |
| 21 | TC-SEC-01 | ☐ | User blocked from admin |

## Quick reference

**TC-AI-01:** Admin → AI Draft → Generate → Accept Reviewed Draft → Confirm no new row in `events` (only `ai_logs` updated).

**TC-AI-02:** Empty Longcat keys in `.env` → Help assistant → Ask "How do I request tickets?" → FAQ answer appears.

**TC-AI-03:** Ask off-topic question → exact no-match message; disclaimer visible on AI pages.

**TC-SEC-01:** Login as `user@eventhub.test` → open `/admin/events` → 403 or safe redirect.
