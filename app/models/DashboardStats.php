<?php
/**
 * DashboardStats.php — Aggregate counts for admin and user dashboards.
 *
 * Uses prepared statements. Keeps dashboard logic out of views.
 */

declare(strict_types=1);

class DashboardStats extends Model
{
    /**
     * Admin dashboard summary cards.
     *
     * @return array{total_events: int, published_events: int, pending_tickets: int, published_announcements: int}
     */
    public function adminSummary(): array
    {
        return [
            'total_events' => $this->scalar('SELECT COUNT(*) FROM events'),
            'published_events' => $this->scalar("SELECT COUNT(*) FROM events WHERE status = 'published'"),
            'pending_tickets' => $this->scalar("SELECT COUNT(*) FROM ticket_requests WHERE status = 'pending'"),
            'published_announcements' => $this->scalar("SELECT COUNT(*) FROM announcements WHERE status = 'published'"),
            'ai_suggestions' => $this->scalar('SELECT COUNT(*) FROM ai_logs'),
        ];
    }

    /**
     * Latest events for admin dashboard widget.
     *
     * @param int $limit
     * @return array<int, array>
     */
    public function recentEvents(int $limit = 5): array
    {
        $sql = 'SELECT id, title, status, event_date, category, image
                FROM events ORDER BY updated_at DESC LIMIT :limit';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Ticket counts for standard user dashboard.
     *
     * @param int $userId
     * @return array{total: int, pending: int, approved: int, rejected: int}
     */
    public function userTicketSummary(int $userId): array
    {
        $sql = "SELECT
                    COUNT(*) AS total,
                    SUM(status = 'pending') AS pending,
                    SUM(status = 'approved') AS approved,
                    SUM(status = 'rejected') AS rejected
                FROM ticket_requests WHERE user_id = :uid";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':uid', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch() ?: [];

        return [
            'total' => (int) ($row['total'] ?? 0),
            'pending' => (int) ($row['pending'] ?? 0),
            'approved' => (int) ($row['approved'] ?? 0),
            'rejected' => (int) ($row['rejected'] ?? 0),
        ];
    }

    /**
     * Recent activity log rows for admin dashboard widget.
     *
     * @param int $limit
     * @return array<int, array>
     */
    public function recentActivity(int $limit = 5): array
    {
        $sql = 'SELECT a.*, u.name AS user_name
                FROM activity_logs a
                LEFT JOIN users u ON u.id = a.user_id
                ORDER BY a.created_at DESC
                LIMIT :limit';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Latest published announcements for user dashboard.
     *
     * @param int $limit
     * @return array<int, array>
     */
    public function publishedAnnouncementsCount(): int
    {
        return $this->scalar("SELECT COUNT(*) FROM announcements WHERE status = 'published'");
    }

    /**
     * @param int $limit
     * @return array<int, array>
     */
    public function latestAnnouncements(int $limit = 3): array
    {
        $sql = "SELECT id, title, body, created_at FROM announcements
                WHERE status = 'published' ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Public landing page hero statistics.
     *
     * @return array{upcoming_events: int, ticket_requests: int, community_members: int}
     */
    public function publicHomeStats(): array
    {
        return [
            'upcoming_events' => $this->scalar(
                "SELECT COUNT(*) FROM events WHERE status = 'published' AND event_date >= CURDATE()"
            ),
            'ticket_requests' => $this->scalar('SELECT COUNT(*) FROM ticket_requests'),
            'community_members' => $this->scalar("SELECT COUNT(*) FROM users WHERE status = 'active'"),
        ];
    }

    /**
     * @param string $sql
     * @return int
     */
    private function scalar(string $sql): int
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }
}
