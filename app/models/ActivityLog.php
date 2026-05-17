<?php
/**
 * ActivityLog.php — Audit trail for important user and admin actions.
 *
 * Records who did what, on which entity, from which IP — supports assessment
 * requirement for auditability and accountability.
 */

declare(strict_types=1);

class ActivityLog extends Model
{
    /**
     * Write a new activity log entry.
     *
     * @param string $action e.g. event_created
     * @param string|null $entityType e.g. event
     * @param int|null $entityId
     * @param string $details Human-readable detail
     * @param int|null $userId Nullable for anonymous actions
     */
    public function log(
        string $action,
        ?string $entityType,
        ?int $entityId,
        string $details,
        ?int $userId = null
    ): void {
        $sql = 'INSERT INTO activity_logs (user_id, action, entity_type, entity_id, details, ip_address, created_at)
                VALUES (:user_id, :action, :entity_type, :entity_id, :details, :ip_address, NOW())';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'details' => $details,
            'ip_address' => client_ip(),
        ]);
    }

    /**
     * Paginated activity logs for admin review.
     *
     * @param int $page
     * @return array{items: array<int, array>, total: int, page: int, per_page: int}
     */
    public function paginate(int $page = 1): array
    {
        $page = max(1, $page);
        $perPage = PER_PAGE;
        $offset = ($page - 1) * $perPage;

        $countStmt = $this->db->prepare('SELECT COUNT(*) FROM activity_logs');
        $countStmt->execute();
        $total = (int) $countStmt->fetchColumn();

        $sql = 'SELECT a.*, u.name AS user_name, u.email AS user_email
                FROM activity_logs a
                LEFT JOIN users u ON u.id = a.user_id
                ORDER BY a.created_at DESC
                LIMIT :limit OFFSET :offset';
        $stmt = $this->db->prepare($sql);
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
}
