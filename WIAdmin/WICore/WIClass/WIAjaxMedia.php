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
| File: WIAjaxMedia.php
| Location: /root/WIAdmin/WICore/WIClass/WIAjaxMedia.php
| Type: PHP AJAX Route Handler
| Layer: Admin AJAX
| Purpose Area: Shared Media AJAX Routing
| Version: 2.4.0
| Created: 2026-05-05
| Last Updated: 2026-05-08
| Status: WM-01 Production Batch
|--------------------------------------------------------------------------
| Summary:
| Dedicated AJAX route handler for shared WIMedia actions.
| - Routes media upload, list, get, update, delete and restore actions
| - Routes media link, unlink, linked lookup, view URL and download URL actions
| - Normalises legacy and dotted media action names to canonical media_* actions
| - Delegates all media work to the WIMedia façade
| - Contains no direct SQL, no direct upload storage logic and no compliance dependency
|--------------------------------------------------------------------------
*/

/**
 * FILE:
 * /WIAdmin/WICore/WIClass/WIAjaxMedia.php
 *
 * Written By: Jules Warner
 * Company: WILabs
 * Product: WICMS / WICOS / WIKitchenCompli
 * Class: WIAjaxMedia
 * Type: AJAX Route Handler
 * Layer: Admin AJAX
 * Version: 2.1.0
 * Status: Active
 *
 * Purpose:
 * - Routes media_* AJAX actions to the canonical WIMedia façade.
 * - Keeps upload/list/link/delete/view/download logic out of WIAjax.php.
 * - Returns structured JSON-ready arrays only.
 *
 * Architecture Rules:
 * - No direct SQL.
 * - No direct file handling.
 * - No UI rendering.
 * - WIMedia owns all media operations.
 * - WIdb remains the only DB layer.
 */

require_once __DIR__ . '/WIMedia.php';
require_once __DIR__ . '/Media/WIMediaContext.php';

final class WIAjaxMedia
{
    private WIMedia $WIMedia;

    /**
     * Creates the media AJAX router.
     *
     * @param WIMedia|null $WIMedia Optional WIMedia façade.
     */
    public function __construct(?WIMedia $WIMedia = null)
    {
        $this->WIMedia = $WIMedia ?? new WIMedia(WIdb::getInstance());
    }

    /**
     * Handles a media AJAX action.
     *
     * @param string $action Action name.
     * @param array<string, mixed> $request Request data.
     * @param array<string, mixed> $files Uploaded file data.
     *
     * @return array<string, mixed>
     */
    public function handle(string $action, array $request = [], array $files = []): array
    {
        $action = $this->normaliseAction($action);

        try {
            return match ($action) {
                'media_upload' => $this->upload($request, $files),
                'media_upload_bulk' => $this->uploadBulk($request, $files),
                'media_upload_and_attach' => $this->uploadAndAttach($request, $files),
                'media_list' => $this->list($request),
                'media_get' => $this->get($request),
                'media_update' => $this->update($request),
                'media_delete' => $this->delete($request),
                'media_restore' => $this->restore($request),
                'media_link' => $this->link($request),
                'media_unlink' => $this->unlink($request),
                'media_linked' => $this->linked($request),
                'media_attach' => $this->attach($request),
                'media_detach' => $this->detach($request),
                'media_view_url' => $this->viewUrl($request),
                'media_download_url' => $this->downloadUrl($request),
                'media_access_check' => $this->accessCheck($request),

                /*
                 * Temporary legacy aliases while old callers are converted.
                 */
                'uploadMedia' => $this->upload($request, $files),
                'getMediaList' => $this->list($request),
                'getMediaItem' => $this->get($request),
                'updateMedia' => $this->update($request),
                'deleteMedia' => $this->delete($request),

                default => $this->error('Unknown media action: ' . $action),
            };
        } catch (Throwable $e) {
            return [
                'success' => false,
                'status' => 'error',
                'message' => defined('APP_DEBUG') && APP_DEBUG
                    ? 'WIMedia backend error: ' . $e->getMessage()
                    : 'WIMedia backend error.',
                'data' => [],
            ];
        }
    }

    /**
     * Uploads one media file.
     *
     * @param array<string, mixed> $request Request data.
     * @param array<string, mixed> $files Files data.
     *
     * @return array<string, mixed>
     */
    private function upload(array $request, array $files): array
    {
        $file = $files['file']
            ?? $files['media']
            ?? $files['document_file']
            ?? $files['evidence_file']
            ?? null;

        if (!is_array($file)) {
            return $this->error('No media file was uploaded.');
        }

        return $this->normalise(
            $this->WIMedia->upload($file, $this->buildContext($request))
        );
    }

