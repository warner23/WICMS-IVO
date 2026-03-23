<?php

class WIMedia
{
    protected mysqli $WIdb;
    protected string $table = 'wi_media';
    protected string $uploadRoot;
    protected string $uploadBaseUrl;
    protected int $maxFileSize = 52428800; // 50MB

    protected array $allowedExtensions = [
        'image'    => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'video'    => ['mp4', 'webm', 'ogv'],
        'audio'    => ['mp3', 'wav', 'ogg'],
        'document' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'csv']
    ];

    protected array $blockedExtensions = [
        'php', 'phtml', 'phar', 'js', 'exe', 'sh', 'bat', 'cmd', 'com', 'msi', 'cgi', 'pl'
    ];

    public function __construct(mysqli $db)
    {
        $this->WIdb = $db;

        $this->uploadRoot = defined('ROOT_PATH')
            ? rtrim(ROOT_PATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'WIMedia' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR
            : dirname(__DIR__) . DIRECTORY_SEPARATOR . 'WIMedia' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;

        $this->uploadBaseUrl = defined('SITE_URL')
            ? rtrim(SITE_URL, '/') . '/WIMedia/uploads/'
            : '/WIMedia/uploads/';
    }

    public function upload(array $file, array $data = []): array
    {
        $validation = $this->validateFile($file);
        if ($validation['status'] === 'error') {
            return $validation;
        }

        $originalName = trim((string)$file['name']);
        $extension    = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $mimeType     = $this->detectMimeType($file['tmp_name']);
        $mediaType    = $this->detectMediaType($mimeType, $extension);

        $directory = $this->getUploadDirectory($mediaType);

        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            return [
                'status'  => 'error',
                'message' => 'Failed to create upload directory.'
            ];
        }

        $storedName  = $this->generateStoredName($originalName);
        $destination = $directory . $storedName;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return [
                'status'  => 'error',
                'message' => 'Failed to move uploaded file.'
            ];
        }

        $relativePath = $this->buildRelativePath($mediaType, $storedName);
        $fileUrl      = $this->buildFileUrl($mediaType, $storedName);
        $dimensions   = $this->extractImageDimensions($destination, $mediaType);

        $title       = isset($data['title']) && trim((string)$data['title']) !== ''
            ? trim((string)$data['title'])
            : pathinfo($originalName, PATHINFO_FILENAME);

        $altText     = $this->nullIfEmptyString($data['alt_text'] ?? null);
        $caption     = $this->nullIfEmptyString($data['caption'] ?? null);
        $description = $this->nullIfEmptyString($data['description'] ?? null);
        $folder      = $this->nullIfEmptyString($data['folder'] ?? null);
        $status      = $this->normaliseFlag($data['status'] ?? 1);
        $isPrivate   = $this->normaliseFlag($data['is_private'] ?? 0);
        $uploadedBy  = isset($data['uploaded_by']) && is_numeric($data['uploaded_by']) ? (int)$data['uploaded_by'] : null;

        $uuid     = $this->generateUuid();
        $fileSize = isset($file['size']) ? (int)$file['size'] : 0;
        $width    = $dimensions['width'];
        $height   = $dimensions['height'];
        $duration = null;

        $sql = "INSERT INTO `{$this->table}` (
                    `uuid`,
                    `title`,
                    `alt_text`,
                    `caption`,
                    `description`,
                    `original_name`,
                    `stored_name`,
                    `file_path`,
                    `file_url`,
                    `folder`,
                    `extension`,
                    `mime_type`,
                    `media_type`,
                    `file_size`,
                    `width`,
                    `height`,
                    `duration`,
                    `status`,
                    `is_private`,
                    `uploaded_by`,
                    `created_at`
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $this->WIdb->prepare($sql);

        if (!$stmt) {
            @unlink($destination);
            return [
                'status'  => 'error',
                'message' => 'Database prepare failed.'
            ];
        }

        $stmt->bind_param(
            'sssssssssssssiiisiii',
            $uuid,
            $title,
            $altText,
            $caption,
            $description,
            $originalName,
            $storedName,
            $relativePath,
            $fileUrl,
            $folder,
            $extension,
            $mimeType,
            $mediaType,
            $fileSize,
            $width,
            $height,
            $duration,
            $status,
            $isPrivate,
            $uploadedBy
        );

        if (!$stmt->execute()) {
            $stmt->close();
            @unlink($destination);

            return [
                'status'  => 'error',
                'message' => 'Failed to save media record.'
            ];
        }

        $insertId = $stmt->insert_id;
        $stmt->close();

        return [
            'status'  => 'success',
            'message' => 'File uploaded successfully.',
            'media'   => $this->getMediaById($insertId)
        ];
    }

    public function getMediaById(int $id): array|false
    {
        $sql  = "SELECT * FROM `{$this->table}` WHERE `id` = ? LIMIT 1";
        $stmt = $this->WIdb->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param('i', $id);
        $stmt->execute();

        $result = $stmt->get_result();
        $row    = $result ? $result->fetch_assoc() : false;

        $stmt->close();

        return $row ?: false;
    }

    public function getMediaList(array $filters = []): array
    {
        $sql    = "SELECT * FROM `{$this->table}` WHERE 1=1";
        $types  = '';
        $values = [];

        if (!empty($filters['media_type'])) {
            $sql .= " AND `media_type` = ?";
            $types .= 's';
            $values[] = $filters['media_type'];
        }

        if (!empty($filters['folder'])) {
            $sql .= " AND `folder` = ?";
            $types .= 's';
            $values[] = $filters['folder'];
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $sql .= " AND `status` = ?";
            $types .= 'i';
            $values[] = $this->normaliseFlag($filters['status']);
        }

        if (!empty($filters['search'])) {
            $search = '%' . trim((string)$filters['search']) . '%';
            $sql .= " AND (
                        `title` LIKE ?
                        OR `original_name` LIKE ?
                        OR `caption` LIKE ?
                        OR `description` LIKE ?
                      )";
            $types .= 'ssss';
            $values[] = $search;
            $values[] = $search;
            $values[] = $search;
            $values[] = $search;
        }

        $sql .= " ORDER BY `id` DESC";

        if (isset($filters['limit']) && (int)$filters['limit'] > 0) {
            $limit  = (int)$filters['limit'];
            $offset = isset($filters['offset']) ? (int)$filters['offset'] : 0;
            $sql .= " LIMIT {$offset}, {$limit}";
        }

        $stmt = $this->WIdb->prepare($sql);
        if (!$stmt) {
            return [];
        }

        if ($types !== '') {
            $stmt->bind_param($types, ...$values);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $items = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
        }

        $stmt->close();

        return $items;
    }

    public function updateMedia(int $id, array $data): array
    {
        $existing = $this->getMediaById($id);
        if (!$existing) {
            return [
                'status'  => 'error',
                'message' => 'Media item not found.'
            ];
        }

        $allowedFields = ['title', 'alt_text', 'caption', 'description', 'folder', 'status', 'is_private'];

        $setParts = [];
        $types    = '';
        $values   = [];

        foreach ($allowedFields as $field) {
            if (!array_key_exists($field, $data)) {
                continue;
            }

            $setParts[] = "`{$field}` = ?";

            if ($field === 'status' || $field === 'is_private') {
                $types .= 'i';
                $values[] = $this->normaliseFlag($data[$field]);
            } else {
                $types .= 's';
                $values[] = $this->nullIfEmptyString($data[$field]);
            }
        }

        if (empty($setParts)) {
            return [
                'status'  => 'error',
                'message' => 'No valid fields provided for update.'
            ];
        }

        $setParts[] = "`updated_at` = NOW()";

        $sql = "UPDATE `{$this->table}` SET " . implode(', ', $setParts) . " WHERE `id` = ?";
        $types .= 'i';
        $values[] = $id;

        $stmt = $this->WIdb->prepare($sql);
        if (!$stmt) {
            return [
                'status'  => 'error',
                'message' => 'Database prepare failed.'
            ];
        }

        $stmt->bind_param($types, ...$values);

        if (!$stmt->execute()) {
            $stmt->close();
            return [
                'status'  => 'error',
                'message' => 'Failed to update media item.'
            ];
        }

        $stmt->close();

        return [
            'status'  => 'success',
            'message' => 'Media item updated successfully.',
            'media'   => $this->getMediaById($id)
        ];
    }

    public function deleteMedia(int $id): array
    {
        $media = $this->getMediaById($id);
        if (!$media) {
            return [
                'status'  => 'error',
                'message' => 'Media item not found.'
            ];
        }

        $fullPath = $this->absolutePathFromRelative((string)$media['file_path']);

        if (is_file($fullPath) && file_exists($fullPath)) {
            if (!@unlink($fullPath)) {
                return [
                    'status'  => 'error',
                    'message' => 'Unable to delete physical file.'
                ];
            }
        }

        $sql  = "DELETE FROM `{$this->table}` WHERE `id` = ? LIMIT 1";
        $stmt = $this->WIdb->prepare($sql);

        if (!$stmt) {
            return [
                'status'  => 'error',
                'message' => 'Database prepare failed.'
            ];
        }

        $stmt->bind_param('i', $id);

        if (!$stmt->execute()) {
            $stmt->close();
            return [
                'status'  => 'error',
                'message' => 'Failed to delete media record.'
            ];
        }

        $stmt->close();

        return [
            'status'  => 'success',
            'message' => 'Media item deleted successfully.'
        ];
    }

    public function validateFile(array $file): array
    {
        if (empty($file)) {
            return ['status' => 'error', 'message' => 'No file received.'];
        }

        if (!isset($file['error']) || is_array($file['error'])) {
            return ['status' => 'error', 'message' => 'Invalid upload parameters.'];
        }

        switch ((int)$file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                return ['status' => 'error', 'message' => 'No file was uploaded.'];
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return ['status' => 'error', 'message' => 'Uploaded file is too large.'];
            default:
                return ['status' => 'error', 'message' => 'Unknown upload error.'];
        }

        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return ['status' => 'error', 'message' => 'Possible file upload attack detected.'];
        }

        $fileSize = isset($file['size']) ? (int)$file['size'] : 0;
        if ($fileSize <= 0) {
            return ['status' => 'error', 'message' => 'Uploaded file is empty.'];
        }

        if ($fileSize > $this->maxFileSize) {
            return ['status' => 'error', 'message' => 'File exceeds the maximum allowed size of 50MB.'];
        }

        $originalName = trim((string)($file['name'] ?? ''));
        if ($originalName === '') {
            return ['status' => 'error', 'message' => 'Invalid file name.'];
        }

        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if ($extension === '') {
            return ['status' => 'error', 'message' => 'File has no extension.'];
        }

        if (in_array($extension, $this->blockedExtensions, true)) {
            return ['status' => 'error', 'message' => 'This file type is not allowed.'];
        }

        $mimeType  = $this->detectMimeType($file['tmp_name']);
        $mediaType = $this->detectMediaType($mimeType, $extension);

        if (!$this->isAllowedExtensionForType($extension, $mediaType)) {
            return ['status' => 'error', 'message' => 'The uploaded file type is not allowed.'];
        }

        return ['status' => 'success', 'message' => 'File is valid.'];
    }

    public function detectMediaType(string $mimeType, string $extension): string
    {
        $extension = strtolower($extension);
        $mimeType  = strtolower($mimeType);

        if (in_array($extension, $this->allowedExtensions['image'], true) || str_starts_with($mimeType, 'image/')) {
            return 'image';
        }

        if (in_array($extension, $this->allowedExtensions['video'], true) || str_starts_with($mimeType, 'video/')) {
            return 'video';
        }

        if (in_array($extension, $this->allowedExtensions['audio'], true) || str_starts_with($mimeType, 'audio/')) {
            return 'audio';
        }

        if (in_array($extension, $this->allowedExtensions['document'], true)) {
            return 'document';
        }

        return 'other';
    }

    public function generateStoredName(string $originalName): string
    {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $baseName  = pathinfo($originalName, PATHINFO_FILENAME);
        $baseName  = $this->slugify($baseName);
        $baseName  = substr($baseName, 0, 80);

        $random = bin2hex(random_bytes(8));

        return $random . '_' . $baseName . ($extension !== '' ? '.' . $extension : '');
    }

    public function getUploadDirectory(string $mediaType): string
    {
        return match ($mediaType) {
            'image'    => $this->uploadRoot . 'images' . DIRECTORY_SEPARATOR,
            'video'    => $this->uploadRoot . 'videos' . DIRECTORY_SEPARATOR,
            'audio'    => $this->uploadRoot . 'audio' . DIRECTORY_SEPARATOR,
            'document' => $this->uploadRoot . 'documents' . DIRECTORY_SEPARATOR,
            default    => $this->uploadRoot . 'other' . DIRECTORY_SEPARATOR,
        };
    }

    public function extractImageDimensions(string $fullPath, string $mediaType): array
    {
        if ($mediaType !== 'image' || !is_file($fullPath)) {
            return ['width' => null, 'height' => null];
        }

        $size = @getimagesize($fullPath);
        if ($size === false) {
            return ['width' => null, 'height' => null];
        }

        return [
            'width'  => isset($size[0]) ? (int)$size[0] : null,
            'height' => isset($size[1]) ? (int)$size[1] : null
        ];
    }

    protected function detectMimeType(string $tmpName): string
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        if ($finfo === false) {
            return 'application/octet-stream';
        }

        $mimeType = finfo_file($finfo, $tmpName) ?: 'application/octet-stream';
        finfo_close($finfo);

        return $mimeType;
    }

    protected function isAllowedExtensionForType(string $extension, string $mediaType): bool
    {
        if ($mediaType === 'other') {
            return false;
        }

        return isset($this->allowedExtensions[$mediaType])
            && in_array($extension, $this->allowedExtensions[$mediaType], true);
    }

    protected function buildRelativePath(string $mediaType, string $storedName): string
    {
        return match ($mediaType) {
            'image'    => 'WIMedia/uploads/images/' . $storedName,
            'video'    => 'WIMedia/uploads/videos/' . $storedName,
            'audio'    => 'WIMedia/uploads/audio/' . $storedName,
            'document' => 'WIMedia/uploads/documents/' . $storedName,
            default    => 'WIMedia/uploads/other/' . $storedName,
        };
    }

    protected function buildFileUrl(string $mediaType, string $storedName): string
    {
        return match ($mediaType) {
            'image'    => $this->uploadBaseUrl . 'images/' . rawurlencode($storedName),
            'video'    => $this->uploadBaseUrl . 'videos/' . rawurlencode($storedName),
            'audio'    => $this->uploadBaseUrl . 'audio/' . rawurlencode($storedName),
            'document' => $this->uploadBaseUrl . 'documents/' . rawurlencode($storedName),
            default    => $this->uploadBaseUrl . 'other/' . rawurlencode($storedName),
        };
    }

    protected function absolutePathFromRelative(string $relativePath): string
    {
        if (defined('ROOT_PATH')) {
            return rtrim(ROOT_PATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($relativePath, DIRECTORY_SEPARATOR);
        }

        return dirname(__DIR__) . DIRECTORY_SEPARATOR . ltrim($relativePath, DIRECTORY_SEPARATOR);
    }

    protected function slugify(string $value): string
    {
        $value = strtolower($value);
        $value = preg_replace('/[^a-z0-9]+/i', '-', $value);
        $value = trim((string)$value, '-');

        return $value !== '' ? $value : 'file';
    }

    protected function generateUuid(): string
    {
        return bin2hex(random_bytes(16));
    }

    protected function nullIfEmptyString(mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string)$value);
        return $value === '' ? null : $value;
    }

    protected function normaliseFlag(mixed $value): int
    {
        return ((int)$value === 1) ? 1 : 0;
    }
}