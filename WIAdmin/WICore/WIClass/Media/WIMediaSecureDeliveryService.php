<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS / WICOS / WIKitchenCompli
| Layer: Shared Core Security Service
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| Purpose
|--------------------------------------------------------------------------
| Securely delivers private WIMedia files through PHP after access checks.
| Public files may still be served directly, but sensitive compliance media
| should use this service so view/download events are timestamped.
|--------------------------------------------------------------------------
*/

class WIMediaSecureDeliveryService
{
    private WIMediaRepository $repository;
    private WIMediaAccessService $access;
    private WIMediaEventService $events;

    /** Creates the secure delivery service. */
    public function __construct(WIMediaRepository $repository, WIMediaAccessService $access, WIMediaEventService $events)
    {
        $this->repository = $repository;
        $this->access = $access;
        $this->events = $events;
    }

    /** Builds delivery metadata for a media item. */
    public function prepare(int $mediaId, array $context = []): array
    {
        $media = $this->repository->findMediaById($mediaId);

        if ($media === false) {
            return ['status' => 'error', 'message' => 'Media item not found.'];
        }

        if (!$this->access->canViewMedia($media, $context)) {
            return ['status' => 'error', 'message' => 'You do not have permission to view this media item.'];
        }

        $path = $this->resolvePath((string) ($media['file_path'] ?? ''));

        if ($path === '' || !is_file($path)) {
            return ['status' => 'error', 'message' => 'Media file is missing from storage.'];
        }

        return [
            'status' => 'success',
            'media' => $media,
            'path' => $path,
            'mime_type' => (string) ($media['mime_type'] ?? 'application/octet-stream'),
            'file_name' => (string) ($media['original_name'] ?? $media['file_name'] ?? basename($path)),
        ];
    }

    /** Records a secure view/download event. */
    public function recordDeliveryEvent(string $eventType, int $mediaId, array $context = []): void
    {
        $allowed = in_array($eventType, ['viewed', 'downloaded'], true) ? $eventType : 'viewed';
        $this->events->record($allowed, array_merge($context, ['media_id' => $mediaId]));
    }

    /** Resolves a relative or absolute media path safely. */
    private function resolvePath(string $path): string
    {
        $path = trim($path);

        if ($path === '') {
            return '';
        }

        if (is_file($path)) {
            return $path;
        }

        $candidate = dirname(__DIR__, 5) . '/' . ltrim($path, '/');

        return is_file($candidate) ? $candidate : '';
    }
}
