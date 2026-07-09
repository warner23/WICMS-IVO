<?php
declare(strict_types=1);

/**
 * FILE:
 * WICMS-IVO/WICore/WIClass/WILang.php
 *
 * Canonical language loader for WICMS core/front-end.
 */
final class WILang
{
    private const DEFAULT_LANGUAGE = 'en';

    private static string $language = self::DEFAULT_LANGUAGE;
    private static array $translations = [];
    private static array $fallbackTranslations = [];
    private static bool $loaded = false;

    public static function setLanguage(string $lang): void
    {
        $lang = self::sanitizeLanguage($lang);

        if ($lang === '') {
            return;
        }

        self::$language = $lang;
        self::loadLanguage();
    }

    public static function getLanguage(): string
    {
        if (!self::$loaded) {
            self::bootstrapLanguage();
        }

        return self::$language;
    }

    public static function get(string $key): string
    {
        if (!self::$loaded) {
            self::bootstrapLanguage();
        }

        if (isset(self::$translations[$key])) {
            return (string) self::$translations[$key];
        }

        if (isset(self::$fallbackTranslations[$key])) {
            return (string) self::$fallbackTranslations[$key];
        }

        return $key;
    }

    public static function e(string $key): string
    {
        return htmlspecialchars(self::get($key), ENT_QUOTES, 'UTF-8');
    }

    public static function all(): string
    {
        if (!self::$loaded) {
            self::bootstrapLanguage();
        }

        return json_encode(
            self::$translations,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        ) ?: '{}';
    }

    public static function loadFromSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return;
        }

        if (!empty($_SESSION['language'])) {
            self::setLanguage((string) $_SESSION['language']);
            return;
        }

        self::bootstrapLanguage();
    }

    public static function change(string $lang): void
    {
        $lang = self::sanitizeLanguage($lang);

        if ($lang === '') {
            return;
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['language'] = $lang;
        }

        self::setLanguage($lang);
    }

    public static function exists(string $key): bool
    {
        if (!self::$loaded) {
            self::bootstrapLanguage();
        }

        return array_key_exists($key, self::$translations);
    }

    public static function raw(): array
    {
        if (!self::$loaded) {
            self::bootstrapLanguage();
        }

        return self::$translations;
    }

    private static function bootstrapLanguage(): void
    {
        $lang = self::sessionLanguage();

        if ($lang === '') {
            $lang = self::settingsLanguage();
        }

        if ($lang === '') {
            $lang = self::DEFAULT_LANGUAGE;
        }

        self::$language = $lang;
        self::loadLanguage();
    }

    private static function loadLanguage(): void
    {
        $langFile = self::languageFile(self::$language);

        if (!is_file($langFile)) {
            $langFile = self::languageFile(self::DEFAULT_LANGUAGE);
        }

        $translations = [];
        $lang = [];

        if (is_file($langFile)) {
            include $langFile;

            if (isset($lang) && is_array($lang)) {
                $translations = $lang;
            }
        }

        self::$translations = $translations;
        self::$fallbackTranslations = self::loadFallbackTranslations();
        self::$loaded = true;
    }


    /**
     * @return array<string, mixed>
     */
    private static function loadFallbackTranslations(): array
    {
        if (self::$language === self::DEFAULT_LANGUAGE) {
            return self::$translations;
        }

        $fallbackFile = self::languageFile(self::DEFAULT_LANGUAGE);
        $fallback = [];
        $lang = [];

        if (is_file($fallbackFile)) {
            include $fallbackFile;
            if (isset($lang) && is_array($lang)) {
                $fallback = $lang;
            }
        }

        return $fallback;
    }

    private static function languageFile(string $lang): string
    {
        return dirname(__DIR__) . '/WILang/' . $lang . '.php';
    }

    private static function settingsLanguage(): string
    {
        if (!class_exists('WISettings')) {
            return '';
        }

        try {
            $settings = new WISettings();
            $value = $settings->website('default_lang');

            if ($value === null || trim((string) $value) === '') {
                $value = $settings->website('language');
            }

            return self::sanitizeLanguage((string) $value);
        } catch (Throwable $e) {
            return '';
        }
    }

    private static function sessionLanguage(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return '';
        }

        return self::sanitizeLanguage((string) ($_SESSION['language'] ?? ''));
    }

    private static function sanitizeLanguage(string $lang): string
    {
        $lang = strtolower(trim($lang));

        return preg_match('/^[a-z]{2}$/', $lang) ? $lang : '';
    }
}