<?php
/**
 * public/home.php — Professional landing page.
 * Each campus_static_image() key is used at most once (no duplicate photos).
 * Most sections use cards, typography, and background bands — not image grids.
 */
$stats = $homeStats ?? ['upcoming_events' => 0, 'ticket_requests' => 0, 'community_members' => 0];
$disclaimer = $disclaimer ?? 'AI-generated content requires human review before saving or publishing.';
?>

<!-- 1. Hero — EVENT-MANAGEMENT bg + text left + hero-right.jpg on right -->
<section class="home-hero home-hero--load" style="background-image: url('<?= e(campus_static_image('hero')) ?>')">
    <div class="home-hero__overlay" aria-hidden="true"></div>
    <div class="container home-hero__wrap">
        <div class="home-hero__grid">
            <div class="home-hero__copy">
                <span class="home-eyebrow reveal" data-reveal-delay="0">Campus event management</span>
                <h1 class="reveal" data-reveal-delay="1">Plan, discover, and manage every campus moment</h1>
                <p class="home-hero__lead reveal" data-reveal-delay="2">
                    <?= e(APP_NAME) ?> unifies events, ticket requests, announcements, and human-reviewed AI tools
                    in one secure platform for students and organisers.
                </p>
                <div class="home-hero__actions reveal" data-reveal-delay="3">
                    <a class="btn btn-primary" href="<?= url('/events') ?>">Browse events</a>
                    <?php if (Auth::check()): ?>
                        <a class="btn btn-ghost" href="<?= url('/dashboard') ?>">Dashboard</a>
                    <?php else: ?>
                        <a class="btn btn-ghost" href="<?= url('/login') ?>">Sign in</a>
                        <a class="btn btn-accent" href="<?= url('/register') ?>">Register</a>
                    <?php endif; ?>
                </div>
                <div class="home-stats reveal" data-reveal-delay="4">
                    <div class="home-stat-card">
                        <strong data-count="<?= e((string) $stats['upcoming_events']) ?>">0</strong>
                        <span>Upcoming events</span>
                    </div>
                    <div class="home-stat-card">
                        <strong data-count="<?= e((string) $stats['ticket_requests']) ?>">0</strong>
                        <span>Ticket requests</span>
                    </div>
                    <div class="home-stat-card">
                        <strong data-count="<?= e((string) $stats['community_members']) ?>">0</strong>
                        <span>Community members</span>
                    </div>
                </div>
            </div>
            <figure class="home-hero__media reveal" data-reveal-delay="2">
                <img
                    src="<?= e(campus_static_image('hero_right')) ?>"
                    alt="Students and organisers at a campus event"
                    width="560"
                    height="420"
                    loading="eager"
                    decoding="async"
                >
            </figure>
        </div>
    </div>
</section>

<!-- 2. Trust — icon cards, no images -->
<section class="home-section home-pillars" aria-label="Platform pillars">
    <div class="container">
        <header class="home-section__head reveal">
            <h2>Built for real campus workflows</h2>
            <p>Clear roles, audited actions, and a calm interface students can trust.</p>
        </header>
        <p class="mobile-swipe-hint">Swipe to view more</p>
        <div class="mobile-carousel">
            <div class="mobile-carousel-track home-pillars__grid">
            <article class="home-pillar-card mobile-carousel-card reveal" data-reveal-delay="0">
                <span class="home-pillar-card__icon" aria-hidden="true">1</span>
                <h3>Secure access</h3>
                <p>Role-based dashboards, CSRF protection, and session safety for every account.</p>
            </article>
            <article class="home-pillar-card mobile-carousel-card reveal" data-reveal-delay="1">
                <span class="home-pillar-card__icon" aria-hidden="true">2</span>
                <h3>Ticket workflow</h3>
                <p>Request, approve, and download PDF tickets with capacity checks built in.</p>
            </article>
            <article class="home-pillar-card mobile-carousel-card reveal" data-reveal-delay="2">
                <span class="home-pillar-card__icon" aria-hidden="true">3</span>
                <h3>Announcements</h3>
                <p>One official channel for campus news — no more scattered social posts.</p>
            </article>
            <article class="home-pillar-card mobile-carousel-card reveal" data-reveal-delay="3">
                <span class="home-pillar-card__icon" aria-hidden="true">4</span>
                <h3>Human-reviewed AI</h3>
                <p>AI assists drafts and help answers; nothing publishes without admin review.</p>
            </article>
            </div>
        </div>
    </div>
</section>

