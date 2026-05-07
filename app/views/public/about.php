<?php
/**
 * public/about.php — About Campus EventHub.
 */
$bannerTitle = 'About Us';
$bannerLead = 'Connecting students, organisers, and campus communities through thoughtful event management.';
$bannerImageKey = 'campus_union';
require APP_PATH . '/views/partials/page-banner.php';
?>

<div class="page-content-below-banner about-page">
    <section class="about-intro reveal">
        <p class="about-eyebrow">Our mission</p>
        <h2>One calm place for every campus event</h2>
        <p>
            Campus EventHub helps universities plan, publish, and manage events with clarity. Students discover
            what is happening on campus, request tickets in a few steps, and track approvals from a personal dashboard.
        </p>
        <p>
            Organisers use structured workflows for events, announcements, and ticket requests — with human-reviewed
            AI tools that support drafting without replacing professional judgment.
        </p>
    </section>

    <div class="about-grid">
        <article class="about-card reveal">
            <h3>For students</h3>
            <ul>
                <li>Browse published events with search and filters</li>
                <li>Request tickets and follow approval status</li>
                <li>Download approved ticket PDFs when ready</li>
                <li>Read official announcements in one feed</li>
            </ul>
        </article>
        <article class="about-card reveal">
            <h3>For organisers</h3>
            <ul>
                <li>Create and manage events with draft and published states</li>
                <li>Review ticket requests with capacity checks</li>
                <li>Publish campus news and updates</li>
                <li>Optional AI draft assistant with mandatory human review</li>
            </ul>
        </article>
        <article class="about-card reveal">
            <h3>Responsible by design</h3>
            <ul>
                <li>Secure login and role-based access</li>
                <li>Activity and AI audit logs for accountability</li>
                <li>No automatic publishing from AI suggestions</li>
                <li>Warm, accessible interface across devices</li>
            </ul>
        </article>
    </div>

    <section class="about-cta reveal">
        <div class="about-cta__inner">
            <h2>Explore what is on campus</h2>
            <p>Browse upcoming events or sign in to manage your tickets and dashboard.</p>
            <div class="btn-group">
                <a class="btn btn-primary" href="<?= url('/events') ?>">Browse events</a>
                <?php if (!Auth::check()): ?>
                    <a class="btn" href="<?= url('/register') ?>">Create account</a>
                <?php else: ?>
                    <a class="btn" href="<?= url('/dashboard') ?>">Go to dashboard</a>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>
