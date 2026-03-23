<?php

class WILang
{
    private WIdb $WIdb;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
    }

    public static function all(bool $jsonEncode = true)
    {
        $language = self::getLanguage();
        $WIdb = WIdb::getInstance();

        if (!self::isValidLanguage($language)) {
            return $jsonEncode ? json_encode([]) : [];
        }

        $sql = "SELECT keyword, translation FROM wi_trans WHERE lang = :lang";
        $query = $WIdb->prepare($sql);
        $query->bindParam(':lang', $language, PDO::PARAM_STR);
        $query->execute();

        $translations = [];

        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $translations[$row['keyword']] = $row['translation'];
        }

        return $jsonEncode
            ? json_encode($translations, JSON_UNESCAPED_UNICODE)
            : $translations;
    }

    public static function get(string $key): string
    {
        $language = self::getLanguage();
        $WIdb = WIdb::getInstance();

        $sql = "SELECT translation FROM wi_trans WHERE keyword = :key AND lang = :lang";
        $query = $WIdb->prepare($sql);
        $query->bindParam(':key', $key, PDO::PARAM_STR);
        $query->bindParam(':lang', $language, PDO::PARAM_STR);
        $query->execute();

        $res = $query->fetch(PDO::FETCH_ASSOC);

        return $res['translation'] ?? '';
    }

    public static function setLanguage(string $language): void
    {
        if (self::isValidLanguage($language)) {

            setcookie('wi_lang', $language, time() + 60 * 60 * 24 * 365, '/');

            WISession::set('wi_lang', $language);

            header('Location: ' . ($_SERVER['PHP_SELF'] ?? '/'));
            exit;
        }
    }

    public static function getLanguage(): string
    {
        if (
            isset($_COOKIE['wi_lang']) &&
            self::isValidLanguage($_COOKIE['wi_lang'])
        ) {
            return $_COOKIE['wi_lang'];
        }

        $site = new WISite();

        return WISession::get(
            'wi_lang',
            (string)$site->Website_Info('default_lang')
        );
    }

    private static function getFile(string $language): string
    {
        $WIdb = WIdb::getInstance();

        $sql = "SELECT lang FROM wi_lang WHERE lang = :lang";
        $query = $WIdb->prepare($sql);
        $query->bindParam(':lang', $language, PDO::PARAM_STR);
        $query->execute();

        $res = $query->fetch(PDO::FETCH_ASSOC);

        return $res['lang'] ?? '';
    }

    private static function isValidLanguage(string $lang): bool
    {
        return self::getFile($lang) !== '';
    }
}