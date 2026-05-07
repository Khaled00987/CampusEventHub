<?php
/** layouts/footer.php — Professional footer with logo and scripts */
$layoutWide = $layoutWide ?? false;
?>
<?php if (!$layoutWide): ?></div><?php endif; ?>
</main>

<footer class="site-footer">
    <div class="container footer-inner">
        <div class="footer-brand">
            <?php $logoHref = url('/'); $logoClass = 'brand--footer'; require APP_PATH . '/views/partials/brand-logo.php'; ?>
            <p class="footer-tagline">Campus events, ticket requests, and responsible workflows.</p>
        </div>
        <div class="footer-columns">
            <nav class="footer-col" aria-label="Explore">
                <h4 class="footer-col__title">Explore</h4>
                <ul>
                    <li><a href="<?= url('/') ?>">Home</a></li>
                    <li><a href="<?= url('/events') ?>">Events</a></li>
                    <li><a href="<?= url('/announcements') ?>">Announcements</a></li>
                    <li><a href="<?= url('/about') ?>">About us</a></li>
                </ul>
            </nav>
            <nav class="footer-col" aria-label="Account">
                <h4 class="footer-col__title">Account</h4>
                <ul>
                    <?php if (Auth::check()): ?>
                        <li><a href="<?= url('/dashboard') ?>">Dashboard</a></li>
                        <?php if (!Auth::isAdmin()): ?>
                            <li><a href="<?= url('/my-tickets') ?>">My tickets</a></li>
                        <?php endif; ?>
                        <li><a href="<?= url('/logout') ?>">Logout</a></li>
                    <?php else: ?>
                        <li><a href="<?= url('/login') ?>">Login</a></li>
                        <li><a href="<?= url('/register') ?>">Register</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            <nav class="footer-col" aria-label="Support">
                <h4 class="footer-col__title">Support</h4>
                <ul>
                    <li><a href="<?= url('/help-assistant') ?>">Help assistant</a></li>
                    <li><a href="<?= url('/events') ?>">Browse events</a></li>
                    <li><a href="<?= url('/about') ?>">About us</a></li>
                </ul>
            </nav>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <p>&copy; <?= date('Y') ?> <?= e(APP_NAME) ?>. All rights reserved.</p>
        </div>
    </div>
</footer>

<script src="<?= asset('js/main.js') ?>"></script>
<script src="<?= asset('js/validation.js') ?>"></script>
<?php if (!empty($loadLandingJs)): ?>
<script src="<?= asset('js/landing.js') ?>"></script>
<?php endif; ?>
<?php if (!empty($loadAiJs)): ?>
<script src="<?= asset('js/ai.js') ?>"></script>
<?php endif; ?>
</body>
</html>
