<?php
declare(strict_types=1);

/**
 * File Information
 * Written By: Jules Warner / Warner Infinity
 * Company: Warner Infinity
 * Product: WICMS
 * Project: WIMembers
 * File: WICsrf.php
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


class WICsrf
{
    private const TOKEN_NAME = 'csrf_token';
    private const TOKEN_TIME = 'csrf_token_time';
    private const TOKEN_LIFETIME = 1800;

    public static function generateToken(): string
    {
        WISession::startSession();
        $token = bin2hex(random_bytes(32));
        WISession::set(self::TOKEN_NAME, $token);
        WISession::set(self::TOKEN_TIME, time());
        return $token;
    }

    public static function getToken(): string
    {
        $token = (string) WISession::get(self::TOKEN_NAME, '');
        return $token !== '' ? $token : self::generateToken();
    }

    public static function validateToken(?string $token): bool
    {
        $stored = (string) WISession::get(self::TOKEN_NAME, '');
        $time = (int) WISession::get(self::TOKEN_TIME, 0);

        if ($stored === '' || $token === null || $time <= 0) {
            return false;
        }

        if ((time() - $time) > self::TOKEN_LIFETIME) {
            self::destroyToken();
            return false;
        }

        return hash_equals($stored, $token);
    }

    public static function destroyToken(): void
    {
        WISession::remove(self::TOKEN_NAME);
        WISession::remove(self::TOKEN_TIME);
    }

    public static function inputField(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(self::getToken(), ENT_QUOTES, 'UTF-8') . '">';
    }

    public static function checkPost(): bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST'
            && self::validateToken(isset($_POST['csrf_token']) ? (string) $_POST['csrf_token'] : null);
    }

    public static function requireValid(): void
    {
        if (!self::checkPost()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
            exit;
        }
    }
}
