<?php
/**
 * TicketController.php — User ticket list, admin ticket management, PDF download.
 */

declare(strict_types=1);

class TicketController extends Controller
{
    private TicketRequest $tickets;
    private Event $events;
    private ActivityLog $activityLog;

    public function __construct()
    {
        $this->tickets = new TicketRequest();
        $this->events = new Event();
        $this->activityLog = new ActivityLog();
    }

    /** Standard user: view own ticket requests */
    public function myTickets(): void
    {
        Auth::requireLogin();
        if (Auth::isAdmin()) {
            redirect('/admin/tickets');
        }

        $this->renderDashboard('tickets/my-tickets', [
            'pageTitle' => 'My Tickets',
            'pageSubtitle' => 'Track requests, approvals, and PDF downloads',
            'pageActionUrl' => '/events',
            'pageActionLabel' => 'Browse events',
            'tickets' => $this->tickets->forUser((int) Auth::user()['id']),
        ]);
    }

    /**
     * Download approved ticket as PDF.
     * Users may only download their own tickets; admins may download any approved ticket.
     */
    public function download(string $id): void
    {
        Auth::requireLogin();

        $ticket = $this->tickets->findById((int) $id);
        if (!$ticket) {
            http_response_code(404);
            view('errors/404');
            return;
        }

        $user = Auth::user();
        $isOwner = (int) ($user['id'] ?? 0) === (int) $ticket['user_id'];
        $isAdmin = Auth::isAdmin();

        if (!$isOwner && !$isAdmin) {
            http_response_code(403);
            view('errors/403');
            return;
        }

        if (($ticket['status'] ?? '') !== 'approved') {
            Session::flash('error', 'PDF is only available for approved tickets.');
            redirect($isAdmin ? '/admin/tickets/' . $id : '/my-tickets');
        }

        // Log before PDF output (download() ends the request)
        $this->activityLog->log(
            'ticket_pdf_downloaded',
            'ticket_request',
            (int) $id,
            'Ticket PDF downloaded: #' . $id,
            (int) $user['id']
        );

        require_once APP_PATH . '/services/TicketPdfService.php';
        (new TicketPdfService())->download($ticket);
    }

    /** Admin ticket list */
    public function adminIndex(): void
    {
        Auth::requireAdmin();
        $filters = [
            'q' => $_GET['q'] ?? '',
            'status' => $_GET['status'] ?? '',
            'page' => $_GET['page'] ?? 1,
        ];
        $this->renderDashboard('admin/tickets/index', [
            'pageTitle' => 'Ticket Requests',
            'pageSubtitle' => 'Review and approve attendee requests',
            'pageActionUrl' => '/admin/tickets?status=pending',
            'pageActionLabel' => 'Pending only',
            'tickets' => $this->tickets->paginateAdmin($filters),
            'filters' => $filters,
        ]);
    }

    /** Admin ticket detail */
    public function adminShow(string $id): void
    {
        Auth::requireAdmin();
        $ticket = $this->tickets->findById((int) $id);
        if (!$ticket) {
            http_response_code(404);
            view('errors/404');
            return;
        }

        $this->renderDashboard('admin/tickets/show', [
            'pageTitle' => 'Ticket #' . $id,
            'pageActionUrl' => '/admin/tickets',
            'pageActionLabel' => 'All requests',
            'ticket' => $ticket,
            'errors' => $this->getValidationErrors(),
        ]);
    }

    /** Admin approve/reject with capacity check */
    public function updateStatus(string $id): void
    {
        Auth::requireAdmin();
        $this->validateCsrf();

        $ticket = $this->tickets->findById((int) $id);
        if (!$ticket) {
            http_response_code(404);
            view('errors/404');
            return;
        }

        $v = new Validator($_POST);
        $v->required('status', 'Status');
        $v->inList('status', 'Status', ['approved', 'rejected', 'cancelled']);

        if ($v->fails()) {
            $this->backWithErrors($v->errors(), $_POST, '/admin/tickets/' . $id);
        }

        $newStatus = $v->value('status');
        $adminNote = $v->value('admin_note');

        if ($newStatus === 'approved') {
            $event = $this->events->findById((int) $ticket['event_id']);
            $approvedSum = $this->events->approvedTicketsSum((int) $ticket['event_id']);
            $requestedQty = (int) $ticket['quantity'];

            if ($event && ($approvedSum + $requestedQty) > (int) $event['capacity']) {
                $this->backWithErrors(
                    ['status' => 'Cannot approve: event capacity would be exceeded.'],
                    $_POST,
                    '/admin/tickets/' . $id
                );
            }
        }

        $this->tickets->updateStatus((int) $id, $newStatus, $adminNote, (int) Auth::user()['id']);

        $action = $newStatus === 'approved' ? 'ticket_approved' : ($newStatus === 'rejected' ? 'ticket_rejected' : 'ticket_updated');
        $this->activityLog->log(
            $action,
            'ticket_request',
            (int) $id,
            "Ticket #{$id} status changed to {$newStatus}",
            (int) Auth::user()['id']
        );

        Session::flash('success', 'Ticket status updated.');
        redirect('/admin/tickets/' . $id);
    }
}
