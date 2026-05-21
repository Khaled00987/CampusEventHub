<?php
/**
 * partials/form-errors.php — Display validation errors for a form.
 *
 * @var array<string, string> $errors
 */
if (!empty($errors)): ?>
    <div class="form-errors" role="alert">
        <ul>
            <?php foreach ($errors as $msg): ?>
                <li><?= e($msg) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif;
