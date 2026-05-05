<?php
/**
 * Event.php — Campus events CRUD and public listing queries.
 *
 * Supports search, filter, pagination for admin and public views.
 * Slugs are unique; used in friendly URLs /events/{slug}.
 */

declare(strict_types=1);

class Event extends Model
{
    /**
     * Paginated admin list with search and filters.
     *
     * @param array<string, mixed> $filters q, category, status, page
     * @return array{items: array<int, array>, total: int, page: int, per_page: int}
     */
    public function paginateAdmin(array $filters): array
    {
        return $this->paginate($filters, false);
    }

    /**
     * Public published events with search/filter.
     *
     * @param array<string, mixed> $filters
     * @return array{items: array<int, array>, total: int, page: int, per_page: int}
     */
    public function paginatePublic(array $filters): array
    {
        $filters['status'] = $filters['status'] ?? 'published';
        return $this->paginate($filters, true);
    }

    /**
     * @param array<string, mixed> $filters
     * @param bool $publicOnly Force published for public listing
     * @return array{items: array<int, array>, total: int, page: int, per_page: int}
     */
    private function paginate(array $filters, bool $publicOnly): array
    {
        $page = max(1, (int) ($filters['page'] ?? 1));
        $perPage = PER_PAGE;
        $offset = ($page - 1) * $perPage;

        $where = ['1=1'];
        $params = [];

        if ($publicOnly) {
            $where[] = 'e.status = :status_pub';
            $params['status_pub'] = 'published';
        } elseif (!empty($filters['status'])) {
            $where[] = 'e.status = :status';
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['category'])) {
            $where[] = 'e.category = :category';
            $params['category'] = $filters['category'];
        }

        if (!empty($filters['q'])) {
            $where[] = '(e.title LIKE :q OR e.location LIKE :q2)';
            $params['q'] = '%' . $filters['q'] . '%';
            $params['q2'] = '%' . $filters['q'] . '%';
        }

        // Date filter for public listing: upcoming, past, or all
        if ($publicOnly && !empty($filters['date_filter'])) {
            if ($filters['date_filter'] === 'upcoming') {
                $where[] = 'e.event_date >= CURDATE()';
            } elseif ($filters['date_filter'] === 'past') {
                $where[] = 'e.event_date < CURDATE()';
            }
        }

        $whereSql = implode(' AND ', $where);

        $countSql = "SELECT COUNT(*) FROM events e WHERE {$whereSql}";
        $stmt = $this->db->prepare($countSql);
        $stmt->execute($params);
        $total = (int) $stmt->fetchColumn();

        $sql = "SELECT e.*, u.name AS creator_name
                FROM events e
                LEFT JOIN users u ON u.id = e.created_by
                WHERE {$whereSql}
                ORDER BY e.event_date DESC, e.id DESC
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
     * @param int $id
     * @return array<string, mixed>|null
     */
    public function findById(int $id): ?array
    {
        $sql = 'SELECT e.*, u.name AS creator_name
                FROM events e
                LEFT JOIN users u ON u.id = e.created_by
                WHERE e.id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * @param string $slug
     * @return array<string, mixed>|null
     */
    public function findBySlug(string $slug): ?array
    {
        $sql = 'SELECT e.*, u.name AS creator_name
                FROM events e
                LEFT JOIN users u ON u.id = e.created_by
                WHERE e.slug = :slug';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['slug' => $slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * @param string $slug
     * @param int|null $excludeId
     * @return bool
     */
    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM events WHERE slug = :slug';
        $params = ['slug' => $slug];
        if ($excludeId) {
            $sql .= ' AND id != :id';
            $params['id'] = $excludeId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * @param array<string, mixed> $data
     * @param int $userId
     * @return int
     */
    public function create(array $data, int $userId): int
    {
        $sql = 'INSERT INTO events (title, slug, description, category, location, event_date,
                start_time, end_time, capacity, image, status, created_by, updated_by, created_at, updated_at)
                VALUES (:title, :slug, :description, :category, :location, :event_date,
                :start_time, :end_time, :capacity, :image, :status, :created_by, :updated_by, NOW(), NOW())';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'title' => $data['title'],
            'slug' => $data['slug'],
            'description' => $data['description'],
            'category' => $data['category'],
            'location' => $data['location'],
            'event_date' => $data['event_date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'capacity' => $data['capacity'],
            'image' => $data['image'] ?? null,
            'status' => $data['status'],
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * @param int $id
     * @param array<string, mixed> $data
     * @param int $userId
     */
    public function update(int $id, array $data, int $userId): void
    {
        $sql = 'UPDATE events SET title = :title, slug = :slug, description = :description,
                category = :category, location = :location, event_date = :event_date,
                start_time = :start_time, end_time = :end_time, capacity = :capacity,
                image = :image, status = :status, updated_by = :updated_by, updated_at = NOW()
                WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'title' => $data['title'],
            'slug' => $data['slug'],
            'description' => $data['description'],
            'category' => $data['category'],
            'location' => $data['location'],
            'event_date' => $data['event_date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'capacity' => $data['capacity'],
            'image' => $data['image'] ?? null,
            'status' => $data['status'],
            'updated_by' => $userId,
        ]);
    }

    /**
     * @param int $id
     */
    public function delete(int $id): void
    {
        $sql = 'DELETE FROM events WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
    }

    /**
     * Count ticket requests linked to an event (prevents delete if > 0).
     *
     * @param int $eventId
     * @return int
     */
    public function ticketRequestCount(int $eventId): int
    {
        $sql = 'SELECT COUNT(*) FROM ticket_requests WHERE event_id = :event_id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['event_id' => $eventId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Sum approved ticket quantities for capacity check.
     *
     * @param int $eventId
     * @return int
     */
    public function approvedTicketsSum(int $eventId): int
    {
        $sql = "SELECT COALESCE(SUM(quantity), 0) FROM ticket_requests
                WHERE event_id = :event_id AND status = 'approved'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['event_id' => $eventId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Distinct categories for filter dropdowns.
     *
     * @return array<int, string>
     */
    public function distinctCategories(): array
    {
        $sql = 'SELECT DISTINCT category FROM events ORDER BY category';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
