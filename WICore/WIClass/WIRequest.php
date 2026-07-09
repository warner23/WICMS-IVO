<?php
declare(strict_types=1);

/**
 * FILE:
 * WICMS-IVO/WICore/WIClass/WIRequest.php
 *
 * Canonical request helper for WICMS.
 */
final class WIRequest
{
    public static function method(): string
    {
        return strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
    }

    public static function isPost(): bool
    {
        return self::method() === 'POST';
    }

    public static function isGet(): bool
    {
        return self::method() === 'GET';
    }

    public static function isAjax(): bool
    {
        $requestedWith = strtolower((string) ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? ''));
        return $requestedWith === 'xmlhttprequest';
    }

    public static function input(string $key, mixed $default = null): mixed
    {
        return $_REQUEST[$key] ?? $default;
    }

    public static function query(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }

    public static function post(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $default;
    }

    public static function string(string $key, string $default = ''): string
    {
        $value = self::input($key, $default);

        if (is_array($value)) {
            return $default;
        }

        return trim((string) $value);
    }

    public static function postString(string $key, string $default = ''): string
    {
        $value = self::post($key, $default);

        if (is_array($value)) {
            return $default;
        }

        return trim((string) $value);
    }

    public static function int(string $key, int $default = 0): int
    {
        $value = self::input($key, $default);
        return is_numeric($value) ? (int) $value : $default;
    }

    public static function postInt(string $key, int $default = 0): int
    {
        $value = self::post($key, $default);
        return is_numeric($value) ? (int) $value : $default;
    }

    public static function array(string $key, array $default = []): array
    {
        $value = self::input($key, $default);
        return is_array($value) ? $value : $default;
    }

    public static function postArray(string $key, array $default = []): array
    {
        $value = self::post($key, $default);
        return is_array($value) ? $value : $default;
    }

    public static function currentHost(): string
    {
        return strtolower((string) ($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? ''));
    }

    public static function originHost(): string
    {
        $origin = self::normaliseHost($_SERVER['HTTP_ORIGIN'] ?? null);
        if ($origin !== '') {
            return $origin;
        }

        return self::normaliseHost($_SERVER['HTTP_REFERER'] ?? null);
    }

    public static function sameOrigin(): bool
    {
        $currentHost = self::currentHost();
        $originHost = self::originHost();

        if ($currentHost === '') {
            return false;
        }

        if ($originHost === '') {
            return true;
        }

        return hash_equals($currentHost, $originHost);
    }

    public static function uri(): string
    {
        return trim((string) ($_SERVER['REQUEST_URI'] ?? ''));
    }

    private static function normaliseHost(?string $value): string
    {
        if ($value === null || trim($value) === '') {
            return '';
        }

        $parts = parse_url($value);
        if ($parts === false) {
            return '';
        }

        $host = strtolower((string) ($parts['host'] ?? ''));
        if ($host === '') {
            return '';
        }

        $port = isset($parts['port']) ? (int) $parts['port'] : null;

        return ($port !== null && $port > 0) ? $host . ':' . $port : $host;
    }
}