<!-- 3. About — one inline image (reunion) -->
<section class="home-section home-about">
    <div class="container home-about__layout">
        <div class="home-about__copy reveal">
            <span class="home-eyebrow home-eyebrow--dark">Why <?= e(APP_NAME) ?>?</span>
            <h2>One organised home for campus life</h2>
            <p>Stop juggling spreadsheets and inboxes. Publish events once, let students request tickets transparently, and keep everyone aligned with published announcements.</p>
            <ul class="home-checklist">
                <li>Published events catalogue with search and filters</li>
                <li>Transparent ticket status in My Tickets</li>
                <li>Admin audit trail for accountability</li>
            </ul>
            <a class="btn btn-primary" href="<?= url('/events') ?>">Explore the calendar</a>
        </div>
        <figure class="home-about__figure reveal" data-reveal-delay="1">
            <img src="<?= e(campus_static_image('reunion')) ?>" alt="Students at a campus reunion event" width="560" height="420" loading="lazy">
        </figure>
    </div>
</section>

<!-- 4. Capabilities — six text cards, no photos -->
<section class="home-section home-capabilities">
    <div class="container">
        <header class="home-section__head home-section__head--left reveal">
            <span class="home-eyebrow home-eyebrow--dark">Platform capabilities</span>
            <h2>Everything you need to run events professionally</h2>
        </header>
        <p class="mobile-swipe-hint">Swipe to view more</p>
        <div class="mobile-carousel">
            <div class="mobile-carousel-track home-cap-grid">
            <article class="home-cap-card mobile-carousel-card reveal" data-reveal-delay="0">
                <span class="home-cap-card__num">01</span>
                <h3>Event discovery</h3>
                <p>Responsive listings with categories, dates, and detail pages optimised for mobile.</p>
            </article>
            <article class="home-cap-card mobile-carousel-card reveal" data-reveal-delay="1">
                <span class="home-cap-card__num">02</span>
                <h3>Ticket requests</h3>
                <p>Students submit attendee details; admins approve within capacity limits.</p>
            </article>
            <article class="home-cap-card mobile-carousel-card reveal" data-reveal-delay="2">
                <span class="home-cap-card__num">03</span>
                <h3>PDF tickets</h3>
                <p>Approved requests generate a downloadable ticket with a unique code.</p>
            </article>
            <article class="home-cap-card mobile-carousel-card reveal" data-reveal-delay="0">
                <span class="home-cap-card__num">04</span>
                <h3>Image uploads</h3>
                <p>Admins upload event photos safely — validated, resized paths, smart fallbacks.</p>
            </article>
            <article class="home-cap-card mobile-carousel-card reveal" data-reveal-delay="1">
                <span class="home-cap-card__num">05</span>
                <h3>Admin controls</h3>
                <p>Manage events, tickets, announcements, and view activity plus AI logs.</p>
            </article>
            <article class="home-cap-card mobile-carousel-card reveal" data-reveal-delay="2">
                <span class="home-cap-card__num">06</span>
                <h3>Help assistant</h3>
                <p>FAQ-first answers about using the system — optional AI polish when configured.</p>
            </article>
            </div>
        </div>
    </div>
</section>

<!-- 5. Background band — campus_union (used once) -->
<section class="home-band reveal" style="background-image: url('<?= e(campus_static_image('campus_union')) ?>')">
    <div class="home-band__overlay" aria-hidden="true"></div>
    <div class="container home-band__content">
        <h2>From welcome week to graduation</h2>
        <p>Support every scale of gathering — society meetups, faculty talks, and large union events — with the same reliable tooling.</p>
    </div>
</section>

<!-- 6. Spotlight — one image (convocation) -->
<section class="home-section home-spotlight">
    <div class="container home-spotlight__layout">
        <figure class="home-spotlight__media reveal">
            <img src="<?= e(campus_static_image('convocation')) ?>" alt="Campus convocation ceremony" width="520" height="390" loading="lazy">
        </figure>
        <div class="home-spotlight__copy reveal" data-reveal-delay="1">
            <span class="home-eyebrow home-eyebrow--dark">For organisers</span>
            <h2>Publish with confidence</h2>
            <p>Create draft or published events, upload a cover image, and track ticket demand before doors open.</p>
            <div class="home-mini-cards">
                <div class="home-mini-card">
                    <strong>Capacity-aware</strong>
                    <span>Approvals respect seat limits automatically.</span>
                </div>
                <div class="home-mini-card">
                    <strong>Status notes</strong>
                    <span>Students see admin feedback on every request.</span>
                </div>
            </div>
            <?php if (Auth::check() && Auth::isAdmin()): ?>
                <a class="btn btn-primary" href="<?= url('/admin/events/create') ?>">Create an event</a>
            <?php else: ?>
                <a class="btn btn-primary" href="<?= url('/events') ?>">View published events</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- 7. Featured events (dynamic event images only) -->
