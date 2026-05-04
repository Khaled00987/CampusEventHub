<?php
/**
 * public/index.php — Single front controller (entry point for all requests).
 *
 * Bootstraps config, session, autoloading by require, registers routes,
 * and dispatches to the appropriate controller action.
 */

declare(strict_types=1);

// Error handling: hide raw errors from users in production
error_reporting(E_ALL);
ini_set('display_errors', '0');

require_once dirname(__DIR__) . '/app/config/config.php';
require_once APP_PATH . '/config/database.php';
require_once APP_PATH . '/core/Session.php';
require_once APP_PATH . '/core/CSRF.php';
require_once APP_PATH . '/core/Helpers.php';
require_once APP_PATH . '/core/Validator.php';
require_once APP_PATH . '/core/Auth.php';
require_once APP_PATH . '/core/Model.php';
require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/core/Router.php';

// Load all models for convenience (MVC-lite without Composer autoload)
foreach (glob(APP_PATH . '/models/*.php') as $modelFile) {
    require_once $modelFile;
}

Session::start();

if (!Session::checkTimeout()) {
    Session::flash('error', 'Your session expired. Please log in again.');
}

if (APP_DEBUG) {
    ini_set('display_errors', '1');
}

set_exception_handler(static function (Throwable $e): void {
    if (APP_DEBUG) {
        throw $e;
    }
    http_response_code(500);
    require APP_PATH . '/views/errors/500.php';
    exit;
});

$router = new Router();

// ——— Public routes ———
$router->get('/', 'AdminController@home');
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@showRegister');
$router->post('/register', 'AuthController@register');
$router->get('/logout', 'AuthController@logout');
$router->get('/dashboard', 'DashboardController@index');

$router->get('/events', 'EventController@index');
$router->get('/events/{slug}', 'EventController@show');
$router->post('/events/{id}/request-ticket', 'EventController@requestTicket');
$router->get('/my-tickets', 'TicketController@myTickets');
$router->get('/tickets/{id}/download', 'TicketController@download');

$router->get('/announcements', 'AnnouncementController@index');
$router->get('/about', 'AdminController@about');

$router->get('/help-assistant', 'AiController@helpPage');
$router->post('/help-assistant/ask', 'AiController@helpAsk');

// ——— Admin events ———
$router->get('/admin/events', 'AdminController@eventIndex');
$router->get('/admin/events/create', 'AdminController@eventCreate');
$router->post('/admin/events/store', 'AdminController@eventStore');
$router->get('/admin/events/{id}', 'AdminController@eventShow');
$router->get('/admin/events/{id}/edit', 'AdminController@eventEdit');
$router->post('/admin/events/{id}/update', 'AdminController@eventUpdate');
$router->post('/admin/events/{id}/delete', 'AdminController@eventDelete');

// ——— Admin tickets ———
$router->get('/admin/tickets', 'TicketController@adminIndex');
$router->get('/admin/tickets/{id}', 'TicketController@adminShow');
$router->post('/admin/tickets/{id}/status', 'TicketController@updateStatus');

// ——— Admin announcements ———
$router->get('/admin/announcements', 'AnnouncementController@adminIndex');
$router->get('/admin/announcements/create', 'AnnouncementController@create');
$router->post('/admin/announcements/store', 'AnnouncementController@store');
$router->get('/admin/announcements/{id}/edit', 'AnnouncementController@edit');
$router->post('/admin/announcements/{id}/update', 'AnnouncementController@update');
$router->post('/admin/announcements/{id}/delete', 'AnnouncementController@delete');

// ——— Admin AI ———
$router->get('/admin/ai-draft', 'AiController@draftPage');
$router->post('/admin/ai-draft/generate', 'AiController@generateDraft');
$router->post('/admin/ai-draft/accept', 'AiController@acceptDraft');
$router->get('/admin/ai-logs', 'AiController@aiLogs');
$router->get('/admin/activity-logs', 'AdminController@activityLogs');

$router->dispatch();
