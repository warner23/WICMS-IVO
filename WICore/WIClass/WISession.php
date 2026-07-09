<?php
declare(strict_types=1);

/**
 * FILE: WICore/WIClass/WISession.php
 *
 * Canonical root session manager
 */

class WISession
{
    private const DEFAULT_SESSION_NAME = 'WICMSSESSID';
    private const DEFAULT_IDLE_TIMEOUT = 1800;      // 30 minutes
    private const DEFAULT_ABSOLUTE_TIMEOUT = 28800; // 8 hours

    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            self::enforceTimeouts();
            return;
        }

        $secure = self::isHttps();
        $sessionName = self::getSessionName();

        if ($sessionName !== '') {
            session_name($sessionName);
        }

        if (PHP_VERSION_ID >= 70300) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'domain' => '',
                'secure' => $secure,
                'httponly' => true,
                'samesite' => self::getSameSite(),
            ]);
        } else {
            session_set_cookie_params(
                0,
                '/; samesite=' . self::getSameSite(),
                '',
                $secure,
                true
            );
        }

        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_secure', $secure ? '1' : '0');
        ini_set('session.cookie_samesite', self::getSameSite());

        session_start();

        self::initializeSessionMeta();
        self::enforceTimeouts();
    }

    public static function regenerate(bool $deleteOldSession = true): void
    {
        self::startSession();

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id($deleteOldSession);
            $_SESSION['_wi_last_regenerated'] = time();
        }
    }

    public static function set(string $key, $value): void
    {
        self::startSession();
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, $default = null)
    {
        self::startSession();
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        self::startSession();
        return array_key_exists($key, $_SESSION);
    }

    public static function remove(string $key): void
    {
        self::startSession();

        if (array_key_exists($key, $_SESSION)) {
            unset($_SESSION[$key]);
        }
    }

    public static function destroy(string $key): void
    {
        self::remove($key);
    }

    public static function clear(): void
    {
        self::startSession();
        $_SESSION = [];
    }

    public static function destroySession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            self::startSession();
        }

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'] ?? '/',
                $params['domain'] ?? '',
                (bool) ($params['secure'] ?? false),
                (bool) ($params['httponly'] ?? true)
            );
        }

        session_destroy();
    }

    public static function all(): array
    {
        self::startSession();
        return $_SESSION;
    }

    public static function isActive(): bool
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }

    private static function initializeSessionMeta(): void
    {
        $now = time();

        if (!isset($_SESSION['_wi_session_started'])) {
            $_SESSION['_wi_session_started'] = $now;
        }

        if (!isset($_SESSION['_wi_last_activity'])) {
            $_SESSION['_wi_last_activity'] = $now;
        }

        if (!isset($_SESSION['_wi_last_regenerated'])) {
            $_SESSION['_wi_last_regenerated'] = $now;
        }
    }

    private static function enforceTimeouts(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return;
        }

        $now = time();
        $idleTimeout = self::getIdleTimeout();
        $absoluteTimeout = self::getAbsoluteTimeout();

        $lastActivity = (int) ($_SESSION['_wi_last_activity'] ?? $now);
        $sessionStarted = (int) ($_SESSION['_wi_session_started'] ?? $now);
        $lastRegenerated = (int) ($_SESSION['_wi_last_regenerated'] ?? $now);

        if ($idleTimeout > 0 && ($now - $lastActivity) > $idleTimeout) {
            self::destroySession();
            return;
        }

        if ($absoluteTimeout > 0 && ($now - $sessionStarted) > $absoluteTimeout) {
            self::destroySession();
            return;
        }

        if (($now - $lastRegenerated) > 300) {
            session_regenerate_id(true);
            $_SESSION['_wi_last_regenerated'] = $now;
        }

        $_SESSION['_wi_last_activity'] = $now;
    }

    private static function getSessionName(): string
    {
        if (defined('SESSION_NAME') && trim((string) SESSION_NAME) !== '') {
            return trim((string) SESSION_NAME);
        }

        try {
            $settings = new WISettings();
            $value = trim((string) $settings->website('session_name'));

            if ($value !== '') {
                return $value;
            }
        } catch (Throwable $e) {
        }

        return self::DEFAULT_SESSION_NAME;
    }

    private static function getIdleTimeout(): int
    {
        if (defined('SESSION_IDLE_TIMEOUT')) {
            return max(0, (int) SESSION_IDLE_TIMEOUT);
        }

        try {
            $settings = new WISettings();
            $value = (int) $settings->website('session_idle_timeout');

            if ($value >= 0) {
                return $value;
            }
        } catch (Throwable $e) {
        }

        return self::DEFAULT_IDLE_TIMEOUT;
    }

    private static function getAbsoluteTimeout(): int
    {
        if (defined('SESSION_ABSOLUTE_TIMEOUT')) {
            return max(0, (int) SESSION_ABSOLUTE_TIMEOUT);
        }

        try {
            $settings = new WISettings();
            $value = (int) $settings->website('session_absolute_timeout');

            if ($value >= 0) {
                return $value;
            }
        } catch (Throwable $e) {
        }

        return self::DEFAULT_ABSOLUTE_TIMEOUT;
    }

    private static function getSameSite(): string
    {
        if (defined('SESSION_SAMESITE')) {
            $value = ucfirst(strtolower((string) SESSION_SAMESITE));
            if (in_array($value, ['Lax', 'Strict', 'None'], true)) {
                return $value;
            }
        }

        try {
            $settings = new WISettings();
            $value = ucfirst(strtolower((string) $settings->website('session_samesite')));

            if (in_array($value, ['Lax', 'Strict', 'None'], true)) {
                return $value;
            }
        } catch (Throwable $e) {
        }

        return 'Lax';
    }

    private static function isHttps(): bool
    {
        if (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off') {
            return true;
        }

        if ((int) ($_SERVER['SERVER_PORT'] ?? 0) === 443) {
            return true;
        }

        if (strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https') {
            return true;
        }

        return false;
    }
}