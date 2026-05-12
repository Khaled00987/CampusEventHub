<?php
/**
 * TicketRequest.php — Ticket request CRUD and status workflow.
 *
 * Users request tickets for published events. Admins approve/reject.
 * Duplicate active requests for same user/event are prevented.
 */

declare(strict_types=1);

class TicketRequest extends Model
{
    /**
     * Admin paginated list with search and status filter.
     *
     * @param array<string, mixed> $filters
     * @return array{items: array<int, array>, total: int, page: int, per_page: int}
     */
    public function paginateAdmin(array $filters): array
    {
        $page = max(1, (int) ($filters['page'] ?? 1));
        $perPage = PER_PAGE;
        $offset = ($page - 1) * $perPage;

        $where = ['1=1'];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = 't.status = :status';
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['q'])) {
            $where[] = '(t.attendee_name LIKE :q OR t.attendee_email LIKE :q2 OR e.title LIKE :q3)';
            $params['q'] = '%' . $filters['q'] . '%';
            $params['q2'] = '%' . $filters['q'] . '%';
            $params['q3'] = '%' . $filters['q'] . '%';
        }

        $whereSql = implode(' AND ', $where);

        $countSql = "SELECT COUNT(*) FROM ticket_requests t
                     INNER JOIN events e ON e.id = t.event_id
                     WHERE {$whereSql}";
        $stmt = $this->db->prepare($countSql);
        $stmt->execute($params);
        $total = (int) $stmt->fetchColumn();

        $sql = "SELECT t.*, e.title AS event_title, e.event_date, e.capacity,
                       u.name AS user_name, u.email AS user_email
                FROM ticket_requests t
                INNER JOIN events e ON e.id = t.event_id
                INNER JOIN users u ON u.id = t.user_id
                WHERE {$whereSql}
                ORDER BY t.created_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'items' => $stmt->fetchAll(),
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
        ];
    }

    /**
     * User's own ticket requests.
     *
     * @param int $userId
     * @return array<int, array>
     */
    public function forUser(int $userId): array
    {
        $sql = "SELECT t.*, e.title AS event_title, e.slug AS event_slug, e.event_date
                FROM ticket_requests t
                INNER JOIN events e ON e.id = t.event_id
                WHERE t.user_id = :user_id
                ORDER BY t.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * @param int $id
     * @return array<string, mixed>|null
     */
    public function findById(int $id): ?array
    {
        $sql = "SELECT t.*, e.title AS event_title, e.category AS event_category,
                       e.location AS event_location, e.event_date, e.start_time AS event_start_time,
                       e.end_time AS event_end_time, e.image AS event_image, e.capacity,
                       e.slug AS event_slug, u.name AS user_name, u.email AS user_email
                FROM ticket_requests t
                INNER JOIN events e ON e.id = t.event_id
                INNER JOIN users u ON u.id = t.user_id
                WHERE t.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Check duplicate pending/approved request for same user and event.
     *
     * @param int $userId
     * @param int $eventId
     * @return bool
     */
    public function hasActiveRequest(int $userId, int $eventId): bool
    {
        $sql = "SELECT COUNT(*) FROM ticket_requests
                WHERE user_id = :user_id AND event_id = :event_id
                AND status IN ('pending', 'approved')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId, 'event_id' => $eventId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * @param array<string, mixed> $data
     * @param int $userId
     * @return int
     */
    public function create(array $data, int $userId): int
    {
        $sql = 'INSERT INTO ticket_requests (event_id, user_id, quantity, attendee_name,
                attendee_email, note, status, created_by, updated_by, created_at, updated_at)
                VALUES (:event_id, :user_id, :quantity, :attendee_name, :attendee_email,
                :note, :status, :created_by, :updated_by, NOW(), NOW())';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'event_id' => $data['event_id'],
            'user_id' => $userId,
            'quantity' => $data['quantity'],
            'attendee_name' => $data['attendee_name'],
            'attendee_email' => $data['attendee_email'],
            'note' => $data['note'] ?? '',
            'status' => 'pending',
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Update status and admin note (approve/reject workflow).
     *
     * @param int $id
     * @param string $status
     * @param string $adminNote
     * @param int $adminId
     */
    public function updateStatus(int $id, string $status, string $adminNote, int $adminId): void
    {
        $sql = 'UPDATE ticket_requests SET status = :status, admin_note = :admin_note,
                updated_by = :updated_by, updated_at = NOW() WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'status' => $status,
            'admin_note' => $adminNote,
            'updated_by' => $adminId,
        ]);
    }
}
