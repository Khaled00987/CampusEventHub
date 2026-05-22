# Testing: Campus EventHub

This folder supports **manual testing** for the student assessment. Automated PHPUnit tests are not required for this project.

## Files

| File | Purpose |
|------|---------|
| `manual-test-checklist.md` | Short checklist with pass/fail boxes |
| `test-results-template.md` | Results sheet for submission |

Detailed steps and expected results are in `docs/test-cases.md`.

## How to test

1. Import `database/schema.sql` and `database/seed.sql`.
2. Copy `.env.example` to `.env`.
3. Start Apache and MySQL in XAMPP.
4. Open `docs/test-cases.md` and follow each case.
5. Tick `manual-test-checklist.md` and fill in `test-results-template.md`.

## AI tests without an API key

- AI Event Draft must work with **local fallback** when `LONGCAT_API_KEY` is empty.
- Accepting a draft must update `ai_logs.final_text` and **must not** insert into `events`.
- Help assistant must return FAQ answers or the exact no-match message from `AiController`.

## Suggested evidence for submission

- Completed `test-results-template.md`
- Screenshots of pass scenarios (home, events, admin dashboard, AI disclaimer)
- Optional: export of `activity_logs` or `ai_logs` after AI tests
