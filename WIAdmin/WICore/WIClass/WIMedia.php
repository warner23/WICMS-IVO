<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WI Ecosystem
| Project: WIKitchenCompli / WICOS
| File: WIMedia.php
| Location: /root/WIAdmin/WICore/WIClass/WIMedia.php
| Type: PHP Class
| Layer: Shared Media Foundation
| Purpose Area: Media Upload / Listing / Linking / Delivery
| Version: 2.3.0
| Created: 2026-05-05
| Last Updated: 2026-05-08
| Status: WM-01 Production Batch
|--------------------------------------------------------------------------
| Summary:
| Canonical shared WIMedia façade for all WI systems.
| - Uploads, lists, gets, updates, deletes and restores media
| - Links and unlinks media to system/entity records
| - Provides safe view/download URL generation and controlled delivery
| - Delegates storage, repository, event, access and link work to media services
| - Must remain independent from WICompliance/WIKitchenCompli plugin classes
| - Uses WIdb only for database-backed services
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/Media/WIMediaTableGuard.php';
require_once __DIR__ . '/Media/WIMediaContext.php';
require_once __DIR__ . '/Media/WIMediaSecurityPolicy.php';
require_once __DIR__ . '/Media/WIMediaStorageService.php';
require_once __DIR__ . '/Media/WIMediaRepository.php';
require_once __DIR__ . '/Media/WIMediaEventService.php';
require_once __DIR__ . '/Media/WIMediaLinkService.php';
require_once __DIR__ . '/Media/WIMediaUploadService.php';
require_once __DIR__ . '/Media/WIMediaAccessPolicy.php';
require_once __DIR__ . '/Media/WIMediaFileDeliveryService.php';

final class WIMedia
{
    private WIdb $WIdb;

    private WIMediaTableGuard $Guard;

    private WIMediaRepository $Repository;

    private WIMediaUploadService $Uploader;

    private WIMediaLinkService $Links;

    private WIMediaEventService $Events;

    private WIMediaAccessPolicy $AccessPolicy;

    private WIMediaFileDeliveryService $Delivery;

    /**
     * Creates the shared WIMedia façade.
     *
     * @param WIdb|null $WIdb Optional shared WIdb instance.
     */
    public function __construct(?WIdb $WIdb = null)
    {
        $this->WIdb = $WIdb instanceof WIdb ? $WIdb : WIdb::getInstance();
        $this->Guard = new WIMediaTableGuard($this->WIdb);
        $this->Repository = new WIMediaRepository($this->WIdb, $this->Guard);
        $this->Events = new WIMediaEventService($this->WIdb, $this->Guard);
        $this->AccessPolicy = new WIMediaAccessPolicy($this->WIdb);
        $this->Delivery = new WIMediaFileDeliveryService(new WIMediaStorageService());
        $this->Links = new WIMediaLinkService($this->WIdb, $this->Guard, $this->Events);
        $this->Uploader = new WIMediaUploadService(
            $this->Repository,
            new WIMediaSecurityPolicy(),
            new WIMediaStorageService(),
            $this->Events
        );
    }

    /**
     * Uploads a single media file through the shared upload service.
     *
     * @param array<string, mixed> $file Normalised PHP upload file array.
     * @param array<string, mixed> $context Optional media/entity context.
     *
     * @return array<string, mixed>
     */
    public function upload(array $file, array $context = []): array
    {
        $context = $this->buildContext($context);
        $result = $this->Uploader->upload($file, $context);

        if (($result['success'] ?? false) === true && !empty($context['entity_type'])) {
            $mediaId = (int)($result['media_id'] ?? 0);

            if ($mediaId > 0 && !empty($context['system_code']) && !empty($context['entity_id']) && !empty($context['link_type'])) {
                $link = $this->linkMedia($mediaId, $context);
                $result['link'] = $link;
            }
        }

        return $this->normalise($result);
    }

    /**
     * Uploads one or more media files.
     *
     * @param array<string, mixed> $files PHP upload files array.
     * @param array<string, mixed> $context Optional media/entity context.
     *
     * @return array<string, mixed>
     */
    public function uploadBulk(array $files, array $context = []): array
    {
        $normalised = $this->normaliseFilesArray($files);
        $items = [];
        $successCount = 0;

        foreach ($normalised as $file) {
            $result = $this->upload($file, $context);
            $items[] = $result;

            if (($result['success'] ?? false) === true) {
                $successCount++;
            }
        }

        return [
            'success' => $successCount > 0,
            'status' => $successCount > 0 ? 'success' : 'error',
            'message' => $successCount . ' file(s) uploaded.',
            'items' => $items,
            'count' => count($items),
            'success_count' => $successCount,
        ];
    }

