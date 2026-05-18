<?php
/**
 * LongcatService.php — Optional Longcat AI integration with safe local fallback.
 *
 * RESPONSIBLE AI:
 * - Never saves or publishes content; controllers require human review.
 * - Help assistant only polishes curated FAQ text — never sends ticket/PII data.
 * - Event draft returns suggestions only; admin must click "Accept Reviewed Draft".
 *
 * Methods:
 * - generateEventDraft($idea, $audience, $tone, $dateLocation)
 * - polishFaqAnswer($question, $curatedAnswer)
 */

declare(strict_types=1);

require_once APP_PATH . '/config/longcat.php';

class LongcatService
{
    /**
     * Check whether Longcat API is configured (both key and URL required).
     */
    public function isApiConfigured(): bool
    {
        return LONGCAT_API_KEY !== '' && LONGCAT_API_URL !== '';
    }

    /**
     * Generate structured event draft fields from admin inputs.
     *
     * @param string $idea Main event concept
     * @param string $audience Target audience
     * @param string $tone e.g. formal, casual, energetic
     * @param string $dateLocation Optional date/location hints (not personal data)
     * @return array{
     *   title: string,
     *   short_description: string,
     *   detailed_description: string,
     *   category: string,
     *   from_api: bool,
     *   disclaimer: string
     * }
     */
    public function generateEventDraft(
        string $idea,
        string $audience,
        string $tone,
        string $dateLocation = ''
    ): array {
        $userPrompt = $this->buildEventDraftPrompt($idea, $audience, $tone, $dateLocation);

        $system = 'You are a university events copywriter. Reply ONLY with valid JSON, no markdown, using keys: '
            . '"title", "short_description", "detailed_description", "category". '
            . 'Keep content factual. Do not invent real personal names, emails, or ticket data. '
            . 'Category must be one word or two words like Social, Technology, Workshop, Sports, Arts, Careers, Culture, Wellbeing.';

        $raw = $this->callApi($system, $userPrompt);

        if ($raw !== null) {
            $parsed = $this->parseDraftJson($raw);
            if ($parsed !== null) {
                $parsed['from_api'] = true;
                $parsed['disclaimer'] = AI_DISCLAIMER;
                return $parsed;
            }
        }

        if (!AI_FALLBACK) {
            return $this->emptyDraftWithMessage('AI service unavailable. Please write the event manually.');
        }

        return $this->fallbackEventDraft($idea, $audience, $tone, $dateLocation);
    }

    /**
     * Polish a curated FAQ answer for readability (optional Longcat step).
     * Only the FAQ question and official answer are sent — never ticket or user PII.
     *
     * @param string $question User question (for context only)
     * @param string $curatedAnswer Approved FAQ answer from database
     * @return array{answer: string, from_api: bool, disclaimer: string}
     */
    public function polishFaqAnswer(string $question, string $curatedAnswer): array
    {
        $system = 'You polish help text for a campus event booking website. '
            . 'Only rephrase the provided official answer to be clear and friendly. '
            . 'Do NOT add new facts, links, or topics. '
            . 'Only answer questions about using this system (events, tickets, login, announcements). '
            . 'If the question is unrelated, respond with exactly: OUT_OF_SCOPE';

        $user = "User question: {$question}\n\nOfficial answer to polish:\n{$curatedAnswer}";

        $raw = $this->callApi($system, $user);

        if ($raw !== null && stripos($raw, 'OUT_OF_SCOPE') === false) {
            return [
                'answer' => trim($raw),
                'from_api' => true,
                'disclaimer' => AI_DISCLAIMER,
            ];
        }

        // Fallback: return curated answer unchanged (still correct and safe)
        return [
            'answer' => $curatedAnswer,
            'from_api' => false,
            'disclaimer' => AI_DISCLAIMER,
        ];
    }

    /**
     * Build the prompt sent to Longcat for event drafts.
     */
    private function buildEventDraftPrompt(
        string $idea,
        string $audience,
        string $tone,
        string $dateLocation
    ): string {
        $parts = [
            "Event idea: {$idea}",
            "Target audience: {$audience}",
            "Tone: {$tone}",
        ];
        if ($dateLocation !== '') {
            $parts[] = "Date/location notes: {$dateLocation}";
        }
        return implode("\n", $parts);
    }