<section class="home-section home-events">
    <div class="container">
        <header class="home-section__head reveal">
            <h2>Featured upcoming events</h2>
            <p>Live listings from your campus database.</p>
        </header>
        <?php if (empty($featuredEvents)): ?>
            <div class="home-empty reveal">
                <p>No upcoming events right now.</p>
                <a class="btn btn-primary" href="<?= url('/events') ?>">View all events</a>
            </div>
        <?php else: ?>
            <p class="mobile-swipe-hint">Swipe to view more</p>
            <div class="mobile-carousel">
            <div class="mobile-carousel-track home-events__grid">
                <?php foreach ($featuredEvents as $i => $event): ?>
                    <article class="home-event-card mobile-carousel-card reveal" data-reveal-delay="<?= e((string) ($i % 3)) ?>">
                        <img src="<?= e(event_image($event['image'] ?? null)) ?>" alt="<?= e($event['title'] ?? 'Event') ?>">
                        <div class="home-event-card__body">
                            <span class="badge"><?= e($event['category']) ?></span>
                            <h3><a href="<?= url('/events/' . $event['slug']) ?>"><?= e($event['title']) ?></a></h3>
                            <p class="meta"><?= e(format_date($event['event_date'])) ?> · <?= e($event['location']) ?></p>
                            <a class="btn btn-sm" href="<?= url('/events/' . $event['slug']) ?>">Details</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            </div>
            <p class="home-events__more reveal">
                <a class="btn" href="<?= url('/events') ?>">See full catalogue</a>
            </p>
        <?php endif; ?>
    </div>
</section>

<!-- 8. Background band — campus_55149 (used once) -->
<section class="home-band home-band--compact reveal" style="background-image: url('<?= e(campus_static_image('campus_55149')) ?>')">
    <div class="home-band__overlay home-band__overlay--deep" aria-hidden="true"></div>
    <div class="container home-band__content home-band__content--row">
        <div>
            <h2>Students stay in the loop</h2>
            <p>Announcements and ticket status updates reduce confusion before event day.</p>
        </div>
        <a class="btn btn-accent" href="<?= url('/announcements') ?>">Read announcements</a>
    </div>
</section>

<!-- 9. How it works — step cards -->
<section class="home-section home-process">
    <div class="container">
        <header class="home-section__head reveal">
            <h2>How it works</h2>
            <p>Four steps from discovery to attending.</p>
        </header>
        <p class="mobile-swipe-hint">Swipe to view more</p>
        <div class="mobile-carousel">
        <ol class="mobile-carousel-track home-process__list">
            <li class="home-process-card mobile-carousel-card reveal" data-reveal-delay="0">
                <span class="home-process-card__step">1</span>
                <h3>Browse</h3>
                <p>Find published events with search and filters.</p>
            </li>
            <li class="home-process-card mobile-carousel-card reveal" data-reveal-delay="1">
                <span class="home-process-card__step">2</span>
                <h3>Request</h3>
                <p>Sign in and submit ticket details online.</p>
            </li>
            <li class="home-process-card mobile-carousel-card reveal" data-reveal-delay="2">
                <span class="home-process-card__step">3</span>
                <h3>Review</h3>
                <p>Admins approve or reject within capacity.</p>
            </li>
            <li class="home-process-card mobile-carousel-card reveal" data-reveal-delay="3">
                <span class="home-process-card__step">4</span>
                <h3>Attend</h3>
                <p>Download your PDF ticket when approved.</p>
            </li>
        </ol>
        </div>
    </div>
</section>

<!-- 10. AI — background only (organizer_team), no duplicate inline image -->
<section class="home-band home-band--ai reveal" style="background-image: url('<?= e(campus_static_image('organizer_team')) ?>')">
    <div class="home-band__overlay home-band__overlay--ai" aria-hidden="true"></div>
    <div class="container home-band__content home-band__content--ai">
        <span class="home-eyebrow">Responsible AI</span>
        <h2>Human decisions, always</h2>
        <p>AI can draft event copy and answer system questions from curated FAQs. Every output is logged and must be reviewed before saving or publishing. Ticket personal data never leaves the platform for AI processing.</p>
        <div class="ai-disclaimer ai-disclaimer--on-dark" role="note"><?= e($disclaimer) ?></div>
        <div class="home-band__actions">
            <a class="btn btn-primary" href="<?= url('/help-assistant') ?>">Help assistant</a>
            <?php if (Auth::check() && Auth::isAdmin()): ?>
                <a class="btn btn-ghost" href="<?= url('/admin/ai-draft') ?>">AI draft (admin)</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- 11. Testimonials — typography cards only -->
