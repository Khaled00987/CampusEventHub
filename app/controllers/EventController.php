<?php
/**
 * EventController.php — Public event browsing and ticket requests.
 */

declare(strict_types=1);

class EventController extends Controller
{
    private Event $events;
    private TicketRequest $tickets;
    private ActivityLog $activityLog;

    public function __construct()
    {
        $this->events = new Event();
        $this->tickets = new TicketRequest();
        $this->activityLog = new ActivityLog();
    }

    /** Public event list with search, filter, pagination */
    public function index(): void
    {
        $filters = [
            'q' => $_GET['q'] ?? '',
            'category' => $_GET['category'] ?? '',
            'date_filter' => $_GET['date_filter'] ?? 'upcoming',
            'page' => $_GET['page'] ?? 1,
        ];
        $result = $this->events->paginatePublic($filters);

        $this->render('events/index', [
            'pageTitle' => 'Events',
            'events' => $result,
            'categories' => $this->events->distinctCategories(),
            'filters' => $filters,
        ]);
    }

    /** Single event detail by slug */
    public function show(string $slug): void
    {
        $event = $this->events->findBySlug($slug);
        if (!$event || $event['status'] !== 'published') {
            http_response_code(404);
            view('errors/404');
            return;
        }

        $hasActiveRequest = false;
        if (Auth::check() && !Auth::isAdmin()) {
            $hasActiveRequest = $this->tickets->hasActiveRequest(
                (int) Auth::user()['id'],
                (int) $event['id']
            );
        }

        $this->render('events/show', [
            'pageTitle' => $event['title'],
            'event' => $event,
            'hasActiveRequest' => $hasActiveRequest,
            'errors' => $this->getValidationErrors(),
            'isAdmin' => Auth::isAdmin(),
        ]);
    }

    /** POST ticket request for published event */
    public function requestTicket(string $id): void
    {
        Auth::requireLogin();
        if (Auth::isAdmin()) {
            Session::flash('error', 'Admins manage tickets from the admin panel.');
            redirect('/events');
        }

        $this->validateCsrf();

        $event = $this->events->findById((int) $id);
        if (!$event || $event['status'] !== 'published') {
            http_response_code(404);
            view('errors/404');
            return;
        }

        $userId = (int) Auth::user()['id'];

        if ($this->tickets->hasActiveRequest($userId, (int) $event['id'])) {
            Session::flash('error', 'You already have a pending or approved request for this event.');
            redirect('/events/' . $event['slug']);
        }

        $v = new Validator($_POST);
        $v->required('quantity', 'Quantity');
        $v->integerRange('quantity', 'Quantity', 1, 5);
        $v->required('attendee_name', 'Attendee name');
        $v->required('attendee_email', 'Attendee email');
        $v->email('attendee_email', 'Attendee email');
        if ($v->value('note') !== '') {
            $v->length('note', 'Note', 0, 500);
        }

        if ($v->fails()) {
            $this->backWithErrors($v->errors(), $_POST, '/events/' . $event['slug']);
        }

        $ticketId = $this->tickets->create([
            'event_id' => (int) $event['id'],
            'quantity' => (int) $v->value('quantity'),
            'attendee_name' => $v->value('attendee_name'),
            'attendee_email' => $v->value('attendee_email'),
            'note' => $v->value('note'),
        ], $userId);

        $this->activityLog->log(
            'ticket_created',
            'ticket_request',
            $ticketId,
            'Ticket request created for event #' . $event['id'],
            $userId
        );

        Session::flash('success', 'Ticket request submitted. You can track status under My Tickets.');
        redirect('/my-tickets');
    }
}
