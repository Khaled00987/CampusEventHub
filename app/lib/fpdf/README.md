# FPDF (local copy)

Campus EventHub uses [FPDF](http://www.fpdf.org/) for ticket PDF downloads.

- **Location:** `app/lib/fpdf/fpdf.php`
- **Fonts:** `app/lib/fpdf/font/` (Helvetica family, required)
- **No Composer:** loaded from `app/services/TicketPdfService.php`
- **License:** FPDF free software; see the FPDF website for terms

## Install core fonts (once per machine)

If PDF download reports a missing `helveticab.php` error, run from the project root:

```bash
php app/lib/fpdf/install-fonts.php
```

Fonts may also download automatically on first PDF generation when PHP can reach GitHub.

## When PDFs are generated

- Route: `GET /tickets/{id}/download` (`TicketController@download`)
- **Users:** approved tickets they own
- **Admins:** any approved ticket from admin ticket detail
- Output filename: `ticket_CEH-{ticket_id}-{event_id}-{user_id}.pdf`

Generation is on demand; PDF files are not stored on disk.
