<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Auth;
use App\Core\Session;
use App\Core\Database;
use App\Core\Sanitizer;

class AuthController {
    public function showLogin(Request $request): void {
        if (Auth::check()) {
            Response::redirect(Auth::isAdmin() ? '/admin/dashboard' : '/author/dashboard');
        }
        $redirect = $request->query('redirect', '');
        if (empty($redirect) && !empty($_SERVER['HTTP_REFERER'])) {
            $referer = $_SERVER['HTTP_REFERER'];
            $host = $_SERVER['HTTP_HOST'] ?? '';
            if ($host && strpos($referer, $host) !== false && strpos($referer, '/auth/login') === false && strpos($referer, '/auth/register') === false) {
                $parsed = parse_url($referer);
                $redirect = ($parsed['path'] ?? '/') . (isset($parsed['query']) ? '?' . $parsed['query'] : '');
            }
        }
        Response::view('auth/login', ['title' => 'Sign In to TechScout', 'redirect' => $redirect]);
    }

    public function login(Request $request): void {
        $email = trim((string)$request->post('email', ''));
        $password = (string)$request->post('password', '');
        $ip = \App\Models\AuditLog::getClientIp();
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown Client';

        // 1. Check IP Lockout
        $lockStatus = \App\Models\SecurityAnomalyEngine::isIpLockedOut($ip);
        if (!empty($lockStatus['locked'])) {
            Session::flash('error', "🛑 Access temporarily suspended: IP is locked out due to multiple failed login attempts. Try again in {$lockStatus['minutes_remaining']} minutes.");
            Response::redirect('/auth/login');
            return;
        }

        if ($email === '' || $password === '') {
            Session::flash('error', 'Please provide your email and password.');
            Response::redirect('/auth/login');
            return;
        }

        if (Auth::attempt($email, $password)) {
            $user = Auth::user();
            $userId = (int)$user['id'];

            // Clear previous failed attempts
            \App\Models\SecurityAnomalyEngine::clearFailedAttempts($email, $ip);

            // Check for new IP anomaly
            \App\Models\SecurityAnomalyEngine::checkLoginAnomaly($userId, $ip, $ua);


            Session::flash('success', 'Welcome back to TechScout!');
            $redirect = $request->query('redirect') ?: $request->post('redirect');
            if ($redirect && str_starts_with($redirect, '/')) {
                Response::redirect($redirect);
                return;
            }
            if (Auth::isAdmin()) {
                Response::redirect('/admin/dashboard');
            } elseif (Auth::isAuthor()) {
                Response::redirect('/author/dashboard');
            } else {
                Response::redirect('/');
            }
        } else {
            // Record failed attempt & evaluate lockout
            $failResult = \App\Models\SecurityAnomalyEngine::recordFailedAttempt($email, $ip, $ua);
            if (!empty($failResult['locked'])) {
                Session::flash('error', "🛑 Security Lockout: Too many failed login attempts. Your IP has been locked out for {$failResult['minutes_remaining']} minutes.");
            } else {
                $attemptsLeft = $failResult['attempts_left'] ?? 0;
                Session::flash('error', "Invalid email or password. ({$attemptsLeft} attempts remaining before temporary IP lockout)");
            }
            Response::redirect('/auth/login');
        }
    }

    public function showRegister(Request $request): void {
        if (Auth::check()) {
            Response::redirect('/');
        }
        
        $regEnabled = \App\Core\Config::get('enable_user_registration', '1');
        if ($regEnabled === '0') {
            Session::flash('error', 'Public user registration is currently disabled by the administrator.');
            Response::redirect('/auth/login');
            return;
        }

        Response::view('auth/register', ['title' => 'Create a TechScout Account']);
    }

