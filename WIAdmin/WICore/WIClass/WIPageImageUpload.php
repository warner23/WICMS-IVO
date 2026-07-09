<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS / WICOS / WIKitchenCompli
| Class: WIPageImageUpload
| File: PageImageUpload.php
| Location: /WIAdmin/WICore/WIClass/PageImageUpload.php
| Type: Legacy Upload Wrapper
| Layer: Compatibility
| Version: 2.0.0
| Created: 2026-05-01
| Last Updated: 2026-05-01
| Status: Compatibility
|--------------------------------------------------------------------------
|
| Purpose:
| - Keeps old page image upload entry points alive.
| - Routes page images through canonical WIMedia.
| - Links uploaded media to WICMS pages.
|
| Rules:
| - No direct upload handling.
| - No direct move_uploaded_file.
| - No direct DB connection.
| - Uses WIdb only.
| - Uses $this->WIdb only.
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/WI.php';
require_once __DIR__ . '/WIMedia.php';

final class WIPageImageUpload
{
    private WIdb $WIdb;

    private WIMedia $WIMedia;

    /**
     * Creates the page image upload compatibility wrapper.
     *
     * @param WIdb|null $WIdb Optional shared DB layer.
     * @param WIMedia|null $WIMedia Optional shared media façade.
     */
    public function __construct(?WIdb $WIdb = null, ?WIMedia $WIMedia = null)
    {
        $this->WIdb = $WIdb ?? WIdb::getInstance();
        $this->WIMedia = $WIMedia ?? new WIMedia($this->WIdb);
    }

    /**
     * Handles a legacy page image upload request.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->sendJsonHeaders();

        $file = $_FILES['file'] ??
            $_FILES['media'] ??
            $_FILES['page_image'] ??
            $_FILES['ImageFile'] ??
            null;

        if (!is_array($file)) {
            $this->json([
                'success' => false,
                'status' => 'error',
                'message' => 'No page image uploaded.',
            ], 400);
        }

        $result = $this->upload($file, $this->buildContext());

        $this->json($result, ($result['success'] ?? false) === true ? 200 : 400);
    }

    /**
     * Uploads a page image through WIMedia.
     *
     * @param array<string, mixed> $file File entry.
     * @param array<string, mixed> $context Upload context.
     *
     * @return array<string, mixed>
     */
    public function upload(array $file, array $context = []): array
    {
        $pageId = (int)($context['entity_id'] ?? $context['page_id'] ?? 0);

        $context = array_merge([
            'system_code' => 'wicms',
            'entity_type' => 'page',
            'entity_id' => $pageId,
            'link_type' => 'page_image',
            'folder' => 'pages',
            'title' => 'Page image',
            'visibility' => 'public',
            'access_scope' => 'global',
            'is_private' => 0,
            'is_sensitive' => 0,
            'uploaded_by_user_id' => $this->currentUserId(),
            'user_id' => $this->currentUserId(),
        ], $context);

        $result = $this->WIMedia->upload($file, $context);

        if (($result['success'] ?? false) !== true) {
            return $result;
        }

        $media = is_array($result['media'] ?? null) ? $result['media'] : [];
        $mediaId = (int)($result['media_id'] ?? $media['id'] ?? 0);
        $fileReference = (string)($media['file_url'] ?? $media['file_path'] ?? '');

        $this->updateLegacyPageImage(
            (int)($context['entity_id'] ?? 0),
            $fileReference,
            $mediaId
        );

        return array_merge($result, [
            'legacy_updated' => true,
            'page_image' => $fileReference,
            'page_media_id' => $mediaId,
            'name' => $fileReference,
            'url' => $fileReference,
            'path' => (string)($media['file_path'] ?? ''),
        ]);
    }

