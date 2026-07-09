<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS / WICOS / WIKitchenCompli
| Layer: Shared Core AJAX Controller
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| Purpose
|--------------------------------------------------------------------------
| Secure media delivery controller for WIAjax integration. Use this from a
| route that is already protected by the admin/front-side session layer.
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/WIMedia.php';
require_once __DIR__ . '/Media/WIMediaSecureDeliveryService.php';

class WIAjaxMediaDelivery
{
    private WIMediaSecureDeliveryService $delivery;

    /** Creates the delivery controller. */
    public function __construct()
    {
        $repository = new WIMediaRepository(WIdb::getInstance());
        $events = new WIMediaEventService($repository);
        $access = new WIMediaAccessService();
        $this->delivery = new WIMediaSecureDeliveryService($repository, $access, $events);
    }

    /** Returns a secure JSON payload for viewer usage. */
    public function view(array $request): array
    {
        $mediaId = (int) ($request['media_id'] ?? $request['id'] ?? 0);
        $prepared = $this->delivery->prepare($mediaId, $request);

        if (($prepared['status'] ?? '') !== 'success') {
            return $prepared;
        }

        $this->delivery->recordDeliveryEvent('viewed', $mediaId, $request);

        return [
            'status' => 'success',
            'media' => $prepared['media'],
            'mime_type' => $prepared['mime_type'],
            'file_name' => $prepared['file_name'],
        ];
    }

    /** Streams a secure file download and exits. */
    public function download(array $request): void
    {
        $mediaId = (int) ($request['media_id'] ?? $request['id'] ?? 0);
        $prepared = $this->delivery->prepare($mediaId, $request);

        if (($prepared['status'] ?? '') !== 'success') {
            http_response_code(404);
            echo $prepared['message'] ?? 'Media unavailable.';
            exit;
        }

        $this->delivery->recordDeliveryEvent('downloaded', $mediaId, $request);

        header('Content-Type: ' . $prepared['mime_type']);
        header('Content-Disposition: attachment; filename="' . basename($prepared['file_name']) . '"');
        header('Content-Length: ' . filesize($prepared['path']));
        readfile($prepared['path']);
        exit;
    }
}
