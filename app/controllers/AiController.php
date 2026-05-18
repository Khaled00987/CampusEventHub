<?php
/**
 * AiController.php — Responsible AI: Event Draft Assistant + Smart Help Assistant.
 *
 * HUMAN-IN-THE-LOOP (mandatory):
 * 1. generateDraft() stores a suggestion in ai_logs — NOT an event record.
 * 2. Admin reviews/edits fields on screen.
 * 3. acceptDraft() saves final_text and accepted=1, then creates an event with status draft.
 * 4. Admin completes schedule, location, and image on the event edit screen before publishing.
 *
 * HELP ASSISTANT:
 * - FAQ keyword search first; only curated answers used.
 * - Longcat optionally polishes FAQ text only (no ticket PII, no user emails).
 * - No FAQ match → fixed message (no AI guessing).
 */

declare(strict_types=1);

require_once APP_PATH . '/config/longcat.php';
require_once APP_PATH . '/services/LongcatService.php';

class AiController extends Controller
{
    private AiLog $aiLogs;
    private ActivityLog $activityLog;
    private LongcatService $ai;

    public function __construct()
    {
        $this->aiLogs = new AiLog();
        $this->activityLog = new ActivityLog();
        $this->ai = new LongcatService();
    }

    /**
     * GET /admin/ai-draft — Admin-only draft generator UI.
     */
    public function draftPage(): void
    {
        Auth::requireAdmin();
        $this->renderDashboard('admin/ai-draft', [
            'pageTitle' => 'AI Event Draft Assistant',
            'pageSubtitle' => 'Generate and review event copy before publishing',
            'disclaimer' => AI_DISCLAIMER,
            'draft' => Session::get('ai_draft'),
            'apiConfigured' => $this->ai->isApiConfigured(),
        ]);
    }

    /**
     * POST /admin/ai-draft/generate — Creates ai_logs row with suggestion only.
     */
    public function generateDraft(): void
    {
        Auth::requireAdmin();
        $this->validateCsrf();

        $idea = trim($_POST['event_idea'] ?? '');
        $audience = trim($_POST['target_audience'] ?? '');
        $tone = trim($_POST['tone'] ?? 'friendly');
        $dateLocation = trim($_POST['date_location'] ?? '');

        if ($idea === '' || $audience === '') {
            Session::flash('error', 'Event idea and target audience are required.');
            redirect('/admin/ai-draft');
        }

        // Build input summary for audit log (no ticket or student PII)
        $inputSummary = json_encode([
            'event_idea' => $idea,
            'target_audience' => $audience,
            'tone' => $tone,
            'date_location' => $dateLocation,
        ], JSON_THROW_ON_ERROR);

        $draft = $this->ai->generateEventDraft($idea, $audience, $tone, $dateLocation);
        $suggestionJson = LongcatService::encodeDraftForLog($draft);

        $userId = (int) Auth::user()['id'];
        $logId = $this->aiLogs->create($userId, 'event_draft', $inputSummary, $suggestionJson);

        $this->activityLog->log(
            'ai_suggestion_generated',
            'ai_log',
            $logId,
            'AI event draft suggestion generated (not published)',
            $userId
        );

        // Store in session for review form — still NOT saved as an event
        Session::set('ai_draft', array_merge($draft, [
            'log_id' => $logId,
        ]));

        Session::flash('success', 'Draft generated. Please review and edit every field before accepting.');
        redirect('/admin/ai-draft');
    }

