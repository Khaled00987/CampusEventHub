<?php
/** admin/ai-logs.php */
require_once APP_PATH . '/services/LongcatService.php';
?>

<div class="dash-ai-disclaimer"><?= e($disclaimer ?? AI_DISCLAIMER) ?></div>

<?php if (empty($logs['items'])): ?>
    <div class="dash-card dash-empty"><p>No AI interactions logged yet.</p></div>
<?php else: ?>
    <div class="dash-card dash-table-card">
        <div class="dash-table-wrap">
            <table class="dash-table dash-table--responsive">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>User</th>
                        <th>Feature</th>
                        <th>Input</th>
                        <th>Accepted</th>
                        <th>Final</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs['items'] as $log): ?>
                        <?php
                        $input = $log['input_text'];
                        $suggestion = $log['ai_suggestion'];
                        $final = $log['final_text'] ?? '—';
                        if ($log['feature'] === 'event_draft') {
                            $decoded = LongcatService::decodeDraftFromLog($suggestion);
                            $suggestion = $decoded ? ($decoded['title'] ?? 'draft') : $suggestion;
                            if (!empty($log['final_text'])) {
                                $fd = json_decode($log['final_text'], true);
                                $final = is_array($fd) ? ($fd['title'] ?? 'reviewed') : $log['final_text'];
                            }
                        }
                        ?>
                        <tr>
                            <td data-label="Date"><?= e(format_datetime($log['created_at'])) ?></td>
                            <td data-label="User"><?= e($log['user_name']) ?></td>
                            <td data-label="Feature"><span class="dash-badge dash-badge-muted"><?= e($log['feature']) ?></span></td>
                            <td data-label="Input"><?= e(substr((string) $input, 0, 80)) ?>…</td>
                            <td data-label="Accepted"><?= !empty($log['accepted']) ? '<span class="dash-badge dash-badge-success">Yes</span>' : '<span class="dash-badge dash-badge-muted">No</span>' ?></td>
                            <td data-label="Final"><?= e(is_string($final) ? substr($final, 0, 60) : '—') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php $pagination = $logs; $baseUrl = '/admin/ai-logs'; require APP_PATH . '/views/partials/pagination.php'; ?>
    </div>
<?php endif; ?>
