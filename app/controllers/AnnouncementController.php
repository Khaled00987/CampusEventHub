<?php
/**
 * AnnouncementController.php — Public announcements and admin CRUD.
 */

declare(strict_types=1);

class AnnouncementController extends Controller
{
    private Announcement $announcements;
    private ActivityLog $activityLog;

    public function __construct()
    {
        $this->announcements = new Announcement();
        $this->activityLog = new ActivityLog();
    }

    /** Public published announcements */
    public function index(): void
    {
        $this->render('announcements/index', [
            'pageTitle' => 'Announcements',
            'announcements' => $this->announcements->published(),
        ]);
    }

    /** Admin list */
    public function adminIndex(): void
    {
        Auth::requireAdmin();
        $filters = [
            'q' => $_GET['q'] ?? '',
            'status' => $_GET['status'] ?? '',
            'page' => $_GET['page'] ?? 1,
        ];
        $this->renderDashboard('admin/announcements/index', [
            'pageTitle' => 'Announcements',
            'pageSubtitle' => 'Publish campus news and updates',
            'pageActionUrl' => '/admin/announcements/create',
            'pageActionLabel' => 'Create announcement',
            'announcements' => $this->announcements->paginateAdmin($filters),
            'filters' => $filters,
        ]);
    }

    public function create(): void
    {
        Auth::requireAdmin();
        $this->renderDashboard('admin/announcements/create', [
            'pageTitle' => 'Create Announcement',
            'pageActionUrl' => '/admin/announcements',
            'pageActionLabel' => 'Back',
            'errors' => $this->getValidationErrors(),
        ]);
    }

    public function store(): void
    {
        Auth::requireAdmin();
        $this->validateCsrf();

        $data = $this->validateAnnouncement($_POST);
        if (isset($data['errors'])) {
            $this->backWithErrors($data['errors'], $_POST, '/admin/announcements/create');
        }

        $id = $this->announcements->create($data, (int) Auth::user()['id']);
        $this->activityLog->log('announcement_created', 'announcement', $id, 'Announcement created', (int) Auth::user()['id']);

        Session::flash('success', 'Announcement created.');
        redirect('/admin/announcements');
    }

    public function edit(string $id): void
    {
        Auth::requireAdmin();
        $item = $this->announcements->findById((int) $id);
        if (!$item) {
            http_response_code(404);
            view('errors/404');
            return;
        }
        $this->renderDashboard('admin/announcements/edit', [
            'pageTitle' => 'Edit Announcement',
            'pageActionUrl' => '/admin/announcements',
            'pageActionLabel' => 'Back',
            'announcement' => $item,
            'errors' => $this->getValidationErrors(),
        ]);
    }

    public function update(string $id): void
    {
        Auth::requireAdmin();
        $this->validateCsrf();

        $item = $this->announcements->findById((int) $id);
        if (!$item) {
            http_response_code(404);
            view('errors/404');
            return;
        }

        $data = $this->validateAnnouncement($_POST);
        if (isset($data['errors'])) {
            $this->backWithErrors($data['errors'], $_POST, '/admin/announcements/' . $id . '/edit');
        }

        $this->announcements->update((int) $id, $data, (int) Auth::user()['id']);
        $this->activityLog->log('announcement_updated', 'announcement', (int) $id, 'Announcement updated', (int) Auth::user()['id']);

        Session::flash('success', 'Announcement updated.');
        redirect('/admin/announcements');
    }

    public function delete(string $id): void
    {
        Auth::requireAdmin();
        $this->validateCsrf();

        $this->announcements->delete((int) $id);
        $this->activityLog->log('announcement_deleted', 'announcement', (int) $id, 'Announcement deleted', (int) Auth::user()['id']);

        Session::flash('success', 'Announcement deleted.');
        redirect('/admin/announcements');
    }

    /**
     * @param array<string, mixed> $post
     * @return array<string, mixed>
     */
    private function validateAnnouncement(array $post): array
    {
        $v = new Validator($post);
        $v->required('title', 'Title');
        $v->length('title', 'Title', 5, 150);
        $v->required('body', 'Body');
        $v->required('status', 'Status');
        $v->inList('status', 'Status', ['draft', 'published']);

        $errors = $v->errors();
        if ($v->value('body') !== '' && strlen($v->value('body')) < 20) {
            $errors['body'] = 'Body must be at least 20 characters.';
        }

        if ($errors !== []) {
            return ['errors' => $errors];
        }

        return [
            'title' => $v->value('title'),
            'body' => $v->value('body'),
            'status' => $v->value('status'),
        ];
    }
}
