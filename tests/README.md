# Campus EventHub — Testing

This folder supports manual assessment testing. Automated PHPUnit is not required for this project.

## Files

| File | Purpose |
|------|---------|
| `manual-test-checklist.md` | Step-by-step checklist with pass/fail columns |
| `test-results-template.md` | Blank results sheet for submission |

## How to test

1. Import `database/schema.sql` and `database/seed.sql`.
2. Copy `.env.example` to `.env`.
3. Open `docs/test-cases.md` for detailed expected results.
4. Work through `manual-test-checklist.md` and record results in `test-results-template.md`.

## AI-specific tests (no API key required)

- AI Event Draft must work with **local fallback** when `LONGCAT_API_KEY` is empty.
- Accepting a draft must update `ai_logs.final_text` and **must not** insert into `events`.
- Help assistant must return FAQ answers or the exact no-match message.