    /**
     * POST /admin/ai-draft/accept — Human review complete; logs acceptance and creates draft event.
     */
    public function acceptDraft(): void
    {
        Auth::requireAdmin();
        $this->validateCsrf();

        $logId = (int) ($_POST['log_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $short = trim($_POST['short_description'] ?? '');
        $detail = trim($_POST['detailed_description'] ?? '');
        $category = trim($_POST['category'] ?? 'General');

        if ($logId <= 0 || $title === '' || $detail === '') {
            Session::flash('error', 'Please complete the reviewed draft fields before accepting.');
            redirect('/admin/ai-draft');
        }

        if (strlen($title) < 5) {
            Session::flash('error', 'Title must be at least 5 characters.');
            redirect('/admin/ai-draft');
        }

        if (strlen($detail) < 20) {
            Session::flash('error', 'Detailed description must be at least 20 characters.');
            redirect('/admin/ai-draft');
        }

        $log = $this->aiLogs->findById($logId);
        if ($log === null) {
            Session::flash('error', 'AI log not found. Please generate a new draft.');
            redirect('/admin/ai-draft');
        }

        $input = json_decode((string) ($log['input_text'] ?? '{}'), true);
        $dateLocation = is_array($input) ? trim((string) ($input['date_location'] ?? '')) : '';

        $description = $detail;
        if ($short !== '' && !str_contains($detail, $short)) {
            $description = $short . "\n\n" . $detail;
        }

        $eventModel = new Event();
        $slug = slugify($title);
        if ($eventModel->slugExists($slug)) {
            $slug .= '-' . time();
        }

        $userId = (int) Auth::user()['id'];
        $schedule = $this->defaultScheduleFromNotes($dateLocation);

        $eventId = $eventModel->create([
            'title' => $title,
            'slug' => $slug,
            'description' => $description,
            'category' => $category,
            'location' => $schedule['location'],
            'event_date' => $schedule['event_date'],
            'start_time' => $schedule['start_time'],
            'end_time' => $schedule['end_time'],
            'capacity' => 100,
            'image' => null,
            'status' => 'draft',
        ], $userId);

        $finalText = json_encode([
            'title' => $title,
            'short_description' => $short,
            'detailed_description' => $detail,
            'category' => $category,
            'event_id' => $eventId,
            'status' => 'draft',
            'reviewed_at' => date('c'),
            'reviewed_by' => Auth::user()['email'] ?? 'admin',
        ], JSON_THROW_ON_ERROR);

        $this->aiLogs->markAccepted($logId, $finalText);

        $this->activityLog->log(
            'ai_suggestion_accepted',
            'ai_log',
            $logId,
            'AI draft accepted; event #' . $eventId . ' created as draft',
            $userId
        );

        $this->activityLog->log(
            'event_created',
            'event',
            $eventId,
            'Event created as draft from AI review: ' . $title,
            $userId
        );

        Session::remove('ai_draft');
        Session::remove('ai_prefill_optional');

        Session::flash(
            'success',
            'Event created as draft and listed under Manage Events. Complete date, location, and image before publishing.'
        );
        redirect('/admin/events/' . $eventId . '/edit');
    }

    /**
     * Build placeholder schedule/location from optional AI date/location notes.
     *
     * @return array{location: string, event_date: string, start_time: string, end_time: string}
     */
    private function defaultScheduleFromNotes(string $dateLocation): array
    {
        $location = 'Campus venue (update before publishing)';
        $eventDate = date('Y-m-d', strtotime('+30 days'));
        $startTime = '10:00:00';
        $endTime = '12:00:00';

        if ($dateLocation !== '') {
            $location = $dateLocation;
            if (preg_match('/\b(\d{4}-\d{2}-\d{2})\b/', $dateLocation, $m)) {
                $eventDate = $m[1];
            } elseif (preg_match('/\b(\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4})\b/', $dateLocation, $m)) {
                $parsed = strtotime($m[1]);
                if ($parsed !== false) {
                    $eventDate = date('Y-m-d', $parsed);
                }
            } elseif (($parsed = strtotime($dateLocation)) !== false) {
                $eventDate = date('Y-m-d', $parsed);
            }

            if (preg_match('/\b(\d{1,2}:\d{2}\s*(?:am|pm)?)\b/i', $dateLocation, $tm)) {
                $parsedTime = strtotime($tm[1]);
                if ($parsedTime !== false) {
                    $startTime = date('H:i:s', $parsedTime);
                    $endTime = date('H:i:s', strtotime($startTime . ' +2 hours'));
                }
            }
        }

        return [
            'location' => $location,
            'event_date' => $eventDate,
            'start_time' => $startTime,
            'end_time' => $endTime,
        ];
    }

    /** GET /admin/ai-logs */
    public function aiLogs(): void
    {
        Auth::requireAdmin();
        $page = (int) ($_GET['page'] ?? 1);
        $this->renderDashboard('admin/ai-logs', [
            'pageTitle' => 'AI Logs',
            'pageSubtitle' => 'Audit trail of AI suggestions',
            'logs' => $this->aiLogs->paginate($page),
            'disclaimer' => AI_DISCLAIMER,
        ]);
    }

    /** GET /help-assistant */
    public function helpPage(): void
    {
        $faqModel = new FaqItem();
        $this->render('help-assistant/index', [
            'pageTitle' => 'Smart Help Assistant',
            'disclaimer' => AI_DISCLAIMER,
            'sampleFaqs' => array_slice($faqModel->active(), 0, 5),
            'apiConfigured' => $this->ai->isApiConfigured(),
            'loadAiJs' => true,
        ]);
    }

    /**
     * POST /help-assistant/ask — FAQ-first; optional polish; no unrelated topics.
     */
    public function helpAsk(): void
    {
        $this->validateCsrf();

        $question = trim($_POST['question'] ?? '');
        if ($question === '') {
            $this->json(['error' => 'Please enter a question about using Campus EventHub.'], 422);
        }

        // Reject obviously off-topic questions before FAQ search (system usage only)
        if ($this->isOutOfScopeQuestion($question)) {
            $this->json([
                'answer' => 'Sorry, I could not find an exact answer. Please contact an administrator.',
                'disclaimer' => AI_DISCLAIMER,
                'faq_matched' => false,
                'from_api' => false,
            ]);
        }

        $faqModel = new FaqItem();
        $faq = $faqModel->searchBestMatch($question);

        if ($faq === null) {
            $this->json([
                'answer' => 'Sorry, I could not find an exact answer. Please contact an administrator.',
                'disclaimer' => AI_DISCLAIMER,
                'faq_matched' => false,
                'from_api' => false,
            ]);
        }

        $curated = (string) ($faq['answer'] ?? '');

        // Only send FAQ question + curated answer to AI (never ticket rows or user PII)
        if ($this->ai->isApiConfigured()) {
            $result = $this->ai->polishFaqAnswer($question, $curated);
        } else {
            $result = [
                'answer' => $curated,
                'from_api' => false,
                'disclaimer' => AI_DISCLAIMER,
            ];
        }

        if (stripos($result['answer'], 'OUT_OF_SCOPE') !== false) {
            $this->json([
                'answer' => 'Sorry, I could not find an exact answer. Please contact an administrator.',
                'disclaimer' => AI_DISCLAIMER,
                'faq_matched' => false,
                'from_api' => false,
            ]);
        }

        // Log suggestion for logged-in users only (schema requires user_id)
        if (Auth::check()) {
            $this->aiLogs->create(
                (int) Auth::user()['id'],
                'help_assistant',
                $question,
                $result['answer']
            );
        }

        $this->json([
            'answer' => $result['answer'],
            'disclaimer' => $result['disclaimer'],
            'from_api' => $result['from_api'],
            'faq_matched' => true,
        ]);
    }

    /**
     * Block non-system questions (weather, homework, etc.) without calling AI.
     */
    private function isOutOfScopeQuestion(string $question): bool
    {
        $lower = strtolower($question);
        $blocked = ['weather', 'football', 'bitcoin', 'homework', 'essay', 'recipe', 'diagnosis', 'medical'];
        foreach ($blocked as $word) {
            if (str_contains($lower, $word)) {
                return true;
            }
        }
        return false;
    }
}
