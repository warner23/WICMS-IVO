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
| File: WIMediaStorageService.php
| Location: /root/WIAdmin/WICore/WIClass/Media/WIMediaStorageService.php
| Type: PHP Service Class
| Layer: Shared Media Storage
| Purpose Area: Physical Storage / URL Resolution
| Version: 1.1.0
| Created: 2026-05-05
| Last Updated: 2026-05-05
| Status: Batch 1 Refactor
|--------------------------------------------------------------------------
| Summary:
| Shared WIMedia physical storage service.
| - Owns canonical storage paths under /WIAdmin/WIMedia/
| - Moves uploaded files after validation
| - Builds relative paths and file URLs
| - Contains no database logic and no compliance dependency
|--------------------------------------------------------------------------
*/

final class WIMediaStorageService
{
    private string $rootPath;

    private string $baseUrl;

    /**
     * Creates the storage service.
     *
     * @param string|null $rootPath Optional absolute storage root.
     * @param string|null $baseUrl Optional public base URL.
     */
    public function __construct(?string $rootPath = null, ?string $baseUrl = null)
    {
        $this->rootPath = $rootPath ?? $this->resolveRootPath();
        $this->baseUrl = $baseUrl ?? $this->resolveBaseUrl();
    }

    /**
     * Stores a validated uploaded file in the correct WIMedia folder.
     *
     * @param array<string, mixed> $file Standard PHP upload file array.
     * @param string $mediaType WIMedia media type.
     * @param string $storedName Generated safe stored filename.
     *
     * @return array<string, mixed>
     */
    public function storeUploadedFile(array $file, string $mediaType, string $storedName, ?string $folder = null): array
    {
        $directory = $this->getDirectory($mediaType, $folder);

        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            return $this->error('Failed to create media storage directory.');
        }

        $destination = $directory . $storedName;

        $tmpName = (string)$file['tmp_name'];

        $moved = is_uploaded_file($tmpName)
            ? move_uploaded_file($tmpName, $destination)
            : (
                defined('WI_MEDIA_ALLOW_LOCAL_TEST_UPLOADS')
                && WI_MEDIA_ALLOW_LOCAL_TEST_UPLOADS === true
                && is_file($tmpName)
                && copy($tmpName, $destination)
            );

        if (!$moved) {
            return $this->error('Failed to move uploaded file.');
        }

        return [
            'success' => true,
            'status' => 'success',
            'absolute_path' => $destination,
            'relative_path' => $this->buildRelativePath($mediaType, $storedName, $folder),
            'file_url' => $this->buildFileUrl($mediaType, $storedName, $folder),
        ];
    }

    /**
     * Returns the absolute storage directory for a media type.
     *
     * @param string $mediaType WIMedia media type.
     *
     * @return string
     */
    public function getDirectory(string $mediaType, ?string $folder = null): string
    {
        return $this->rootPath . $this->folderForType($mediaType, $folder) . DIRECTORY_SEPARATOR;
    }

    /**
     * Builds the stored relative file path.
     *
     * @param string $mediaType WIMedia media type.
     * @param string $storedName Stored filename.
     *
     * @return string
     */
    public function buildRelativePath(string $mediaType, string $storedName, ?string $folder = null): string
    {
        return 'WIAdmin/WIMedia/' . $this->folderForType($mediaType, $folder) . '/' . $storedName;
    }

    /**
     * Builds the public file URL.
     *
     * @param string $mediaType WIMedia media type.
     * @param string $storedName Stored filename.
     *
     * @return string
     */
    public function buildFileUrl(string $mediaType, string $storedName, ?string $folder = null): string
    {
        return rtrim($this->baseUrl, '/') . '/' . $this->folderForType($mediaType, $folder) . '/' . rawurlencode($storedName);
    }

    /**
     * Resolves absolute path from stored relative path.
     *
     * @param string $relativePath Relative path.
     *
     * @return string
     */
    public function absolutePathFromRelative(string $relativePath): string
    {
        $relativePath = ltrim($relativePath, '/\\');

        if (defined('ROOT_PATH')) {
            return rtrim((string)ROOT_PATH, DIRECTORY_SEPARATOR)
                . DIRECTORY_SEPARATOR
                . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath);
        }

        /*
         * From:
         * /WIAdmin/WICore/WIClass/Media/
         *
         * dirname(__DIR__, 4) = project root.
         */
        return dirname(__DIR__, 4)
            . DIRECTORY_SEPARATOR
            . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath);
    }

    /**
     * Deletes a stored physical file when it exists.
     *
     * @param string $relativePath Stored relative path.
     *
     * @return bool
     */
    public function deletePhysicalFile(string $relativePath): bool
    {
        $absolute = $this->absolutePathFromRelative($relativePath);

        if (!is_file($absolute)) {
            return true;
        }

        return @unlink($absolute);
    }

    /**
     * Resolves canonical WIMedia storage root.
     *
     * @return string
     */
    private function resolveRootPath(): string
    {
        /*
         * When this file is in:
         * /WIAdmin/WICore/WIClass/Media/
         *
         * dirname(__DIR__, 2) = /WIAdmin/WICore
         * dirname(__DIR__, 3) = /WIAdmin
         *
         * Canonical storage:
         * /WIAdmin/WIMedia/
         */
        if (defined('ROOT_PATH')) {
            return rtrim((string)ROOT_PATH, DIRECTORY_SEPARATOR)
                . DIRECTORY_SEPARATOR
                . 'WIAdmin'
                . DIRECTORY_SEPARATOR
                . 'WIMedia'
                . DIRECTORY_SEPARATOR;
        }

        return dirname(__DIR__, 3)
            . DIRECTORY_SEPARATOR
            . 'WIMedia'
            . DIRECTORY_SEPARATOR;
    }

    private function resolveBaseUrl(): string
    {
        if (defined('SITE_URL')) {
            return rtrim((string)SITE_URL, '/') . '/WIAdmin/WIMedia';
        }

        return '/WIAdmin/WIMedia';
    }

    private function folderForType(string $mediaType, ?string $folder = null): string
    {
        $base = match ($mediaType) {
            'image' => 'Images',
            'video' => 'Videos',
            'audio' => 'Audio',
            'document' => 'Documents',
            'archive' => 'Archives',
            default => 'Other',
        };

        $folder = trim((string)$folder, " /\\\t\n\r\0\x0B");

        if ($folder === '') {
            return $base;
        }

        $parts = preg_split('#[\\/]+#', $folder) ?: [];
        $safeParts = [];

        foreach ($parts as $part) {
            $part = strtolower(trim((string)$part));
            $part = preg_replace('/[^a-z0-9_-]+/', '-', $part) ?? '';
            $part = trim($part, '-_');

            if ($part !== '') {
                $safeParts[] = $part;
            }
        }

        if ($safeParts === []) {
            return $base;
        }

        return $base . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $safeParts);
    }

    private function error(string $message): array
    {
        return [
            'success' => false,
            'status' => 'error',
            'message' => $message,
        ];
    }
}