    /**
     * HTTP POST to Longcat-compatible chat API using cURL.
     *
     * @return string|null Assistant text or null on any failure
     */
    private function callApi(string $system, string $user): ?string
    {
        if (!$this->isApiConfigured()) {
            return null;
        }

        if (!function_exists('curl_init')) {
            return null;
        }

        $payload = json_encode([
            'model' => LONGCAT_MODEL,
            'messages' => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => $user],
            ],
        ]);

        if ($payload === false) {
            return null;
        }

        $ch = curl_init(LONGCAT_API_URL);
        if ($ch === false) {
            return null;
        }

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . LONGCAT_API_KEY,
            ],
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_TIMEOUT => LONGCAT_TIMEOUT,
            CURLOPT_CONNECTTIMEOUT => 10,
        ]);

        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        // Graceful failure: caller uses fallback
        if ($response === false || $curlError !== '' || $httpCode < 200 || $httpCode >= 300) {
            return null;
        }

        $data = json_decode($response, true);
        if (!is_array($data)) {
            return null;
        }

        $content = $data['choices'][0]['message']['content']
            ?? $data['message']['content']
            ?? $data['content']
            ?? null;

        return is_string($content) ? trim($content) : null;
    }

    /**
     * Parse JSON draft from API (strips markdown code fences if present).
     *
     * @return array<string, string>|null
     */
    private function parseDraftJson(string $raw): ?array
    {
        $raw = preg_replace('/^```json\s*|\s*```$/i', '', trim($raw)) ?? trim($raw);
        $data = json_decode($raw, true);
        if (!is_array($data)) {
            return null;
        }

        $title = trim((string) ($data['title'] ?? ''));
        $short = trim((string) ($data['short_description'] ?? ''));
        $detail = trim((string) ($data['detailed_description'] ?? ''));
        $category = trim((string) ($data['category'] ?? 'General'));

        if ($title === '' || $detail === '') {
            return null;
        }

        return [
            'title' => $title,
            'short_description' => $short !== '' ? $short : substr($detail, 0, 160),
            'detailed_description' => $detail,
            'category' => $category !== '' ? $category : 'General',
        ];
    }

    /**
     * Local fallback draft when API is not configured or fails (XAMPP-friendly).
     *
     * @return array{title: string, short_description: string, detailed_description: string, category: string, from_api: bool, disclaimer: string}
     */
    private function fallbackEventDraft(
        string $idea,
        string $audience,
        string $tone,
        string $dateLocation
    ): array {
        $title = ucfirst($tone) . ' campus event: ' . substr($idea, 0, 50);
        $loc = $dateLocation !== '' ? " Planned context: {$dateLocation}." : '';
        $detail = "Join us for {$idea}. This event is designed for {$audience}."
            . $loc
            . " Please verify date, time, location, and capacity before publishing."
            . "\n\n" . AI_DISCLAIMER;

        return [
            'title' => $title,
            'short_description' => substr($detail, 0, 160),
            'detailed_description' => $detail,
            'category' => 'General',
            'from_api' => false,
            'disclaimer' => AI_DISCLAIMER,
        ];
    }

    /**
     * @return array{title: string, short_description: string, detailed_description: string, category: string, from_api: bool, disclaimer: string}
     */
    private function emptyDraftWithMessage(string $message): array
    {
        return [
            'title' => 'Draft unavailable',
            'short_description' => $message,
            'detailed_description' => $message,
            'category' => 'General',
            'from_api' => false,
            'disclaimer' => AI_DISCLAIMER,
        ];
    }

    /**
     * Encode draft array for storage in ai_logs.ai_suggestion column.
     *
     * @param array<string, mixed> $draft
     */
    public static function encodeDraftForLog(array $draft): string
    {
        return json_encode([
            'title' => $draft['title'] ?? '',
            'short_description' => $draft['short_description'] ?? '',
            'detailed_description' => $draft['detailed_description'] ?? '',
            'category' => $draft['category'] ?? '',
            'from_api' => $draft['from_api'] ?? false,
        ], JSON_THROW_ON_ERROR);
    }

    /**
     * Decode draft from ai_logs.ai_suggestion JSON.
     *
     * @return array<string, mixed>|null
     */
    public static function decodeDraftFromLog(string $json): ?array
    {
        $data = json_decode($json, true);
        return is_array($data) ? $data : null;
    }
}
