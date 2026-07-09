<?php
declare(strict_types=1);

/**
 * File Information
 * Written By: Jules Warner / Warner Infinity
 * Company: Warner Infinity
 * Product: WICMS
 * Project: WIMembers / WIProfile
 * File: WIConfig.php
 * Location: WIMembers/WICore/WIClass/
 * Type: Configuration
 * Layer: Core
 * Purpose Area: WIMembers root-style configuration bootstrap
 * Version: 1.0.1
 * Created: 2026-06-11
 * Last Updated: 2026-06-11
 * Status: Active
 *
 * Summary:
 * Root-style WIMembers config loader. This mirrors the canonical root
 * WICore/WIClass/WIConfig.php structure while resolving the public root from
 * the WIMembers folder location so .env and shared constants remain aligned.
 */
final class WIConfig
{
    private static bool $loaded = false;
    private static array $env = [];

    public static function load(): void
    {
        if (self::$loaded) {
            return;
        }

        // Set this first to prevent recursive re-entry via env().
        self::$loaded = true;

        $rootPath = self::publicRootPath();

        if (!defined('ROOT_PATH')) {
            define('ROOT_PATH', $rootPath);
        }

        $candidateFiles = [
            $rootPath . '/.env',
            dirname(__DIR__, 2) . '/.env',
        ];

        foreach ($candidateFiles as $file) {
            if (is_file($file) && is_readable($file)) {
                self::loadEnvFile($file);
                break;
            }
        }

        self::defineCoreConstants();
    }

    public static function env(string $key, mixed $default = null): mixed
    {
        self::load();

        if (array_key_exists($key, self::$env)) {
            return self::$env[$key];
        }

        $serverValue = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
        if ($serverValue !== false && $serverValue !== null) {
            return $serverValue;
        }

        return $default;
    }

    private static function publicRootPath(): string
    {
        if (defined('WI_PUBLIC_ROOT_DIR')) {
            return rtrim((string) WI_PUBLIC_ROOT_DIR, '/\\');
        }

        if (defined('ROOT_PATH')) {
            return rtrim((string) ROOT_PATH, '/\\');
        }

        // __DIR__ = /WIMembers/WICore/WIClass, so level 3 is the public root.
        return dirname(__DIR__, 3);
    }

    private static function loadEnvFile(string $file): void
    {
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if (!is_array($lines)) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (!str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);

            $key = trim($key);
            $value = trim($value);

            if ($key === '') {
                continue;
            }

            $value = self::normalizeEnvValue($value);

            self::$env[$key] = $value;
            $_ENV[$key] = (string) $value;
            $_SERVER[$key] = (string) $value;

            if (function_exists('putenv')) {
                putenv($key . '=' . $value);
            }
        }
    }

    private static function normalizeEnvValue(string $value): mixed
    {
        $value = trim($value);

        if (
            (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
            (str_starts_with($value, "'") && str_ends_with($value, "'"))
        ) {
            $value = substr($value, 1, -1);
        }

        $lower = strtolower($value);

        return match ($lower) {
            'true' => true,
            'false' => false,
            'null' => null,
            'empty' => '',
            default => $value,
        };
    }

    private static function defineCoreConstants(): void
    {
        self::defineIfMissing('APP_ENV', (string) self::env('APP_ENV', 'local'));
        self::defineIfMissing('APP_DEBUG', self::toBool(self::env('APP_DEBUG', true)));
        self::defineIfMissing('APP_KEY', (string) self::env('APP_KEY', ''));

        self::defineIfMissing('DB_HOST', (string) self::env('DB_HOST', 'localhost'));
        self::defineIfMissing('DB_NAME', (string) self::env('DB_NAME', ''));
        self::defineIfMissing('DB_USER', (string) self::env('DB_USER', 'root'));
        self::defineIfMissing('DB_PASS', (string) self::env('DB_PASS', ''));
        self::defineIfMissing('DB_PORT', (string) self::env('DB_PORT', '3306'));
        self::defineIfMissing('DB_TYPE', (string) self::env('DB_TYPE', 'mysql'));

        self::defineIfMissing('SESSION_NAME', (string) self::env('SESSION_NAME', 'WICMSSESSID'));
        self::defineIfMissing('SESSION_SAMESITE', (string) self::env('SESSION_SAMESITE', 'Lax'));
        self::defineIfMissing('SESSION_IDLE_TIMEOUT', (int) self::env('SESSION_IDLE_TIMEOUT', 1800));
        self::defineIfMissing('SESSION_ABSOLUTE_TIMEOUT', (int) self::env('SESSION_ABSOLUTE_TIMEOUT', 28800));

        self::defineIfMissing('CSRF_TOKEN_TTL', (int) self::env('CSRF_TOKEN_TTL', 7200));

        // Legacy site constants used by older panels/modules. WI.php later
        // refreshes from WISite where available, but these prevent early fatals.
        self::defineIfMissing('WEBSITE_NAME', (string) self::env('WEBSITE_NAME', 'WICMS'));
        self::defineIfMissing('WEBSITE_DOMAIN', (string) self::env('WEBSITE_DOMAIN', 'localhost'));
        self::defineIfMissing('WEBSITE_URL', (string) self::env('WEBSITE_URL', 'http://localhost'));
        self::defineIfMissing('CONTACT_EMAIL', (string) self::env('CONTACT_EMAIL', ''));
        self::defineIfMissing('DEFAULT_LANGUAGE', (string) self::env('DEFAULT_LANGUAGE', 'en'));
        self::defineIfMissing('MULTI_LANGUAGE', (string) self::env('MULTI_LANGUAGE', 'false'));
        self::defineIfMissing('WICMS_VERSION', (string) self::env('WICMS_VERSION', '1.0.0'));

        self::defineIfMissing('PASSWORD_BCRYPT_COST', (int) self::env('PASSWORD_BCRYPT_COST', 12));
        self::defineIfMissing('LOGIN_MAX_LOGIN_ATTEMPTS', (int) self::env('LOGIN_MAX_LOGIN_ATTEMPTS', 5));

        // Social-login compatibility constants used by legacy modules such as panel.
        // Keep these as string flags because existing module code checks === "true".
        self::defineIfMissing('TWITTER_ENABLED', self::socialFlagString(self::env('TWITTER_ENABLED', 'false')));
        self::defineIfMissing('FACEBOOK_ENABLED', self::socialFlagString(self::env('FACEBOOK_ENABLED', 'false')));
        self::defineIfMissing('GOOGLE_ENABLED', self::socialFlagString(self::env('GOOGLE_ENABLED', 'false')));
    }

    private static function socialFlagString(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        $value = strtolower(trim((string) $value));

        return in_array($value, ['1', 'true', 'yes', 'on'], true) ? 'true' : 'false';
    }

    private static function defineIfMissing(string $name, mixed $value): void
    {
        if (!defined($name)) {
            define($name, $value);
        }
    }

    private static function toBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        $filtered = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        return $filtered ?? false;
    }
}

WIConfig::load();
