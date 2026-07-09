<?php
declare(strict_types=1);

/**
 * File Information
 * Written By: Jules Warner / Warner Infinity
 * Company: Warner Infinity
 * Product: WICMS
 * Project: WIMembers
 * File: WISession.php
 * Location: WIMembers/WICore/WIClass/
 * Type: Class
 * Layer: Security
 * Purpose Area: Member workspace, profile, account, settings, payments, forms and training
 * Version: 1.0.0
 * Created: 2026-05-24
 * Last Updated: 2026-05-24
 * Status: Refactored
 * Summary: WICMS-compatible WIMembers modernisation. Keeps WI-prefixed classes, database-driven modules, sessions and page rendering.
 */


class WISession
{
    private const DEFAULT_SESSION_NAME = 'WICMSSESSID';
    private const DEFAULT_IDLE_TIMEOUT = 3600;
    private const DEFAULT_ABSOLUTE_TIMEOUT = 28800;

    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            self::touch();
            return;
        }

        $sessionName = defined('SESSION_NAME') && trim((string) SESSION_NAME) !== ''
            ? trim((string) SESSION_NAME)
            : self::DEFAULT_SESSION_NAME;

        session_name($sessionName);

        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.cookie_httponly', '1');

        session_start();

        if (!isset($_SESSION['_wi_started'])) {
            $_SESSION['_wi_started'] = time();
            $_SESSION['_wi_last_activity'] = time();
            session_regenerate_id(true);
        }

        self::touch();
    }

    public static function regenerate(bool $deleteOldSession = true): void
    {
        self::startSession();
        session_regenerate_id($deleteOldSession);
        $_SESSION['_wi_last_regenerated'] = time();
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
        unset($_SESSION[$key]);
    }

    public static function destroy(string $key): void
    {
        self::remove($key);
    }

    public static function all(): array
    {
        self::startSession();
        return $_SESSION;
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
            setcookie(session_name(), '', time() - 42000, $params['path'] ?? '/', $params['domain'] ?? '', (bool) ($params['secure'] ?? false), (bool) ($params['httponly'] ?? true));
        }

        session_destroy();
    }

    private static function touch(): void
    {
        $now = time();
        $last = (int) ($_SESSION['_wi_last_activity'] ?? $now);
        $started = (int) ($_SESSION['_wi_started'] ?? $now);

        if (($now - $last) > self::DEFAULT_IDLE_TIMEOUT || ($now - $started) > self::DEFAULT_ABSOLUTE_TIMEOUT) {
            self::destroySession();
            return;
        }

        $_SESSION['_wi_last_activity'] = $now;
    }
}
