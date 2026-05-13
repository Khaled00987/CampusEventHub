<?php
/**
 * AdminController.php — Admin CRUD for events (and shared admin utilities).
 *
 * Full create/read/update/delete with search, filter, pagination.
 * Prevents deleting events that have ticket requests.
 */

declare(strict_types=1);

class AdminController extends Controller
{
    private Event $events;
    private ActivityLog $activityLog;

    public function __construct()
    {
        $this->events = new Event();
        $this->activityLog = new ActivityLog();
    }

    /** Public about page */
    public function about(): void
    {
        $this->render('public/about', [
            'pageTitle' => 'About Us',
            'bodyClass' => 'page-about',
        ]);
    }

    /** Public home page — full landing layout with stats and featured events */
    public function home(): void
    {
        $featured = $this->events->paginatePublic(['page' => 1, 'date_filter' => 'upcoming']);
        $featured['items'] = array_slice($featured['items'], 0, 3);

        $statsModel = new DashboardStats();

        require_once APP_PATH . '/config/longcat.php';

        $this->render('public/home', [
            'pageTitle' => 'Home',
            'layoutWide' => true,
            'loadLandingCss' => true,
            'loadLandingJs' => true,
            'bodyClass' => 'page-home',
            'featuredEvents' => $featured['items'],
            'homeStats' => $statsModel->publicHomeStats(),
            'disclaimer' => AI_DISCLAIMER,
        ]);
    }

    /** Admin events list */
    public function eventIndex(): void
    {
        Auth::requireAdmin();
        $filters = [
            'q' => $_GET['q'] ?? '',
            'category' => $_GET['category'] ?? '',
            'status' => $_GET['status'] ?? '',
            'page' => $_GET['page'] ?? 1,
        ];
        $this->renderDashboard('admin/events/index', [
            'pageTitle' => 'Manage Events',
            'pageSubtitle' => 'Search, filter, and maintain campus events',
            'pageActionUrl' => '/admin/events/create',
            'pageActionLabel' => '+ Create event',
            'events' => $this->events->paginateAdmin($filters),
            'categories' => $this->events->distinctCategories(),
            'filters' => $filters,
        ]);
    }

    public function eventCreate(): void
    {
        Auth::requireAdmin();
        $this->renderDashboard('admin/events/create', [
            'pageTitle' => 'Create Event',
            'pageActionUrl' => '/admin/events',
            'pageActionLabel' => 'Back to events',
            'errors' => $this->getValidationErrors(),
        ]);
    }

    public function eventStore(): void
    {
        Auth::requireAdmin();
        $this->validateCsrf();

        $data = $this->validateEvent($_POST);
        if (isset($data['errors'])) {
            $this->backWithErrors($data['errors'], $_POST, '/admin/events/create');
        }

        // Optional image upload from admin device
        $imageResult = $this->handleEventImageUpload($_FILES['image'] ?? null, null);
        if (isset($imageResult['error'])) {
            $this->backWithErrors(['image' => $imageResult['error']], $_POST, '/admin/events/create');
        }
        $data['image'] = $imageResult['filename'] ?? null;

        $userId = (int) Auth::user()['id'];
        $id = $this->events->create($data, $userId);

        $this->activityLog->log('event_created', 'event', $id, 'Event created: ' . $data['title'], $userId);

        Session::flash('success', 'Event created successfully.');
        redirect('/admin/events');
    }

    public function eventShow(string $id): void
    {
        Auth::requireAdmin();
        $event = $this->events->findById((int) $id);
        if (!$event) {
            http_response_code(404);
            view('errors/404');
            return;
        }
        $this->renderDashboard('admin/events/show', [
            'pageTitle' => $event['title'],
            'pageActionUrl' => '/admin/events/' . $event['id'] . '/edit',
            'pageActionLabel' => 'Edit event',
            'event' => $event,
            'ticketCount' => $this->events->ticketRequestCount((int) $id),
        ]);
    }

    public function eventEdit(string $id): void
    {
        Auth::requireAdmin();
        $event = $this->events->findById((int) $id);
        if (!$event) {
            http_response_code(404);
            view('errors/404');
            return;
        }
        $this->renderDashboard('admin/events/edit', [
            'pageTitle' => 'Edit Event',
            'pageActionUrl' => '/admin/events/' . $event['id'],
            'pageActionLabel' => 'View event',
            'event' => $event,
            'errors' => $this->getValidationErrors(),
        ]);
    }