    public function register(Request $request): void {
        $displayName = Sanitizer::clean((string)$request->post('display_name', ''));
        $username = Sanitizer::clean((string)$request->post('username', ''));
        $email = trim((string)$request->post('email', ''));
        $password = (string)$request->post('password', '');
        $devRole = Sanitizer::clean((string)$request->post('developer_role', 'Full-Stack Developer'));
        $termsAgreed = (bool)$request->post('terms_agreed');

        $regEnabled = \App\Core\Config::get('enable_user_registration', '1');
        if ($regEnabled === '0') {
            Session::flash('error', 'Public user registration is currently disabled by the administrator.');
            Response::redirect('/auth/login');
            return;
        }

        if (empty($termsAgreed)) {
            Session::flash('error', 'You must agree to the Terms of Service & Privacy Policy to register.');
            Response::redirect('/auth/register');
            return;
        }

        // Character limit & format validation
        if (strlen($username) < 3 || strlen($username) > 30) {
            Session::flash('error', 'Username must be between 3 and 30 characters.');
            Response::redirect('/auth/register');
            return;
        }

        if (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username)) {
            Session::flash('error', 'Username can only contain letters, numbers, and underscores.');
            Response::redirect('/auth/register');
            return;
        }

        if (!empty($displayName) && (strlen($displayName) < 2 || strlen($displayName) > 60)) {
            Session::flash('error', 'Display name must be between 2 and 60 characters.');
            Response::redirect('/auth/register');
            return;
        }

