<?php
/**
 * DashboardController.php — Role-specific dashboards with summaries and shortcuts.
 *
 * Admin: stats, pending tickets, recent activity, quick links.
 * User: upcoming events, my tickets, announcements, help shortcut.
 */

declare(strict_types=1);

class DashboardController extends Controller
{
    /**
     * GET /dashboard — Different content for admin vs standard user.
     */
    public function index(): void
    {
        Auth::requireLogin();

        if (Auth::isAdmin()) {
            $this->adminDashboard();
            return;
        }

        $this->userDashboard();
    }

    /**
     * Admin dashboard with operational overview.
     */
    private function adminDashboard(): void
    {
        $statsModel = new DashboardStats();
        $ticketModel = new TicketRequest();

        $pending = $ticketModel->paginateAdmin(['status' => 'pending', 'page' => 1]);
        $pending['items'] = array_slice($pending['items'], 0, 5);

        $this->renderDashboard('dashboard/admin', [
            'pageTitle' => 'Admin Dashboard',
            'pageSubtitle' => 'Overview of events, tickets, and campus activity',
            'stats' => $statsModel->adminSummary(),
            'recentActivity' => $statsModel->recentActivity(8),
            'recentEvents' => $statsModel->recentEvents(5),
            'pendingTickets' => $pending['items'],
        ]);
    }

    /**
     * Standard user dashboard.
     */
    private function userDashboard(): void
    {
        $userId = (int) Auth::user()['id'];
        $ticketModel = new TicketRequest();
        $eventModel = new Event();
        $statsModel = new DashboardStats();

        $upcoming = $eventModel->paginatePublic([
            'page' => 1,
            'date_filter' => 'upcoming',
        ]);
        $upcoming['items'] = array_slice($upcoming['items'], 0, 4);

        $ticketSummary = $statsModel->userTicketSummary($userId);
        $publicStats = $statsModel->publicHomeStats();

        $this->renderDashboard('dashboard/index', [
            'pageTitle' => 'My Dashboard',
            'pageSubtitle' => 'Discover events and track your ticket requests',
            'tickets' => $ticketModel->forUser($userId),
            'ticketSummary' => $ticketSummary,
            'upcomingEvents' => $upcoming['items'],
            'upcomingEventsCount' => $publicStats['upcoming_events'] ?? 0,
            'announcements' => $statsModel->latestAnnouncements(3),
            'announcementsCount' => $statsModel->publishedAnnouncementsCount(),
        ]);
    }
}
