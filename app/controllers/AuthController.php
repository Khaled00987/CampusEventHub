<?php
/**
 * AuthController.php — Login, register, and logout.
 *
 * Registration always creates standard users. Login enforces rate limiting,
 * blocked account checks, and session regeneration for security.
 */

declare(strict_types=1);

class AuthController extends Controller
{
    private User $users;
    private ActivityLog $activityLog;

    public function __construct()
    {
        $this->users = new User();
        $this->activityLog = new ActivityLog();
    }

    /** Show login form */
    public function showLogin(): void
    {
        Auth::redirectIfAuthenticated();
        $this->render('auth/login', [
            'pageTitle' => 'Login',
            'errors' => $this->getValidationErrors(),
        ]);
    }

    /** Process login POST */
    public function login(): void
    {
        Auth::redirectIfAuthenticated();
        $this->validateCsrf();

        if (Auth::isLoginLocked()) {
            Session::flash('error', 'Too many failed attempts. Try again in ' . Auth::loginLockMinutesRemaining() . ' minutes.');
            redirect('/login');
        }

        $v = new Validator($_POST);
        $v->required('email', 'Email');
        $v->email('email', 'Email');
        $v->required('password', 'Password');

        if ($v->fails()) {
            $this->backWithErrors($v->errors(), $_POST, '/login');
        }

        $user = $this->users->findByEmail($v->value('email'));

        if (!$user || !password_verify($v->value('password'), $user['password'])) {
            Auth::recordFailedLogin();
            $this->backWithErrors(['email' => 'Invalid email or password.'], $_POST, '/login');
        }

        if (($user['status'] ?? '') === 'blocked') {
            $this->backWithErrors(['email' => 'Your account has been blocked. Contact support.'], $_POST, '/login');
        }

        Auth::clearLoginAttempts();
        Auth::login($user);
        clear_old_input();

        $this->activityLog->log(
            'user_logged_in',
            'user',
            (int) $user['id'],
            'User logged in: ' . $user['email'],
            (int) $user['id']
        );

        Session::flash('success', 'Welcome back, ' . $user['name'] . '!');
        redirect('/dashboard');
    }

    /** Show registration form */
    public function showRegister(): void
    {
        Auth::redirectIfAuthenticated();
        $this->render('auth/register', [
            'pageTitle' => 'Register',
            'errors' => $this->getValidationErrors(),
        ]);
    }

    /** Process registration — standard user role only */
    public function register(): void
    {
        Auth::redirectIfAuthenticated();
        $this->validateCsrf();

        $v = new Validator($_POST);
        $v->required('name', 'Name');
        $v->length('name', 'Name', 2, 100);
        $v->required('email', 'Email');
        $v->email('email', 'Email');
        $v->required('password', 'Password');
        $v->passwordStrength('password', 'Password');
        $v->confirmed('password');

        if ($v->fails()) {
            $this->backWithErrors($v->errors(), $_POST, '/register');
        }

        if ($this->users->emailExists($v->value('email'))) {
            $this->backWithErrors(['email' => 'This email is already registered.'], $_POST, '/register');
        }

        $userId = $this->users->create($v->value('name'), $v->value('email'), $v->value('password'));

        $this->activityLog->log(
            'user_registered',
            'user',
            $userId,
            'New user registered: ' . $v->value('email'),
            $userId
        );

        Session::flash('success', 'Account created. Please log in.');
        redirect('/login');
    }

    /** Destroy session and redirect home */
    public function logout(): void
    {
        $user = Auth::user();
        if ($user) {
            $this->activityLog->log(
                'user_logged_out',
                'user',
                (int) $user['id'],
                'User logged out',
                (int) $user['id']
            );
        }
        Auth::logout();
        Session::flash('success', 'You have been logged out.');
        redirect('/');
    }
}
