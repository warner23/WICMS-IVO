<?php
declare(strict_types=1);

/**
 * Session Class
 * Created by Warner Infinity
 * Author Jules Warner
 */

class WISession
{
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        if (SESSION_USE_ONLY_COOKIES) {
            ini_set('session.use_only_cookies', '1');
        }

        ini_set('session.use_strict_mode', '1');

        $isHttps = (
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || ((int)($_SERVER['SERVER_PORT'] ?? 0) === 443)
        );

        $params = session_get_cookie_params();

        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => $params['path'] ?? '/',
            'domain'   => $params['domain'] ?? '',
            'secure'   => SESSION_SECURE ? $isHttps : false,
            'httponly' => SESSION_HTTP_ONLY,
            'samesite' => 'Lax'
        ]);

        session_start();

        if (!isset($_SESSION['__wi_session_started'])) {

            $_SESSION['__wi_session_started'] = time();

            if (SESSION_REGENERATE_ID) {
                session_regenerate_id(true);
            }

        }
    }

    public static function destroySession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return;
        }

        $_SESSION = [];

        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            [
                'expires'  => time() - 42000,
                'path'     => $params['path'] ?? '/',
                'domain'   => $params['domain'] ?? '',
                'secure'   => $params['secure'] ?? false,
                'httponly' => $params['httponly'] ?? true,
                'samesite' => 'Lax'
            ]
        );

        session_destroy();
    }

    public static function regenerate(bool $deleteOld = true): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id($deleteOld);
        }
    }

    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function destroy(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }
}