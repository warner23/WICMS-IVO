<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS / WICOS / WIKitchenCompli
| Class: WIMediaImageUpload
| File: MediaImageUpload.php
| Location: /WIAdmin/WICore/WIClass/MediaImageUpload.php
| Type: Legacy Upload Wrapper
| Layer: Compatibility
| Version: 2.0.0
| Created: 2026-05-01
| Last Updated: 2026-05-01
| Status: Compatibility
|--------------------------------------------------------------------------
|
| Purpose:
| - Replaces the old mixed image/video standalone upload script.
| - Routes files through canonical WIMedia.
| - Preserves simple JSON response fields expected by older callers.
|
| Rules:
| - No direct move_uploaded_file here.
| - No direct storage paths here.
| - No separate media logic.
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/WI.php';
require_once __DIR__ . '/WIMedia.php';

final class WIMediaImageUpload
{
    private WIMedia $WIMedia;

    /**
     * Creates the compatibility uploader.
     *
     * @param WIMedia|null $WIMedia Optional shared media façade.
     */
    public function __construct(?WIMedia $WIMedia = null)
    {
        $this->WIMedia = $WIMedia ?? new WIMedia(WIdb::getInstance());
    }

    /**
     * Handles legacy image/media upload.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->sendJsonHeaders();

        $file = $_FILES['file'] ?? $_FILES['media'] ?? null;

        if (!is_array($file)) {
            $this->json([
                'success' => false,
                'status' => 'error',
                'message' => 'No file uploaded.',
                'error' => 'No file uploaded.',
            ], 400);
        }

        $result = $this->upload($file, $this->buildContext());

        $this->json($result, ($result['success'] ?? false) === true ? 200 : 400);
    }

    /**
     * Uploads using WIMedia and returns legacy-compatible output.
     *
     * @param array<string, mixed> $file File entry.
     * @param array<string, mixed> $context Context.
     *
     * @return array<string, mixed>
     */
    public function upload(array $file, array $context = []): array
    {
        $result = $this->WIMedia->upload($file, array_merge([
            'system_code' => 'wicms',
            'folder' => 'legacy-media',
            'visibility' => 'private',
            'access_scope' => 'business',
            'is_private' => 1,
        ], $context));

        $media = is_array($result['media'] ?? null) ? $result['media'] : [];

        if (($result['success'] ?? false) !== true) {
            return array_merge($result, [
                'error' => $result['message'] ?? 'Upload failed.',
            ]);
        }

        return array_merge($result, [
            'name' => $media['file_url'] ?? $media['file_path'] ?? '',
            'id' => $media['stored_name'] ?? $media['id'] ?? '',
            'media_id' => $media['id'] ?? $result['media_id'] ?? 0,
            'url' => $media['file_url'] ?? '',
            'path' => $media['file_path'] ?? '',
            'error' => UPLOAD_ERR_OK,
        ]);
    }

    /**
     * Builds context from request.
     *
     * @return array<string, mixed>
     */
    private function buildContext(): array
    {
        return [
            'system_code' => $this->string('system_code', 'wicms'),
            'entity_type' => $this->string('entity_type'),
            'entity_id' => $this->int('entity_id'),
            'link_type' => $this->string('link_type'),
            'org_business_id' => $this->int('org_business_id', $this->int('business_id')),
            'org_site_id' => $this->int('org_site_id', $this->int('site_id')),
            'org_department_id' => $this->int('org_department_id', $this->int('department_id')),
            'folder' => $this->string('folder', 'legacy-media'),
            'title' => $this->string('title'),
            'alt_text' => $this->string('alt_text'),
            'caption' => $this->string('caption'),
            'description' => $this->string('description'),
            'visibility' => $this->string('visibility', 'private'),
            'access_scope' => $this->string('access_scope', 'business'),
            'is_private' => $this->int('is_private', 1),
            'is_sensitive' => $this->int('is_sensitive', 0),
            'uploaded_by_user_id' => $this->currentUserId(),
            'user_id' => $this->currentUserId(),
        ];
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
     * Reads a string value from request.
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
     * Reads an integer value from request.
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

/*
|--------------------------------------------------------------------------
| Direct legacy endpoint support
|--------------------------------------------------------------------------
*/
if (basename((string)($_SERVER['SCRIPT_FILENAME'] ?? '')) === basename(__FILE__)) {
    if (session_status() === PHP_SESSION_NONE && class_exists('WISession')) {
        WISession::startSession();
    }

    $wrapper = new WIMediaImageUpload();
    $wrapper->handle();
}