    public function eventUpdate(string $id): void
    {
        Auth::requireAdmin();
        $this->validateCsrf();

        $event = $this->events->findById((int) $id);
        if (!$event) {
            http_response_code(404);
            view('errors/404');
            return;
        }

        $data = $this->validateEvent($_POST, (int) $id);
        if (isset($data['errors'])) {
            $this->backWithErrors($data['errors'], $_POST, '/admin/events/' . $id . '/edit');
        }

        $oldImage = $event['image'] ?? null;
        $imageResult = $this->handleEventImageUpload($_FILES['image'] ?? null, $oldImage);
        if (isset($imageResult['error'])) {
            $this->backWithErrors(['image' => $imageResult['error']], $_POST, '/admin/events/' . $id . '/edit');
        }
        $data['image'] = $imageResult['filename'] ?? null;

        // Remove replaced file from disk (not placeholders)
        if ($oldImage && ($data['image'] ?? null) !== $oldImage) {
            require_once APP_PATH . '/services/EventImageUpload.php';
            (new EventImageUpload())->deleteOldFile($oldImage);
        }

        $userId = (int) Auth::user()['id'];
        $this->events->update((int) $id, $data, $userId);

        $this->activityLog->log('event_updated', 'event', (int) $id, 'Event updated: ' . $data['title'], $userId);

        Session::flash('success', 'Event updated.');
        redirect('/admin/events/' . $id);
    }

    public function eventDelete(string $id): void
    {
        Auth::requireAdmin();
        $this->validateCsrf();

        $eventId = (int) $id;
        if ($this->events->ticketRequestCount($eventId) > 0) {
            Session::flash('error', 'Cannot delete event: ticket requests exist.');
            redirect('/admin/events/' . $eventId);
        }

        $event = $this->events->findById($eventId);
        $this->events->delete($eventId);

        $this->activityLog->log(
            'event_deleted',
            'event',
            $eventId,
            'Event deleted: ' . ($event['title'] ?? $eventId),
            (int) Auth::user()['id']
        );

        Session::flash('success', 'Event deleted.');
        redirect('/admin/events');
    }

    /** Activity logs admin page */
    public function activityLogs(): void
    {
        Auth::requireAdmin();
        $page = (int) ($_GET['page'] ?? 1);
        $logModel = new ActivityLog();
        $this->renderDashboard('admin/activity-logs', [
            'pageTitle' => 'Activity Logs',
            'pageSubtitle' => 'System audit trail',
            'logs' => $logModel->paginate($page),
        ]);
    }

    /**
     * Validate event form fields per assessment rules.
     *
     * @param array<string, mixed> $post
     * @param int|null $excludeId For unique slug on update
     * @return array<string, mixed>
     */
    private function validateEvent(array $post, ?int $excludeId = null): array
    {
        $v = new Validator($post);
        $v->required('title', 'Title');
        $v->length('title', 'Title', 5, 150);
        $v->required('description', 'Description');
        $v->required('category', 'Category');
        $v->required('location', 'Location');
        $v->required('event_date', 'Event date');
        $v->required('start_time', 'Start time');
        $v->required('end_time', 'End time');
        $v->timeAfter('start_time', 'end_time');
        $v->required('capacity', 'Capacity');
        $v->integerRange('capacity', 'Capacity', 1, 5000);
        $v->required('status', 'Status');
        $v->inList('status', 'Status', ['draft', 'published', 'cancelled']);

        $errors = $v->errors();
        if ($v->value('description') !== '' && strlen($v->value('description')) < 20) {
            $errors['description'] = 'Description must be at least 20 characters.';
        }

        if ($errors !== []) {
            return ['errors' => $errors];
        }

        $slug = slugify($v->value('title'));
        if ($this->events->slugExists($slug, $excludeId)) {
            $slug .= '-' . ($excludeId ?? time());
        }

        return [
            'title' => $v->value('title'),
            'slug' => $slug,
            'description' => $v->value('description'),
            'category' => $v->value('category'),
            'location' => $v->value('location'),
            'event_date' => $v->value('event_date'),
            'start_time' => $v->value('start_time'),
            'end_time' => $v->value('end_time'),
            'capacity' => (int) $v->value('capacity'),
            'status' => $v->value('status'),
        ];
    }

    /**
     * Handle multipart image upload; keeps existing filename when no new file.
     *
     * @param array<string, mixed>|null $file $_FILES['image']
     * @param string|null $existingFilename Current DB value on edit
     * @return array{filename?: string|null, error?: string}
     */
    private function handleEventImageUpload(?array $file, ?string $existingFilename): array
    {
        require_once APP_PATH . '/services/EventImageUpload.php';
        $uploader = new EventImageUpload();

        return $uploader->process($file, $existingFilename);
    }
}