    /**
     * Uploads multiple files.
     *
     * @param array<string, mixed> $request Request data.
     * @param array<string, mixed> $files Files data.
     *
     * @return array<string, mixed>
     */
    private function uploadBulk(array $request, array $files): array
    {
        $fileGroup = $files['files']
            ?? $files['file']
            ?? $files['media']
            ?? null;

        if (!is_array($fileGroup)) {
            return $this->error('No media files were uploaded.');
        }

        return $this->normalise(
            $this->WIMedia->uploadBulk($fileGroup, $this->buildContext($request))
        );
    }


    /**
     * Uploads one or more files and attaches them to a site-wide entity context.
     *
     * @param array<string, mixed> $request Request data.
     * @param array<string, mixed> $files Files data.
     *
     * @return array<string, mixed>
     */
    private function uploadAndAttach(array $request, array $files): array
    {
        $fileGroup = $files['files']
            ?? $files['file']
            ?? $files['media']
            ?? $files['document_file']
            ?? $files['evidence_file']
            ?? null;

        if (!is_array($fileGroup)) {
            return $this->error('No media files were uploaded.');
        }

        return $this->normalise(
            $this->WIMedia->uploadAndAttach($fileGroup, $this->buildContext($request))
        );
    }

    /**
     * Lists media.
     *
     * @param array<string, mixed> $request Request data.
     *
     * @return array<string, mixed>
     */
    private function list(array $request): array
    {
        $filters = [
            'media_type' => $this->string($request, 'media_type'),
            'visibility' => $this->string($request, 'visibility'),
            'access_scope' => $this->string($request, 'access_scope'),
            'status' => $request['status'] ?? null,
            'search' => $this->string($request, 'search'),
            'org_business_id' => $this->positiveInt($request['org_business_id'] ?? $request['business_id'] ?? null),
            'org_site_id' => $this->positiveInt($request['org_site_id'] ?? $request['site_id'] ?? null),
            'org_department_id' => $this->positiveInt($request['org_department_id'] ?? $request['department_id'] ?? null),
            'limit' => (int)($request['limit'] ?? 100),
            'offset' => (int)($request['offset'] ?? 0),
        ];

        $items = $this->WIMedia->getMediaList($filters);
        $context = $this->buildContext($request);

        foreach ($items as &$item) {
            $mediaId = (int)($item['id'] ?? 0);

            if ($mediaId <= 0) {
                continue;
            }

            $item['safe_view_url'] = $this->WIMedia->viewUrl($mediaId, $context);
            $item['safe_download_url'] = $this->WIMedia->downloadUrl($mediaId, $context);
        }

        unset($item);

        return $this->success('Media list loaded successfully.', [
            'items' => $items,
            'count' => count($items),
            'data' => $items,
        ]);
    }

    /**
     * Gets a media item.
     *
     * @param array<string, mixed> $request Request data.
     *
     * @return array<string, mixed>
     */
    private function get(array $request): array
    {
        $mediaId = $this->id($request);

        if ($mediaId <= 0) {
            return $this->error('A valid media ID is required.');
        }

        $media = $this->WIMedia->getMediaById($mediaId);

        if ($media === false || $media === []) {
            return $this->error('Media item was not found.');
        }

        $context = $this->buildContext($request);
        $media['safe_view_url'] = $this->WIMedia->viewUrl($mediaId, $context);
        $media['safe_download_url'] = $this->WIMedia->downloadUrl($mediaId, $context);

        $this->WIMedia->logEvent('viewed_metadata', array_merge($context, [
            'media_id' => $mediaId,
        ]));

        return $this->success('Media item loaded successfully.', [
            'media' => $media,
            'item' => $media,
            'data' => $media,
        ]);
    }

    /**
     * Updates media metadata.
     *
     * @param array<string, mixed> $request Request data.
     *
     * @return array<string, mixed>
     */
    private function update(array $request): array
    {
        $mediaId = $this->id($request);

        if ($mediaId <= 0) {
            return $this->error('A valid media ID is required.');
        }

        return $this->normalise(
            $this->WIMedia->updateMedia($mediaId, [
                'title' => $this->string($request, 'title'),
                'alt_text' => $this->string($request, 'alt_text'),
                'caption' => $this->string($request, 'caption'),
                'description' => $this->string($request, 'description'),
                'folder' => $this->string($request, 'folder'),
                'status' => $request['status'] ?? null,
                'visibility' => $this->string($request, 'visibility'),
                'access_scope' => $this->string($request, 'access_scope'),
                'is_private' => $request['is_private'] ?? null,
                'is_sensitive' => $request['is_sensitive'] ?? null,
                'user_id' => $this->currentUserId(),
            ])
        );
    }

