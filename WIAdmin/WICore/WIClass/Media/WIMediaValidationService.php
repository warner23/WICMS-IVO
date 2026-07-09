<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS / WICOS / WIKitchenCompli
| Class: WIMediaValidationService
| File: WIMediaValidationService.php
| Location: /WIAdmin/WICore/WIClass/Media/WIMediaValidationService.php
| Type: Media Validation Service
| Layer: Shared Core Service
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Purpose
|--------------------------------------------------------------------------
| Validates uploaded files using server-side checks, extension policy, upload
| error state, file size, and detected MIME type.
|--------------------------------------------------------------------------
*/

class WIMediaValidationService
{
    private WIMediaPolicy $policy;

    /**
     * Creates the validation service.
     *
     * @param WIMediaPolicy $policy
     */
    public function __construct(WIMediaPolicy $policy)
    {
        $this->policy = $policy;
    }

    /**
     * Validates an uploaded file.
     *
     * @param array<string, mixed> $file
     * @return array<string, mixed>
     */
    public function validateFile(array $file): array
    {
        if ($file === []) {
            return ['status' => 'error', 'message' => 'No file received.'];
        }

        if (!isset($file['error']) || is_array($file['error'])) {
            return ['status' => 'error', 'message' => 'Invalid upload parameters.'];
        }

        switch ((int) $file['error']) {
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

        $tmpName = (string) ($file['tmp_name'] ?? '');

        if ($tmpName === '' || !is_uploaded_file($tmpName)) {
            return ['status' => 'error', 'message' => 'Possible file upload attack detected.'];
        }

        $fileSize = isset($file['size']) ? (int) $file['size'] : 0;

        if ($fileSize <= 0) {
            return ['status' => 'error', 'message' => 'Uploaded file is empty.'];
        }

        if ($fileSize > $this->policy->maxFileSize()) {
            return ['status' => 'error', 'message' => 'File exceeds the maximum allowed size.'];
        }

        $originalName = trim((string) ($file['name'] ?? ''));

        if ($originalName === '') {
            return ['status' => 'error', 'message' => 'Invalid file name.'];
        }

        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if ($extension === '') {
            return ['status' => 'error', 'message' => 'File has no extension.'];
        }

        if ($this->policy->isBlockedExtension($extension)) {
            return ['status' => 'error', 'message' => 'This file type is not allowed.'];
        }

        $mimeType = $this->detectMimeType($tmpName);
        $mediaType = $this->policy->detectMediaType($mimeType, $extension);

        if (!$this->policy->isAllowedExtensionForType($extension, $mediaType)) {
            return ['status' => 'error', 'message' => 'The uploaded file type is not allowed.'];
        }

        return [
            'status' => 'success',
            'message' => 'File is valid.',
            'extension' => $extension,
            'mime_type' => $mimeType,
            'media_type' => $mediaType,
        ];
    }

    /**
     * Detects MIME type from the actual temporary file.
     *
     * @param string $filePath
     * @return string
     */
    public function detectMimeType(string $filePath): string
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        if ($finfo === false) {
            return 'application/octet-stream';
        }

        $mimeType = finfo_file($finfo, $filePath) ?: 'application/octet-stream';
        finfo_close($finfo);

        return $mimeType;
    }
}
