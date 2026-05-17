<?php
/**
 * AiLog.php — Logs AI feature usage for governance and human review tracking.
 *
 * Stores input, AI suggestion, whether admin accepted, and disclaimer flag.
 */

declare(strict_types=1);

class AiLog extends Model
{
    /**
     * @param int $userId
     * @param string $feature e.g. event_draft, help_assistant
     * @param string $inputText
     * @param string $suggestion
     * @return int Log ID
     */
    public function create(int $userId, string $feature, string $inputText, string $suggestion): int
    {
        $sql = 'INSERT INTO ai_logs (user_id, feature, input_text, ai_suggestion, accepted, disclaimer_shown, created_at)
                VALUES (:user_id, :feature, :input_text, :ai_suggestion, 0, 1, NOW())';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'feature' => $feature,
            'input_text' => $inputText,
            'ai_suggestion' => $suggestion,
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Mark suggestion as accepted with final edited text (human review).
     *
     * @param int $id
     * @param string $finalText
     */
    public function markAccepted(int $id, string $finalText): void
    {
        $sql = 'UPDATE ai_logs SET accepted = 1, final_text = :final_text WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id, 'final_text' => $finalText]);
    }

    /**
     * @param int $page
     * @return array{items: array<int, array>, total: int, page: int, per_page: int}
     */
    public function paginate(int $page = 1): array
    {
        $page = max(1, $page);
        $perPage = PER_PAGE;
        $offset = ($page - 1) * $perPage;

        $countStmt = $this->db->prepare('SELECT COUNT(*) FROM ai_logs');
        $countStmt->execute();
        $total = (int) $countStmt->fetchColumn();

        $sql = 'SELECT a.*, u.name AS user_name, u.email AS user_email
                FROM ai_logs a
                INNER JOIN users u ON u.id = a.user_id
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

    /**
     * @param int $id
     * @return array<string, mixed>|null
     */
    public function findById(int $id): ?array
    {
        $sql = 'SELECT * FROM ai_logs WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