    /**
     * Finds one media row by ID.
     *
     * @param int $id Media ID.
     *
     * @return array<string, mixed>|false
     */
    public function getMediaById(int $id): array|false
    {
        return $this->Repository->find($id);
    }

    /**
     * Lists media rows using repository filters.
     *
     * @param array<string, mixed> $filters List filters.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getMediaList(array $filters = []): array
    {
        return $this->Repository->list($filters);
    }

    /**
     * Updates media metadata.
     *
     * @param int $id Media ID.
     * @param array<string, mixed> $data Update payload.
     *
     * @return array<string, mixed>
     */
    public function updateMedia(int $id, array $data): array
    {
        if ($id <= 0) {
            return $this->error('A valid media ID is required.');
        }

        $update = [
            'title' => $this->stringOrNull($data['title'] ?? null),
            'alt_text' => $this->stringOrNull($data['alt_text'] ?? null),
            'caption' => $this->stringOrNull($data['caption'] ?? null),
            'description' => $this->stringOrNull($data['description'] ?? null),
            'folder' => $this->stringOrNull($data['folder'] ?? null),
            'status' => $data['status'] ?? null,
            'visibility' => $this->stringOrNull($data['visibility'] ?? null),
            'access_scope' => $this->stringOrNull($data['access_scope'] ?? null),
            'is_private' => isset($data['is_private']) ? (int)$data['is_private'] : null,
            'is_sensitive' => isset($data['is_sensitive']) ? (int)$data['is_sensitive'] : null,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $update = array_filter($update, static fn ($value) => $value !== null);

        $ok = $this->Repository->update($id, $update);

        if (!$ok) {
            return $this->error('Failed to update media item.');
        }

        $this->Events->log('updated', array_merge($data, [
            'media_id' => $id,
        ]));

        return $this->success('Media item updated successfully.', [
            'media' => $this->getMediaById($id),
        ]);
    }

    /**
     * Soft-deletes a media item.
     *
     * @param int $id Media ID.
     * @param array<string, mixed> $context Optional event context.
     *
     * @return array<string, mixed>
     */
    public function deleteMedia(int $id, array $context = []): array
    {
        if ($id <= 0) {
            return $this->error('A valid media ID is required.');
        }

        $ok = $this->Repository->softDelete($id, $this->positiveInt($context['user_id'] ?? null));

        if (!$ok) {
            return $this->error('Failed to delete media item.');
        }

        $this->Events->log('deleted', array_merge($context, [
            'media_id' => $id,
        ]));

        return $this->success('Media item deleted successfully.');
    }

    /**
     * Restores a soft-deleted media item.
     *
     * @param int $id Media ID.
     * @param array<string, mixed> $context Optional event context.
     *
     * @return array<string, mixed>
     */
    public function restoreMedia(int $id, array $context = []): array
    {
        if ($id <= 0) {
            return $this->error('A valid media ID is required.');
        }

        $ok = $this->Repository->restore($id);

        if (!$ok) {
            return $this->error('Failed to restore media item.');
        }

        $this->Events->log('restored', array_merge($context, [
            'media_id' => $id,
        ]));

        return $this->success('Media item restored successfully.', [
            'media' => $this->getMediaById($id),
        ]);
    }


    /**
     * Builds canonical site-wide WIMedia context.
     *
     * @param array<string, mixed> $payload Raw payload.
     *
     * @return array<string, mixed>
     */
    public function buildContext(array $payload = []): array
    {
        return WIMediaContext::fromPayload($payload, $this->positiveInt($payload['user_id'] ?? null));
    }

    /**
     * Uploads one or more files and attaches them to an entity when link context is present.
     *
     * @param array<string, mixed> $files PHP upload file array or group.
     * @param array<string, mixed> $context Link/upload context.
     *
     * @return array<string, mixed>
     */
    public function uploadAndAttach(array $files, array $context = []): array
    {
        $context = $this->buildContext($context);

        if (!$this->contextCanLink($context)) {
            return $this->error('Media attachment context is incomplete.');
        }

        return $this->uploadBulk($files, $context);
    }

    /**
     * Attaches an existing media item to an entity.
     *
     * @param int $mediaId Media ID.
     * @param array<string, mixed> $context Link context.
     *
     * @return array<string, mixed>
     */
    public function attachMedia(int $mediaId, array $context): array
    {
        $context = $this->buildContext($context);

        if (!$this->contextCanLink($context)) {
            return $this->error('Media attachment context is incomplete.');
        }

        return $this->linkMedia($mediaId, $context);
    }

    /**
     * Detaches an existing media item from an entity.
     *
     * @param int $mediaId Media ID.
     * @param array<string, mixed> $context Link context.
     *
     * @return array<string, mixed>
     */
    public function detachMedia(int $mediaId, array $context): array
    {
        $context = $this->buildContext($context);

        if (!$this->contextCanLink($context)) {
            return $this->error('Media detachment context is incomplete.');
        }

        return $this->unlinkMedia($mediaId, $context);
    }

    /**
     * Lists media attached to an entity.
     *
     * @param array<string, mixed> $context Link context.
     *
     * @return array<int, array<string, mixed>>
     */
    public function linkedFor(array $context): array
    {
        return $this->getLinkedMedia($this->buildContext($context));
    }

    /**
     * Checks whether a context can create/remove a media link.
     *
     * @param array<string, mixed> $context Canonical context.
     *
     * @return bool
     */
    private function contextCanLink(array $context): bool
    {
        return WIMediaContext::canLink($context);
    }

    /**
     * Links media to a system/entity record.
     *
     * @param int $mediaId Media ID.
     * @param array<string, mixed> $context Link context.
     *
     * @return array<string, mixed>
     */
    public function linkMedia(int $mediaId, array $context): array
    {
        return $this->normalise($this->Links->linkMedia($mediaId, $context));
    }

    /**
     * Removes a media link from a system/entity record.
     *
     * @param int $mediaId Media ID.
     * @param array<string, mixed> $context Link context.
     *
     * @return array<string, mixed>
     */
    public function unlinkMedia(int $mediaId, array $context): array
    {
        return $this->normalise($this->Links->unlinkMedia($mediaId, $context));
    }

    /**
     * Lists media linked to a system/entity record.
     *
     * @param array<string, mixed> $context Link context.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getLinkedMedia(array $context): array
    {
        return $this->Links->getLinkedMedia($context);
    }

    /**
     * Logs a media event.
     *
     * @param string $eventType Event type.
     * @param array<string, mixed> $context Event context.
     *
     * @return void
     */
    public function logEvent(string $eventType, array $context = []): void
    {
        $this->Events->log($eventType, $context);
    }

    /**
     * Normalises service responses into the standard WI success/status/message shape.
     *
     * @param array<string, mixed> $result Raw service result.
     *
     * @return array<string, mixed>
     */
    private function normalise(array $result): array
    {
        if (!array_key_exists('success', $result)) {
            $result['success'] = ($result['status'] ?? '') === 'success';
        }

        if (!array_key_exists('status', $result)) {
            $result['status'] = ($result['success'] ?? false) ? 'success' : 'error';
        }

        if (!array_key_exists('message', $result)) {
            $result['message'] = '';
        }

        if (isset($result['media']) && is_array($result['media']) && !isset($result['items'])) {
            $result['items'] = $result['media'];
        }

        if (isset($result['items']) && is_array($result['items']) && !isset($result['media'])) {
            $result['media'] = $result['items'];
        }

        return $result;
    }

    /**
     * Normalises PHP multi-file upload arrays into single file arrays.
     *
     * @param array<string, mixed> $files PHP files array.
     *
     * @return array<int, array<string, mixed>>
     */
    private function normaliseFilesArray(array $files): array
    {
        if (!isset($files['name']) || !is_array($files['name'])) {
            return [$files];
        }

        $normalised = [];

        foreach ($files['name'] as $index => $name) {
            $normalised[] = [
                'name' => $name,
                'type' => $files['type'][$index] ?? '',
                'tmp_name' => $files['tmp_name'][$index] ?? '',
                'error' => $files['error'][$index] ?? UPLOAD_ERR_NO_FILE,
                'size' => $files['size'][$index] ?? 0,
            ];
        }

        return $normalised;
    }

    /**
     * Builds a standard success response.
     *
     * @param string $message Response message.
     * @param array<string, mixed> $extra Extra response data.
     *
     * @return array<string, mixed>
     */
    private function success(string $message, array $extra = []): array
    {
        return array_merge([
            'success' => true,
            'status' => 'success',
            'message' => $message,
        ], $extra);
    }

    /**
     * Builds a standard error response.
     *
     * @param string $message Response message.
     *
     * @return array<string, mixed>
     */
    private function error(string $message): array
    {
        return [
            'success' => false,
            'status' => 'error',
            'message' => $message,
        ];
    }

    /**
     * Converts a value to a trimmed string or null.
     *
     * @param mixed $value Raw value.
     *
     * @return string|null
     */
    private function stringOrNull(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string)$value);