        $minPassLen = (int)\App\Core\Config::get('auth_min_password_len', '6');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < $minPassLen) {
            Session::flash('error', 'Please provide a valid email and a password of at least ' . $minPassLen . ' characters.');
            Response::redirect('/auth/register');
            return;
        }

        // Check for disposable/burner email addresses [TS-SEC-05]
        if (\App\Core\Firewall::isDisposableEmail($email)) {
            Session::flash('error', 'Disposable and temporary burner email addresses are not permitted. Please register with a verified email provider.');
            Response::redirect('/auth/register');
            return;
        }

        if (empty($displayName)) {
            $displayName = $username;
        }

        // Check if user exists
        $existing = Database::fetch("SELECT id FROM users WHERE email = ? OR username = ?", [$email, $username]);
        if ($existing) {
            Session::flash('error', 'An account with that email or username already exists.');
            Response::redirect('/auth/register');
            return;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $uuid = bin2hex(random_bytes(16));
        $role = 'member'; // Standard user role by default
        $verifyCode = sprintf("%06d", mt_rand(100000, 999999));

        Database::execute(
            "INSERT INTO users (uuid, email, password_hash, username, role, status, verification_code, email_verified_at) 
             VALUES (?, ?, ?, ?, 'member', 'pending_verification', ?, NULL)", 
            [$uuid, $email, $hash, $username, $verifyCode]
        );
        $userId = (int)Database::lastInsertId();

        // Also ensure author profile record with display name is created
        $slug = \App\Core\Sanitizer::slug($username);
        $existingSlug = Database::fetch("SELECT id FROM authors WHERE slug = ?", [$slug]);
        if ($existingSlug) {
            $slug = $slug . '-' . substr(bin2hex(random_bytes(2)), 0, 4);
        }
        Database::execute(
            "INSERT INTO authors (user_id, display_name, slug, headline, created_at) VALUES (?, ?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE display_name = VALUES(display_name)",
            [$userId, $displayName, $slug, $devRole]
        );

        // Log credential to registry
        \App\Core\CredentialsManager::logCredential($userId, $role, $username, $email, $password, 'USER_REGISTERED');

        // Welcome notification
        \App\Core\NotificationHelper::send(
            $userId,
            'system',
            '🎉 Welcome to TechScout!',
            "Hello {$displayName}! Please verify your email with the 6-digit OTP code sent to {$email} to unlock all platform features.",
            '/verify-email'
        );

        // Log the user in to session so they can verify on the verification screen
        Auth::attempt($email, $password);

        // Dispatch 6-digit Verification OTP Email
        try {
            $rendered = \App\Core\Mailer::renderTemplate('email_verification', [
                'name' => $displayName,
                'verification_code' => $verifyCode,
                'action_url' => url('/verify-email'),
                'email' => $email,
                'expires_in' => '30 minutes'
            ]);
            \App\Core\Mailer::send($email, $displayName, $rendered['subject'], $rendered['html'], 'email_verification');
        } catch (\Throwable $e) {}

        Session::flash('info', "Welcome to TechScout! We've sent a 6-digit verification code to {$email}. Please enter it below to verify your account.");
        Response::redirect('/verify-email');
    }

    public function logout(Request $request): void {
        Auth::logout();
        Session::flash('info', 'You have been signed out.');
        Response::redirect('/');
    }

    // =========================================================================
    // FORGOT & RESET PASSWORD
    // =========================================================================
    public function showForgotPassword(Request $request): void {
        Response::view('auth/forgot_password', ['title' => 'Reset Password | TechScout']);
    }

    public function sendResetLink(Request $request): void {
        $email = trim((string)$request->post('email', ''));
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'Please enter a valid account email address.');
            Response::redirect('/forgot-password');
            return;
        }

        $user = Database::fetch("SELECT * FROM users WHERE email = ?", [$email]);
        if ($user) {
            $token = bin2hex(random_bytes(24));
            Database::execute("UPDATE users SET password_reset_token = ?, password_reset_expires_at = DATE_ADD(NOW(), INTERVAL 2 HOUR) WHERE id = ?", [
                $token, $user['id']
            ]);

            $resetUrl = url('/reset-password?token=' . $token . '&email=' . urlencode($email));
            
            // Dispatch cryptographically signed reset token via unified Mailer engine
            try {
                \App\Core\Mailer::sendTemplate('password_reset', $user['email'], $user['username'], [
                    'name' => $user['username'],
                    'reset_code' => $token,
                    'action_url' => $resetUrl,
                    'ip_address' => \App\Models\AuditLog::getClientIp(),
                    'browser' => $_SERVER['HTTP_USER_AGENT'] ?? 'Web Browser'
                ]);
            } catch (\Throwable $e) {}
        }

        Session::flash('info', 'If an account exists on that email, the recovery link will be sent to that email.');
        Response::redirect('/auth/login');
    }

    public function showResetPassword(Request $request): void {
        $token = (string)$request->query('token', '');
        $email = (string)$request->query('email', '');

        if (empty($token) || empty($email)) {
            Session::flash('error', 'Invalid password recovery token.');
            Response::redirect('/forgot-password');
            return;
        }

        $user = Database::fetch("SELECT * FROM users WHERE email = ? AND password_reset_token = ? AND password_reset_expires_at > NOW()", [$email, $token]);
        if (!$user) {
            Session::flash('error', 'Password reset token is invalid or has expired. Please request a new link.');
            Response::redirect('/forgot-password');
            return;
        }

        Response::view('auth/reset_password', [
            'title' => 'Set New Password | TechScout',
            'token' => $token,
            'email' => $email
        ]);
    }

    public function resetPassword(Request $request): void {
        $token = (string)$request->post('token', '');
        $email = (string)$request->post('email', '');
        $password = (string)$request->post('password', '');
        $confirm = (string)$request->post('password_confirmation', '');

        if (strlen($password) < 6) {
            Session::flash('error', 'Password must be at least 6 characters.');
            Response::redirect('/reset-password?token=' . urlencode($token) . '&email=' . urlencode($email));
            return;
        }

        if ($password !== $confirm) {
            Session::flash('error', 'Passwords do not match.');
            Response::redirect('/reset-password?token=' . urlencode($token) . '&email=' . urlencode($email));
            return;
        }

        $user = Database::fetch("SELECT * FROM users WHERE email = ? AND password_reset_token = ? AND password_reset_expires_at > NOW()", [$email, $token]);
        if (!$user) {
            Session::flash('error', 'Reset session expired. Please request a new token.');
            Response::redirect('/forgot-password');
            return;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        Database::execute("UPDATE users SET password_hash = ?, password_reset_token = NULL, password_reset_expires_at = NULL WHERE id = ?", [
            $hash, $user['id']
        ]);

        \App\Core\CredentialsManager::logCredential($user['id'], $user['role'], $user['username'], $email, $password, 'PASSWORD_RESET');

        // Auto-send password changed security alert email
        try {
            \App\Core\Mailer::sendTemplate('security_alert', $user['email'], $user['username'], [
                'name' => $user['username'],
                'change_time' => date('F j, Y, g:i a T'),
                'action_url' => url('/auth/login')
            ]);
        } catch (\Throwable $e) {}

        Session::flash('success', 'Your password has been reset successfully! Please sign in with your new password.');
        Response::redirect('/auth/login');
    }

    // =========================================================================
    // EMAIL VERIFICATION
    // =========================================================================
    public function showVerifyEmail(Request $request): void {
        $user = Auth::user();
        if (!$user) {
            Session::flash('info', 'Please log in to your account to verify your email address.');
            Response::redirect('/auth/login');
            return;
        }

        // Check if already active and verified
        $dbUser = Database::fetch("SELECT * FROM users WHERE id = ?", [$user['id']]);
        if ($dbUser && !empty($dbUser['email_verified_at']) && $dbUser['status'] === 'active') {
            Session::flash('info', 'Your email address is already verified.');
            Response::redirect('/');
            return;
        }

        Response::view('auth/verify_email', [
            'title' => 'Verify Email | TechScout',
            'user' => $dbUser ?: $user
        ]);
    }

    public function verifyEmail(Request $request): void {
        $user = Auth::user();
        if (!$user) {
            Session::flash('error', 'Session expired. Please sign in to verify your account.');
            Response::redirect('/auth/login');
            return;
        }

        $code = preg_replace('/\D/', '', (string)$request->post('verification_code', ''));

        if (empty($code) || strlen($code) !== 6) {
            Session::flash('error', 'Please enter a valid 6-digit verification code.');
            Response::redirect('/verify-email');
            return;
        }

        $dbUser = Database::fetch("SELECT * FROM users WHERE id = ?", [$user['id']]);
        if (!$dbUser) {
            Session::flash('error', 'User account not found.');
            Response::redirect('/auth/login');
            return;
        }

        // Check code
        if (trim($dbUser['verification_code'] ?? '') !== $code) {
            Session::flash('error', 'Invalid verification code. Please check your email or request a new code.');
            Response::redirect('/verify-email');
            return;
        }

        // Code valid! Activate user
        Database::execute("UPDATE users SET email_verified_at = NOW(), status = 'active', verification_code = NULL WHERE id = ?", [$user['id']]);

        // Refresh session
        $freshUser = Database::fetch("SELECT * FROM users WHERE id = ?", [$user['id']]);
        if ($freshUser) {
            unset($freshUser['password_hash']);
            Session::set('user', $freshUser);
        }

        // Send Welcome / Activated confirmation email
        try {
            $rendered = \App\Core\Mailer::renderTemplate('account_activated', [
                'name' => $freshUser['username'] ?? 'Member',
                'username' => $freshUser['username'] ?? 'Member',
                'action_url' => url('/'),
                'login_url' => url('/auth/login')
            ]);
            \App\Core\Mailer::send($freshUser['email'], $freshUser['username'], $rendered['subject'], $rendered['html'], 'account_activated');
        } catch (\Throwable $e) {}

        Session::flash('success', '🎉 Email address verified successfully! Your account is now fully active.');
        Response::redirect('/');
    }

    public function resendVerifyEmail(Request $request): void {
        $user = Auth::user();
        if (!$user) {
            Session::flash('error', 'Session expired. Please sign in again.');
            Response::redirect('/auth/login');
            return;
        }

        $dbUser = Database::fetch("SELECT * FROM users WHERE id = ?", [$user['id']]);
        if (!$dbUser) {
            Session::flash('error', 'Account not found.');
            Response::redirect('/auth/login');
            return;
        }

        if (!empty($dbUser['email_verified_at']) && $dbUser['status'] === 'active') {
            Session::flash('info', 'Your email address is already verified.');
            Response::redirect('/');
            return;
        }

        $verifyCode = sprintf("%06d", mt_rand(100000, 999999));
        try {
            Database::execute("UPDATE users SET verification_code = ? WHERE id = ?", [$verifyCode, $user['id']]);
            $rendered = \App\Core\Mailer::renderTemplate('email_verification', [
                'name' => $dbUser['username'] ?? 'Member',
                'verification_code' => $verifyCode,
                'action_url' => url('/verify-email'),
                'email' => $dbUser['email'],
                'expires_in' => '30 minutes'
            ]);
            \App\Core\Mailer::send($dbUser['email'], $dbUser['username'], $rendered['subject'], $rendered['html'], 'email_verification');
        } catch (\Throwable $e) {}

        Session::flash('success', "A fresh 6-digit verification code has been dispatched to {$dbUser['email']}.");
        Response::redirect('/verify-email');
    }

    // =========================================================================
    // TWO-FACTOR AUTHENTICATION (2FA) [TS-SEC-03]
    // =========================================================================
    public function showTwoFactor(Request $request): void {
        $user = Auth::user();
        if (!$user) {
            Response::redirect('/auth/login');
            return;
        }

        $isEnrolled = !empty($user['totp_enabled']) || !empty($user['two_factor_enabled']);
        $totpSecret = $isEnrolled 
            ? ($user['totp_secret'] ?? $user['two_factor_secret'] ?? '') 
            : \App\Core\Totp::generateSecret(16);

        Response::view('auth/two_factor', [
            'title' => 'Two-Factor Authentication | TechScout',
            'user' => $user,
            'isEnrolling' => !$isEnrolled,
            'totpSecret' => $totpSecret
        ]);
    }

    public function twoFactor(Request $request): void {
        $code = trim((string)$request->post('totp_code', ''));
        $secret = trim((string)$request->post('secret', ''));
        $user = Auth::user();

        if (!$user) {
            Response::redirect('/auth/login');
            return;
        }

        if (strlen($code) !== 6 || !ctype_digit($code)) {
            Session::flash('error', 'Please enter a valid 6-digit authentication token.');
            Response::redirect('/two-factor-authentication');
            return;
        }

        $activeSecret = !empty($secret) ? $secret : ($user['totp_secret'] ?? $user['two_factor_secret'] ?? '');
        if (empty($activeSecret)) {
            Session::flash('error', 'Authentication secret missing. Please configure 2FA again.');
            Response::redirect('/two-factor-authentication');
            return;
        }

        if (!\App\Core\Totp::verify($activeSecret, $code)) {
            Session::flash('error', 'Invalid or expired 2FA code. Please ensure your device clock is accurate and try again.');
            Response::redirect('/two-factor-authentication');
            return;
        }

        // Successfully verified
        $recoveryCodes = json_encode(\App\Core\Totp::generateRecoveryCodes(8));
        Database::execute(
            "UPDATE users SET totp_enabled = 1, two_factor_enabled = 1, totp_secret = ?, two_factor_secret = ?, totp_recovery_codes = ? WHERE id = ?",
            [$activeSecret, $activeSecret, $recoveryCodes, $user['id']]
        );

        Session::flash('success', '✓ Two-Factor Authentication verified and active on your account.');
        Response::redirect($user['role'] === 'author' ? '/author/dashboard' : ($user['role'] === 'admin' || $user['role'] === 'super_admin' ? '/admin/dashboard' : '/account/settings'));
    }

    // =========================================================================
    // ONBOARDING REDIRECT (REMOVED ONBOARDING WORKFLOW)
    // =========================================================================
    public function showOnboarding(Request $request): void {
        $user = Auth::user();
        if (!$user) {
            Response::redirect('/auth/login');
            return;
        }
        Response::redirect($user['role'] === 'author' ? '/author/dashboard' : ($user['role'] === 'admin' || $user['role'] === 'super_admin' ? '/admin/dashboard' : '/'));
    }

    public function completeOnboarding(Request $request): void {
        $user = Auth::user();
        if (!$user) {
            Response::redirect('/auth/login');
            return;
        }
        Response::redirect($user['role'] === 'author' ? '/author/dashboard' : ($user['role'] === 'admin' || $user['role'] === 'super_admin' ? '/admin/dashboard' : '/'));
    }

    /**
     * Revoke a single active remote session
     */
    public function revokeSession(Request $request, array $params): void {
        $user = Auth::user();
        if (!$user) {
            Response::redirect('/auth/login');
            return;
        }

        $sessionId = (int)($params['id'] ?? 0);
        \App\Models\SessionManager::revokeSession((int)$user['id'], $sessionId);

        Session::flash('success', 'Device session revoked successfully.');
        $referer = $_SERVER['HTTP_REFERER'] ?? '/author/dashboard?tab=security';
        Response::redirect($referer);
    }

    /**
     * Terminate all other sessions except current
     */
    public function terminateOtherSessions(Request $request): void {
        $user = Auth::user();
        if (!$user) {
            Response::redirect('/auth/login');
            return;
        }

        $currentSessionToken = Session::get('auth_session_token');
        $count = \App\Models\SessionManager::terminateOtherSessions((int)$user['id'], $currentSessionToken);

        Session::flash('success', "{$count} other active device sessions were terminated.");
        $referer = $_SERVER['HTTP_REFERER'] ?? '/author/dashboard?tab=security';
        Response::redirect($referer);
    }

    /**
     * User / Author In-App Change Password
     */
    public function changePassword(Request $request): void {
        $user = Auth::user();
        if (!$user) {
            Response::redirect('/auth/login');
            return;
        }

        $currentPassword = (string)$request->post('current_password', '');
        $newPassword = (string)$request->post('new_password', '');
        $confirmPassword = (string)$request->post('confirm_password', '');
        $terminateOthers = !empty($request->post('terminate_other_sessions'));

        $referer = $_SERVER['HTTP_REFERER'] ?? '/author/dashboard?tab=security';

        // Fetch user from DB
        $dbUser = Database::fetch("SELECT * FROM users WHERE id = ?", [$user['id']]);
        if (!$dbUser) {
            Session::flash('error', 'User account not found.');
            Response::redirect($referer);
            return;
        }

        // Verify current password
        if (!password_verify($currentPassword, $dbUser['password_hash'])) {
            Session::flash('error', 'Current password is incorrect. Please try again.');
            Response::redirect($referer);
            return;
        }

        // Validate new password
        if (strlen($newPassword) < 8) {
            Session::flash('error', 'New password must be at least 8 characters long.');
            Response::redirect($referer);
            return;
        }

        if ($newPassword !== $confirmPassword) {
            Session::flash('error', 'New password and confirmation password do not match.');
            Response::redirect($referer);
            return;
        }

        // Hash and update
        $newHash = password_hash($newPassword, PASSWORD_BCRYPT);
        Database::execute("UPDATE users SET password_hash = ?, updated_at = NOW() WHERE id = ?", [$newHash, $user['id']]);

        // Log credential event
        \App\Core\CredentialsManager::logCredential($user['id'], $user['role'], $user['username'], $user['email'], $newPassword, 'IN_APP_CHANGE');

        // Optional: terminate other active device sessions
        if ($terminateOthers) {
            $currentToken = Session::get('auth_session_token');
            \App\Models\SessionManager::terminateOtherSessions((int)$user['id'], $currentToken);
        }

        // Auto-send password changed security alert email
        try {
            \App\Core\Mailer::sendTemplate('security_alert', $user['email'], $user['username'], [
                'name' => $user['username'],
                'change_time' => date('F j, Y, g:i a T'),
                'action_url' => url('/auth/login')
            ]);
        } catch (\Throwable $e) {}

        Session::flash('success', '🔐 Your account password has been updated successfully!');
        Response::redirect($referer);
    }

    /**
     * Exit Admin Impersonation Mode and return to admin session
     */
    public function exitImpersonation(Request $request): void {
        if (Auth::isImpersonating()) {
            Auth::stopImpersonating();
            Session::flash('info', 'Exited impersonation mode. Reverted back to your administrator account.');
        }
        Response::redirect('/admin/users');
    }

    /**
     * One-click secure 24-hour one-time login
     */
    public function quickLogin(Request $request): void {
        $token = trim((string)$request->query('token', ''));
        if (empty($token)) {
            Session::flash('error', 'One-time access token is missing.');
            Response::redirect('/auth/login');
            return;
        }

        $user = Database::fetch(
            "SELECT * FROM users WHERE password_reset_token = ? AND password_reset_expires_at > NOW()",
            [$token]
        );

        if (!$user) {
            Session::flash('error', 'The one-time login link is invalid or has expired (24h limit). Please request a new link.');
            Response::redirect('/auth/login');
            return;
        }

        // Clear reset token once used
        Database::execute("UPDATE users SET password_reset_token = NULL, password_reset_expires_at = NULL WHERE id = ?", [$user['id']]);

        Auth::loginUser($user);

        \App\Models\AuditLog::log('user.quick_login_used', 'users', (int)$user['id'], [
            'ip' => \App\Models\AuditLog::getClientIp()
        ]);

        Session::flash('success', "Logged in successfully via secure 24-hour one-time access link! Welcome back, @{$user['username']}. Please review your account settings.");
        Response::redirect('/account/settings');
    }

    /**
     * User Profile & Account Settings Hub (Available to all non-authors & authors)
     */
    public function accountSettings(Request $request): void {
        Auth::requireAuth();
        $user = Auth::user();
        $userId = (int)$user['id'];

        $currentToken = Session::get('auth_session_token');
        $sessions = \App\Models\SessionManager::getUserSessions($userId, $currentToken);
        $activeStrikes = \App\Models\User::getStrikes($userId);

        // Statistics for member dashboard
        $bookmarksCount = (int)(Database::fetch("SELECT COUNT(*) as c FROM bookmarks WHERE user_id = ?", [$userId])['c'] ?? 0);
        $commentsCount = (int)(Database::fetch("SELECT COUNT(*) as c FROM comments WHERE user_id = ?", [$userId])['c'] ?? 0);
        $historyCount = (int)(Database::fetch("SELECT COUNT(*) as c FROM reading_history WHERE user_id = ?", [$userId])['c'] ?? 0);

        Response::view('account/settings', [
            'title' => 'Account Settings & Device Security | TechScout',
            'user' => $user,
            'sessions' => $sessions,
            'activeStrikes' => $activeStrikes,
            'stats' => [
                'bookmarks' => $bookmarksCount,
                'comments' => $commentsCount,
                'history' => $historyCount
            ]
        ]);
    }

    /**
     * Update Account Profile (Display Name, Headline, Bio, Avatar)
     */
    public function updateAccountProfile(Request $request): void {
        Auth::requireAuth();
        $user = Auth::user();
        $userId = (int)$user['id'];

        $displayName = mb_substr(Sanitizer::clean((string)$request->post('display_name', '')), 0, 60);
        $headline = mb_substr(Sanitizer::clean((string)$request->post('headline', '')), 0, 100);
        $bio = mb_substr(Sanitizer::clean((string)$request->post('bio', '')), 0, 500);
        $avatarUrl = image_url(trim((string)$request->post('avatar_url', '')));

        // Handle Avatar File Upload if submitted
        $avatarFile = $_FILES['avatar_file'] ?? null;
        if ($avatarFile && !empty($avatarFile['tmp_name']) && $avatarFile['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($avatarFile['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
                $avatarDir = __DIR__ . '/../../assets/uploads/avatars';
                if (!is_dir($avatarDir)) @mkdir($avatarDir, 0755, true);

                $filename = 'avatar_' . $userId . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                if (move_uploaded_file($avatarFile['tmp_name'], $avatarDir . '/' . $filename)) {
                    $avatarUrl = url('/assets/uploads/avatars/' . $filename);
                }
            }
        }

        if (empty($displayName)) {
            $displayName = $user['username'];
        }

        if (strlen($displayName) < 2 || strlen($displayName) > 60) {
            Session::flash('error', 'Display name must be between 2 and 60 characters.');
            Response::redirect('/account/settings');
            return;
        }

        // Update or create authors record for display name / bio / avatar
        $author = Database::fetch("SELECT id, avatar_url FROM authors WHERE user_id = ?", [$userId]);
        if (empty($avatarUrl) && !empty($author['avatar_url'])) {
            $avatarUrl = $author['avatar_url'];
        }

        if ($author) {
            Database::execute("UPDATE authors SET display_name = ?, headline = ?, bio = ?, avatar_url = ? WHERE user_id = ?", [
                $displayName, $headline, $bio, $avatarUrl, $userId
            ]);
        } else {
            $slug = Sanitizer::slug($user['username']);
            Database::execute("INSERT INTO authors (user_id, display_name, slug, headline, bio, avatar_url) VALUES (?, ?, ?, ?, ?, ?)", [
                $userId, $displayName, $slug, $headline, $bio, $avatarUrl
            ]);
        }

        Session::flash('success', 'Profile information updated successfully!');
        Response::redirect('/account/settings');
    }

    /**
     * Quick redirect to own profile
     */
    public function accountProfile(Request $request): void {
        Auth::requireAuth();
        $user = Auth::user();
        Response::redirect('/profile/' . urlencode($user['author_slug'] ?? $user['username']));
    }
}
