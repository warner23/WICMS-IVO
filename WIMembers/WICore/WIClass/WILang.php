<?php
declare(strict_types=1);

/**
 * File Information
 * Written By: Jules Warner / Warner Infinity
 * Company: Warner Infinity
 * Product: WICMS
 * Project: WIMembers / WIProfile
 * File: WILang.php
 * Location: WIMembers/WICore/WIClass/
 * Type: Class
 * Layer: Language
 * Purpose Area: Root-style member language loading
 * Version: 1.0.1
 * Created: 2026-06-11
 * Last Updated: 2026-06-11
 * Status: Active
 *
 * Summary:
 * WIMembers-compatible language class with the same public API expected by the
 * root bootstrap, including loadFromSession(). It prefers WIMembers language
 * files and falls back to the canonical root language folder where available.
 */
class WILang
{
    private const DEFAULT_LANGUAGE = 'en';

    private static string $language = self::DEFAULT_LANGUAGE;
    private static array $translations = [];
    private static bool $loaded = false;

    private static array $fallback = [
        'logout' => 'Logout',
        'admin_panel' => 'Admin Portal',
        'member_profile' => 'Profile',
    ];

    public static function setLanguage(string $lang): void
    {
        $lang = self::sanitizeLanguage($lang);

        if ($lang === '') {
            return;
        }

        self::$language = $lang;
        self::storeSessionLanguage($lang);
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

        if (array_key_exists($key, self::$translations)) {
            return (string) self::$translations[$key];
        }

        if (array_key_exists($key, self::$fallback)) {
            return self::$fallback[$key];
        }

        return ucwords(str_replace('_', ' ', $key));
    }

    public static function getTranslation(string $key): string
    {
        return self::get($key);
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

        $translations = array_merge(self::$fallback, self::$translations);

        return json_encode(
            $translations,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        ) ?: '{}';
    }

    public static function loadFromSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            self::bootstrapLanguage();
            return;
        }

        $language = self::sessionLanguage();

        if ($language !== '') {
            self::setLanguage($language);
            return;
        }

        self::bootstrapLanguage();
    }

    public static function change(string $lang): void
    {
        self::setLanguage($lang);
    }

    public static function exists(string $key): bool
    {
        if (!self::$loaded) {
            self::bootstrapLanguage();
        }

        return array_key_exists($key, self::$translations) || array_key_exists($key, self::$fallback);
    }

    public static function raw(): array
    {
        if (!self::$loaded) {
            self::bootstrapLanguage();
        }

        return array_merge(self::$fallback, self::$translations);
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
        $translations = [];
        $languageFile = self::languageFile(self::$language);

        if ($languageFile === '') {
            $languageFile = self::languageFile(self::DEFAULT_LANGUAGE);
        }

        if ($languageFile !== '' && is_file($languageFile)) {
            $lang = [];
            include $languageFile;

            if (isset($lang) && is_array($lang)) {
                $translations = $lang;
            }
        }

        self::$translations = $translations;
        self::$loaded = true;
    }

    private static function languageFile(string $lang): string
    {
        $lang = self::sanitizeLanguage($lang);

        if ($lang === '') {
            return '';
        }

        $candidateDirs = [
            dirname(__DIR__) . '/WILang',
        ];

        if (defined('WI_PUBLIC_ROOT_DIR')) {
            $candidateDirs[] = rtrim((string) WI_PUBLIC_ROOT_DIR, '/\\') . '/WICore/WILang';
        }

        if (defined('ROOT_PATH')) {
            $candidateDirs[] = rtrim((string) ROOT_PATH, '/\\') . '/WICore/WILang';
        }

        foreach (array_unique($candidateDirs) as $dir) {
            $file = $dir . '/' . $lang . '.php';

            if (is_file($file)) {
                return $file;
            }
        }

        return '';
    }

    private static function settingsLanguage(): string
    {
        if (!class_exists('WISettings')) {
            return '';
        }

        try {
            $settings = new WISettings();

            if (method_exists($settings, 'website')) {
                $value = $settings->website('default_lang');

                if ($value === null || trim((string) $value) === '') {
                    $value = $settings->website('language');
                }

                return self::sanitizeLanguage((string) $value);
            }
        } catch (Throwable $e) {
            return '';
        }

        return '';
    }

    private static function sessionLanguage(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return '';
        }

        $language = $_SESSION['language'] ?? $_SESSION['lang'] ?? '';

        return self::sanitizeLanguage((string) $language);
    }

    private static function storeSessionLanguage(string $lang): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return;
        }

        $_SESSION['language'] = $lang;
        $_SESSION['lang'] = $lang;
    }

    private static function sanitizeLanguage(string $lang): string
    {
        $lang = strtolower(trim($lang));

        return preg_match('/^[a-z]{2}$/', $lang) ? $lang : '';
    }
}
