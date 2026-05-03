<?php /** auth/register.php — Register with campus photo panel */ ?>
<div class="auth-split reveal">
    <div class="auth-split__visual" style="background-image: url('<?= e(campus_static_image('convocation')) ?>')">
        <div class="auth-split__visual-overlay">
            <h2>Join the community</h2>
            <p>Create your account to request tickets and stay updated on campus events.</p>
        </div>
    </div>
    <div class="auth-split__form">
        <div class="text-center" style="margin-bottom:1.5rem">
            <?php $logoHref = url('/'); require APP_PATH . '/views/partials/brand-logo.php'; ?>
        </div>
        <h1>Create account</h1>
        <p class="meta">Standard user registration. Admins are assigned by the system.</p>
        <?php require APP_PATH . '/views/partials/form-errors.php'; ?>
        <form method="post" action="<?= url('/register') ?>" class="form-card" data-validate="register" novalidate>
            <?= CSRF::field() ?>
            <div class="form-group">
                <label for="name">Full name <span class="required">*</span></label>
                <input type="text" id="name" name="name" required minlength="2" maxlength="100" value="<?= e(old('name')) ?>">
            </div>
            <div class="form-group">
                <label for="email">Email <span class="required">*</span></label>
                <input type="email" id="email" name="email" required value="<?= e(old('email')) ?>">
            </div>
            <div class="form-group">
                <label for="password">Password <span class="required">*</span></label>
                <input type="password" id="password" name="password" required minlength="8"
                       pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}" aria-describedby="pwd-hint">
                <small id="pwd-hint" class="hint">8+ characters with uppercase, lowercase, and a number.</small>
            </div>
            <div class="form-group">
                <label for="password_confirmation">Confirm password <span class="required">*</span></label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Register</button>
        </form>
        <p class="auth-footer"><a href="<?= url('/login') ?>">Already have an account?</a></p>
    </div>
</div>
