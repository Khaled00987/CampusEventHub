<?php /** auth/login.php — Sign in with campus photo panel */ ?>
<div class="auth-split reveal">
    <div class="auth-split__visual" style="background-image: url('<?= e(campus_static_image('reunion')) ?>')">
        <div class="auth-split__visual-overlay">
            <h2>Welcome back</h2>
            <p>Sign in to manage tickets, track approvals, and explore campus events.</p>
        </div>
    </div>
    <div class="auth-split__form">
        <div class="text-center" style="margin-bottom:1.5rem">
            <?php $logoHref = url('/'); require APP_PATH . '/views/partials/brand-logo.php'; ?>
        </div>
        <h1>Sign in</h1>
        <p class="meta">Access your dashboard and ticket requests.</p>
        <?php require APP_PATH . '/views/partials/form-errors.php'; ?>
        <form method="post" action="<?= url('/login') ?>" class="form-card" data-validate="login" novalidate>
            <?= CSRF::field() ?>
            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" id="email" name="email" required autocomplete="email" value="<?= e(old('email')) ?>">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn btn-primary btn-block">Sign in</button>
        </form>
        <p class="auth-footer">No account? <a href="<?= url('/register') ?>">Register</a></p>
    </div>
</div>
