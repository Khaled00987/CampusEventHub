# Manual Test Checklist

Tester: _______________  Date: _______________  Environment: XAMPP / Live

| # | Test ID | Pass? | Notes |
|---|---------|-------|-------|
| 1 | TC-AUTH-01 | ☐ | Admin login |
| 2 | TC-AUTH-02 | ☐ | User login |
| 3 | TC-AUTH-03 | ☐ | Login failure |
| 4 | TC-REG-01 | ☐ | Register validation |
| 5 | TC-EVT-01 | ☐ | Admin create event |
| 6 | TC-EVT-02 | ☐ | Admin edit event |
| 7 | TC-EVT-03 | ☐ | Event search/filter |
| 8 | TC-TKT-01 | ☐ | Ticket request |
| 9 | TC-TKT-02 | ☐ | Duplicate blocked |
| 10 | TC-TKT-03 | ☐ | Capacity check |
| 11 | TC-TKT-04 | ☐ | Admin approve |
| 12 | TC-AI-01 | ☐ | AI draft not auto-saved |
| 13 | TC-AI-02 | ☐ | Help assistant fallback |
| 14 | TC-AI-03 | ☐ | Disclaimer visible |
| 15 | TC-SEC-01 | ☐ | User blocked from admin |

## Quick steps

**TC-AI-01:** Admin → AI Draft → Generate → Accept Reviewed Draft → Confirm no new row in `events` table (only `ai_logs.final_text` updated).

**TC-AI-02:** Empty Longcat keys in `.env` → Help assistant → Ask "How do I request tickets?" → Curated FAQ answer appears.

**TC-AI-03:** Disclaimer text appears on AI Draft page and Help responses.
