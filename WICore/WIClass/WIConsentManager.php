<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Product: WICMS
| File: WICore/WIClass/WIConsentManager.php
| Type: Core Consent / Legal Manager
| Layer: Core WICMS
| Status: Production foundation
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
| Free/open-source WICMS core consent manager. Handles public cookie banner
| settings, fixed consent categories, managed scripts, Google Consent Mode
| defaults/updates, and basic consent logging without touching Compliance.
*/

final class WIConsentManager
{
    private WIdb $db;

    /** @var array<string,mixed> */
    private array $defaults = [
        'id' => 1,
        'enabled' => 0,
        'banner_position' => 'bottom',
        'theme_mode' => 'match_site',
        'custom_primary_color' => '#2563eb',
        'custom_background_color' => '#ffffff',
        'custom_text_color' => '#111827',
        'show_accept_all' => 1,
        'show_reject_non_essential' => 1,
        'show_manage_choices' => 1,
        'show_save_preferences' => 1,
        'banner_title' => 'Your privacy choices',
        'banner_text' => 'We use cookies and similar technologies to keep the site secure, remember preferences, and improve the site. You can accept all, reject non-essential, or manage your choices.',
        'privacy_policy_url' => 'privacy.php',
        'cookie_policy_url' => 'cookies.php',
        'terms_url' => 'terms.php',
        'policy_version' => '1.0',
        'google_consent_enabled' => 0,
        'google_consent_mode' => 'basic',
        'google_default_denied' => 1,
        'gtm_enabled' => 0,
        'gtm_container_id' => '',
        'ga4_enabled' => 0,
        'ga4_measurement_id' => '',
        'marketing_remarketing_enabled' => 0,
    ];

    /** @var array<int,array<string,mixed>> */
    private array $categoryDefaults = [
        [
            'category_key' => 'essential',
            'label' => 'Strictly necessary',
            'description' => 'Required for login, security, sessions, CSRF protection and core site operation.',
            'is_required' => 1,
            'is_enabled' => 1,
            'sort_order' => 10,
        ],
        [
            'category_key' => 'preferences',
            'label' => 'Preferences',
            'description' => 'Remembers theme, accessibility, language and display choices.',
            'is_required' => 0,
            'is_enabled' => 1,
            'sort_order' => 20,
        ],
        [
            'category_key' => 'analytics',
            'label' => 'Analytics',
            'description' => 'Helps site owners understand how the website is used.',
            'is_required' => 0,
            'is_enabled' => 0,
            'sort_order' => 30,
        ],
        [
            'category_key' => 'marketing',
            'label' => 'Marketing',
            'description' => 'Advertising, remarketing, tracking pixels and ad personalisation.',
            'is_required' => 0,
            'is_enabled' => 0,
            'sort_order' => 40,
        ],
        [
            'category_key' => 'embedded',
            'label' => 'Embedded content',
            'description' => 'Videos, maps, social embeds and other third-party embedded content.',
            'is_required' => 0,
            'is_enabled' => 0,
            'sort_order' => 50,
        ],
    ];

    public function __construct()
    {
        $this->db = WIdb::getInstance();
    }

    /**
     * Create the core consent tables if they do not exist.
     */
    public function installSchema(): void
    {
        $statements = $this->schemaStatements();

        foreach ($statements as $sql) {
            $this->db->exec($sql);
        }

        $this->seedDefaults();
    }

