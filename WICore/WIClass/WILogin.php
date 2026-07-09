<?php
declare(strict_types=1);

/**
 * WICMS Canonical Login Service
 * Root core source of truth
 */

class WILogin
{
    private WIdb $WIdb;
    private ?WIMaintenace $maint;
    private array $lastResult = [
        'status'  => 'error',
        'message' => 'Login not attempted',
        'errors'  => []
    ];

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
        $this->maint = class_exists('WIMaintenace') ? new WIMaintenace() : null;
    }

    public function getLastResult(): array
    {
        return $this->lastResult;
    }

    public function byId(int $id): void
    {
        if ($id <= 0) {
            return;
        }

        $this->updateLoginDate($id);
        $this->establishAuthenticatedSession($id);
    }

    public function isLoggedIn(): bool
    {
        $userId = (int) WISession::get('user_id', 0);

        if ($userId <= 0) {
            return false;
        }

        if ($this->loginFingerprintEnabled()) {
            $expected = (string) WISession::get('login_fingerprint', '');
            $current = $this->generateLoginFingerprint();

            if ($expected === '' || !hash_equals($expected, $current)) {
                $this->logout();
                return false;
            }
        }

        return true;
    }

    public function userLogin(string $username, string $password): bool
    {
        $result = $this->attemptLogin($username, $password);
        $this->lastResult = $result;

        return $result['status'] === 'success';
    }

    public function logout(): void
    {
        WISession::destroySession();
    }

    public function increaseLoginAttempts(): void
    {
        $date = date('Y-m-d');
        $userIp = $this->getClientIp();

        if ($userIp === '') {
            return;
        }

        $attempts = $this->getLoginAttempts();

        if ($attempts > 0) {
            $this->WIdb->update(
                'wi_login_attempts',
                ['attempt_number' => $attempts + 1],
                '`ip_addr` = :ip_addr AND `date` = :d',
                [
                    'ip_addr' => $userIp,
                    'd' => $date
                ]
            );
            return;
        }

        $this->WIdb->insert('wi_login_attempts', [
            'ip_addr' => $userIp,
            'date' => $date,
            'attempt_number' => 1
        ]);
    }

    public function clearLoginAttempts(): void
    {
        $userIp = $this->getClientIp();

        if ($userIp === '') {
            return;
        }

        $this->WIdb->delete(
            'wi_login_attempts',
            '`ip_addr` = :ip_addr AND `date` = :d',
            [
                'ip_addr' => $userIp,
                'd' => date('Y-m-d')
            ]
        );
    }

    public function isBruteForce(): bool
    {
        return $this->getLoginAttempts() >= $this->getMaxLoginAttempts();
    }

    private function attemptLogin(string $username, string $password): array
    {
        $username = trim($username);
        $password = (string) $password;

        $errors = $this->validateLoginFields($username, $password);

        if ($errors !== []) {
            return [
                'status' => 'error',
                'message' => implode('<br>', $errors),
                'errors' => $errors
            ];
        }

        if ($this->isBruteForce()) {
            return [
                'status' => 'error',
                'message' => WILang::get('brute_force'),
                'errors' => [WILang::get('brute_force')]
            ];
        }

        $result = $this->WIdb->select(
            'SELECT * FROM `wi_members` WHERE `username` = :username LIMIT 1',
            ['username' => $username]
        );

        if (count($result) !== 1) {
            $this->increaseLoginAttempts();

            return [
                'status' => 'error',
                'message' => WILang::get('wrong_username_password'),
                'errors' => [WILang::get('wrong_username_password')]
            ];
        }

        $user = $result[0];
        $userId = (int) ($user['user_id'] ?? 0);
        $storedHash = (string) ($user['password'] ?? '');

        $register = new WIRegister();

        if (!$register->verifyPassword($password, $storedHash)) {
            $this->increaseLoginAttempts();

            return [
                'status' => 'error',
                'message' => WILang::get('wrong_username_password'),
                'errors' => [WILang::get('wrong_username_password')]
            ];
        }

        if ($this->mailConfirmationRequired() && (($user['confirmed'] ?? 'N') === 'N')) {
            return [
                'status' => 'error',
                'message' => WILang::get('user_not_confirmed'),
                'errors' => [WILang::get('user_not_confirmed')]
            ];
        }

        if (($user['banned'] ?? 'N') === 'Y') {
            $this->increaseLoginAttempts();

            return [
                'status' => 'error',
                'message' => WILang::get('user_banned'),
                'errors' => [WILang::get('user_banned')]
            ];
        }

        if ($register->needsRehash($storedHash)) {
            $this->WIdb->update(
                'wi_members',
                ['password' => $register->hashPassword($password)],
                '`user_id` = :id',
                ['id' => $userId]
            );
        }

        $this->updateLoginDate($userId);
        $this->establishAuthenticatedSession($userId);
        $this->clearLoginAttempts();

        if ($this->maint !== null) {
            $this->maint->LogFunction($username, 'Successfully logged in user');
        }

        return [
            'status' => 'success',
            'message' => 'Login successful',
            'user_id' => $userId
        ];
    }

    private function establishAuthenticatedSession(int $userId): void
    {
        WISession::set('user_id', $userId);
        WISession::set('login_time', time());

        if ($this->loginFingerprintEnabled()) {
            WISession::set('login_fingerprint', $this->generateLoginFingerprint());
        } else {
            WISession::destroy('login_fingerprint');
        }

        WISession::regenerate(true);
    }

    private function validateLoginFields(string $username, string $password): array
    {
        $errors = [];

        if ($username === '') {
            $errors[] = WILang::get('username_required');
        }

        if ($password === '') {
            $errors[] = WILang::get('password_required');
        }

        return $errors;
    }

    private function getLoginAttempts(): int
    {
        $userIp = $this->getClientIp();

        if ($userIp === '') {
            return PHP_INT_MAX;
        }

        $result = $this->WIdb->select(
            'SELECT `attempt_number`
             FROM `wi_login_attempts`
             WHERE `ip_addr` = :ip_addr AND `date` = :d
             LIMIT 1',
            [
                'ip_addr' => $userIp,
                'd' => date('Y-m-d')
            ]
        );

        if (count($result) !== 1) {
            return 0;
        }

        return (int) ($result[0]['attempt_number'] ?? 0);
    }

    private function updateLoginDate(int $userId): void
    {
        if ($userId <= 0) {
            return;
        }

        $this->WIdb->update(
            'wi_members',
            ['last_login' => date('Y-m-d H:i:s')],
            '`user_id` = :u',
            ['u' => $userId]
        );
    }

    private function getClientIp(): string
    {
        return trim((string) ($_SERVER['REMOTE_ADDR'] ?? ''));
    }

    private function generateLoginFingerprint(): string
    {
        $userAgent = (string) ($_SERVER['HTTP_USER_AGENT'] ?? '');
        $sessionSalt = session_id();

        return hash('sha256', $userAgent . '|' . $sessionSalt);
    }

    private function getMaxLoginAttempts(): int
    {
        if (defined('LOGIN_MAX_LOGIN_ATTEMPTS')) {
            return max(1, (int) LOGIN_MAX_LOGIN_ATTEMPTS);
        }

        try {
            $settings = new WISettings();
            $value = (int) $settings->website('max_login_attempts');

            if ($value > 0) {
                return max(1, $value);
            }
        } catch (Throwable $e) {
        }

        return 5;
    }

    private function loginFingerprintEnabled(): bool
    {
        if (defined('LOGIN_FINGERPRINT')) {
            return (bool) LOGIN_FINGERPRINT;
        }

        try {
            $settings = new WISettings();

            return filter_var(
                (string) $settings->website('login_fingerprint'),
                FILTER_VALIDATE_BOOLEAN
            );
        } catch (Throwable $e) {
            return false;
        }
    }

    private function mailConfirmationRequired(): bool
    {
        if (defined('MAIL_CONFIRMATION_REQUIRED')) {
            return (bool) MAIL_CONFIRMATION_REQUIRED;
        }

        try {
            $settings = new WISettings();

            return filter_var(
                (string) $settings->website('mail_confirm_required'),
                FILTER_VALIDATE_BOOLEAN
            );
        } catch (Throwable $e) {
            return false;
        }
    }
}