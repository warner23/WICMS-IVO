<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| WICMS Header / Footer Settings Service
|--------------------------------------------------------------------------
| Core WICMS admin service for public presentation settings.
| - Owns header/logo/favicon/footer settings persistence
| - Uses the modern WIMedia upload service for files
| - Keeps Compliance evidence/media separate from WICMS public assets
| - Contains no UI rendering and no Compliance dependencies
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/WIMedia.php';

final class WIHeaderFooterSettingsService
{
    private WIdb $db;

    public function __construct(?WIdb $db = null)
    {
        $this->db = $db instanceof WIdb ? $db : WIdb::getInstance();
        $this->ensureCoreMediaFolders();
    }

    /**
     * Returns all presentation data needed by the admin page.
     *
     * @return array<string, mixed>
     */
    public function pageData(): array
    {
        $header = $this->headerRow();
        $footer = $this->footerRow();
        $site = $this->siteRow();

        return [
            'header' => [
                'logo' => (string)($header['logo'] ?? ''),
                'logo_url' => $this->adminAssetUrl((string)($header['logo'] ?? ''), 'header'),
                'header_image' => (string)($header['header_image'] ?? ''),
                'header_image_url' => $this->adminAssetUrl((string)($header['header_image'] ?? ''), 'header'),
                'bk_header_image' => (string)($header['bk_header_image'] ?? ''),
                'header_content' => (string)($header['header_content'] ?? ''),
                'header_slogan' => (string)($header['header_slogan'] ?? ''),
            ],
            'footer' => [
                'website_name' => (string)($footer['website_name'] ?? ''),
                'footer_content' => (string)($footer['footer_content'] ?? ''),
                'footer_linking' => (string)($footer['footer_linking'] ?? ''),
            ],
            'favicon' => [
                'favicon' => (string)($site['favicon'] ?? ''),
                'favicon_url' => $this->adminAssetUrl((string)($site['favicon'] ?? ''), 'favicon'),
            ],
            'tokens' => $this->freshTokens(),
        ];
    }

    /**
     * Saves header text/footer settings from the modern form.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function save(array $payload): array
    {
        $section = $this->string($payload['section'] ?? 'all');

        if ($section === 'header') {
            return $this->withTokens($this->saveHeaderText($payload));
        }

        if ($section === 'footer') {
            return $this->withTokens($this->saveFooter($payload));
        }

        $header = $this->saveHeaderText($payload, false);
        $footer = $this->saveFooter($payload, false);

        if (($header['success'] ?? false) !== true) {
            return $this->withTokens($header);
        }

        if (($footer['success'] ?? false) !== true) {
            return $this->withTokens($footer);
        }

        return $this->withTokens($this->success('Header and footer settings saved successfully.', $this->pageData()));
    }

    /**
     * Supports the old header_settings action while the UI is being cleaned.
     *
     * @param array<string, mixed> $legacySettings
     * @return array<string, mixed>
     */
    public function saveLegacyHeader(array $legacySettings): array
    {
        $data = $legacySettings['UserData'] ?? $legacySettings;
        if (!is_array($data)) {
            return $this->withTokens($this->error('Invalid header settings.'));
        }

        return $this->withTokens($this->saveHeaderText($data));
    }

    /**
     * Supports the old footer_settings action while the UI is being cleaned.
     *
     * @param array<string, mixed> $legacySettings
     * @return array<string, mixed>
     */
    public function saveLegacyFooter(array $legacySettings): array
    {
        $data = $legacySettings['UserData'] ?? $legacySettings;
        if (!is_array($data)) {
            return $this->withTokens($this->error('Invalid footer settings.'));
        }

        return $this->withTokens($this->saveFooter($data));
    }