<section class="home-section home-quotes">
    <div class="container">
        <header class="home-section__head reveal">
            <h2>Trusted on campus</h2>
        </header>
        <p class="mobile-swipe-hint">Swipe to view more</p>
        <div class="mobile-carousel">
        <div class="mobile-carousel-track home-quotes__grid">
            <blockquote class="home-quote-card mobile-carousel-card reveal" data-reveal-delay="0">
                <p>“One portal for events and tickets saved our society hours each term.”</p>
                <footer>Society organiser</footer>
            </blockquote>
            <blockquote class="home-quote-card mobile-carousel-card reveal" data-reveal-delay="1">
                <p>“Students always know where to check request status — far clearer than email.”</p>
                <footer>Events office</footer>
            </blockquote>
            <blockquote class="home-quote-card mobile-carousel-card reveal" data-reveal-delay="2">
                <p>“AI drafts are a useful start, but we approve every detail ourselves.”</p>
                <footer>Club administrator</footer>
            </blockquote>
        </div>
        </div>
    </div>
</section>

<!-- 12. Campus moments — three unique thumbnails (71942, 71939, 67315) -->
<section class="home-section home-moments">
    <div class="container">
        <header class="home-section__head reveal">
            <span class="home-eyebrow home-eyebrow--dark">On campus</span>
            <h2>Moments that bring communities together</h2>
        </header>
        <p class="mobile-swipe-hint">Swipe to view more</p>
        <div class="mobile-carousel">
        <div class="mobile-carousel-track home-moments__row">
            <figure class="home-moment mobile-carousel-card reveal" data-reveal-delay="0">
                <img src="<?= e(campus_static_image('campus_71942')) ?>" alt="Campus event atmosphere" loading="lazy">
            </figure>
            <figure class="home-moment mobile-carousel-card reveal" data-reveal-delay="1">
                <img src="<?= e(campus_static_image('campus_71939')) ?>" alt="Students at a campus gathering" loading="lazy">
            </figure>
            <figure class="home-moment mobile-carousel-card reveal" data-reveal-delay="2">
                <img src="<?= e(campus_static_image('campus_67315')) ?>" alt="Campus community event" loading="lazy">
            </figure>
        </div>
        </div>
    </div>
</section>

<!-- 13. Spotlight 2 — one image (residence_halls), right-aligned layout -->
<section class="home-section home-spotlight home-spotlight--alt">
    <div class="container home-spotlight__layout home-spotlight__layout--reverse">
        <div class="home-spotlight__copy reveal">
            <span class="home-eyebrow home-eyebrow--dark">Residence &amp; halls</span>
            <h2>Engage every corner of campus</h2>
            <p>Hall events, union programmes, and faculty open days all belong in the same calendar students already use.</p>
            <a class="btn" href="<?= url('/register') ?>">Join as a student</a>
        </div>
        <figure class="home-spotlight__media reveal" data-reveal-delay="1">
            <img src="<?= e(campus_static_image('residence_halls')) ?>" alt="Residence halls community" width="520" height="390" loading="lazy">
        </figure>
    </div>
</section>

<!-- 14. Background band — organizer_jakarta (used once) -->
<section class="home-band reveal" style="background-image: url('<?= e(campus_static_image('organizer_jakarta')) ?>')">
    <div class="home-band__overlay" aria-hidden="true"></div>
    <div class="container home-band__content">
        <h2>Ready for your next event?</h2>
        <p>Browse what is on campus and request your place in a few clicks.</p>
        <a class="btn btn-accent" href="<?= url('/events') ?>">Explore events</a>
    </div>
</section>

<!-- 15. Final CTA — background events_news (used once) -->
<section class="home-band home-band--final reveal" style="background-image: url('<?= e(campus_static_image('events_news')) ?>')">
    <div class="home-band__overlay home-band__overlay--deep" aria-hidden="true"></div>
    <div class="container home-band__content">
        <h2>Start with <?= e(APP_NAME) ?> today</h2>
        <p>Professional event management for your campus — secure, clear, and ready on XAMPP or live hosting.</p>
        <div class="home-band__actions">
            <a class="btn btn-accent" href="<?= url('/events') ?>">View events</a>
            <a class="btn btn-ghost" href="<?= url('/register') ?>">Create account</a>
            <a class="btn btn-ghost" href="<?= url('/help-assistant') ?>">Get help</a>
        </div>
    </div>
</section>