    /**
     * @return array<string,mixed>
     */
    public function getSettings(): array
    {
        if (!$this->tableExists('wi_consent_settings')) {
            return $this->defaults;
        }

        $rows = $this->db->select('SELECT * FROM `wi_consent_settings` WHERE `id` = :id LIMIT 1', ['id' => 1]);
        $row = is_array($rows[0] ?? null) ? $rows[0] : [];

        if ($row === []) {
            return $this->defaults;
        }

        return array_merge($this->defaults, $row);
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function getCategories(): array
    {
        if (!$this->tableExists('wi_consent_categories')) {
            return $this->categoryDefaults;
        }

        $rows = $this->db->select(
            'SELECT * FROM `wi_consent_categories` ORDER BY `sort_order` ASC, `id` ASC',
            []
        );

        return $rows !== [] ? $rows : $this->categoryDefaults;
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function getScripts(): array
    {
        if (!$this->tableExists('wi_consent_scripts')) {
            return [];
        }

        return $this->db->select(
            'SELECT * FROM `wi_consent_scripts` WHERE `is_enabled` = :enabled ORDER BY `sort_order` ASC, `id` ASC',
            ['enabled' => 1]
        );
    }

    /**
     * @param array<string,mixed> $payload
     * @return array<string,mixed>
     */
    public function saveSettings(array $payload): array
    {
        $this->installSchema();

        $settings = isset($payload['settings']) && is_array($payload['settings'])
            ? $payload['settings']
            : $payload;

        $data = $this->normaliseSettings($settings);
        $exists = $this->db->select('SELECT `id` FROM `wi_consent_settings` WHERE `id` = :id LIMIT 1', ['id' => 1]);

        if ($exists === []) {
            $data['id'] = 1;
            $this->db->insert('wi_consent_settings', $data);
        } else {
            $data['updated_at'] = date('Y-m-d H:i:s');
            $this->db->update('wi_consent_settings', $data, '`id` = :id', ['id' => 1]);
        }

        $this->saveCategorySwitches(isset($payload['categories']) && is_array($payload['categories']) ? $payload['categories'] : []);
        $this->upsertGoogleScripts($data);

        return [
            'status' => 'success',
            'message' => 'Consent and legal settings saved.',
            'settings' => $this->getSettings(),
            'categories' => $this->getCategories(),
        ];
    }

    /**
     * @param array<string,mixed> $payload
     * @return array<string,mixed>
     */
    public function recordPublicConsent(array $payload): array
    {
        if (!$this->tableExists('wi_consent_logs')) {
            $this->installSchema();
        }

        $settings = $this->getSettings();
        $categories = $this->normaliseAcceptedCategories($payload['categories'] ?? []);
        $consentId = $this->safeConsentId((string) ($payload['consent_id'] ?? ''));

        if ($consentId === '') {
            $consentId = bin2hex(random_bytes(16));
        }

        $memberId = class_exists('WISession') ? (int) WISession::get('user_id', 0) : 0;
        $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? '');
        $ua = (string) ($_SERVER['HTTP_USER_AGENT'] ?? '');
        $salt = $this->logHashSalt();

        $this->db->insert('wi_consent_logs', [
            'consent_id' => $consentId,
            'member_id' => $memberId > 0 ? $memberId : null,
            'accepted_categories' => json_encode($categories, JSON_UNESCAPED_SLASHES),
            'policy_version' => (string) ($settings['policy_version'] ?? '1.0'),
            'ip_hash' => $ip !== '' ? hash('sha256', $salt . '|' . $ip) : null,
            'user_agent_hash' => $ua !== '' ? hash('sha256', $salt . '|' . $ua) : null,
            'source' => 'public_banner',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return [
            'status' => 'success',
            'message' => 'Consent preference saved.',
            'consent_id' => $consentId,
            'csrf_token' => class_exists('WIToken') ? WIToken::getToken('public_consent') : '',
        ];
    }

    public function renderPublicCssLink(): string
    {
        if (!$this->isEnabled()) {
            return '';
        }

        return '<link rel="stylesheet" href="WITheme/WICMS/public/css/wi-cookie-consent.css">' . PHP_EOL;
    }

    public function renderGoogleConsentDefaultScript(): string
    {
        $settings = $this->getSettings();

        if ((int) ($settings['enabled'] ?? 0) !== 1 || (int) ($settings['google_consent_enabled'] ?? 0) !== 1) {
            return '';
        }

        $defaultDenied = (int) ($settings['google_default_denied'] ?? 1) === 1;
        $analytics = $defaultDenied ? 'denied' : 'granted';
        $ads = $defaultDenied ? 'denied' : 'granted';

        $payload = [
            'ad_storage' => $ads,
            'analytics_storage' => $analytics,
            'ad_user_data' => $ads,
            'ad_personalization' => $ads,
            'functionality_storage' => 'granted',
            'personalization_storage' => $defaultDenied ? 'denied' : 'granted',
            'security_storage' => 'granted',
        ];

        return '<script>' . PHP_EOL
            . 'window.dataLayer = window.dataLayer || [];' . PHP_EOL
            . 'function gtag(){dataLayer.push(arguments);}' . PHP_EOL
            . 'gtag("consent", "default", ' . json_encode($payload, JSON_UNESCAPED_SLASHES) . ');' . PHP_EOL
            . '</script>' . PHP_EOL;
    }

    public function renderPublicJsLink(): string
    {
        if (!$this->isEnabled()) {
            return '';
        }

        return '<script src="WITheme/WICMS/public/js/wi-cookie-consent.js" defer></script>' . PHP_EOL;
    }

    public function renderFooterCookieLink(): string
    {
        if (!$this->isEnabled()) {
            return '';
        }

        return '<button type="button" class="wi-cookie-settings-link" data-wi-cookie-settings-open>Cookie settings</button>';
    }

    public function renderBanner(): string
    {
        $settings = $this->getSettings();

        if ((int) ($settings['enabled'] ?? 0) !== 1) {
            return '';
        }

        $categories = $this->getCategories();
        $scripts = $this->getScripts();
        $enabledCategories = [];

        foreach ($categories as $category) {
            $key = (string) ($category['category_key'] ?? '');
            if ($key === '') {
                continue;
            }

            $enabledCategories[$key] = [
                'key' => $key,
                'label' => (string) ($category['label'] ?? $key),
                'description' => (string) ($category['description'] ?? ''),
                'required' => (int) ($category['is_required'] ?? 0) === 1,
                'enabled' => (int) ($category['is_enabled'] ?? 0) === 1 || (int) ($category['is_required'] ?? 0) === 1,
            ];
        }

        $config = [
            'endpoint' => 'WICore/WIClass/WIAjax.php',
            'action' => 'public_consent_save',
            'csrf_token' => class_exists('WIToken') ? WIToken::getToken('public_consent') : '',
            'policy_version' => (string) ($settings['policy_version'] ?? '1.0'),
            'position' => (string) ($settings['banner_position'] ?? 'bottom'),
            'theme' => (string) ($settings['theme_mode'] ?? 'match_site'),
            'google' => [
                'enabled' => (int) ($settings['google_consent_enabled'] ?? 0) === 1,
                'mode' => (string) ($settings['google_consent_mode'] ?? 'basic'),
                'gtm_enabled' => (int) ($settings['gtm_enabled'] ?? 0) === 1,
                'gtm_container_id' => (string) ($settings['gtm_container_id'] ?? ''),
                'ga4_enabled' => (int) ($settings['ga4_enabled'] ?? 0) === 1,
                'ga4_measurement_id' => (string) ($settings['ga4_measurement_id'] ?? ''),
                'remarketing_enabled' => (int) ($settings['marketing_remarketing_enabled'] ?? 0) === 1,
            ],
            'categories' => $enabledCategories,
            'scripts' => $this->serialiseScripts($scripts),
        ];

        $style = '';
        if ((string) ($settings['theme_mode'] ?? '') === 'custom') {
            $style = sprintf(
                ' style="--wi-cookie-primary:%s;--wi-cookie-bg:%s;--wi-cookie-text:%s;"',
                $this->e((string) ($settings['custom_primary_color'] ?? '#2563eb')),
                $this->e((string) ($settings['custom_background_color'] ?? '#ffffff')),
                $this->e((string) ($settings['custom_text_color'] ?? '#111827'))
            );
        }

        $positionClass = $this->positionClass((string) ($settings['banner_position'] ?? 'bottom'));
        $themeClass = $this->themeClass((string) ($settings['theme_mode'] ?? 'match_site'));

        $html = '<section class="wi-cookie-consent ' . $this->e($positionClass) . ' ' . $this->e($themeClass) . '" data-wi-cookie-consent hidden' . $style . '>';
        $html .= '<script type="application/json" data-wi-cookie-config>' . json_encode($config, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . '</script>';
        $html .= '<div class="wi-cookie-card" role="region" aria-live="polite" aria-label="Cookie and privacy choices">';
        $html .= '<div class="wi-cookie-main">';
        $html .= '<h2>' . $this->e((string) ($settings['banner_title'] ?? 'Your privacy choices')) . '</h2>';
        $html .= '<p>' . $this->e((string) ($settings['banner_text'] ?? '')) . '</p>';
        $html .= '<div class="wi-cookie-policy-links">';
        $html .= $this->policyLink((string) ($settings['privacy_policy_url'] ?? ''), 'Privacy policy');
        $html .= $this->policyLink((string) ($settings['cookie_policy_url'] ?? ''), 'Cookie policy');
        $html .= $this->policyLink((string) ($settings['terms_url'] ?? ''), 'Terms');
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="wi-cookie-actions">';

        if ((int) ($settings['show_reject_non_essential'] ?? 1) === 1) {
            $html .= '<button type="button" class="wi-cookie-btn wi-cookie-btn--ghost" data-wi-cookie-reject>Reject non-essential</button>';
        }
        if ((int) ($settings['show_manage_choices'] ?? 1) === 1) {
            $html .= '<button type="button" class="wi-cookie-btn wi-cookie-btn--secondary" data-wi-cookie-manage>Manage choices</button>';
        }
        if ((int) ($settings['show_accept_all'] ?? 1) === 1) {
            $html .= '<button type="button" class="wi-cookie-btn wi-cookie-btn--primary" data-wi-cookie-accept>Accept all</button>';
        }

        $html .= '</div>';
        $html .= '</div>';
        $html .= $this->renderPreferencesModal();
        $html .= '</section>';

        return $html . PHP_EOL;
    }

    private function renderPreferencesModal(): string
    {
        if (!class_exists('WIModal')) {
            $modalClass = __DIR__ . '/WIModal.php';
            if (is_file($modalClass)) {
                require_once $modalClass;
            }
        }

        if (!class_exists('WIModal')) {
            return '<div class="wi-cookie-modal-missing">Cookie preferences are unavailable because WIModal could not be loaded.</div>';
        }

        ob_start();
        $modal = new WIModal();
        $modal->moduleModal('wi-consent-preferences', 'Cookie settings', 'WIConsentPublic', 'cookieConsentPreferences', '', '');
        return (string) ob_get_clean();
    }

    public function isEnabled(): bool
    {
        $settings = $this->getSettings();
        return (int) ($settings['enabled'] ?? 0) === 1;
    }

    /** @return array<int,string> */
    private function schemaStatements(): array
    {
        return [
            "CREATE TABLE IF NOT EXISTS `wi_consent_settings` (
                `id` int NOT NULL AUTO_INCREMENT,
                `enabled` tinyint(1) NOT NULL DEFAULT 0,
                `banner_position` varchar(40) NOT NULL DEFAULT 'bottom',
                `theme_mode` varchar(40) NOT NULL DEFAULT 'match_site',
                `custom_primary_color` varchar(30) NOT NULL DEFAULT '#2563eb',
                `custom_background_color` varchar(30) NOT NULL DEFAULT '#ffffff',
                `custom_text_color` varchar(30) NOT NULL DEFAULT '#111827',
                `show_accept_all` tinyint(1) NOT NULL DEFAULT 1,
                `show_reject_non_essential` tinyint(1) NOT NULL DEFAULT 1,
                `show_manage_choices` tinyint(1) NOT NULL DEFAULT 1,
                `show_save_preferences` tinyint(1) NOT NULL DEFAULT 1,
                `banner_title` varchar(180) NOT NULL DEFAULT 'Your privacy choices',
                `banner_text` text NULL,
                `privacy_policy_url` varchar(255) NOT NULL DEFAULT 'privacy.php',
                `cookie_policy_url` varchar(255) NOT NULL DEFAULT 'cookies.php',
                `terms_url` varchar(255) NOT NULL DEFAULT 'terms.php',
                `policy_version` varchar(50) NOT NULL DEFAULT '1.0',
                `google_consent_enabled` tinyint(1) NOT NULL DEFAULT 0,
                `google_consent_mode` varchar(30) NOT NULL DEFAULT 'basic',
                `google_default_denied` tinyint(1) NOT NULL DEFAULT 1,
                `gtm_enabled` tinyint(1) NOT NULL DEFAULT 0,
                `gtm_container_id` varchar(50) NOT NULL DEFAULT '',
                `ga4_enabled` tinyint(1) NOT NULL DEFAULT 0,
                `ga4_measurement_id` varchar(50) NOT NULL DEFAULT '',
                `marketing_remarketing_enabled` tinyint(1) NOT NULL DEFAULT 0,
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            "CREATE TABLE IF NOT EXISTS `wi_consent_categories` (
                `id` int NOT NULL AUTO_INCREMENT,
                `category_key` varchar(60) NOT NULL,
                `label` varchar(120) NOT NULL,
                `description` text NULL,
                `is_required` tinyint(1) NOT NULL DEFAULT 0,
                `is_enabled` tinyint(1) NOT NULL DEFAULT 0,
                `sort_order` int NOT NULL DEFAULT 100,
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uniq_wi_consent_category_key` (`category_key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            "CREATE TABLE IF NOT EXISTS `wi_consent_scripts` (
                `id` int NOT NULL AUTO_INCREMENT,
                `script_key` varchar(80) NOT NULL,
                `label` varchar(160) NOT NULL,
                `category_key` varchar(60) NOT NULL,
                `location` varchar(30) NOT NULL DEFAULT 'body_end',
                `script_type` varchar(30) NOT NULL DEFAULT 'external',
                `src` varchar(500) NULL,
                `inline_script` mediumtext NULL,
                `is_enabled` tinyint(1) NOT NULL DEFAULT 0,
                `sort_order` int NOT NULL DEFAULT 100,
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uniq_wi_consent_script_key` (`script_key`),
                KEY `idx_wi_consent_scripts_category` (`category_key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            "CREATE TABLE IF NOT EXISTS `wi_consent_logs` (
                `id` bigint NOT NULL AUTO_INCREMENT,
                `consent_id` varchar(80) NOT NULL,
                `member_id` int NULL,
                `accepted_categories` text NULL,
                `policy_version` varchar(50) NOT NULL DEFAULT '1.0',
                `ip_hash` varchar(80) NULL,
                `user_agent_hash` varchar(80) NULL,
                `source` varchar(60) NOT NULL DEFAULT 'public_banner',
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `idx_wi_consent_logs_consent_id` (`consent_id`),
                KEY `idx_wi_consent_logs_created_at` (`created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            "CREATE TABLE IF NOT EXISTS `wi_legal_pages` (
                `id` int NOT NULL AUTO_INCREMENT,
                `page_key` varchar(80) NOT NULL,
                `title` varchar(180) NOT NULL,
                `slug` varchar(180) NOT NULL,
                `content` mediumtext NULL,
                `version` varchar(50) NOT NULL DEFAULT '1.0',
                `is_published` tinyint(1) NOT NULL DEFAULT 0,
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uniq_wi_legal_page_key` (`page_key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        ];
    }

    private function seedDefaults(): void
    {
        if ($this->db->select('SELECT `id` FROM `wi_consent_settings` WHERE `id` = :id LIMIT 1', ['id' => 1]) === []) {
            $this->db->insert('wi_consent_settings', $this->defaults);
        }

        foreach ($this->categoryDefaults as $category) {
            $this->upsertByKey('wi_consent_categories', 'category_key', (string) $category['category_key'], $category);
        }

        $legalPages = [
            ['page_key' => 'privacy_policy', 'title' => 'Privacy Policy', 'slug' => 'privacy.php', 'content' => '', 'version' => '1.0', 'is_published' => 0],
            ['page_key' => 'cookie_policy', 'title' => 'Cookie Policy', 'slug' => 'cookies.php', 'content' => '', 'version' => '1.0', 'is_published' => 0],
            ['page_key' => 'terms', 'title' => 'Terms', 'slug' => 'terms.php', 'content' => '', 'version' => '1.0', 'is_published' => 0],
        ];

        foreach ($legalPages as $page) {
            $this->upsertByKey('wi_legal_pages', 'page_key', (string) $page['page_key'], $page, false);
        }
    }

    /** @param array<string,mixed> $data */
    private function upsertByKey(string $table, string $keyColumn, string $keyValue, array $data, bool $updateExisting = true): void
    {
        $row = $this->db->select(
            sprintf('SELECT `id` FROM `%s` WHERE `%s` = :key_value LIMIT 1', $table, $keyColumn),
            ['key_value' => $keyValue]
        );

        if ($row === []) {
            $this->db->insert($table, $data);
            return;
        }

        if ($updateExisting) {
            $data['updated_at'] = date('Y-m-d H:i:s');
            $this->db->update($table, $data, sprintf('`%s` = :key_value', $keyColumn), ['key_value' => $keyValue]);
        }
    }

    /** @param array<string,mixed> $settings @return array<string,mixed> */
    private function normaliseSettings(array $settings): array
    {
        $position = $this->choice((string) ($settings['banner_position'] ?? $this->defaults['banner_position']), [
            'top', 'bottom', 'floating_bottom_left', 'floating_bottom_right', 'modal',
        ], 'bottom');

        $theme = $this->choice((string) ($settings['theme_mode'] ?? $this->defaults['theme_mode']), [
            'match_site', 'light', 'dark', 'custom',
        ], 'match_site');

        $googleMode = $this->choice((string) ($settings['google_consent_mode'] ?? 'basic'), ['basic', 'advanced'], 'basic');

        return [
            'enabled' => $this->boolInt($settings['enabled'] ?? 0),
            'banner_position' => $position,
            'theme_mode' => $theme,
            'custom_primary_color' => $this->safeColour((string) ($settings['custom_primary_color'] ?? '#2563eb'), '#2563eb'),
            'custom_background_color' => $this->safeColour((string) ($settings['custom_background_color'] ?? '#ffffff'), '#ffffff'),
            'custom_text_color' => $this->safeColour((string) ($settings['custom_text_color'] ?? '#111827'), '#111827'),
            'show_accept_all' => $this->boolInt($settings['show_accept_all'] ?? 0),
            'show_reject_non_essential' => $this->boolInt($settings['show_reject_non_essential'] ?? 0),
            'show_manage_choices' => $this->boolInt($settings['show_manage_choices'] ?? 0),
            'show_save_preferences' => $this->boolInt($settings['show_save_preferences'] ?? 0),
            'banner_title' => $this->limit((string) ($settings['banner_title'] ?? $this->defaults['banner_title']), 180),
            'banner_text' => $this->limit((string) ($settings['banner_text'] ?? $this->defaults['banner_text']), 1200),
            'privacy_policy_url' => $this->safeUrlPath((string) ($settings['privacy_policy_url'] ?? 'privacy.php')),
            'cookie_policy_url' => $this->safeUrlPath((string) ($settings['cookie_policy_url'] ?? 'cookies.php')),
            'terms_url' => $this->safeUrlPath((string) ($settings['terms_url'] ?? 'terms.php')),
            'policy_version' => $this->safeVersion((string) ($settings['policy_version'] ?? '1.0')),
            'google_consent_enabled' => $this->boolInt($settings['google_consent_enabled'] ?? 0),
            'google_consent_mode' => $googleMode,
            'google_default_denied' => $this->boolInt($settings['google_default_denied'] ?? 1),
            'gtm_enabled' => $this->boolInt($settings['gtm_enabled'] ?? 0),
            'gtm_container_id' => $this->safeGoogleId((string) ($settings['gtm_container_id'] ?? '')),
            'ga4_enabled' => $this->boolInt($settings['ga4_enabled'] ?? 0),
            'ga4_measurement_id' => $this->safeGoogleId((string) ($settings['ga4_measurement_id'] ?? '')),
            'marketing_remarketing_enabled' => $this->boolInt($settings['marketing_remarketing_enabled'] ?? 0),
        ];
    }

    /** @param array<string,mixed> $switches */
    private function saveCategorySwitches(array $switches): void
    {
        foreach ($this->getCategories() as $category) {
            $key = (string) ($category['category_key'] ?? '');
            if ($key === '') {
                continue;
            }

            $required = (int) ($category['is_required'] ?? 0) === 1;
            $enabled = $required ? 1 : $this->boolInt($switches[$key] ?? 0);

            $this->db->update(
                'wi_consent_categories',
                ['is_enabled' => $enabled, 'updated_at' => date('Y-m-d H:i:s')],
                '`category_key` = :category_key',
                ['category_key' => $key]
            );
        }
    }

    /** @param array<string,mixed> $settings */
    private function upsertGoogleScripts(array $settings): void
    {
        $ga4 = (string) ($settings['ga4_measurement_id'] ?? '');
        $gtm = (string) ($settings['gtm_container_id'] ?? '');

        if ($ga4 !== '') {
            $this->upsertByKey('wi_consent_scripts', 'script_key', 'google_analytics_4', [
                'script_key' => 'google_analytics_4',
                'label' => 'Google Analytics 4',
                'category_key' => 'analytics',
                'location' => 'head',
                'script_type' => 'external',
                'src' => 'https://www.googletagmanager.com/gtag/js?id=' . $ga4,
                'inline_script' => '',
                'is_enabled' => ((int) ($settings['google_consent_enabled'] ?? 0) === 1 && (int) ($settings['ga4_enabled'] ?? 0) === 1) ? 1 : 0,
                'sort_order' => 10,
            ]);
        }

        if ($gtm !== '') {
            $this->upsertByKey('wi_consent_scripts', 'script_key', 'google_tag_manager', [
                'script_key' => 'google_tag_manager',
                'label' => 'Google Tag Manager',
                'category_key' => 'analytics',
                'location' => 'head',
                'script_type' => 'external',
                'src' => 'https://www.googletagmanager.com/gtm.js?id=' . $gtm,
                'inline_script' => '',
                'is_enabled' => ((int) ($settings['google_consent_enabled'] ?? 0) === 1 && (int) ($settings['gtm_enabled'] ?? 0) === 1) ? 1 : 0,
                'sort_order' => 20,
            ]);
        }
    }

    private function tableExists(string $table): bool
    {
        if (!$this->safeIdentifier($table)) {
            return false;
        }

        $rows = $this->db->select(
            'SELECT COUNT(*) AS count_value FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table_name',
            ['table_name' => $table]
        );

        return (int) ($rows[0]['count_value'] ?? 0) > 0;
    }

    private function safeIdentifier(string $value): bool
    {
        return (bool) preg_match('/^[A-Za-z0-9_]+$/', $value);
    }

    private function boolInt(mixed $value): int
    {
        if (is_bool($value)) {
            return $value ? 1 : 0;
        }

        return in_array(strtolower(trim((string) $value)), ['1', 'true', 'on', 'yes', 'granted'], true) ? 1 : 0;
    }

    /** @param array<int,string> $allowed */
    private function choice(string $value, array $allowed, string $default): string
    {
        $value = trim($value);
        return in_array($value, $allowed, true) ? $value : $default;
    }

    private function limit(string $value, int $max): string
    {
        $value = trim($value);
        return mb_substr($value, 0, $max, 'UTF-8');
    }

    private function safeColour(string $value, string $default): string
    {
        $value = trim($value);
        return preg_match('/^#[0-9A-Fa-f]{6}$/', $value) ? $value : $default;
    }

    private function safeVersion(string $value): string
    {
        $value = preg_replace('/[^A-Za-z0-9._-]/', '', trim($value)) ?? '';
        return $value !== '' ? mb_substr($value, 0, 50, 'UTF-8') : '1.0';
    }

    private function safeGoogleId(string $value): string
    {
        $value = strtoupper(trim($value));
        return preg_match('/^[A-Z0-9_-]{0,50}$/', $value) ? $value : '';
    }

    private function safeUrlPath(string $value): string
    {
        $value = trim($value);

        if ($value === '') {
            return '#';
        }

        if (preg_match('/^https?:\/\//i', $value)) {
            return mb_substr($value, 0, 255, 'UTF-8');
        }

        $value = preg_replace('/[^A-Za-z0-9_\-\.\/#\?=&]/', '', $value) ?? '#';
        return mb_substr($value, 0, 255, 'UTF-8');
    }

    /** @param mixed $value @return array<string,bool> */
    private function normaliseAcceptedCategories(mixed $value): array
    {
        $accepted = ['essential' => true];

        if (!is_array($value)) {
            return $accepted;
        }

        foreach ($this->getCategories() as $category) {
            $key = (string) ($category['category_key'] ?? '');
            if ($key === '') {
                continue;
            }

            $required = (int) ($category['is_required'] ?? 0) === 1;
            $accepted[$key] = $required || $this->boolInt($value[$key] ?? 0) === 1;
        }

        return $accepted;
    }

    /** @param array<int,array<string,mixed>> $scripts @return array<int,array<string,string|bool>> */
    private function serialiseScripts(array $scripts): array
    {
        $items = [];

        foreach ($scripts as $script) {
            $scriptKey = (string) ($script['script_key'] ?? '');

            if (in_array($scriptKey, ['google_analytics_4', 'google_tag_manager'], true)) {
                continue;
            }

            $items[] = [
                'key' => $scriptKey,
                'label' => (string) ($script['label'] ?? ''),
                'category' => (string) ($script['category_key'] ?? ''),
                'location' => (string) ($script['location'] ?? 'body_end'),
                'type' => (string) ($script['script_type'] ?? 'external'),
                'src' => (string) ($script['src'] ?? ''),
                'inline' => (string) ($script['inline_script'] ?? ''),
            ];
        }

        return $items;
    }

    private function safeConsentId(string $value): string
    {
        $value = preg_replace('/[^A-Za-z0-9_-]/', '', trim($value)) ?? '';
        return mb_substr($value, 0, 80, 'UTF-8');
    }

    private function logHashSalt(): string
    {
        if (defined('APP_KEY') && is_string(APP_KEY) && APP_KEY !== '') {
            return APP_KEY;
        }

        if (class_exists('WIConfig') && method_exists('WIConfig', 'get')) {
            return (string) WIConfig::get('APP_KEY', 'wicms-consent');
        }

        return 'wicms-consent';
    }

    private function policyLink(string $href, string $label): string
    {
        if (trim($href) === '' || $href === '#') {
            return '';
        }

        return '<a href="' . $this->e($href) . '">' . $this->e($label) . '</a>';
    }

    private function positionClass(string $position): string
    {
        return match ($position) {
            'top' => 'wi-cookie--top',
            'floating_bottom_left' => 'wi-cookie--floating-left',
            'floating_bottom_right' => 'wi-cookie--floating-right',
            'modal' => 'wi-cookie--modal',
            default => 'wi-cookie--bottom',
        };
    }

    private function themeClass(string $theme): string
    {
        return match ($theme) {
            'light' => 'wi-cookie--light',
            'dark' => 'wi-cookie--dark',
            'custom' => 'wi-cookie--custom',
            default => 'wi-cookie--match-site',
        };
    }

    private function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