    /**
     * Soft deletes media.
     *
     * @param array<string, mixed> $request Request data.
     *
     * @return array<string, mixed>
     */
    private function delete(array $request): array
    {
        $mediaId = $this->id($request);

        if ($mediaId <= 0) {
            return $this->error('A valid media ID is required.');
        }

        return $this->normalise(
            $this->WIMedia->deleteMedia($mediaId, array_merge($this->buildContext($request), [
                'user_id' => $this->currentUserId(),
            ]))
        );
    }

    /**
     * Restores media.
     *
     * @param array<string, mixed> $request Request data.
     *
     * @return array<string, mixed>
     */
    private function restore(array $request): array
    {
        $mediaId = $this->id($request);

        if ($mediaId <= 0) {
            return $this->error('A valid media ID is required.');
        }

        return $this->normalise(
            $this->WIMedia->restoreMedia($mediaId, array_merge($this->buildContext($request), [
                'user_id' => $this->currentUserId(),
            ]))
        );
    }


    /**
     * Attaches existing media to a site-wide entity context.
     *
     * @param array<string, mixed> $request Request data.
     *
     * @return array<string, mixed>
     */
    private function attach(array $request): array
    {
        $mediaId = $this->id($request);

        if ($mediaId <= 0) {
            return $this->error('A valid media ID is required.');
        }

        return $this->normalise(
            $this->WIMedia->attachMedia($mediaId, $this->buildContext($request))
        );
    }

    /**
     * Detaches existing media from a site-wide entity context.
     *
     * @param array<string, mixed> $request Request data.
     *
     * @return array<string, mixed>
     */
    private function detach(array $request): array
    {
        $mediaId = $this->id($request);

        if ($mediaId <= 0) {
            return $this->error('A valid media ID is required.');
        }

        return $this->normalise(
            $this->WIMedia->detachMedia($mediaId, $this->buildContext($request))
        );
    }

    /**
     * Links media to an entity.
     *
     * @param array<string, mixed> $request Request data.
     *
     * @return array<string, mixed>
     */
    private function link(array $request): array
    {
        $mediaId = $this->id($request);

        if ($mediaId <= 0) {
            return $this->error('A valid media ID is required.');
        }

        return $this->normalise(
            $this->WIMedia->linkMedia($mediaId, $this->buildContext($request))
        );
    }

    /**
     * Unlinks media from an entity.
     *
     * @param array<string, mixed> $request Request data.
     *
     * @return array<string, mixed>
     */
    private function unlink(array $request): array
    {
        $mediaId = $this->id($request);

        if ($mediaId <= 0) {
            return $this->error('A valid media ID is required.');
        }

        return $this->normalise(
            $this->WIMedia->unlinkMedia($mediaId, $this->buildContext($request))
        );
    }

    /**
     * Gets media linked to a context.
     *
     * @param array<string, mixed> $request Request data.
     *
     * @return array<string, mixed>
     */
    private function linked(array $request): array
    {
        $items = $this->WIMedia->getLinkedMedia($this->buildContext($request));

        return $this->success('Linked media loaded successfully.', [
            'items' => $items,
            'count' => count($items),
            'data' => $items,
        ]);
    }

    /**
     * Returns safe media view URL.
     *
     * @param array<string, mixed> $request Request data.
     *
     * @return array<string, mixed>
     */
    private function viewUrl(array $request): array
    {
        $mediaId = $this->id($request);

        if ($mediaId <= 0) {
            return $this->error('A valid media ID is required.');
        }

        return $this->success('Media view URL generated.', [
            'url' => $this->WIMedia->viewUrl($mediaId, $this->buildContext($request)),
        ]);
    }

    /**
     * Returns safe media download URL.
     *
     * @param array<string, mixed> $request Request data.
     *
     * @return array<string, mixed>
     */
    private function downloadUrl(array $request): array
    {
        $mediaId = $this->id($request);

        if ($mediaId <= 0) {
            return $this->error('A valid media ID is required.');
        }

        return $this->success('Media download URL generated.', [
            'url' => $this->WIMedia->downloadUrl($mediaId, $this->buildContext($request)),
        ]);
    }

    /**
     * Checks media access.
     *
     * @param array<string, mixed> $request Request data.
     *
     * @return array<string, mixed>
     */
    private function accessCheck(array $request): array
    {
        $mediaId = $this->id($request);

        if ($mediaId <= 0) {
            return $this->error('A valid media ID is required.');
        }

        return $this->normalise(
            $this->WIMedia->canAccessMedia($mediaId, $this->buildContext($request))
        );
    }



