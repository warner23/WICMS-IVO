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
| File: WIMediaUploadService.php
| Location: /root/WIAdmin/WICore/WIClass/Media/WIMediaUploadService.php
| Type: PHP Service Class
| Layer: Shared Media Service
| Purpose Area: Media Upload / Storage Registration
| Version: 1.1.0
| Created: 2026-05-05
| Last Updated: 2026-05-05
| Status: Batch 1 Refactor
|--------------------------------------------------------------------------
| Summary:
| Shared WIMedia upload service.
| - Validates uploaded files through WIMediaSecurityPolicy
| - Stores files through WIMediaStorageService
| - Creates wi_media records through WIMediaRepository
| - Logs upload events through WIMediaEventService
| - Contains no direct database connection and no compliance dependency
|--------------------------------------------------------------------------
*/

final class WIMediaUploadService
{
    private WIMediaRepository $Repository;

    private WIMediaSecurityPolicy $Policy;

    private WIMediaStorageService $Storage;

    private WIMediaEventService $Events;

    /*
    |--------------------------------------------------------------------------
    | Constructor
    |--------------------------------------------------------------------------
    */

    /**
     * Creates the upload service with injectable media dependencies.
     *
     * @param WIMediaRepository|null $Repository Optional media repository.
     * @param WIMediaSecurityPolicy|null $Policy Optional upload security policy.
     * @param WIMediaStorageService|null $Storage Optional storage service.
     * @param WIMediaEventService|null $Events Optional event service.
     */
    public function __construct(
        ?WIMediaRepository $Repository = null,
        ?WIMediaSecurityPolicy $Policy = null,
        ?WIMediaStorageService $Storage = null,
        ?WIMediaEventService $Events = null
    ) {
        $this->Repository = $Repository ?? new WIMediaRepository();
        $this->Policy = $Policy ?? new WIMediaSecurityPolicy();
        $this->Storage = $Storage ?? new WIMediaStorageService();
        $this->Events = $Events ?? new WIMediaEventService();
    }

    /*
    |--------------------------------------------------------------------------
    | Upload
    |--------------------------------------------------------------------------
    */

