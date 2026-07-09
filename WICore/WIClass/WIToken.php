<?php
declare(strict_types=1);

/**
 * FILE: WICore/WIClass/WIToken.php
 *
 * Canonical root token / CSRF manager
 */

class WIToken
{
    private const SESSION_KEY = '_wi_csrf_tokens';
    private const DEFAULT_TTL = 7200; // 2 hours
    private const POST_FIELD = 'csrf_token';

    public static function generate(string $form): string
    {
        WISession::startSession();
        self::cleanupExpiredCsrfTokens();

        $token = bin2hex(random_bytes(32));
        $tokens = WISession::get(self::SESSION_KEY, []);

        if (!is_array($tokens)) {
            $tokens = [];
        }

        $tokens[$form] = [
            'token' => $token,
            'expires_at' => time() + self::getTtl(),
        ];

        WISession::set(self::SESSION_KEY, $tokens);

        return $token;
    }

    public static function getToken(string $form): string
    {
        WISession::startSession();
        self::cleanupExpiredCsrfTokens();

        $tokens = WISession::get(self::SESSION_KEY, []);

        if (
            is_array($tokens) &&
            isset($tokens[$form]) &&
            is_array($tokens[$form]) &&
            isset($tokens[$form]['token'], $tokens[$form]['expires_at'])
        ) {
            $expiresAt = (int) $tokens[$form]['expires_at'];

            if ($expiresAt >= time()) {
                return (string) $tokens[$form]['token'];
            }
        }

        return self::generate($form);
    }

    public static function csrfField(string $form): string
    {
        $token = self::getToken($form);

        return '<input type="hidden" name="' . self::POST_FIELD . '" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    public static function validate(string $form, ?string $submittedToken): bool
    {
        WISession::startSession();
        self::cleanupExpiredCsrfTokens();

        if ($submittedToken === null || trim($submittedToken) === '') {
            return false;
        }

        $tokens = WISession::get(self::SESSION_KEY, []);

        if (
            !is_array($tokens) ||
            !isset($tokens[$form]) ||
            !is_array($tokens[$form])
        ) {
            return false;
        }

        $storedToken = (string) ($tokens[$form]['token'] ?? '');
        $expiresAt = (int) ($tokens[$form]['expires_at'] ?? 0);

        if ($storedToken === '' || $expiresAt < time()) {
            self::remove($form);
            return false;
        }

        $valid = hash_equals($storedToken, trim($submittedToken));

        if ($valid) {
            self::remove($form);
        }

        return $valid;
    }

    public static function validatePostToken(string $form): bool
    {
        $submittedToken = $_POST[self::POST_FIELD] ?? null;

        if (is_array($submittedToken)) {
            return false;
        }

        return self::validate($form, $submittedToken !== null ? (string) $submittedToken : null);
    }

    public static function cleanupExpiredCsrfTokens(): void
    {
        WISession::startSession();

        $tokens = WISession::get(self::SESSION_KEY, []);

        if (!is_array($tokens) || $tokens === []) {
            return;
        }

        $now = time();
        $filtered = [];

        foreach ($tokens as $form => $data) {
            if (!is_array($data)) {
                continue;
            }

            $token = (string) ($data['token'] ?? '');
            $expiresAt = (int) ($data['expires_at'] ?? 0);

            if ($token !== '' && $expiresAt >= $now) {
                $filtered[$form] = [
                    'token' => $token,
                    'expires_at' => $expiresAt,
                ];
            }
        }

        WISession::set(self::SESSION_KEY, $filtered);
    }

    public static function remove(string $form): void
    {
        WISession::startSession();

        $tokens = WISession::get(self::SESSION_KEY, []);

        if (!is_array($tokens)) {
            $tokens = [];
        }

        if (isset($tokens[$form])) {
            unset($tokens[$form]);
            WISession::set(self::SESSION_KEY, $tokens);
        }
    }

    public static function clear(): void
    {
        WISession::startSession();
        WISession::remove(self::SESSION_KEY);
    }

    public static function token(string $form): string
    {
        return self::getToken($form);
    }

    private static function getTtl(): int
    {
        if (defined('CSRF_TOKEN_TTL')) {
            return max(300, (int) CSRF_TOKEN_TTL);
        }

        try {
            $settings = new WISettings();
            $value = (int) $settings->website('csrf_token_ttl');

            if ($value > 0) {
                return max(300, $value);
            }
        } catch (Throwable $e) {
        }

        return self::DEFAULT_TTL;
    }
}