    /**
     * Updates legacy page image columns if they exist.
     *
     * @param int $pageId Page ID.
     * @param string $fileReference File URL/path.
     * @param int $mediaId Media ID.
     *
     * @return void
     */
    private function updateLegacyPageImage(int $pageId, string $fileReference, int $mediaId): void
    {
        if ($pageId <= 0 || $fileReference === '') {
            return;
        }

        $candidateTables = [
            'wi_pages',
            'wi_page',
            'wi_content_pages',
        ];

        foreach ($candidateTables as $table) {
            if (!$this->tableExists($table)) {
                continue;
            }

            $primaryKey = $this->firstPrimaryKeyColumn($table);
            $data = [];

            foreach (['page_image', 'image', 'featured_image', 'header_image', 'thumbnail'] as $column) {
                if ($this->columnExists($table, $column)) {
                    $data[$column] = $fileReference;
                    break;
                }
            }

            foreach (['page_media_id', 'featured_media_id', 'media_id'] as $column) {
                if ($mediaId > 0 && $this->columnExists($table, $column)) {
                    $data[$column] = $mediaId;
                    break;
                }
            }

            if ($data === [] || $primaryKey === null) {
                continue;
            }

            $this->WIdb->update(
                $table,
                $data,
                "`{$primaryKey}` = :id",
                [
                    'id' => $pageId,
                ]
            );

            return;
        }
    }

    /**
     * Builds upload context from request values.
     *
     * @return array<string, mixed>
     */
    private function buildContext(): array
    {
        $pageId = $this->int('entity_id', $this->int('page_id', $this->int('id', 0)));

        return [
            'system_code' => $this->string('system_code', 'wicms'),
            'entity_type' => $this->string('entity_type', 'page'),
            'entity_id' => $pageId,
            'link_type' => $this->string('link_type', 'page_image'),
            'folder' => $this->string('folder', 'pages'),
            'title' => $this->string('title', 'Page image'),
            'visibility' => $this->string('visibility', 'public'),
            'access_scope' => $this->string('access_scope', 'global'),
            'is_private' => $this->int('is_private', 0),
            'is_sensitive' => $this->int('is_sensitive', 0),
            'uploaded_by_user_id' => $this->currentUserId(),
            'user_id' => $this->currentUserId(),
        ];
    }

    /**
     * Finds first primary key column.
     *
     * @param string $table Table name.
     *
     * @return string|null
     */
    private function firstPrimaryKeyColumn(string $table): ?string
    {
        $rows = $this->WIdb->select(
            "SELECT `COLUMN_NAME`
             FROM `information_schema`.`COLUMNS`
             WHERE `TABLE_SCHEMA` = DATABASE()
               AND `TABLE_NAME` = :table_name
               AND `COLUMN_KEY` = 'PRI'
             ORDER BY `ORDINAL_POSITION` ASC
             LIMIT 1",
            [
                'table_name' => $table,
            ]
        );

        $column = trim((string)($rows[0]['COLUMN_NAME'] ?? ''));

        return $column !== '' ? $column : null;
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
     * Sends JSON headers.
     *
     * @return void
     */
    private function sendJsonHeaders(): void
    {
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
        }
    }

    /**
     * Emits JSON and exits.
     *
     * @param array<string, mixed> $payload Payload.
     * @param int $statusCode HTTP status code.
     *
     * @return never
     */
    private function json(array $payload, int $statusCode = 200): never
    {
        http_response_code($statusCode);
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Reads a string request value.
     *
     * @param string $key Key.
     * @param string|null $default Default.
     *
     * @return string|null
     */
    private function string(string $key, ?string $default = null): ?string
    {
        $source = $_POST + $_GET;

        if (!array_key_exists($key, $source)) {
            return $default;
        }

        $value = trim((string)$source[$key]);

        return $value === '' ? $default : $value;
    }

    /**
     * Reads an integer request value.
     *
     * @param string $key Key.
     * @param int|null $default Default.
     *
     * @return int|null
     */
    private function int(string $key, ?int $default = null): ?int
    {
        $source = $_POST + $_GET;

        if (!array_key_exists($key, $source)) {
            return $default;
        }

        return is_numeric($source[$key]) ? (int)$source[$key] : $default;
    }

    /**
     * Returns current user ID.
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

/*
|--------------------------------------------------------------------------
| Backward Compatibility Alias
|--------------------------------------------------------------------------
*/
if (!class_exists('PageImageUpload', false)) {
    class_alias('WIPageImageUpload', 'PageImageUpload');
}

/*
|--------------------------------------------------------------------------
| Direct legacy endpoint support
|--------------------------------------------------------------------------
*/
if (basename((string)($_SERVER['SCRIPT_FILENAME'] ?? '')) === basename(__FILE__)) {
    if (session_status() === PHP_SESSION_NONE && class_exists('WISession')) {
        WISession::startSession();
    }

    $wrapper = new WIPageImageUpload();
    $wrapper->handle();
}