<?php
declare(strict_types=1);

/**
 * CSRF Protection Class
 * Created by Warner Infinity
 * Author Jules Warner
 */

#[\AllowDynamicProperties]
class WICsrf
{
    private const TOKEN_NAME = 'csrf_token';
    private const TOKEN_TIME = 'csrf_token_time';
    private const TOKEN_LIFETIME = 1800; // 30 minutes

    /**
     * Ensure session started
     */
    private static function ensureSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    /**
     * Generate token
     */
    public static function generateToken(): string
    {
        self::ensureSession();

        $token = bin2hex(random_bytes(32));

        $_SESSION[self::TOKEN_NAME] = $token;
        $_SESSION[self::TOKEN_TIME] = time();

        return $token;
    }

    /**
     * Get existing token
     */
    public static function getToken(): string
    {
        self::ensureSession();

        if (!isset($_SESSION[self::TOKEN_NAME])) {
            return self::generateToken();
        }

        return $_SESSION[self::TOKEN_NAME];
    }

    /**
     * Validate token
     */
    public static function validateToken(?string $token): bool
    {
        self::ensureSession();

        if (!isset($_SESSION[self::TOKEN_NAME])) {
            return false;
        }

        if (!isset($_SESSION[self::TOKEN_TIME])) {
            return false;
        }

        if ($token === null) {
            return false;
        }

        $storedToken = $_SESSION[self::TOKEN_NAME];
        $tokenTime = $_SESSION[self::TOKEN_TIME];

        if ((time() - $tokenTime) > self::TOKEN_LIFETIME) {
            self::destroyToken();
            return false;
        }

        return hash_equals($storedToken, $token);
    }

    /**
     * Destroy token
     */
    public static function destroyToken(): void
    {
        self::ensureSession();

        unset($_SESSION[self::TOKEN_NAME]);
        unset($_SESSION[self::TOKEN_TIME]);
    }

    /**
     * CSRF hidden input field
     */
    public static function inputField(): string
    {
        $token = self::getToken();

        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Check POST request token
     */
    public static function checkPost(): bool
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return false;
        }

        $token = $_POST['csrf_token'] ?? null;

        return self::validateToken($token);
    }

    /**
     * Force validation or stop execution
     */
    public static function requireValid(): void
    {
        if (!self::checkPost()) {
            http_response_code(403);
            exit('Invalid CSRF token');
        }
    }
}
?>