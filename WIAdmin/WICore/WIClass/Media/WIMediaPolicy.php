<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS / WICOS / WIKitchenCompli
| Class: WIMediaPolicy
| File: WIMediaPolicy.php
| Location: /WIAdmin/WICore/WIClass/Media/WIMediaPolicy.php
| Type: Media Policy
| Layer: Shared Core Service
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Purpose
|--------------------------------------------------------------------------
| Central media policy for allowed file types, blocked file types, size
| limits, storage buckets, and shared media type detection.
|--------------------------------------------------------------------------
*/

class WIMediaPolicy
{
    /** @var int Maximum upload size in bytes. */
    private int $maxFileSize = 104857600;

    /** @var array<string, array<int, string>> */
    private array $allowedExtensions = [
        'image' => [
            'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'tif', 'tiff', 'heic', 'avif',
        ],
        'video' => [
            'mp4', 'mpeg', 'mpg', 'mov', 'avi', 'webm', 'ogv', 'mkv', 'm4v', 'wmv', '3gp',
        ],
        'audio' => [
            'mp3', 'wav', 'ogg', 'oga', 'm4a', 'aac', 'flac', 'wma', 'webm',
        ],
        'document' => [
            'pdf', 'txt', 'rtf', 'csv', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'odt', 'ods', 'odp',
        ],
        'archive' => [
            'zip', '7z', 'rar', 'tar', 'gz',
        ],
    ];

    /** @var array<int, string> */
    private array $blockedExtensions = [
        'php', 'php3', 'php4', 'php5', 'phtml', 'phar', 'inc',
        'exe', 'dll', 'com', 'msi', 'scr', 'bat', 'cmd', 'sh', 'bash', 'zsh', 'ps1',
        'js', 'mjs', 'html', 'htm', 'xhtml', 'shtml', 'cgi', 'pl', 'py', 'rb', 'jar',
        'docm', 'xlsm', 'pptm',
    ];

    /**
     * Returns the configured maximum upload size.
     *
     * @return int
     */
    public function maxFileSize(): int
    {
        return $this->maxFileSize;
    }

    /**
     * Returns all allowed extensions grouped by media type.
     *
     * @return array<string, array<int, string>>
     */
    public function allowedExtensions(): array
    {
        return $this->allowedExtensions;
    }

    /**
     * Returns blocked extensions.
     *
     * @return array<int, string>
     */
    public function blockedExtensions(): array
    {
        return $this->blockedExtensions;
    }

    /**
     * Detects the high-level media type from MIME and extension.
     *
     * @param string $mimeType
     * @param string $extension
     * @return string
     */
    public function detectMediaType(string $mimeType, string $extension): string
    {
        $extension = strtolower(trim($extension));
        $mimeType = strtolower(trim($mimeType));

        foreach ($this->allowedExtensions as $type => $extensions) {
            if (in_array($extension, $extensions, true)) {
                return $type === 'archive' ? 'document' : $type;
            }
        }

        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }

        if (str_starts_with($mimeType, 'video/')) {
            return 'video';
        }

        if (str_starts_with($mimeType, 'audio/')) {
            return 'audio';
        }

        return 'other';
    }

    /**
     * Checks whether an extension is blocked.
     *
     * @param string $extension
     * @return bool
     */
    public function isBlockedExtension(string $extension): bool
    {
        return in_array(strtolower(trim($extension)), $this->blockedExtensions, true);
    }

    /**
     * Checks whether a file extension is allowed for a detected media type.
     *
     * @param string $extension
     * @param string $mediaType
     * @return bool
     */
    public function isAllowedExtensionForType(string $extension, string $mediaType): bool
    {
        $extension = strtolower(trim($extension));
        $mediaType = strtolower(trim($mediaType));

        if ($mediaType === 'other') {
            return false;
        }

        if ($mediaType === 'document' && in_array($extension, $this->allowedExtensions['archive'], true)) {
            return true;
        }

        return isset($this->allowedExtensions[$mediaType])
            && in_array($extension, $this->allowedExtensions[$mediaType], true);
    }

    /**
     * Returns a safe storage folder by media type.
     *
     * @param string $mediaType
     * @return string
     */
    public function folderForMediaType(string $mediaType): string
    {
        return match ($mediaType) {
            'image' => 'images',
            'video' => 'videos',
            'audio' => 'audio',
            'document' => 'documents',
            default => 'other',
        };
    }
}
