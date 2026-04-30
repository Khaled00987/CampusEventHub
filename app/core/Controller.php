<?php
/**
 * Controller.php — Base controller for shared behaviour.
 *
 * Validates CSRF on POST requests, renders views, and provides helpers
 * for validation errors and JSON responses.
 */

declare(strict_types=1);

abstract class Controller
{
    /**
     * Enforce CSRF token on POST; abort with 403 if invalid.
     */
    protected function validateCsrf(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !CSRF::validate()) {
            http_response_code(403);
            Session::flash('error', 'Invalid security token. Please try again.');
            redirect('/');
        }
    }

    /**
     * Render a public page with header and footer layout.
     *
     * @param string $view
     * @param array<string, mixed> $data
     */
    protected function render(string $view, array $data = []): void
    {
        $data['_inner_view'] = $view;
        extract($data, EXTR_SKIP);
        require APP_PATH . '/views/layouts/header.php';
        require APP_PATH . '/views/' . str_replace('.', '/', $view) . '.php';
        require APP_PATH . '/views/layouts/footer.php';
    }

    /**
     * Render dashboard pages with sidebar layout.
     *
     * @param string $view
     * @param array<string, mixed> $data
     */
    protected function renderDashboard(string $view, array $data = []): void
    {
        $data['_inner_view'] = $view;
        extract($data, EXTR_SKIP);
        require APP_PATH . '/views/layouts/dashboard_layout.php';
    }

    /**
     * Redirect back with validation errors and old input.
     *
     * @param array<string, string> $errors
     * @param array<string, mixed> $input
     * @param string $path
     */
    protected function backWithErrors(array $errors, array $input, string $path): void
    {
        $_SESSION['_validation_errors'] = $errors;
        flash_old_input($input);
        redirect($path);
    }

    /**
     * @return array<string, string>
     */
    protected function getValidationErrors(): array
    {
        $errors = $_SESSION['_validation_errors'] ?? [];
        unset($_SESSION['_validation_errors']);
        return is_array($errors) ? $errors : [];
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function json(array $data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_THROW_ON_ERROR);
        exit;
    }
}
