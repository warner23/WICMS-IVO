<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS / WICOS / WIKitchenCompli
| Class: WIRequest
| File: WIRequest.php
| Location: /WIAdmin/WICore/WIClass/WIRequest.php
| Type: Core Request Helper
| Layer: Shared Core
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Purpose
|--------------------------------------------------------------------------
| Canonical request helper with backward-compatible aliases used across
| legacy admin AJAX, compliance admin, and newer controller code.
|--------------------------------------------------------------------------
*/

final class WIRequest
{
    public static function method(): string
    {
        return strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
    }

    public static function isGet(): bool
    {
        return self::method() === 'GET';
    }

    public static function isPost(): bool
    {
        return self::method() === 'POST';
    }

    public static function isAjax(): bool
    {
        $requestedWith = (string) ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '');
        return strtolower($requestedWith) === 'xmlhttprequest';
    }

    public static function sameOrigin(): bool
    {
        $origin = (string) ($_SERVER['HTTP_ORIGIN'] ?? '');
        $host   = (string) ($_SERVER['HTTP_HOST'] ?? '');

        if ($origin === '' || $host === '') {
            return true;
        }

        $originHost = (string) parse_url($origin, PHP_URL_HOST);

        return strtolower($originHost) === strtolower($host);
    }

    public static function input(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $_POST)) {
            return self::normalize($_POST[$key]);
        }

        if (array_key_exists($key, $_GET)) {
            return self::normalize($_GET[$key]);
        }

        return $default;
    }

    public static function post(string $key, mixed $default = null): mixed
    {
        if (!array_key_exists($key, $_POST)) {
            return $default;
        }

        return self::normalize($_POST[$key]);
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        if (!array_key_exists($key, $_GET)) {
            return $default;
        }

        return self::normalize($_GET[$key]);
    }

    public static function allPost(): array
    {
        return self::normalizeArray($_POST);
    }

    public static function allGet(): array
    {
        return self::normalizeArray($_GET);
    }

    public static function only(array $keys, string $source = 'both'): array
    {
        $data = [];

        foreach ($keys as $key) {
            $data[$key] = match (strtolower($source)) {
                'post' => self::post((string) $key, null),
                'get'  => self::get((string) $key, null),
                default => self::input((string) $key, null),
            };
        }

        return $data;
    }

    public static function has(string $key, string $source = 'both'): bool
    {
        return match (strtolower($source)) {
            'post' => array_key_exists($key, $_POST),
            'get'  => array_key_exists($key, $_GET),
            default => array_key_exists($key, $_POST) || array_key_exists($key, $_GET),
        };
    }

    public static function filled(string $key, string $source = 'both'): bool
    {
        $value = match (strtolower($source)) {
            'post' => self::post($key, null),
            'get'  => self::get($key, null),
            default => self::input($key, null),
        };

        if (is_array($value)) {
            return !empty($value);
        }

        return trim((string) $value) !== '';
    }

    public static function int(string $key, int $default = 0, string $source = 'both'): int
    {
        $value = match (strtolower($source)) {
            'post' => self::post($key, null),
            'get'  => self::get($key, null),
            default => self::input($key, null),
        };

        return is_numeric($value) ? (int) $value : $default;
    }

    public static function bool(string $key, bool $default = false, string $source = 'both'): bool
    {
        $value = match (strtolower($source)) {
            'post' => self::post($key, null),
            'get'  => self::get($key, null),
            default => self::input($key, null),
        };

        if (is_bool($value)) {
            return $value;
        }

        if (is_int($value)) {
            return $value === 1;
        }

        if (is_string($value)) {
            $parsed = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            return $parsed ?? $default;
        }

        return $default;
    }

    public static function string(string $key, string $default = '', string $source = 'both'): string
    {
        $value = match (strtolower($source)) {
            'post' => self::post($key, null),
            'get'  => self::get($key, null),
            default => self::input($key, null),
        };

        if ($value === null || is_array($value)) {
            return $default;
        }

        return trim((string) $value);
    }

    public static function float(string $key, float $default = 0.0, string $source = 'both'): float
    {
        $value = match (strtolower($source)) {
            'post' => self::post($key, null),
            'get'  => self::get($key, null),
            default => self::input($key, null),
        };

        return is_numeric($value) ? (float) $value : $default;
    }

    public static function array(string $key, array $default = [], string $source = 'both'): array
    {
        $value = match (strtolower($source)) {
            'post' => self::post($key, null),
            'get'  => self::get($key, null),
            default => self::input($key, null),
        };

        return is_array($value) ? self::normalizeArray($value) : $default;
    }

    public static function postInt(string $key, int $default = 0): int
    {
        return self::int($key, $default, 'post');
    }

    public static function getInt(string $key, int $default = 0): int
    {
        return self::int($key, $default, 'get');
    }

    public static function postString(string $key, string $default = ''): string
    {
        return self::string($key, $default, 'post');
    }

    public static function getString(string $key, string $default = ''): string
    {
        return self::string($key, $default, 'get');
    }

    public static function postBool(string $key, bool $default = false): bool
    {
        return self::bool($key, $default, 'post');
    }

    public static function getBool(string $key, bool $default = false): bool
    {
        return self::bool($key, $default, 'get');
    }

    public static function json(): array
    {
        static $decoded = null;

        if ($decoded !== null) {
            return $decoded;
        }

        $raw = file_get_contents('php://input');

        if (!is_string($raw) || trim($raw) === '') {
            $decoded = [];
            return $decoded;
        }

        $data = json_decode($raw, true);

        $decoded = is_array($data) ? self::normalizeArray($data) : [];
        return $decoded;
    }

    public static function jsonInput(string $key, mixed $default = null): mixed
    {
        $data = self::json();
        return array_key_exists($key, $data) ? $data[$key] : $default;
    }

    public static function server(string $key, mixed $default = null): mixed
    {
        return $_SERVER[$key] ?? $default;
    }

    public static function ip(): string
    {
        return (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
    }

    public static function userAgent(): string
    {
        return (string) ($_SERVER['HTTP_USER_AGENT'] ?? 'unknown');
    }

    private static function normalize(mixed $value): mixed
    {
        if (is_array($value)) {
            return self::normalizeArray($value);
        }

        if (is_string($value)) {
            return trim($value);
        }

        return $value;
    }

    private static function normalizeArray(array $data): array
    {
        foreach ($data as $key => $value) {
            $data[$key] = self::normalize($value);
        }

        return $data;
    }
}