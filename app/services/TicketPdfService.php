<?php
/**
 * TicketPdfService.php — Generate professional ticket PDFs with FPDF.
 *
 * Logo-only branding (wordmark is in logo.png). Event photo when available.
 */

declare(strict_types=1);

require_once APP_PATH . '/lib/fpdf/fpdf.php';
require_once APP_PATH . '/lib/fpdf/FpdfFonts.php';

class TicketPdfService
{
    private const COLOR_GREEN = [45, 74, 62];
    private const COLOR_GREEN_LIGHT = [58, 95, 78];
    private const COLOR_IVORY = [250, 247, 240];
    private const COLOR_GOLD = [201, 169, 98];
    private const COLOR_MUTED = [90, 90, 85];
    private const COLOR_WHITE = [255, 255, 255];

    /**
     * @param array<string, mixed> $ticket Row from TicketRequest::findById()
     */
    public function download(array $ticket): void
    {
        FpdfFonts::ensureInstalled();

        $ticketId = (int) $ticket['id'];
        $eventId = (int) $ticket['event_id'];
        $userId = (int) $ticket['user_id'];
        $code = ticket_code($ticketId, $eventId, $userId);
        $entryCode = entry_gate_code($ticketId, $eventId, $userId);

        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->SetAutoPageBreak(false);
        $pdf->AddPage();

        $pdf->SetFillColor(...self::COLOR_IVORY);
        $pdf->Rect(0, 0, 210, 297, 'F');

        $left = 14;
        $width = 182;
        $top = 14;

        // Outer ticket frame
        $pdf->SetDrawColor(...self::COLOR_GREEN);
        $pdf->SetLineWidth(0.5);
        $pdf->Rect($left, $top, $width, 248);

        // Header band
        $headerH = 22;
        $pdf->SetFillColor(...self::COLOR_GREEN);
        $pdf->Rect($left, $top, $width, $headerH, 'F');

        $logoPath = logo_path_for_pdf();
        $logoX = $left + 6;
        $logoY = $top + 4;
        if ($logoPath !== null) {
            $pdf->Image($logoPath, $logoX, $logoY, 48, 0);
        }

        $pdf->SetTextColor(...self::COLOR_WHITE);
        $pdf->SetFont('Helvetica', 'B', 11);
        $pdf->SetXY($left + $width - 62, $top + 5);
        $pdf->Cell(56, 6, 'OFFICIAL ADMISSION', 0, 1, 'R');

        $pdf->SetFont('Helvetica', '', 9);
        $pdf->SetXY($left + $width - 62, $top + 12);
        $pdf->Cell(56, 5, 'APPROVED - VALID ENTRY', 0, 1, 'R');

        $bodyTop = $top + $headerH;
        $bodyH = 118;
        $imageW = 72;
        $contentX = $left + $imageW + 8;
        $contentW = $width - $imageW - 16;

        $eventImagePath = event_image_path_for_pdf($ticket['event_image'] ?? null);
        if ($eventImagePath !== null) {
            $pdf->Image($eventImagePath, $left + 4, $bodyTop + 4, $imageW, $bodyH - 8);
        } else {
            $pdf->SetFillColor(235, 232, 225);
            $pdf->Rect($left + 4, $bodyTop + 4, $imageW, $bodyH - 8, 'F');
            $pdf->SetTextColor(...self::COLOR_MUTED);
            $pdf->SetFont('Helvetica', 'I', 9);
            $pdf->SetXY($left + 4, $bodyTop + ($bodyH / 2) - 4);
            $pdf->Cell($imageW, 6, 'Event', 0, 0, 'C');
        }

        $pdf->SetXY($contentX, $bodyTop + 8);
        $pdf->SetFont('Helvetica', 'B', 15);
        $pdf->SetTextColor(...self::COLOR_GREEN);
        $pdf->MultiCell($contentW, 7, (string) ($ticket['event_title'] ?? 'Event'), 0, 'L');

        $y = $pdf->GetY() + 2;
        $pdf->SetFont('Helvetica', '', 10);
        $pdf->SetTextColor(...self::COLOR_MUTED);

        $meta = [
            ['label' => 'Date', 'value' => format_date($ticket['event_date'] ?? null)],
            ['label' => 'Time', 'value' => $this->formatTimeRange($ticket)],
            ['label' => 'Venue', 'value' => $this->pdfText((string) ($ticket['event_location'] ?? ''))],
            ['label' => 'Category', 'value' => $this->pdfText((string) ($ticket['event_category'] ?? ''))],
        ];

        foreach ($meta as $row) {
            $pdf->SetXY($contentX, $y);
            $pdf->SetFont('Helvetica', 'B', 9);
            $pdf->SetTextColor(...self::COLOR_GREEN_LIGHT);
            $pdf->Cell(18, 5, $row['label'] . ':', 0, 0, 'L');
            $pdf->SetFont('Helvetica', '', 10);
            $pdf->SetTextColor(50, 50, 48);
            $pdf->Cell($contentW - 18, 5, $row['value'], 0, 1, 'L');
            $y += 6;
        }

        // Ticket code strip
        $stripY = $bodyTop + $bodyH;
        $pdf->SetFillColor(...self::COLOR_GOLD);
        $pdf->Rect($left, $stripY, $width, 14, 'F');
        $pdf->SetFont('Helvetica', 'B', 13);
        $pdf->SetTextColor(35, 35, 32);
        $pdf->SetXY($left + 6, $stripY + 3.5);
        $pdf->Cell($width - 12, 7, 'TICKET NO.  ' . $code, 0, 1, 'C');

        // Perforation line
        $perfY = $stripY + 18;
        $this->drawPerforation($pdf, $left + 4, $perfY, $width - 8);

        // Stub — attendee
        $stubY = $perfY + 6;
        $pdf->SetFont('Helvetica', 'B', 10);
        $pdf->SetTextColor(...self::COLOR_GREEN);
        $pdf->SetXY($left + 6, $stubY);
        $pdf->Cell(80, 6, 'Holder', 0, 0, 'L');
        $pdf->Cell(80, 6, 'Request details', 0, 1, 'L');

        $pdf->SetFont('Helvetica', '', 10);
        $pdf->SetTextColor(40, 40, 40);
        $pdf->SetXY($left + 6, $stubY + 8);
        $pdf->Cell(80, 5, $this->pdfText((string) ($ticket['attendee_name'] ?? '')), 0, 0, 'L');
        $pdf->Cell(80, 5, 'Qty: ' . (string) ($ticket['quantity'] ?? '1'), 0, 1, 'L');

        $pdf->SetX($left + 6);
        $pdf->SetFont('Helvetica', '', 9);
        $pdf->SetTextColor(...self::COLOR_MUTED);
        $pdf->Cell(80, 5, $this->pdfText((string) ($ticket['attendee_email'] ?? '')), 0, 0, 'L');
        $pdf->Cell(80, 5, 'Request #' . $ticketId, 0, 1, 'L');

        $pdf->SetX($left + 6);
        $pdf->Cell(160, 5, 'Issued: ' . format_datetime($ticket['updated_at'] ?? $ticket['created_at'] ?? null), 0, 1, 'L');

        // Entry gate code (numeric, for venue check-in)
        $entryBoxW = 52;
        $entryBoxH = 18;
        $entryX = $left + $width - $entryBoxW - 8;
        $entryY = $stubY + 24;
        $pdf->SetDrawColor(...self::COLOR_GREEN);
        $pdf->SetLineWidth(0.35);
        $pdf->SetFillColor(248, 246, 240);
        $pdf->Rect($entryX, $entryY, $entryBoxW, $entryBoxH, 'DF');
        $pdf->SetFont('Helvetica', 'B', 14);
        $pdf->SetTextColor(...self::COLOR_GREEN);
        $pdf->SetXY($entryX, $entryY + 4.5);
        $pdf->Cell($entryBoxW, 8, $entryCode, 0, 0, 'C');

        // Footer note inside frame
        $pdf->SetDrawColor(...self::COLOR_GOLD);
        $pdf->SetLineWidth(0.3);
        $pdf->Line($left + 6, $top + 238, $left + $width - 6, $top + 238);

        $pdf->SetFont('Helvetica', 'I', 8);
        $pdf->SetTextColor(...self::COLOR_MUTED);
        $pdf->SetXY($left + 6, $top + 240);
        $pdf->MultiCell(
            $width - 12,
            4,
            'Present this ticket with valid photo ID at the venue. Non-transferable unless stated by the organiser.',
            0,
            'C'
        );

        if ($logoPath !== null) {
            $pdf->Image($logoPath, $left + ($width / 2) - 18, $top + 252, 36, 0);
        }

        $filename = 'ticket_' . $code . '.pdf';
        $pdf->Output('D', $filename);
        exit;
    }