    /**
     * Uploads a logo/header image/favicon through modern WIMedia and updates the matching core setting.
     *
     * @param array<string, mixed> $payload
     * @param array<string, mixed> $files
     * @return array<string, mixed>
     */
    public function uploadAsset(array $payload, array $files): array
    {
        $assetType = $this->string($payload['asset_type'] ?? '');
        $file = $files['file'] ?? $files['media'] ?? null;

        if (!is_array($file)) {
            return $this->withTokens($this->error('No file was uploaded.'));
        }

        $allowed = [
            'header_logo' => [
                'folder' => 'wicms/header',
                'entity_type' => 'site_header',
                'link_type' => 'header_logo',
                'table' => 'wi_header',
                'column' => 'logo',
                'where' => '`header_id` = :id',
                'bind' => ['id' => 1],
                'label' => 'Header logo',
            ],
            'header_image' => [
                'folder' => 'wicms/header',
                'entity_type' => 'site_header',
                'link_type' => 'header_image',
                'table' => 'wi_header',
                'column' => 'header_image',
                'where' => '`header_id` = :id',
                'bind' => ['id' => 1],
                'label' => 'Header image',
            ],
            'favicon' => [
                'folder' => 'wicms/favicon',
                'entity_type' => 'site_favicon',
                'link_type' => 'favicon',
                'table' => 'wi_site',
                'column' => 'favicon',
                'where' => '`id` = :id',
                'bind' => ['id' => 1],
                'label' => 'Favicon',
            ],
        ];

        if (!isset($allowed[$assetType])) {
            return $this->withTokens($this->error('Unsupported header/footer asset type.'));
        }

        $target = $allowed[$assetType];
        $media = new WIMedia($this->db);

        $result = $media->upload($file, [
            'system_code' => 'wicms',
            'entity_type' => $target['entity_type'],
            'entity_id' => 1,
            'link_type' => $target['link_type'],
            'folder' => $target['folder'],
            'visibility' => 'public',
            'access_scope' => 'public',
            'is_private' => 0,
            'is_sensitive' => 0,
            'status' => 1,
            'title' => $target['label'],
            'alt_text' => $target['label'],
            'user_id' => (int)WISession::get('user_id', 0),
            'uploaded_by_user_id' => (int)WISession::get('user_id', 0),
            'created_by_user_id' => (int)WISession::get('user_id', 0),
        ]);

        if (($result['success'] ?? false) !== true) {
            return $this->withTokens($this->normaliseFailure($result, 'Upload failed.'));
        }

        $mediaRow = is_array($result['media'] ?? null) ? $result['media'] : [];
        $filePath = (string)($mediaRow['file_path'] ?? '');

        if ($filePath === '') {
            return $this->withTokens($this->error('Upload completed, but no stored file path was returned.'));
        }

        $this->ensureSeedRows();

        $ok = $this->db->update(
            (string)$target['table'],
            [(string)$target['column'] => $filePath],
            (string)$target['where'],
            (array)$target['bind']
        );

        if (!$ok) {
            return $this->withTokens($this->error('File uploaded, but the site setting could not be updated.'));
        }

        return $this->withTokens($this->success($target['label'] . ' updated successfully.', [
            'asset_type' => $assetType,
            'path' => $filePath,
            'url' => $this->adminAssetUrl($filePath, $assetType === 'favicon' ? 'favicon' : 'header'),
            'media' => $mediaRow,
        ]));
    }

