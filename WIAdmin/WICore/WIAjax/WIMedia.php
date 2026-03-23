<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| Core Includes
|--------------------------------------------------------------------------
| Keep structure as-is.
| Adjust include lines only if your exact startup/db loader differs.
*/
require_once dirname(__DIR__) . '/WIInc/WI_Start_Up.php';
require_once dirname(__DIR__) . '/WIClass/WIMedia.php';

/*
|--------------------------------------------------------------------------
| Database Connection
|--------------------------------------------------------------------------
*/
global $WIdb;

if (!isset($WIdb) || !($WIdb instanceof mysqli)) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Database connection not available.'
    ]);
    exit;
}

$media = new WIMedia($WIdb);

function wiMediaJson(array $response): void
{
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function wiMediaPost(string $key, mixed $default = null): mixed
{
    return $_POST[$key] ?? $default;
}

function wiMediaRequireLogin(): void
{
    $loggedIn = isset($_SESSION['user_id']) || isset($_SESSION['admin_id']);

    if (!$loggedIn) {
        wiMediaJson([
            'status'  => 'error',
            'message' => 'You must be logged in to perform this action.'
        ]);
    }
}

function wiMediaCheckCsrf(): void
{
    if (isset($_POST['csrf_token'], $_SESSION['csrf_token'])) {
        if (!hash_equals((string)$_SESSION['csrf_token'], (string)$_POST['csrf_token'])) {
            wiMediaJson([
                'status'  => 'error',
                'message' => 'Invalid security token.'
            ]);
        }
    }
}

function wiMediaCurrentUserId(): ?int
{
    if (isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id'])) {
        return (int)$_SESSION['user_id'];
    }

    if (isset($_SESSION['admin_id']) && is_numeric($_SESSION['admin_id'])) {
        return (int)$_SESSION['admin_id'];
    }

    return null;
}

function wiMediaNormalizeInt(mixed $value, int $default = 0): int
{
    if ($value === null || $value === '') {
        return $default;
    }

    return is_numeric($value) ? (int)$value : $default;
}

function wiMediaNormalizeString(mixed $value, bool $trim = true): ?string
{
    if ($value === null) {
        return null;
    }

    $value = (string)$value;
    $value = $trim ? trim($value) : $value;

    return trim($value) === '' ? null : $value;
}

function wiMediaAllowedType(?string $type): ?string
{
    $allowed = ['image', 'video', 'audio', 'document', 'other'];

    if ($type === null || $type === '') {
        return null;
    }

    return in_array($type, $allowed, true) ? $type : null;
}

wiMediaRequireLogin();
wiMediaCheckCsrf();

$action = wiMediaPost('action', '');

if ($action === '') {
    wiMediaJson([
        'status'  => 'error',
        'message' => 'No action provided.'
    ]);
}

switch ($action) {
    case 'uploadMedia':
        handleUploadMedia($media);
        break;

    case 'getMediaList':
        handleGetMediaList($media);
        break;

    case 'getMediaItem':
        handleGetMediaItem($media);
        break;

    case 'updateMedia':
        handleUpdateMedia($media);
        break;

    case 'deleteMedia':
        handleDeleteMedia($media);
        break;

    default:
        wiMediaJson([
            'status'  => 'error',
            'message' => 'Invalid action.'
        ]);
}

function handleUploadMedia(WIMedia $media): void
{
    if (!isset($_FILES['file'])) {
        wiMediaJson([
            'status'  => 'error',
            'message' => 'No file was uploaded.'
        ]);
    }

    $data = [
        'title'       => wiMediaNormalizeString(wiMediaPost('title')),
        'alt_text'    => wiMediaNormalizeString(wiMediaPost('alt_text')),
        'caption'     => wiMediaNormalizeString(wiMediaPost('caption'), false),
        'description' => wiMediaNormalizeString(wiMediaPost('description'), false),
        'folder'      => wiMediaNormalizeString(wiMediaPost('folder')),
        'status'      => wiMediaNormalizeInt(wiMediaPost('status', 1), 1),
        'is_private'  => wiMediaNormalizeInt(wiMediaPost('is_private', 0), 0),
        'uploaded_by' => wiMediaCurrentUserId()
    ];

    wiMediaJson($media->upload($_FILES['file'], $data));
}

function handleGetMediaList(WIMedia $media): void
{
    $filters = [
        'media_type' => wiMediaAllowedType(wiMediaNormalizeString(wiMediaPost('media_type'))),
        'folder'     => wiMediaNormalizeString(wiMediaPost('folder')),
        'status'     => wiMediaPost('status', ''),
        'search'     => wiMediaNormalizeString(wiMediaPost('search')),
        'limit'      => wiMediaNormalizeInt(wiMediaPost('limit', 50), 50),
        'offset'     => wiMediaNormalizeInt(wiMediaPost('offset', 0), 0)
    ];

    if ($filters['limit'] < 1) {
        $filters['limit'] = 50;
    }

    if ($filters['limit'] > 500) {
        $filters['limit'] = 500;
    }

    $items = $media->getMediaList($filters);

    wiMediaJson([
        'status'  => 'success',
        'message' => 'Media list loaded successfully.',
        'items'   => $items,
        'count'   => count($items)
    ]);
}

function handleGetMediaItem(WIMedia $media): void
{
    $id = wiMediaNormalizeInt(wiMediaPost('id'));

    if ($id <= 0) {
        wiMediaJson([
            'status'  => 'error',
            'message' => 'Invalid media ID.'
        ]);
    }

    $item = $media->getMediaById($id);

    if (!$item) {
        wiMediaJson([
            'status'  => 'error',
            'message' => 'Media item not found.'
        ]);
    }

    wiMediaJson([
        'status'  => 'success',
        'message' => 'Media item loaded successfully.',
        'item'    => $item
    ]);
}

function handleUpdateMedia(WIMedia $media): void
{
    $id = wiMediaNormalizeInt(wiMediaPost('id'));

    if ($id <= 0) {
        wiMediaJson([
            'status'  => 'error',
            'message' => 'Invalid media ID.'
        ]);
    }

    $data = [];

    $fields = ['title', 'alt_text', 'caption', 'description', 'folder', 'status', 'is_private'];

    foreach ($fields as $field) {
        if (!array_key_exists($field, $_POST)) {
            continue;
        }

        if ($field === 'status' || $field === 'is_private') {
            $data[$field] = wiMediaNormalizeInt($_POST[$field]);
        } else {
            $data[$field] = trim((string)$_POST[$field]);
        }
    }

    wiMediaJson($media->updateMedia($id, $data));
}

function handleDeleteMedia(WIMedia $media): void
{
    $id = wiMediaNormalizeInt(wiMediaPost('id'));

    if ($id <= 0) {
        wiMediaJson([
            'status'  => 'error',
            'message' => 'Invalid media ID.'
        ]);
    }

    wiMediaJson($media->deleteMedia($id));
}