    private function drawPerforation(FPDF $pdf, float $x, float $y, float $w): void
    {
        $pdf->SetDrawColor(...self::COLOR_GOLD);
        $pdf->SetLineWidth(0.25);
        $step = 3;
        for ($i = 0; $i < $w; $i += $step) {
            $pdf->Line($x + $i, $y, $x + $i + 1.5, $y);
        }
        $pdf->SetFont('Helvetica', '', 7);
        $pdf->SetTextColor(...self::COLOR_MUTED);
        $pdf->SetXY($x, $y + 1);
        $pdf->Cell($w, 4, '- - - detach along perforation - - -', 0, 0, 'C');
    }

    /** FPDF core fonts are Latin-1; avoid UTF-8 punctuation in PDF strings. */
    private function pdfText(string $value): string
    {
        $value = trim($value);

        return $value !== '' ? $value : 'N/A';
    }

    /**
     * @param array<string, mixed> $ticket
     */
    private function formatTimeRange(array $ticket): string
    {
        $start = $ticket['event_start_time'] ?? '';
        $end = $ticket['event_end_time'] ?? '';

        if ($start === '' && $end === '') {
            return 'N/A';
        }

        return trim(substr((string) $start, 0, 5) . ' - ' . substr((string) $end, 0, 5));
    }
}
