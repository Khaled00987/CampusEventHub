# AI Governance: Campus EventHub

This document explains how optional AI features are designed, used, and controlled in this student project.

## AI features

1. **AI Event Draft Assistant** (admin only): suggests title, descriptions, and category from admin inputs.
2. **Smart Help Assistant** (public and logged-in users): answers system usage questions using FAQ data.

## Longcat integration

- Optional provider configured in `.env`: `LONGCAT_API_KEY`, `LONGCAT_API_URL`, `LONGCAT_MODEL`.
- Implemented in `app/services/LongcatService.php` using **cURL** with a timeout.
- If the key or URL is empty, the application uses **local fallback** and remains fully functional on XAMPP.

## Fallback behaviour

| Feature | API available | API missing or error |
|---------|---------------|----------------------|
| Event draft | JSON draft from Longcat | Template draft from idea, audience, and tone |
| Help | Polish curated FAQ answer | Return curated FAQ answer unchanged |

## Prompt approach

- **Event draft:** System prompt requests JSON only (`title`, `short_description`, `detailed_description`, `category`). No personal or ticket data in prompts.
- **Help:** System prompt instructs the model to rephrase **only** the provided official FAQ answer. Respond `OUT_OF_SCOPE` for unrelated topics.

## Data handling

- Do **not** send ticket request rows, attendee emails, or passwords to AI.
- Help assistant sends: user question and matched FAQ answer only.
- Event draft sends: idea, audience, tone, optional date and location notes (no PII).

## Human-in-the-loop

1. **Generate** stores `ai_suggestion` in `ai_logs` only.
2. Admin reviews and edits all fields on screen.
3. **Accept Reviewed Draft** updates `final_text` and sets `accepted=1`.
4. **No automatic** insert into `events` or `announcements`.

Required disclaimer on every AI output:

> AI-generated content requires human review before saving or publishing.

Routes: `GET/POST /admin/ai-draft`, `POST /admin/ai-draft/generate`, `POST /admin/ai-draft/accept`, `GET /admin/ai-logs`.

## AI logs (`ai_logs` table)

| Column | Purpose |
|--------|---------|
| `user_id` | Who triggered the feature |
| `feature` | `event_draft` or `help_assistant` |
| `input_text` | Prompt or question |
| `ai_suggestion` | Raw or polished suggestion |
| `final_text` | Human-reviewed content (event draft accept only) |
| `accepted` | `1` after explicit accept |
| `disclaimer_shown` | Always `1` |
| `created_at` | Timestamp |

## Limitations

- Help assistant cannot answer general knowledge or non-system questions.
- AI may produce generic copy; admins must verify dates, policies, and capacity.
- Anonymous help users are not logged in `ai_logs` (schema requires `user_id`).

## Mitigations

- FAQ-first help flow reduces off-topic answers.
- Off-topic keyword block before AI call.
- Activity logs for `ai_suggestion_generated` and `ai_suggestion_accepted`.
- Set `APP_DEBUG=false` on production to avoid leaking errors.
