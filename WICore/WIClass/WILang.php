<?php
declare(strict_types=1);

/**
 * Language Class
 * Created by Warner Infinity
 * Author Jules Warner
 */

#[\AllowDynamicProperties]
class WILang
{
    private static string $language = 'en';
    private static array $translations = [];

    public static function setLanguage(string $lang): void
    {
        $lang = strtolower(trim($lang));

        if (!preg_match('/^[a-z]{2}$/', $lang)) {
            return;
        }

        self::$language = $lang;
        self::loadLanguage();
    }

    public static function getLanguage(): string
    {
        return self::$language;
    }

    private static function loadLanguage(): void
    {
        $file = dirname(__DIR__) . "/WILang/" . self::$language . ".php";

        if (!file_exists($file)) {
            $file = dirname(__DIR__) . "/WILang/en.php";

            if (!file_exists($file)) {
                self::$translations = [];
                return;
            }
        }

        $lang = [];
        include $file;

        if (isset($lang) && is_array($lang)) {
            self::$translations = $lang;
        } else {
            self::$translations = [];
        }
    }

    public static function get(string $key): string
    {
        if (empty(self::$translations)) {
            self::loadLanguage();
        }

        if (isset(self::$translations[$key])) {
            return (string)self::$translations[$key];
        }

        return $key;
    }

    public static function e(string $key): string
    {
        return htmlspecialchars(self::get($key), ENT_QUOTES, 'UTF-8');
    }

    public static function all(): string
    {
        if (empty(self::$translations)) {
            self::loadLanguage();
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

        if (isset($_SESSION['language'])) {
            self::setLanguage((string)$_SESSION['language']);
        }
    }

    public static function change(string $lang): void
    {
        $lang = strtolower(trim($lang));

        if (!preg_match('/^[a-z]{2}$/', $lang)) {
            return;
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['language'] = $lang;
        }

        self::setLanguage($lang);
    }
}
?>