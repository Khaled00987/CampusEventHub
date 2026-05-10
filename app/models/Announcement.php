<?php
/**
 * Announcement.php — Admin announcements CRUD and public feed.
 */

declare(strict_types=1);

class Announcement extends Model
{
    /**
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
            $where[] = 'a.status = :status';
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['q'])) {
            $where[] = '(a.title LIKE :q OR a.body LIKE :q2)';
            $params['q'] = '%' . $filters['q'] . '%';
            $params['q2'] = '%' . $filters['q'] . '%';
        }

        $whereSql = implode(' AND ', $where);

        $countSql = "SELECT COUNT(*) FROM announcements a WHERE {$whereSql}";
        $stmt = $this->db->prepare($countSql);
        $stmt->execute($params);
        $total = (int) $stmt->fetchColumn();

        $sql = "SELECT a.*, u.name AS creator_name
                FROM announcements a
                LEFT JOIN users u ON u.id = a.created_by
                WHERE {$whereSql}
                ORDER BY a.created_at DESC
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
     * Published announcements for public page.
     *
     * @return array<int, array>
     */
    public function published(): array
    {
        $sql = "SELECT a.*, u.name AS creator_name
                FROM announcements a
                LEFT JOIN users u ON u.id = a.created_by
                WHERE a.status = 'published'
                ORDER BY a.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * @param int $id
     * @return array<string, mixed>|null
     */
    public function findById(int $id): ?array
    {
        $sql = 'SELECT a.*, u.name AS creator_name FROM announcements a
                LEFT JOIN users u ON u.id = a.created_by WHERE a.id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * @param array<string, mixed> $data
     * @param int $userId
     * @return int
     */
    public function create(array $data, int $userId): int
    {
        $sql = 'INSERT INTO announcements (title, body, status, created_by, updated_by, created_at, updated_at)
                VALUES (:title, :body, :status, :created_by, :updated_by, NOW(), NOW())';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'title' => $data['title'],
            'body' => $data['body'],
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
        $sql = 'UPDATE announcements SET title = :title, body = :body, status = :status,
                updated_by = :updated_by, updated_at = NOW() WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'title' => $data['title'],
            'body' => $data['body'],
            'status' => $data['status'],
            'updated_by' => $userId,
        ]);
    }

    /**
     * @param int $id
     */
    public function delete(int $id): void
    {
        $sql = 'DELETE FROM announcements WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
    }
}
