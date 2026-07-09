<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS / WICOS / WIKitchenCompli
| Class: WIMediaUploads
| File: MediaUploads.php
| Location: /WIAdmin/WICore/WIClass/MediaUploads.php
| Type: Legacy Upload Wrapper
| Layer: Compatibility
| Version: 2.0.0
| Created: 2026-05-01
| Last Updated: 2026-05-01
| Status: Compatibility
|--------------------------------------------------------------------------
|
| Purpose:
| - Replaces the old standalone image resize/upload script.
| - Routes uploads through canonical WIMedia.
| - Keeps old AJAX entry point alive while preventing duplicate upload logic.
|
| Rules:
| - No direct upload logic.
| - No direct DB access.
| - WIMedia owns the file.
| - WIAdmin/WIMedia/ is the canonical storage root.
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/WI.php';
require_once __DIR__ . '/WIMedia.php';

final class WIMediaUploads
{
    private WIMedia $WIMedia;

    /**
     * Creates the legacy uploads wrapper.
     *
     * @param WIMedia|null $WIMedia Optional shared media façade.
     */
    public function __construct(?WIMedia $WIMedia = null)
    {
        $this->WIMedia = $WIMedia ?? new WIMedia(WIdb::getInstance());
    }

    /**
     * Handles the legacy upload request.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->sendJsonHeaders();

        if (!$this->isAjaxRequest()) {
            $this->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Invalid upload request.',
            ], 400);
        }

        $file = $_FILES['file'] ?? $_FILES['media'] ?? null;

        if (!is_array($file)) {
            $this->json([
                'success' => false,
                'status' => 'error',
                'message' => 'No file uploaded.',
            ], 400);
        }

        $result = $this->WIMedia->upload($file, $this->buildContext());

        $this->json($this->normaliseResponse($result));
    }

    /**
     * Builds upload context from request values.
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
            'folder' => $this->string('folder', 'legacy'),
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
     * Normalises the response for older JavaScript callers.
     *
     * @param array<string, mixed> $result WIMedia result.
     *
     * @return array<string, mixed>
     */
    private function normaliseResponse(array $result): array
    {
        $media = is_array($result['media'] ?? null) ? $result['media'] : [];

        if (($result['success'] ?? false) !== true) {
            return $result;
        }

        return array_merge($result, [
            'name' => $media['file_url'] ?? $media['file_path'] ?? '',
            'id' => $media['id'] ?? $result['media_id'] ?? 0,
            'media_id' => $media['id'] ?? $result['media_id'] ?? 0,
            'url' => $media['file_url'] ?? '',
            'path' => $media['file_path'] ?? '',
        ]);
    }

    /**
     * Checks if request was AJAX-like.
     *
     * @return bool
     */
    private function isAjaxRequest(): bool
    {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            return true;
        }

        return isset($_POST['action']) || isset($_GET['action']) || !empty($_FILES);
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
     * @param string $key Request key.
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
     * @param string $key Request key.
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
     * Returns the current user ID if available.
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

    $wrapper = new WIMediaUploads();
    $wrapper->handle();
}