    /**
     * Resolves a stored asset value for use from inside /WIAdmin/*.php pages.
     */
    public function adminAssetUrl(string $value, string $legacyType = 'header'): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }

        if (preg_match('#^https?://#i', $value) === 1) {
            return $value;
        }

        if (str_starts_with($value, '/')) {
            return $value;
        }

        if (str_starts_with($value, 'WIAdmin/')) {
            return '../' . $value;
        }

        if (str_starts_with($value, 'WIMedia/')) {
            return $value;
        }

        $legacyFolder = $legacyType === 'favicon' ? 'favicon' : 'header';
        return 'WIMedia/Img/' . $legacyFolder . '/' . rawurlencode($value);
    }

    private function saveHeaderText(array $payload, bool $wrapTokens = true): array
    {
        $this->ensureSeedRows();

        $data = [
            'header_content' => $this->limitedString($payload['header_content'] ?? '', 255),
            'header_slogan' => $this->limitedString($payload['header_slogan'] ?? '', 255),
        ];

        $ok = $this->db->update('wi_header', $data, '`header_id` = :id', ['id' => 1]);
        $result = $ok
            ? $this->success('Header settings saved successfully.', $this->pageData())
            : $this->error('Failed to save header settings.');

        return $wrapTokens ? $this->withTokens($result) : $result;
    }

    private function saveFooter(array $payload, bool $wrapTokens = true): array
    {
        $this->ensureSeedRows();

        $data = [
            'website_name' => $this->limitedString($payload['website_name'] ?? '', 255),
            'footer_content' => $this->limitedString($payload['footer_content'] ?? '', 255),
            'footer_linking' => $this->limitedString($payload['footer_linking'] ?? '', 255),
        ];

        $ok = $this->db->update('wi_footer', $data, '`footer_id` = :id', ['id' => 1]);
        $result = $ok
            ? $this->success('Footer settings saved successfully.', $this->pageData())
            : $this->error('Failed to save footer settings.');

        return $wrapTokens ? $this->withTokens($result) : $result;
    }

    /** @return array<string, mixed> */
    private function headerRow(): array
    {
        $this->ensureSeedRows();
        $rows = $this->db->select('SELECT * FROM `wi_header` WHERE `header_id` = :id LIMIT 1', ['id' => 1]);
        return is_array($rows[0] ?? null) ? $rows[0] : [];
    }

    /** @return array<string, mixed> */
    private function footerRow(): array
    {
        $this->ensureSeedRows();
        $rows = $this->db->select('SELECT * FROM `wi_footer` WHERE `footer_id` = :id LIMIT 1', ['id' => 1]);
        return is_array($rows[0] ?? null) ? $rows[0] : [];
    }

    /** @return array<string, mixed> */
    private function siteRow(): array
    {
        $this->ensureSeedRows();
        $rows = $this->db->select('SELECT * FROM `wi_site` WHERE `id` = :id LIMIT 1', ['id' => 1]);
        return is_array($rows[0] ?? null) ? $rows[0] : [];
    }

    private function ensureSeedRows(): void
    {
        $header = $this->db->select('SELECT `header_id` FROM `wi_header` WHERE `header_id` = :id LIMIT 1', ['id' => 1]);
        if (!isset($header[0])) {
            $this->db->insert('wi_header', [
                'header_id' => 1,
                'logo' => '',
                'bk_header_image' => '',
                'header_image' => '',
                'header_content' => '',
                'header_slogan' => '',
            ]);
        }

        $footer = $this->db->select('SELECT `footer_id` FROM `wi_footer` WHERE `footer_id` = :id LIMIT 1', ['id' => 1]);
        if (!isset($footer[0])) {
            $this->db->insert('wi_footer', [
                'footer_id' => 1,
                'footer_content' => '',
                'footer_linking' => '',
                'website_name' => 'WICMS',
            ]);
        }
    }

    private function ensureCoreMediaFolders(): void
    {
        $root = defined('ROOT_PATH')
            ? rtrim((string)ROOT_PATH, DIRECTORY_SEPARATOR)
            : dirname(__DIR__, 4);

        $folders = [
            $root . DIRECTORY_SEPARATOR . 'WIAdmin' . DIRECTORY_SEPARATOR . 'WIMedia' . DIRECTORY_SEPARATOR . 'Images' . DIRECTORY_SEPARATOR . 'wicms',
            $root . DIRECTORY_SEPARATOR . 'WIAdmin' . DIRECTORY_SEPARATOR . 'WIMedia' . DIRECTORY_SEPARATOR . 'Images' . DIRECTORY_SEPARATOR . 'wicms' . DIRECTORY_SEPARATOR . 'header',
            $root . DIRECTORY_SEPARATOR . 'WIAdmin' . DIRECTORY_SEPARATOR . 'WIMedia' . DIRECTORY_SEPARATOR . 'Images' . DIRECTORY_SEPARATOR . 'wicms' . DIRECTORY_SEPARATOR . 'favicon',
            $root . DIRECTORY_SEPARATOR . 'WIAdmin' . DIRECTORY_SEPARATOR . 'WIMedia' . DIRECTORY_SEPARATOR . 'Images' . DIRECTORY_SEPARATOR . 'compliance',
        ];

        foreach ($folders as $folder) {
            if (!is_dir($folder)) {
                @mkdir($folder, 0755, true);
            }
        }
    }

    /** @return array<string, string> */
    private function freshTokens(): array
    {
        if (!class_exists('WIToken')) {
            return [];
        }

        return [
            'save' => WIToken::getToken('wicms_header_footer_save'),
            'upload' => WIToken::getToken('wicms_header_footer_upload'),
            'legacy_header' => WIToken::getToken('header_settings'),
            'legacy_footer' => WIToken::getToken('footer_settings'),
        ];
    }

    /** @param array<string, mixed> $payload @return array<string, mixed> */
    private function withTokens(array $payload): array
    {
        $payload['csrf_tokens'] = $this->freshTokens();
        if (!isset($payload['data']) || !is_array($payload['data'])) {
            $payload['data'] = [];
        }
        $payload['data']['csrf_tokens'] = $payload['csrf_tokens'];
        return $payload;
    }

    /** @param array<string, mixed> $result @return array<string, mixed> */
    private function normaliseFailure(array $result, string $fallback): array
    {
        return [
            'success' => false,
            'status' => 'error',
            'message' => (string)($result['message'] ?? $result['msg'] ?? $fallback),
            'data' => $result['data'] ?? [],
        ];
    }

    /** @return array<string, mixed> */
    private function success(string $message, array $data = []): array
    {
        return [
            'success' => true,
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ];
    }

    /** @return array<string, mixed> */
    private function error(string $message): array
    {
        return [
            'success' => false,
            'status' => 'error',
            'message' => $message,
            'data' => [],
        ];
    }

    private function string(mixed $value): string
    {
        return trim((string)$value);
    }

    private function limitedString(mixed $value, int $max): string
    {
        return mb_substr(trim((string)$value), 0, $max);
    }
}