        return $value !== '' ? $value : null;
    }

    /**
     * Converts a value to a positive integer or null.
     *
     * @param mixed $value Raw value.
     *
     * @return int|null
     */
    private function positiveInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = (int)$value;

        return $value > 0 ? $value : null;
    }

    /**
     * Returns a safe view URL for a media item.
     *
     * @param int $mediaId Media ID.
     * @param array<string, mixed> $context Context.
     *
     * @return string
     */
    public function viewUrl(int $mediaId, array $context = []): string
    {
        return $this->deliveryUrl($mediaId, 'view', $context);
    }

    /**
     * Returns a safe download URL for a media item.
     *
     * @param int $mediaId Media ID.
     * @param array<string, mixed> $context Context.
     *
     * @return string
     */
    public function downloadUrl(int $mediaId, array $context = []): string
    {
        return $this->deliveryUrl($mediaId, 'download', $context);
    }

    /**
     * Delivers a media file after access check.
     *
     * @param int $mediaId Media ID.
     * @param string $mode Delivery mode.
     * @param array<string, mixed> $context Context.
     *
     * @return never
     */
    public function deliverMedia(int $mediaId, string $mode = 'view', array $context = []): never
    {
        $media = $this->getMediaById($mediaId);

        if ($media === false) {
            $this->abortDelivery(404, 'Media item was not found.');
        }

        $access = $this->AccessPolicy->canAccess($media, $context);

        if (($access['allowed'] ?? false) !== true) {
            $this->abortDelivery(403, (string)($access['message'] ?? 'Access denied.'));
        }

        $this->Events->log($mode === 'download' ? 'downloaded' : 'viewed', array_merge($context, [
            'media_id' => $mediaId,
        ]));

        $this->Delivery->deliver($media, $mode === 'download');
    }

    /**
     * Checks media access without delivering file.
     *
     * @param int $mediaId Media ID.
     * @param array<string, mixed> $context Context.
     *
     * @return array<string, mixed>
     */
    public function canAccessMedia(int $mediaId, array $context = []): array
    {
        $media = $this->getMediaById($mediaId);

        if ($media === false) {
            return [
                'success' => false,
                'allowed' => false,
                'message' => 'Media item was not found.',
            ];
        }

        return $this->AccessPolicy->canAccess($media, $context);
    }

    /**
     * Builds a media delivery URL.
     *
     * @param int $mediaId Media ID.
     * @param string $mode Mode.
     * @param array<string, mixed> $context Context.
     *
     * @return string
     */
    private function deliveryUrl(int $mediaId, string $mode, array $context = []): string
    {
        $query = [
            'id' => $mediaId,
            'mode' => $mode,
        ];

        foreach (['system_code', 'entity_type', 'entity_id', 'link_type', 'org_business_id', 'org_site_id', 'org_department_id'] as $key) {
            if (isset($context[$key]) && $context[$key] !== '') {
                $query[$key] = $context[$key];
            }
        }

        return $this->mediaDeliveryEndpoint() . '?' . http_build_query($query);
    }

    /**
     * Resolves the media delivery endpoint from any page context.
     *
     * The old relative WICore/WIAjax path worked inside WIAdmin, but worker
     * pages under /WICompliance/ resolved it to the wrong folder. A site-root
     * relative URL keeps thumbnails, inline previews and downloads working
     * from admin, worker, manager and future module pages.
     *
     * @return string
     */
    private function mediaDeliveryEndpoint(): string
    {
        if (defined('SITE_URL')) {
            return rtrim((string) SITE_URL, '/') . '/WIAdmin/WICore/WIAjax/WIMediaView.php';
        }

        $scriptName = isset($_SERVER['SCRIPT_NAME'])
            ? str_replace('\\', '/', (string) $_SERVER['SCRIPT_NAME'])
            : '';

        $base = '';

        foreach (['/WIAdmin/', '/WICompliance/', '/WIMembers/', '/WIInstall/'] as $marker) {
            if ($scriptName !== '' && str_contains($scriptName, $marker)) {
                $base = explode($marker, $scriptName, 2)[0];
                break;
            }
        }

        if ($base === '' && $scriptName !== '') {
            $base = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
        }

        return rtrim($base, '/') . '/WIAdmin/WICore/WIAjax/WIMediaView.php';
    }

    /**
     * Aborts media delivery.
     *
     * @param int $code Status code.
     * @param string $message Message.
     *
     * @return never
     */
    private function abortDelivery(int $code, string $message): never
    {
        http_response_code($code);
        header('Content-Type: text/plain; charset=utf-8');
        echo $message;
        exit;
    }
}