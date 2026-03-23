<?php
declare(strict_types=1);

/**
 * Login Class
 * Created by Warner Infinity
 * Author Jules Warner
 */

class WILogin
{
    private WIdb $WIdb;
    private ?WIMaintenace $maint;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
        $this->maint = class_exists('WIMaintenace') ? new WIMaintenace() : null;
    }

    public function byId(int $id): void
    {
        if ($id <= 0) {
            return;
        }

        $this->updateLoginDate($id);
        WISession::set('user_id', $id);

        if ($this->loginFingerprintEnabled()) {
            WISession::set('login_fingerprint', $this->generateLoginString());
        }

        WISession::regenerate(true);
    }

    public function isLoggedIn(): bool
    {
        $userId = WISession::get('user_id');

        if ($userId === null) {
            return false;
        }

        if ($this->loginFingerprintEnabled()) {
            $loginString = $this->generateLoginString();
            $currentString = WISession::get('login_fingerprint');

            if ($currentString !== null && hash_equals((string) $currentString, $loginString)) {
                return true;
            }

            $this->logout();
            return false;
        }

        return true;
    }

    public function userLogin(string $username, string $password): bool
    {
        $errors = $this->validateLoginFields($username, $password);

        if ($errors !== []) {
            $this->jsonError(implode('<br />', $errors));
            return false;
        }

        if ($this->isBruteForce()) {
            $this->jsonError(WILang::get('brute_force'));
            return false;
        }

        $username = trim($username);
        $password = (string) $password;

        $result = $this->WIdb->select(
            'SELECT * FROM `wi_members` WHERE `username` = :u LIMIT 1',
            ['u' => $username]
        );

        if (count($result) !== 1) {
            $this->increaseLoginAttempts();
            $this->jsonError(WILang::get('wrong_username_password'));
            return false;
        }

        $user = $result[0];
        $register = new WIRegister();

        if (!$register->verifyPassword($password, (string) ($user['password'] ?? ''))) {
            $this->increaseLoginAttempts();
            $this->jsonError(WILang::get('wrong_username_password'));
            return false;
        }

        if ($this->mailConfirmationRequired() && (($user['confirmed'] ?? 'N') === 'N')) {
            $this->jsonError(WILang::get('user_not_confirmed'));
            return false;
        }

        if (($user['banned'] ?? 'N') === 'Y') {
            $this->increaseLoginAttempts();
            $this->jsonError(WILang::get('user_banned'));
            return false;
        }

        if ($register->needsRehash((string) $user['password'])) {
            $this->WIdb->update(
                'wi_members',
                ['password' => $register->hashPassword($password)],
                '`user_id` = :id',
                ['id' => (int) $user['user_id']]
            );
        }

        $this->updateLoginDate((int) $user['user_id']);
        WISession::set('user_id', (int) $user['user_id']);

        if ($this->loginFingerprintEnabled()) {
            WISession::set('login_fingerprint', $this->generateLoginString());
        }

        WISession::regenerate(true);

        if ($this->maint !== null) {
            $this->maint->LogFunction($username, 'Successfully logged in user');
        }

        return true;
    }

    public function increaseLoginAttempts(): void
    {
        $date = date('Y-m-d');
        $userIp = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        $loginAttempts = $this->getLoginAttempts();

        if ($loginAttempts > 0) {
            $this->WIdb->update(
                'wi_login_attempts',
                ['attempt_number' => $loginAttempts + 1],
                '`ip_addr` = :ip_addr AND `date` = :d',
                ['ip_addr' => $userIp, 'd' => $date]
            );
            return;
        }

        $this->WIdb->insert('wi_login_attempts', [
            'ip_addr' => $userIp,
            'date' => $date,
            'attempt_number' => 1
        ]);
    }

    public function logout(): void
    {
        WISession::destroySession();
    }

    public function isBruteForce(): bool
    {
        return $this->getLoginAttempts() >= $this->getMaxLoginAttempts();
    }

    private function validateLoginFields(string $username, string $password): array
    {
        $errors = [];

        if (trim($username) === '') {
            $errors[] = WILang::get('username_required');
        }

        if ($password === '') {
            $errors[] = WILang::get('password_required');
        }

        return $errors;
    }

    private function generateLoginString(): string
    {
        $userIP = $_SERVER['REMOTE_ADDR'] ?? '';
        $userBrowser = $_SERVER['HTTP_USER_AGENT'] ?? '';

        return hash('sha512', $userIP . $userBrowser);
    }

    private function getLoginAttempts(): int
    {
        $date = date('Y-m-d');
        $userIp = $_SERVER['REMOTE_ADDR'] ?? null;

        if (!$userIp) {
            return PHP_INT_MAX;
        }

        $result = $this->WIdb->select(
            'SELECT `attempt_number`
             FROM `wi_login_attempts`
             WHERE `ip_addr` = :ip AND `date` = :date
             LIMIT 1',
            [
                'ip' => $userIp,
                'date' => $date
            ]
        );

        if (count($result) === 0) {
            return 0;
        }

        return (int) ($result[0]['attempt_number'] ?? 0);
    }

    private function updateLoginDate(int $userId): void
    {
        $this->WIdb->update(
            'wi_members',
            ['last_login' => date('Y-m-d H:i:s')],
            'user_id = :u',
            ['u' => $userId]
        );
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
                return $value;
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

    private function jsonError(string $message): void
    {
        echo json_encode([
            'status' => 'error',
            'message' => $message
        ]);
    }
}