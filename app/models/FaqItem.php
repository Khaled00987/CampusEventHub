<?php
/**
 * FaqItem.php — FAQ knowledge base for the help assistant.
 *
 * Keyword search matches user questions to stored answers when AI
 * fallback or local FAQ mode is used.
 */

declare(strict_types=1);

class FaqItem extends Model
{
    /**
     * All active FAQ entries.
     *
     * @return array<int, array>
     */
    public function active(): array
    {
        $sql = "SELECT * FROM faq_items WHERE status = 'active' ORDER BY id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Simple keyword search for help assistant (prepared LIKE queries).
     *
     * @param string $question User question
     * @return array<string, mixed>|null Best matching FAQ or null
     */
    public function searchBestMatch(string $question): ?array
    {
        $words = preg_split('/\s+/', strtolower(trim($question)), -1, PREG_SPLIT_NO_EMPTY) ?? [];
        if ($words === []) {
            return null;
        }

        $items = $this->active();
        $best = null;
        $bestScore = 0;

        foreach ($items as $item) {
            $haystack = strtolower($item['question'] . ' ' . $item['answer'] . ' ' . $item['keywords']);
            $score = 0;
            foreach ($words as $word) {
                // Match words length 2+ (e.g. "do", "ai") for better FAQ hits on short questions
                if (strlen($word) >= 2 && str_contains($haystack, $word)) {
                    $score++;
                }
            }
            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $item;
            }
        }

        return $bestScore > 0 ? $best : null;
    }
}
