<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS / WICOS / WIKitchenCompli
| Class: WIUpload
| File: WIUpload.php
| Location: /WIAdmin/WICore/WIClass/WIUpload.php
| Type: Legacy Upload Adapter
| Layer: Compatibility
| Version: 2.0.0
| Created: 2026-05-01
| Last Updated: 2026-05-01
| Status: Compatibility
|--------------------------------------------------------------------------
|
| Purpose:
| - Keeps older WIUpload callers alive.
| - Routes uploaded files through canonical WIMedia.
| - Uses WIdb only for optional legacy compatibility updates.
|
| Rules:
| - No direct move_uploaded_file.
| - No hardcoded upload folders.
| - Uses $this->WIdb only.
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/WI.php';
require_once __DIR__ . '/WIMedia.php';

class WIUpload
{
    private WIdb $WIdb;

    private WIMedia $WIMedia;

    /**
     * Creates the legacy upload adapter.
     *
     * @param WIdb|null $WIdb Optional shared DB layer.
     * @param WIMedia|null $WIMedia Optional media façade.
     */
    public function __construct(?WIdb $WIdb = null, ?WIMedia $WIMedia = null)
    {
        $this->WIdb = $WIdb ?? WIdb::getInstance();
        $this->WIMedia = $WIMedia ?? new WIMedia($this->WIdb);
    }

    /**
     * Legacy upload method.
     *
     * Older callers used ImageFile and expected the header image to be updated.
     * The upload now goes through WIMedia and then optionally updates legacy
     * columns where they still exist.
     *
     * @param array<string, mixed> $data Optional context.
     *
     * @return array<string, mixed>
     */
    public function uploadFile(array $data = []): array
    {
        $file = $_FILES['ImageFile'] ?? $_FILES['file'] ?? $_FILES['media'] ?? null;

        if (!is_array($file)) {
            return [
                'success' => false,
                'status' => 'error',
                'message' => 'No upload file was provided.',
            ];
        }

        $context = array_merge([
            'system_code' => 'wicms',
            'entity_type' => $data['entity_type'] ?? 'header',
            'entity_id' => (int)($data['entity_id'] ?? $data['header_id'] ?? 1),
            'link_type' => $data['link_type'] ?? 'header_image',
            'folder' => $data['folder'] ?? 'headers',
            'title' => $data['title'] ?? '',
            'visibility' => $data['visibility'] ?? 'public',
            'access_scope' => $data['access_scope'] ?? 'global',
            'is_private' => (int)($data['is_private'] ?? 0),
            'uploaded_by_user_id' => $this->currentUserId(),
            'user_id' => $this->currentUserId(),
        ], $data);

        $result = $this->WIMedia->upload($file, $context);

        if (($result['success'] ?? false) !== true) {
            return $result;
        }

        $media = is_array($result['media'] ?? null) ? $result['media'] : [];
        $mediaId = (int)($result['media_id'] ?? $media['id'] ?? 0);

        $this->updateLegacyHeaderImage(
            (int)($context['entity_id'] ?? 1),
            (string)($media['file_url'] ?? $media['file_path'] ?? ''),
            $mediaId
        );

        return array_merge($result, [
            'legacy_updated' => true,
        ]);
    }

    /**
     * Updates old header table if it exists.
     *
     * @param int $headerId Header ID.
     * @param string $fileReference File URL/path.
     * @param int $mediaId Media ID.
     *
     * @return void
     */
    private function updateLegacyHeaderImage(int $headerId, string $fileReference, int $mediaId): void
    {
        if ($headerId <= 0 || $fileReference === '') {
            return;
        }

        if (!$this->tableExists('wi_header')) {
            return;
        }

        $data = [];

        if ($this->columnExists('wi_header', 'header_image')) {
            $data['header_image'] = $fileReference;
        }

        if ($mediaId > 0 && $this->columnExists('wi_header', 'header_media_id')) {
            $data['header_media_id'] = $mediaId;
        }

        if ($data === []) {
            return;
        }

        $this->WIdb->update(
            'wi_header',
            $data,
            '`header_id` = :header_id',
            [
                'header_id' => $headerId,
            ]
        );
    }

    /**
     * Checks table existence.
     *
     * @param string $table Table name.
     *
     * @return bool
     */
    private function tableExists(string $table): bool
    {
        if (method_exists($this->WIdb, 'tableExists')) {
            return (bool)$this->WIdb->tableExists($table);
        }

        $rows = $this->WIdb->select(
            "SELECT COUNT(*) AS `total`
             FROM `information_schema`.`TABLES`
             WHERE `TABLE_SCHEMA` = DATABASE()
               AND `TABLE_NAME` = :table_name",
            [
                'table_name' => $table,
            ]
        );

        return (int)($rows[0]['total'] ?? 0) > 0;
    }

    /**
     * Checks column existence.
     *
     * @param string $table Table name.
     * @param string $column Column name.
     *
     * @return bool
     */
    private function columnExists(string $table, string $column): bool
    {
        if (method_exists($this->WIdb, 'columnExists')) {
            return (bool)$this->WIdb->columnExists($table, $column);
        }

        $rows = $this->WIdb->select(
            "SELECT COUNT(*) AS `total`
             FROM `information_schema`.`COLUMNS`
             WHERE `TABLE_SCHEMA` = DATABASE()
               AND `TABLE_NAME` = :table_name
               AND `COLUMN_NAME` = :column_name",
            [
                'table_name' => $table,
                'column_name' => $column,
            ]
        );

        return (int)($rows[0]['total'] ?? 0) > 0;
    }

    /**
     * Returns current user ID if available.
     *
     * @return int|null
     */
    private function currentUserId(): ?int
    {
        if (class_exists('WISession')) {
            $userId = (int)WISession::get('user_id', 0);

            return $userId > 0 ? $userId : null;
        }

        if (isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id'])) {
            return (int)$_SESSION['user_id'];
        }

        if (isset($_SESSION['admin_id']) && is_numeric($_SESSION['admin_id'])) {
            return (int)$_SESSION['admin_id'];
        }

        return null;
    }
}