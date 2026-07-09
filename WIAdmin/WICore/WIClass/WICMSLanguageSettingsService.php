<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| WICMS Language Settings Service
|--------------------------------------------------------------------------
| Core WICMS multilingual manager service. This keeps language settings,
| language records and basic translation records out of WIAjax so the AJAX
| file remains a router only.
*/

final class WICMSLanguageSettingsService
{
    private WIdb $db;

    /** @var array<string, mixed> */
    private array $siteSettings = [];

    public function __construct(?WIdb $db = null)
    {
        $this->db = $db ?: WIdb::getInstance();
        $this->ensureTables();
        $this->siteSettings = $this->loadSiteSettings();
    }

    /**
     * @return array<string, mixed>
     */
    public function dashboard(): array
    {
        $settings = $this->settings();
        $languages = $this->languages();

        return [
            'settings'     => $settings,
            'languages'    => $languages,
            'translations' => $this->translations('', '', 80),
            'health'       => $this->health($languages),
            'summary'      => $this->summary($languages),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function saveSettings(array $payload): array
    {
        $settings = [
            'enabled'               => $this->onOff($payload['enabled'] ?? 'off'),
            'public_switcher'       => $this->onOff($payload['public_switcher'] ?? 'off'),
            'admin_switcher'        => $this->onOff($payload['admin_switcher'] ?? 'off'),
            'default_public_lang'   => $this->languageCode((string) ($payload['default_public_lang'] ?? 'en'), 'en'),
            'default_admin_lang'    => $this->languageCode((string) ($payload['default_admin_lang'] ?? 'en'), 'en'),
            'fallback_lang'         => $this->languageCode((string) ($payload['fallback_lang'] ?? 'en'), 'en'),
            'translation_engine'    => $this->choice((string) ($payload['translation_engine'] ?? 'wilang'), ['wilang', 'google'], 'wilang'),
            'date_format'           => $this->safeText((string) ($payload['date_format'] ?? 'd/m/Y'), 30),
            'time_format'           => $this->safeText((string) ($payload['time_format'] ?? 'H:i'), 30),
            'number_format'         => $this->choice((string) ($payload['number_format'] ?? 'uk'), ['uk', 'us', 'eu'], 'uk'),
            'rtl_support'           => $this->onOff($payload['rtl_support'] ?? 'off'),
        ];

        foreach ($settings as $key => $value) {
            $this->setSetting($key, (string) $value);
        }

        $this->syncLegacySiteLanguage($settings);

        return $this->success('Multilingual settings saved.', [
            'settings' => $settings,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function saveLanguage(array $payload): array
    {
        $id = max(0, (int) ($payload['id'] ?? 0));
        $lang = $this->languageCode((string) ($payload['lang'] ?? ''), '');
        $name = $this->safeText((string) ($payload['name'] ?? ''), 120);
        $flag = $this->safeAssetName((string) ($payload['lang_flag'] ?? ''));
        $href = $this->safeHref((string) ($payload['href'] ?? ''));

        if ($lang === '' || $name === '') {
            return $this->error('Language code and name are required.');
        }

        if ($href === '') {
            $href = '?lang=' . $lang;
        }

        if ($flag === '') {
            $flag = $lang . '.png';
        }

        if ($id > 0) {
            $exists = $this->db->exists('wi_lang', '`id` = :id', ['id' => $id]);
            if (!$exists) {
                return $this->error('Language record could not be found.');
            }

            $this->db->update('wi_lang', [
                'lang'      => $lang,
                'name'      => $name,
                'lang_flag' => $flag,
                'href'      => $href,
            ], '`id` = :id', ['id' => $id]);

            return $this->success('Language updated.', ['language' => $this->languageByCode($lang)]);
        }

        if ($this->db->exists('wi_lang', '`lang` = :lang', ['lang' => $lang])) {
            return $this->error('That language code already exists.');
        }

        $this->db->insert('wi_lang', [
            'lang'      => $lang,
            'name'      => $name,
            'lang_flag' => $flag,
            'href'      => $href,
        ]);

        return $this->success('Language added.', ['language' => $this->languageByCode($lang)]);
    }

    /**
     * @return array<string, mixed>
     */
    public function deleteLanguage(int $id): array
    {
        $id = max(0, $id);

        if ($id <= 0) {
            return $this->error('No language selected.');
        }

        $row = $this->db->select('SELECT * FROM `wi_lang` WHERE `id` = :id LIMIT 1', ['id' => $id]);
        $language = $row[0] ?? [];

        if ($language === []) {
            return $this->error('Language record could not be found.');
        }

        $code = (string) ($language['lang'] ?? '');
        $settings = $this->settings();

        if ($code === 'en' || $code === (string) $settings['fallback_lang'] || $code === (string) $settings['default_public_lang'] || $code === (string) $settings['default_admin_lang']) {
            return $this->error('This language is protected because it is English, default, admin or fallback. Change defaults before deleting it.');
        }

        $this->db->delete('wi_lang', '`id` = :id', ['id' => $id]);

        return $this->success('Language deleted. Translation records were not deleted automatically.');
    }

    /**
     * @return array<string, mixed>
     */
    public function saveTranslation(array $payload): array
    {
        $id = max(0, (int) ($payload['id'] ?? 0));
        $lang = $this->languageCode((string) ($payload['lang'] ?? ''), '');
        $keyword = $this->safeKeyword((string) ($payload['keyword'] ?? ''));
        $translation = $this->safeTranslation((string) ($payload['translation'] ?? ''));

        if ($lang === '' || $keyword === '' || $translation === '') {
            return $this->error('Language, keyword and translation are required.');
        }

        if (!$this->db->exists('wi_lang', '`lang` = :lang', ['lang' => $lang])) {
            return $this->error('Selected language is not installed.');
        }

        if ($id > 0) {
            $this->db->update('wi_trans', [
                'lang'        => $lang,
                'keyword'     => $keyword,
                'translation' => $translation,
            ], '`id` = :id', ['id' => $id]);

            return $this->success('Translation updated.', ['translation' => $this->translationById($id)]);
        }

        $existing = $this->db->select(
            'SELECT `id` FROM `wi_trans` WHERE `lang` = :lang AND `keyword` = :keyword LIMIT 1',
            ['lang' => $lang, 'keyword' => $keyword]
        );

        if (isset($existing[0]['id'])) {
            $existingId = (int) $existing[0]['id'];
            $this->db->update('wi_trans', [
                'translation' => $translation,
            ], '`id` = :id', ['id' => $existingId]);

            return $this->success('Translation updated.', ['translation' => $this->translationById($existingId)]);
        }

        $this->db->insert('wi_trans', [
            'lang'        => $lang,
            'keyword'     => $keyword,
            'translation' => $translation,
        ]);

        return $this->success('Translation added.');
    }

    /**
     * @return array<string, mixed>
     */
    public function deleteTranslation(int $id): array
    {
        $id = max(0, $id);

        if ($id <= 0) {
            return $this->error('No translation selected.');
        }

        $this->db->delete('wi_trans', '`id` = :id', ['id' => $id]);

        return $this->success('Translation deleted.');
    }

    /**
     * @return array<string, string>
     */
    public function settings(): array
    {
        $defaults = [
            'enabled'             => (string) ($this->siteSettings['multi_lang'] ?? 'off'),
            'public_switcher'     => 'off',
            'admin_switcher'      => 'off',
            'default_public_lang' => (string) ($this->siteSettings['default_lang'] ?? 'en'),
            'default_admin_lang'  => (string) ($this->siteSettings['default_lang'] ?? 'en'),
            'fallback_lang'       => 'en',
            'translation_engine'  => (string) ($this->siteSettings['lang_choice'] ?? 'wilang'),
            'date_format'         => 'd/m/Y',
            'time_format'         => 'H:i',
            'number_format'       => 'uk',
            'rtl_support'         => 'off',
        ];

        if (!$this->db->tableExists('wi_language_settings')) {
            return $defaults;
        }

        $rows = $this->db->select('SELECT `setting_key`, `setting_value` FROM `wi_language_settings`');
        foreach ($rows as $row) {
            $key = (string) ($row['setting_key'] ?? '');
            if ($key !== '' && array_key_exists($key, $defaults)) {
                $defaults[$key] = (string) ($row['setting_value'] ?? $defaults[$key]);
            }
        }

        $defaults['enabled'] = $this->onOff($defaults['enabled']);
        $defaults['public_switcher'] = $this->onOff($defaults['public_switcher']);
        $defaults['admin_switcher'] = $this->onOff($defaults['admin_switcher']);
        $defaults['rtl_support'] = $this->onOff($defaults['rtl_support']);
        $defaults['default_public_lang'] = $this->languageCode($defaults['default_public_lang'], 'en');
        $defaults['default_admin_lang'] = $this->languageCode($defaults['default_admin_lang'], 'en');
        $defaults['fallback_lang'] = $this->languageCode($defaults['fallback_lang'], 'en');
        $defaults['translation_engine'] = $this->choice($defaults['translation_engine'], ['wilang', 'google'], 'wilang');
        $defaults['number_format'] = $this->choice($defaults['number_format'], ['uk', 'us', 'eu'], 'uk');

        return $defaults;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function languages(): array
    {
        if (!$this->db->tableExists('wi_lang')) {
            return [];
        }

        $languages = $this->db->select('SELECT * FROM `wi_lang` ORDER BY `name` ASC, `lang` ASC');
        $stats = $this->translationCounts();

        foreach ($languages as &$language) {
            $code = (string) ($language['lang'] ?? '');
            $language['translation_count'] = $stats[$code] ?? 0;
            $language['is_rtl'] = in_array($code, ['ar', 'he', 'fa', 'ur'], true);
        }
        unset($language);

        return $languages;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function translations(string $lang = '', string $query = '', int $limit = 80): array
    {
        if (!$this->db->tableExists('wi_trans')) {
            return [];
        }

        $where = [];
        $params = [];

        $lang = $this->languageCode($lang, '');
        if ($lang !== '') {
            $where[] = '`lang` = :lang';
            $params['lang'] = $lang;
        }

        $query = trim($query);
        if ($query !== '') {
            $where[] = '(`keyword` LIKE :q OR `translation` LIKE :q)';
            $params['q'] = '%' . $query . '%';
        }

        $sql = 'SELECT * FROM `wi_trans`';
        if ($where !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY `lang` ASC, `keyword` ASC LIMIT ' . max(10, min(200, $limit));

        return $this->db->select($sql, $params);
    }

    /**
     * @param array<int, array<string, mixed>> $languages
     * @return array<int, array<string, mixed>>
     */
    public function health(array $languages): array
    {
        $health = [];
        $root = dirname(__DIR__, 3);

        foreach ($languages as $language) {
            $code = (string) ($language['lang'] ?? '');
            if ($code === '') {
                continue;
            }

            $adminPath = $root . '/WIAdmin/WICore/WILang/' . $code . '.php';
            $publicPath = $root . '/WICore/WILang/' . $code . '.php';

            $health[] = [
                'lang'            => $code,
                'name'            => (string) ($language['name'] ?? $code),
                'admin_exists'    => is_file($adminPath),
                'admin_readable'  => is_readable($adminPath),
                'admin_format'    => $this->languageFileLooksValid($adminPath, 'admin'),
                'public_exists'   => is_file($publicPath),
                'public_readable' => is_readable($publicPath),
                'public_format'   => $this->languageFileLooksValid($publicPath, 'public'),
            ];
        }

        return $health;
    }

    /**
     * @param array<int, array<string, mixed>> $languages
     * @return array<string, mixed>
     */
    private function summary(array $languages): array
    {
        $settings = $this->settings();
        $translationTotal = 0;
        foreach ($languages as $language) {
            $translationTotal += (int) ($language['translation_count'] ?? 0);
        }

        return [
            'enabled'            => $settings['enabled'],
            'installed_count'    => count($languages),
            'translation_count'  => $translationTotal,
            'default_public'     => $settings['default_public_lang'],
            'default_admin'      => $settings['default_admin_lang'],
            'fallback'           => $settings['fallback_lang'],
        ];
    }

    /**
     * @return array<string, int>
     */
    private function translationCounts(): array
    {
        if (!$this->db->tableExists('wi_trans')) {
            return [];
        }

        $rows = $this->db->select('SELECT `lang`, COUNT(*) AS `count_value` FROM `wi_trans` GROUP BY `lang`');
        $counts = [];
        foreach ($rows as $row) {
            $counts[(string) ($row['lang'] ?? '')] = (int) ($row['count_value'] ?? 0);
        }

        return $counts;
    }

    /**
     * @return array<string, mixed>
     */
    private function languageByCode(string $code): array
    {
        $rows = $this->db->select('SELECT * FROM `wi_lang` WHERE `lang` = :lang LIMIT 1', ['lang' => $code]);
        return $rows[0] ?? [];
    }

    /**
     * @return array<string, mixed>
     */
    private function translationById(int $id): array
    {
        $rows = $this->db->select('SELECT * FROM `wi_trans` WHERE `id` = :id LIMIT 1', ['id' => $id]);
        return $rows[0] ?? [];
    }

    private function ensureTables(): void
    {
        if (!$this->db->tableExists('wi_language_settings')) {
            $this->db->exec(
                "CREATE TABLE IF NOT EXISTS `wi_language_settings` (
                    `id` int unsigned NOT NULL AUTO_INCREMENT,
                    `setting_key` varchar(100) NOT NULL,
                    `setting_value` text NULL,
                    `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `uk_wi_language_settings_key` (`setting_key`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
            );
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function loadSiteSettings(): array
    {
        if (!$this->db->tableExists('wi_site')) {
            return [];
        }

        $rows = $this->db->select('SELECT * FROM `wi_site` WHERE `id` = 1 LIMIT 1');
        return $rows[0] ?? [];
    }

    /**
     * @param array<string, string> $settings
     */
    private function syncLegacySiteLanguage(array $settings): void
    {
        if (!$this->db->tableExists('wi_site')) {
            return;
        }

        $data = [];
        if ($this->db->columnExists('wi_site', 'multi_lang')) {
            $data['multi_lang'] = $settings['enabled'];
        }
        if ($this->db->columnExists('wi_site', 'default_lang')) {
            $data['default_lang'] = $settings['default_public_lang'];
        }
        if ($this->db->columnExists('wi_site', 'lang_choice')) {
            $data['lang_choice'] = $settings['translation_engine'];
        }

        if ($data !== []) {
            $this->db->update('wi_site', $data, '`id` = :id', ['id' => 1]);
        }
    }

    private function setSetting(string $key, string $value): void
    {
        $exists = $this->db->exists('wi_language_settings', '`setting_key` = :setting_key', ['setting_key' => $key]);

        if ($exists) {
            $this->db->update('wi_language_settings', [
                'setting_value' => $value,
            ], '`setting_key` = :setting_key', ['setting_key' => $key]);
            return;
        }

        $this->db->insert('wi_language_settings', [
            'setting_key'   => $key,
            'setting_value' => $value,
        ]);
    }

    private function languageFileLooksValid(string $path, string $type): string
    {
        if (!is_file($path)) {
            return 'missing';
        }

        if (!is_readable($path)) {
            return 'not readable';
        }

        $content = (string) file_get_contents($path);

        if ($type === 'public') {
            return preg_match('/\$lang\s*=\s*\[|\$lang\s*=\s*array\s*\(/', $content) ? 'ok' : 'legacy / needs repair';
        }

        return preg_match('/return\s+array\s*\(|return\s+\[/', $content) ? 'ok' : 'legacy / needs review';
    }

    private function onOff(mixed $value): string
    {
        return (string) $value === 'on' ? 'on' : 'off';
    }

    /**
     * @param array<int, string> $allowed
     */
    private function choice(string $value, array $allowed, string $default): string
    {
        return in_array($value, $allowed, true) ? $value : $default;
    }

    private function languageCode(string $value, string $default): string
    {
        $value = strtolower(trim($value));
        return preg_match('/^[a-z]{2}$/', $value) ? $value : $default;
    }

    private function safeKeyword(string $value): string
    {
        $value = trim($value);
        if (!preg_match('/^[A-Za-z0-9_\.\-:]{1,120}$/', $value)) {
            return '';
        }
        return $value;
    }

    private function safeText(string $value, int $max): string
    {
        $value = trim(strip_tags($value));
        if (mb_strlen($value) > $max) {
            $value = mb_substr($value, 0, $max);
        }
        return $value;
    }

    private function safeTranslation(string $value): string
    {
        $value = trim($value);
        if (mb_strlen($value) > 500) {
            $value = mb_substr($value, 0, 500);
        }
        return $value;
    }

    private function safeAssetName(string $value): string
    {
        $value = trim($value);
        return preg_match('/^[A-Za-z0-9_\-\.]{1,160}$/', $value) ? $value : '';
    }

    private function safeHref(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }
        if (preg_match('/^\?lang=[a-z]{2}$/', $value)) {
            return $value;
        }
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        return '';
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function success(string $message, array $data = []): array
    {
        return [
            'success' => true,
            'status'  => 'success',
            'message' => $message,
            'data'    => $data,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function error(string $message): array
    {
        return [
            'success' => false,
            'status'  => 'error',
            'message' => $message,
            'data'    => [],
        ];
    }
}
