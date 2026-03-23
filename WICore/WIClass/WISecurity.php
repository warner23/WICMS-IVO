<?php
declare(strict_types=1);

/**
 * Security Class
 * Created by Warner Infinity
 * Author Jules Warner
 */

#[\AllowDynamicProperties]
class WISecurity
{

    private const LOGIN_LIMIT = 5;
    private const LOGIN_TIMEOUT = 900; // 15 minutes

    /**
     * Hash password
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Verify password
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Regenerate session
     */
    public static function regenerateSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        session_regenerate_id(true);
    }

    /**
     * Record login attempt
     */
    public static function recordLoginAttempt(string $username): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION['login_attempts'][$username][] = time();
    }

    /**
     * Check brute force attempts
     */
    public static function isBlocked(string $username): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['login_attempts'][$username])) {
            return false;
        }

        $attempts = $_SESSION['login_attempts'][$username];
        $now = time();

        $validAttempts = [];

        foreach ($attempts as $time) {

            if (($now - $time) < self::LOGIN_TIMEOUT) {
                $validAttempts[] = $time;
            }
        }

        $_SESSION['login_attempts'][$username] = $validAttempts;

        return count($validAttempts) >= self::LOGIN_LIMIT;
    }

    /**
     * Clear login attempts after success
     */
    public static function clearLoginAttempts(string $username): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        unset($_SESSION['login_attempts'][$username]);
    }

    /**
     * Sanitize string
     */
    public static function sanitize(string $input): string
    {
        return trim($input);
    }

    /**
     * Escape output
     */
    public static function escape(string $input): string
    {
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Generate secure token
     */
    public static function generateToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }

    /**
     * Validate token
     */
    public static function validateToken(string $token, string $sessionToken): bool
    {
        return hash_equals($sessionToken, $token);
    }

}
?>