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

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
    }

    /**
     * Log in user with provided id.
     */
    public function byId(int|string|null $id): void
    {
        if ($id === 0 || $id === '' || $id === null) {
            return;
        }

        $id = (int) $id;

        $this->_updateLoginDate($id);
        WISession::set('user_id', $id);

        if (defined('LOGIN_FINGERPRINT') && LOGIN_FINGERPRINT === true) {
            WISession::set('login_fingerprint', $this->_generateLoginString());
        }
    }

    /**
     * Check if user is logged in.
     */
    public function isLoggedIn(): bool
    {
        if (WISession::get('user_id') === null) {
            return false;
        }

        if (defined('LOGIN_FINGERPRINT') && LOGIN_FINGERPRINT === true) {
            $loginString = $this->_generateLoginString();
            $currentString = WISession::get('login_fingerprint');

            if ($currentString !== null && hash_equals((string) $currentString, $loginString)) {
                return true;
            }

            $this->logout();
            return false;
        }

        return true;
    }

    /**
     * Attempt user login.
     */
    public function userLogin(string $username, string $password): bool
    {
        $errors = $this->_validateLoginFields($username, $password);

        if (count($errors) !== 0) {
            echo json_encode([
                'status' => 'error',
                'message' => implode('<br />', $errors)
            ]);
            return false;
        }

        if ($this->_isBruteForce()) {
            echo json_encode([
                'status' => 'error',
                'message' => WILang::get('brute_force')
            ]);
            return false;
        }

        $result = $this->WIdb->select(
            'SELECT * FROM `wi_members` WHERE `username` = :u LIMIT 1',
            ['u' => trim($username)]
        );

        if (count($result) !== 1) {
            $this->increaseLoginAttempts();

            echo json_encode([
                'status' => 'error',
                'message' => WILang::get('wrong_username_password')
            ]);
            return false;
        }

        $user = $result[0];
        $storedPassword = (string) ($user['password'] ?? '');

        if ($storedPassword === '' || !password_verify($password, $storedPassword)) {
            $this->increaseLoginAttempts();

            echo json_encode([
                'status' => 'error',
                'message' => WILang::get('wrong_username_password')
            ]);
            return false;
        }

        if (
            defined('MAIL_CONFIRMATION_REQUIRED')
            && MAIL_CONFIRMATION_REQUIRED === true
            && (($user['confirmed'] ?? 'N') === 'N')
        ) {
            echo json_encode([
                'status' => 'error',
                'message' => WILang::get('user_not_confirmed')
            ]);
            return false;
        }

        if (($user['banned'] ?? 'N') === 'Y') {
            $this->increaseLoginAttempts();

            echo json_encode([
                'status' => 'error',
                'message' => WILang::get('user_banned')
            ]);
            return false;
        }

        if ((int) ($user['user_role'] ?? 0) < 4) {
            echo json_encode([
                'status' => 'error',
                'message' => WILang::get('user_not_admin')
            ]);
            return false;
        }

        $userId = (int) ($user['user_id'] ?? 0);

        $this->_updateLoginDate($userId);
        WISession::set('user_id', $userId);

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }

        if (defined('LOGIN_FINGERPRINT') && LOGIN_FINGERPRINT === true) {
            WISession::set('login_fingerprint', $this->_generateLoginString());
        }

        $st1 = trim($username);
        $st2 = 'Successfully logged in user';

        if (class_exists('WIMaintenace')) {
            WIMaintenace::LogFunction($st1, $st2);
        }

        return true;
    }

    /**
     * Increase login attempts from specific IP address to prevent brute force attack.
     */
    public function increaseLoginAttempts(): void
    {
        $date = date('Y-m-d');
        $userIp = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $table = 'wi_login_attempts';

        $loginAttempts = $this->_getLoginAttempts();

        if ($loginAttempts > 0) {
            $this->WIdb->update(
                $table,
                ['attempt_number' => $loginAttempts + 1],
                '`ip_addr` = :ip_addr AND `date` = :d',
                [
                    'ip_addr' => $userIp,
                    'd' => $date
                ]
            );
            return;
        }

        $this->WIdb->insert($table, [
            'ip_addr' => $userIp,
            'date' => $date,
            'attempt_number' => 1
        ]);
    }

    /**
     * Log out user and destroy session.
     */
    public function logout(): void
    {
        WISession::destroySession();
    }

    /**
     * Check brute force.
     */
    public function _isBruteForce(): bool
    {
        $maxAttempts = defined('LOGIN_MAX_LOGIN_ATTEMPTS') ? (int) LOGIN_MAX_LOGIN_ATTEMPTS : 5;
        return $this->_getLoginAttempts() > $maxAttempts;
    }

    /**
     * Validate login fields.
     */
    private function _validateLoginFields(string $username, string $password): array
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

    /**
     * Generate login fingerprint.
     */
    private function _generateLoginString(): string
    {
        $userIP = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $userBrowser = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

        return hash('sha512', $userIP . $userBrowser);
    }

    /**
     * Get login attempts for current IP and date.
     */
    private function _getLoginAttempts(): int
    {
        $date = date('Y-m-d');
        $userIp = $_SERVER['REMOTE_ADDR'] ?? null;

        if ($userIp === null) {
            return PHP_INT_MAX;
        }

        $query = 'SELECT `attempt_number`
                  FROM `wi_login_attempts`
                  WHERE `ip_addr` = :ip AND `date` = :date
                  LIMIT 1';

        $result = $this->WIdb->select($query, [
            'ip' => $userIp,
            'date' => $date
        ]);

        if (count($result) === 0) {
            return 0;
        }

        return (int) ($result[0]['attempt_number'] ?? 0);
    }

    /**
     * Update login date.
     */
    private function _updateLoginDate(int $userid): void
    {
        $this->WIdb->update(
            'wi_members',
            ['last_login' => date('Y-m-d H:i:s')],
            'user_id = :u',
            ['u' => $userid]
        );
    }
}