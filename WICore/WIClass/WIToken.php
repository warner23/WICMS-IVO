<?php
declare(strict_types=1);

/**
 * Token Service
 * WICMS Core
 */

final class WIToken
{
    private const DEFAULT_TTL = 3600;

    /**
     * Generate a secure random token.
     */
    public static function generate(int $length = 32): string
    {
        if ($length < 16) {
            $length = 16;
        }

        return bin2hex(random_bytes($length));
    }

    /**
     * Create and store a CSRF token in session.
     */
    public static function createCsrfToken(string $form, int $ttl = self::DEFAULT_TTL): string
    {
        self::ensureSessionStarted();

        $token = self::generate(32);

        $tokens = WISession::get('wi_csrf_tokens', []);
        if (!is_array($tokens)) {
            $tokens = [];
        }

        $tokens[$form] = [
            'token' => $token,
            'expires' => time() + $ttl,
        ];

        WISession::set('wi_csrf_tokens', $tokens);

        return $token;
    }

    /**
     * Get existing CSRF token for a form if still valid,
     * otherwise create a fresh one.
     */
    public static function getCsrfToken(string $form, int $ttl = self::DEFAULT_TTL): string
    {
        self::ensureSessionStarted();

        $tokens = WISession::get('wi_csrf_tokens', []);
        if (!is_array($tokens)) {
            return self::createCsrfToken($form, $ttl);
        }

        if (
            isset($tokens[$form]['token'], $tokens[$form]['expires']) &&
            is_string($tokens[$form]['token']) &&
            is_int($tokens[$form]['expires']) &&
            $tokens[$form]['expires'] >= time()
        ) {
            return $tokens[$form]['token'];
        }

        return self::createCsrfToken($form, $ttl);
    }

    /**
     * Validate CSRF token.
     * If valid, token is consumed by default.
     */
    public static function validateCsrfToken(string $form, ?string $token, bool $consume = true): bool
    {
        self::ensureSessionStarted();

        if ($token === null || $token === '') {
            return false;
        }

        $tokens = WISession::get('wi_csrf_tokens', []);
        if (!is_array($tokens) || !isset($tokens[$form])) {
            return false;
        }

        $stored = $tokens[$form];

        if (
            !is_array($stored) ||
            !isset($stored['token'], $stored['expires']) ||
            !is_string($stored['token']) ||
            !is_int($stored['expires'])
        ) {
            return false;
        }

        if ($stored['expires'] < time()) {
            unset($tokens[$form]);
            WISession::set('wi_csrf_tokens', $tokens);
            return false;
        }

        $isValid = hash_equals($stored['token'], $token);

        if ($isValid && $consume) {
            unset($tokens[$form]);
            WISession::set('wi_csrf_tokens', $tokens);
        }

        return $isValid;
    }

    /**
     * Remove a form token manually.
     */
    public static function destroyCsrfToken(string $form): void
    {
        self::ensureSessionStarted();

        $tokens = WISession::get('wi_csrf_tokens', []);
        if (!is_array($tokens)) {
            return;
        }

        unset($tokens[$form]);
        WISession::set('wi_csrf_tokens', $tokens);
    }

    /**
     * Clean up expired tokens.
     */
    public static function cleanupExpiredCsrfTokens(): void
    {
        self::ensureSessionStarted();

        $tokens = WISession::get('wi_csrf_tokens', []);
        if (!is_array($tokens)) {
            return;
        }

        $now = time();

        foreach ($tokens as $form => $data) {
            if (
                !is_array($data) ||
                !isset($data['expires']) ||
                !is_int($data['expires']) ||
                $data['expires'] < $now
            ) {
                unset($tokens[$form]);
            }
        }

        WISession::set('wi_csrf_tokens', $tokens);
    }

    /**
     * Generate a hidden HTML field for forms.
     */
    public static function csrfField(string $form, int $ttl = self::DEFAULT_TTL): string
    {
        $token = self::getCsrfToken($form, $ttl);

        return '<input type="hidden" name="csrf_token" value="' .
            htmlspecialchars($token, ENT_QUOTES, 'UTF-8') .
            '">';
    }

    /**
     * Validate request token from POST by default.
     */
    public static function validatePostToken(string $form, string $field = 'csrf_token', bool $consume = true): bool
    {
        $token = $_POST[$field] ?? null;

        if (!is_string($token)) {
            return false;
        }

        return self::validateCsrfToken($form, $token, $consume);
    }

    private static function ensureSessionStarted(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            WISession::startSession();
        }
    }
}