    /**
     * Validates, stores and registers a single uploaded file.
     *
     * @param array<string, mixed> $file Standard PHP uploaded file array.
     * @param array<string, mixed> $context Optional upload/media context.
     *
     * @return array<string, mixed>
     */
    public function upload(array $file, array $context = []): array
    {
        $validation = $this->Policy->validateFile($file);

        if (($validation['success'] ?? false) !== true) {
            return $validation;
        }

        $originalName = trim((string)($file['name'] ?? ''));
        $extension = (string)$validation['extension'];
        $mimeType = (string)$validation['mime_type'];
        $mediaType = (string)$validation['media_type'];
        $storedName = $this->generateStoredName($originalName, $extension);

        $storage = $this->Storage->storeUploadedFile(
            $file,
            $mediaType,
            $storedName,
            $this->stringOrNull($context['folder'] ?? null)
        );

        if (($storage['success'] ?? false) !== true) {
            return $storage;
        }

        $absolutePath = (string)$storage['absolute_path'];
        $dimensions = $this->extractImageDimensions($absolutePath, $mediaType);
        $hash = is_file($absolutePath) ? hash_file('sha256', $absolutePath) : null;

        if (is_file($absolutePath)) {
            @chmod($absolutePath, 0644);
        }

        $insert = [
            'uuid' => $this->uuid(),
            'title' => $this->stringOrDefault($context['title'] ?? null, pathinfo($originalName, PATHINFO_FILENAME)),
            'alt_text' => $this->stringOrNull($context['alt_text'] ?? null),
            'caption' => $this->stringOrNull($context['caption'] ?? null),
            'description' => $this->stringOrNull($context['description'] ?? null),
            'original_name' => $originalName,
            'stored_name' => $storedName,
            'file_path' => (string)$storage['relative_path'],
            'file_url' => (string)$storage['file_url'],
            'folder' => $this->stringOrNull($context['folder'] ?? null),
            'extension' => $extension,
            'mime_type' => $mimeType,
            'media_type' => $mediaType,
            'file_size' => (int)($file['size'] ?? 0),
            'width' => $dimensions['width'],
            'height' => $dimensions['height'],
            'duration' => null,
            'status' => $context['status'] ?? 1,
            'visibility' => $this->stringOrDefault($context['visibility'] ?? null, 'private'),
            'access_scope' => $this->stringOrDefault($context['access_scope'] ?? null, 'business'),
            'is_private' => (int)($context['is_private'] ?? 1),
            'is_sensitive' => (int)($context['is_sensitive'] ?? 0),
            'scan_status' => 'pending',
            'processing_status' => 'stored',
            'sha256_hash' => $hash,
            'org_business_id' => $this->positiveInt($context['org_business_id'] ?? $context['business_id'] ?? null),
            'org_site_id' => $this->positiveInt($context['org_site_id'] ?? $context['site_id'] ?? null),
            'org_department_id' => $this->positiveInt($context['org_department_id'] ?? $context['department_id'] ?? null),
            'uploaded_by' => $this->positiveInt($context['uploaded_by'] ?? $context['uploaded_by_user_id'] ?? $context['user_id'] ?? null),
            'uploaded_by_user_id' => $this->positiveInt($context['uploaded_by_user_id'] ?? $context['uploaded_by'] ?? $context['user_id'] ?? null),
            'created_by_user_id' => $this->positiveInt($context['created_by_user_id'] ?? $context['user_id'] ?? null),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $mediaId = $this->Repository->create($insert);

        if ($mediaId <= 0) {
            @unlink($absolutePath);

            return [
                'success' => false,
                'status' => 'error',
                'message' => 'Failed to save media record.',
            ];
        }

        $media = $this->Repository->find($mediaId);

        $this->Events->log('uploaded', array_merge($context, [
            'media_id' => $mediaId,
            'media_type' => $mediaType,
            'stored_name' => $storedName,
        ]));

        return [
            'success' => true,
            'status' => 'success',
            'message' => 'File uploaded successfully.',
            'media_id' => $mediaId,
            'media' => $media,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Filename / File Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Generates a collision-resistant stored filename.
     *
     * @param string $originalName Original uploaded filename.
     * @param string $extension Validated extension from WIMediaSecurityPolicy.
     *
     * @return string
     */
    private function generateStoredName(string $originalName, string $extension): string
    {
        $extension = strtolower(trim($extension));
        $base = strtolower(pathinfo($originalName, PATHINFO_FILENAME));
        $base = preg_replace('/[^a-z0-9]+/i', '-', $base) ?? 'file';
        $base = trim($base, '-');
        $base = substr($base !== '' ? $base : 'file', 0, 80);

        return bin2hex(random_bytes(16)) . '_' . $base . ($extension !== '' ? '.' . $extension : '');
    }

    /**
     * Extracts image dimensions when the uploaded media type is image.
     *
     * @param string $absolutePath Stored absolute file path.
     * @param string $mediaType Detected media type.
     *
     * @return array{width:int|null,height:int|null}
     */
    private function extractImageDimensions(string $absolutePath, string $mediaType): array
    {
        if ($mediaType !== 'image' || !is_file($absolutePath)) {
            return [
                'width' => null,
                'height' => null,
            ];
        }

        $size = @getimagesize($absolutePath);

        if ($size === false) {
            return [
                'width' => null,
                'height' => null,
            ];
        }

        return [
            'width' => (int)$size[0],
            'height' => (int)$size[1],
        ];
    }

    /**
     * Generates a UUID v4 for the media record.
     *
     * @return string
     */
    private function uuid(): string
    {
        $bytes = random_bytes(16);
        $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));
    }

    /*
    |--------------------------------------------------------------------------
    | Normalisation Helpers
    |--------------------------------------------------------------------------
    */

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
     * Converts a value to a string or returns a default.
     *
     * @param mixed $value Raw value.
     * @param string $default Default value.
     *
     * @return string
     */
    private function stringOrDefault(mixed $value, string $default): string
    {
        $value = $this->stringOrNull($value);

        return $value ?? $default;
    }
}
