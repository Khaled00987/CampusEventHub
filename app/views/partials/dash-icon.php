<?php
/**
 * partials/dash-icon.php — Inline SVG icon for dashboard nav/KPI.
 * Expects: $dashIcon (grid|globe|calendar|ticket|megaphone|spark|list|activity|logout|help|events|news|ai|dot)
 */
$icon = $dashIcon ?? 'dot';
$class = $dashIconClass ?? 'dash-icon-svg';
?>
<svg class="<?= e($class) ?>" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
<?php if ($icon === 'grid'): ?>
    <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
<?php elseif ($icon === 'globe'): ?>
    <circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/>
<?php elseif ($icon === 'calendar'): ?>
    <rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/>
<?php elseif ($icon === 'ticket'): ?>
    <path d="M4 8a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v1.5a1.5 1.5 0 0 0 0 3V14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-1.5a1.5 1.5 0 0 0 0-3V8z"/><path d="M12 8v8"/>
<?php elseif ($icon === 'megaphone'): ?>
    <path d="M4 10v4h4l7 5V5L8 10H4z"/><path d="M16 8.5a4 4 0 0 1 0 7"/>
<?php elseif ($icon === 'spark'): ?>
    <path d="M12 3l1.8 5.5L19 10l-5.2 1.5L12 17l-1.8-5.5L5 10l5.2-1.5L12 3z"/><path d="M19 3v2M20 4h-2M5 19v2M6 20H4"/>
<?php elseif ($icon === 'list'): ?>
    <path d="M8 6h13M8 12h13M8 18h13"/><circle cx="4" cy="6" r="1" fill="currentColor"/><circle cx="4" cy="12" r="1" fill="currentColor"/><circle cx="4" cy="18" r="1" fill="currentColor"/>
<?php elseif ($icon === 'activity'): ?>
    <path d="M4 14l4-6 4 3 5-8 3 11"/>
<?php elseif ($icon === 'logout'): ?>
    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/>
<?php elseif ($icon === 'help'): ?>
    <circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.5 2.5 0 1 1 4.8 1c0 2-3 2-3 4"/><circle cx="12" cy="17" r="0.5" fill="currentColor"/>
<?php elseif ($icon === 'events'): ?>
    <path d="M8 2v4M16 2v4"/><rect x="3" y="6" width="18" height="15" rx="2"/><path d="M3 11h18"/>
<?php elseif ($icon === 'news'): ?>
    <path d="M6 4h12a2 2 0 0 1 2 2v14l-4-3-4 3-4-3-4 3V6a2 2 0 0 1 2-2z"/>
<?php elseif ($icon === 'ai'): ?>
    <path d="M12 2a4 4 0 0 1 4 4v1h1a3 3 0 0 1 0 6h-1v1a4 4 0 0 1-8 0v-1H7a3 3 0 0 1 0-6h1V6a4 4 0 0 1 4-4z"/>
<?php else: ?>
    <circle cx="12" cy="12" r="3"/>
<?php endif; ?>
</svg>
