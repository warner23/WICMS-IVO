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
| File: WIMediaSecurityPolicy.php
| Location: /root/WIAdmin/WICore/WIClass/Media/WIMediaSecurityPolicy.php
| Type: PHP Security Policy
| Layer: Shared Media Security
| Purpose Area: Upload Validation / MIME Policy
| Version: 1.1.0
| Created: 2026-05-05
| Last Updated: 2026-05-05
| Status: Batch 1 Refactor
|--------------------------------------------------------------------------
| Summary:
| Central safe file extension and MIME policy for WIMedia.
| - Validates PHP upload arrays before storage
| - Blocks executable, scriptable and macro-enabled uploads
| - Detects MIME type and maps media type consistently
| - Rejects unsafe filenames containing path/control characters
| - Contains no database logic and no compliance dependency
|--------------------------------------------------------------------------
*/

final class WIMediaSecurityPolicy
{
    private int $maxFileSize = 104857600;

    /**
     * @var array<string, array<int, string>>
     */
    private array $allowedExtensions = [
        'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'tif', 'tiff', 'heic', 'avif'],
        'document' => ['pdf', 'txt', 'rtf', 'csv', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'odt', 'ods', 'odp'],
        'audio' => ['mp3', 'wav', 'ogg', 'oga', 'm4a', 'aac', 'flac', 'wma', 'webm'],
        'video' => ['mp4', 'mpeg', 'mpg', 'mov', 'avi', 'webm', 'ogv', 'mkv', 'm4v', 'wmv', '3gp'],
        'archive' => ['zip', '7z', 'rar', 'tar', 'gz'],
    ];

    /**
     * @var array<int, string>
     */
    private array $blockedExtensions = [
        'php', 'phtml', 'phar', 'exe', 'dll', 'js', 'html', 'htm', 'sh', 'bat', 'cmd',
        'msi', 'com', 'cgi', 'pl', 'py', 'jar', 'docm', 'xlsm', 'pptm',
    ];

    /**
     * Validates one uploaded file before storage.
     *
     * @param array<string, mixed> $file Standard PHP uploaded file array.
     *
     * @return array<string, mixed>
     */
    public function validateFile(array $file): array
    {
        if ($file === []) {
            return $this->error('No file received.');
        }

        if (!isset($file['error']) || is_array($file['error'])) {
            return $this->error('Invalid upload parameters.');
        }

        if ((int)$file['error'] !== UPLOAD_ERR_OK) {
            return $this->error($this->uploadErrorMessage((int)$file['error']));
        }

        if (!isset($file['tmp_name'])) {
            return $this->error('Missing temporary upload path.');
        }

        $tmpName = (string)$file['tmp_name'];

        if (!is_uploaded_file($tmpName)) {
            /*
             * Production must reject non-uploaded files.
             * CLI/local tests may pass a normal temp file only when explicitly allowed.
             */
            if (!defined('WI_MEDIA_ALLOW_LOCAL_TEST_UPLOADS') || WI_MEDIA_ALLOW_LOCAL_TEST_UPLOADS !== true) {
                return $this->error('Possible file upload attack detected.');
            }

            if (!is_file($tmpName)) {
                return $this->error('Temporary upload file was not found.');
            }
        }

        $size = (int)($file['size'] ?? 0);

        if ($size <= 0) {
            return $this->error('Uploaded file is empty.');
        }

        if ($size > $this->maxFileSize) {
            return $this->error('File exceeds the maximum allowed size.');
        }

        $originalName = trim((string)($file['name'] ?? ''));

        if ($originalName === '' || $this->hasUnsafeNameCharacters($originalName)) {
            return $this->error('Invalid file name.');
        }

        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if ($extension === '') {
            return $this->error('Invalid file name.');
        }

        if (in_array($extension, $this->blockedExtensions, true)) {
            return $this->error('This file type is blocked.');
        }

        $mimeType = $this->detectMimeType($tmpName);
        $mediaType = $this->detectMediaType($mimeType, $extension);

        if (!$this->isAllowedExtensionForType($extension, $mediaType)) {
            return $this->error('The uploaded file type is not allowed.');
        }

        return [
            'success' => true,
            'status' => 'success',
            'message' => 'File is valid.',
            'extension' => $extension,
            'mime_type' => $mimeType,
            'media_type' => $mediaType,
        ];
    }

    /**
     * Detects a file MIME type from the temporary upload path.
     *
     * @param string $tmpName Temporary file path.
     *
     * @return string
     */
    public function detectMimeType(string $tmpName): string
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        if ($finfo === false) {
            return 'application/octet-stream';
        }

        $mimeType = finfo_file($finfo, $tmpName) ?: 'application/octet-stream';
        finfo_close($finfo);

        return strtolower($mimeType);
    }

    /**
     * Maps MIME/extension to a WIMedia media type.
     *
     * @param string $mimeType Detected MIME type.
     * @param string $extension Validated extension.
     *
     * @return string
     */
    public function detectMediaType(string $mimeType, string $extension): string
    {
        $extension = strtolower($extension);
        $mimeType = strtolower($mimeType);

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

        if (in_array($extension, $this->allowedExtensions['archive'], true)) {
            return 'archive';
        }

        return 'other';
    }

    /**
     * Checks whether the extension is allowed for the detected WIMedia type.
     *
     * @param string $extension File extension.
     * @param string $mediaType WIMedia media type.
     *
     * @return bool
     */
    private function isAllowedExtensionForType(string $extension, string $mediaType): bool
    {
        return isset($this->allowedExtensions[$mediaType])
            && in_array($extension, $this->allowedExtensions[$mediaType], true);
    }

    /**
     * Detects path/control characters that should never be present in upload names.
     *
     * @param string $name Original upload name.
     *
     * @return bool
     */
    private function hasUnsafeNameCharacters(string $name): bool
    {
        return str_contains($name, "\0")
            || str_contains($name, '/')
            || str_contains($name, '\\')
            || preg_match('/[\x00-\x1F\x7F]/', $name) === 1;
    }

    /**
     * Converts PHP upload error codes into safe user-facing messages.
     *
     * @param int $error PHP upload error code.
     *
     * @return string
     */
    private function uploadErrorMessage(int $error): string
    {
        return match ($error) {
            UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Uploaded file is too large.',
            UPLOAD_ERR_PARTIAL => 'File upload was incomplete.',
            default => 'Unknown upload error.',
        };
    }

    /**
     * Builds a standard error response.
     *
     * @param string $message Error message.
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
}
