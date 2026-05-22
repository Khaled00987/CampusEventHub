# Campus marketing images

Static images for the landing page and branding. Paths are resolved through `asset()` and `campus_static_image()` in `app/core/Helpers.php`.

## Branding

| File | Usage |
|------|--------|
| `logo.png` | Header, footer, auth pages, dashboard sidebar, PDF tickets (`logo_url()` / `logo_path_for_pdf()`). The wordmark is inside the image file; do not add duplicate site name text beside it. |

## Landing page (via `campus_static_image()`)

Each image below is registered in `Helpers.php`. Use each file at most once on the home page.

| File | Landing usage |
|------|----------------|
| `EVENT-MANAGEMENT.jpg` | Hero background (`hero` key) |
| `hero-right.jpg` | Hero right column (`hero_right`) |
| `reunion-weekend.jpg` | About section (`reunion`) |
| `220821-maucker-union-live027-resized.jpg` | Campus union band (`campus_union`) |
| `convocation-pl.jpg` | Spotlight (`convocation`) |
| `55149-.jpg` | Compact band (`campus_55149`) |
| `what-is-an-event-organiser-team-943x630.webp` | AI section background (`organizer_team`) |
| `71942.jpg`, `71939.jpg`, `67315.jpg` | Campus moments row |
| `1.3.2.1.1-residence_halls-body-3-0723.jpg` | Second spotlight (`residence_halls`) |
| `event-organizer-in-jakarta.jpg` | CTA band (`organizer_jakarta`) |
| `2.1-events_&_news-body-1.jpg` | Final CTA (`events_news`) |

If a file is missing, `campus_static_image()` falls back to `placeholder.jpg` when that file exists.

## Event uploads

Admin create/edit event forms save uploads under:

`public/assets/images/events/`

Filenames are generated in `app/services/EventImageUpload.php` and stored in the `events.image_path` column.

## Other pages

Events listing, login, help assistant, and dashboards use `event_image()`, `logo_url()`, or layout partials. See `app/views/` templates for usage.
