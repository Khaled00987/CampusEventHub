# Campus marketing images

| File | Usage |
|------|--------|
| `logo.png` | Site header, footer, auth pages, dashboard sidebar, PDF tickets (`logo_url()` / `logo_path_for_pdf()`) — wordmark is in the image; do not add duplicate site name text beside it |

All other files in this folder are registered in `campus_static_image()` (see `app/core/Helpers.php`).

**Landing page rule:** each image is used at most once on the home page.

| File | Landing usage |
|------|----------------|
| `EVENT-MANAGEMENT.jpg` | Hero section background image |
| `hero-right.jpg` | Hero section — right column (inline) |
| `reunion-weekend.jpg` | About section (inline) |
| `220821-maucker-union-live027-resized.jpg` | Background band |
| `convocation-pl.jpg` | Spotlight (inline) |
| `55149-.jpg` | Compact background band |
| `what-is-an-event-organiser-team-943x630.webp` | AI section background |
| `71942.jpg`, `71939.jpg`, `67315.jpg` | Campus moments row |
| `1.3.2.1.1-residence_halls-body-3-0723.jpg` | Second spotlight |
| `event-organizer-in-jakarta.jpg` | CTA background band |
| `2.1-events_&_news-body-1.jpg` | Final CTA background |

Other pages (events, login, help) use separate keys — see `campus_static_image()` in Helpers.

Event uploads: `events/` subfolder (admin form).
