# FPDF (local copy)

Campus EventHub uses [FPDF](http://www.fpdf.org/) for ticket PDF downloads.

- **Location:** `app/lib/fpdf/fpdf.php`
- **Fonts:** `app/lib/fpdf/font/` (Helvetica family — required)
- **No Composer required** — included directly from `TicketPdfService.php`
- **License:** FPDF is free software; see FPDF website for terms

## Install core fonts (required once)

If PDF download shows a missing `helveticab.php` error, run from the project root:

```bash
php app/lib/fpdf/install-fonts.php
```

Fonts are also auto-downloaded on first PDF generation when PHP can reach GitHub.

Ticket PDFs are generated on demand when a user or admin downloads an approved ticket.