    /**
 * Attach existing WIMedia item to an entity.
 *
 * AJAX action: media_link
 *
 * @return array
 */
public function media_link(): array
{
    require_once __DIR__ . '/WIMediaAttachmentContract.php';

    $contract = new WIMediaAttachmentContract($this->WIdb ?? null);

    return $contract->attach($_POST);
}

/**
 * Detach WIMedia item from an entity.
 *
 * AJAX action: media_unlink
 *
 * @return array
 */
public function media_unlink(): array
{
    require_once __DIR__ . '/WIMediaAttachmentContract.php';

    $contract = new WIMediaAttachmentContract($this->WIdb ?? null);

    return $contract->detach($_POST);
}

/**
 * List WIMedia items linked to an entity.
 *
 * AJAX action: media_linked
 *
 * @return array
 */
public function media_linked(): array
{
    require_once __DIR__ . '/WIMediaAttachmentContract.php';

    $contract = new WIMediaAttachmentContract($this->WIdb ?? null);

    return $contract->linked($_POST);
}

    /**
     * Builds WIMedia context from request.
     *
     * @param array<string, mixed> $request Request data.
     *
     * @return array<string, mixed>
     */
    private function buildContext(array $request): array
    {
        return WIMediaContext::fromPayload($request, $this->currentUserId());
    }

    /**
     * Resolves media ID.
     *
     * @param array<string, mixed> $request Request data.
     *
     * @return int
     */
    private function id(array $request): int
    {
        return (int)($request['media_id'] ?? $request['id'] ?? 0);
    }

    /**
     * Reads a string from request.
     *
     * @param array<string, mixed> $request Request data.
     * @param string $key Key.
     * @param string|null $default Default.
     *
     * @return string|null
     */
    private function string(array $request, string $key, ?string $default = null): ?string
    {
        if (!array_key_exists($key, $request)) {
            return $default;
        }

        $value = trim((string)$request[$key]);

        return $value !== '' ? $value : $default;
    }

    /**
     * Converts a value to positive int or null.
     *
     * @param mixed $value Value.
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
     * Resolves current user ID from session.
     *
     * @return int|null
     */
    private function currentUserId(): ?int
    {
        if (class_exists('WISession')) {
            if (method_exists('WISession', 'get')) {
                $userId = (int)WISession::get('user_id', 0);

                if ($userId > 0) {
                    return $userId;
                }

                $adminId = (int)WISession::get('admin_id', 0);

                if ($adminId > 0) {
                    return $adminId;
                }
            }
        }

        if (isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id'])) {
            return (int)$_SESSION['user_id'];
        }

        if (isset($_SESSION['admin_id']) && is_numeric($_SESSION['admin_id'])) {
            return (int)$_SESSION['admin_id'];
        }

        return null;
    }

    /**
     * Normalises a response.
     *
     * @param array<string, mixed> $response Response.
     *
     * @return array<string, mixed>
     */
    private function normalise(array $response): array
    {
        if (!array_key_exists('success', $response)) {
            $response['success'] = ($response['status'] ?? '') === 'success';
        }

        if (!array_key_exists('status', $response)) {
            $response['status'] = ($response['success'] ?? false) ? 'success' : 'error';
        }

        if (!array_key_exists('message', $response)) {
            $response['message'] = '';
        }

        if (!array_key_exists('data', $response)) {
            $response['data'] = [];
        }

        return $response;
    }

    /**
     * Returns success response.
     *
     * @param string $message Message.
     * @param array<string, mixed> $extra Extra.
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
     * Returns error response.
     *
     * @param string $message Message.
     *
     * @return array<string, mixed>
     */
    private function error(string $message): array
    {
        return [
            'success' => false,
            'status' => 'error',
            'message' => $message,
            'data' => [],
        ];
    }

    /**
     * Normalises supported frontend/legacy action names to canonical backend names.
     *
     * @param string $action Incoming action name.
     *
     * @return string
     */
    private function normaliseAction(string $action): string
    {
        $aliases = [
            'media.upload' => 'media_upload',
            'media.uploadBulk' => 'media_upload_bulk',
            'media.uploadAndAttach' => 'media_upload_and_attach',
            'media.upload_and_attach' => 'media_upload_and_attach',
            'media.list' => 'media_list',
            'media.get' => 'media_get',
            'media.update' => 'media_update',
            'media.delete' => 'media_delete',
            'media.restore' => 'media_restore',
            'media.link' => 'media_link',
            'media.unlink' => 'media_unlink',
            'media.linked' => 'media_linked',
            'media.attach' => 'media_attach',
            'media.detach' => 'media_detach',
            'media.viewUrl' => 'media_view_url',
            'media.downloadUrl' => 'media_download_url',
            'media.accessCheck' => 'media_access_check',
            'uploadMedia' => 'media_upload',
            'uploadBulkMedia' => 'media_upload_bulk',
            'uploadAndAttachMedia' => 'media_upload_and_attach',
            'getMediaList' => 'media_list',
            'getLinkedMedia' => 'media_linked',
            'attachMedia' => 'media_attach',
            'detachMedia' => 'media_detach',
        ];

        $action = trim($action);

        return $aliases[$action] ?? $action;
    }
}
