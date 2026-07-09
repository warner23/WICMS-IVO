<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS / WICOS / WIKitchenCompli
| Class: WIMediaMetadataService
| File: WIMediaMetadataService.php
| Location: /WIAdmin/WICore/WIClass/Media/WIMediaMetadataService.php
| Type: Media Metadata Service
| Layer: Shared Core Service
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Purpose
|--------------------------------------------------------------------------
| Extracts safe metadata such as image dimensions, file hashes, and normalised
| upload metadata. No database work happens in this class.
|--------------------------------------------------------------------------
*/

class WIMediaMetadataService
{
    /**
     * Extracts image dimensions when applicable.
     *
     * @param string $fullPath
     * @param string $mediaType
     * @return array<string, int|null>
     */
    public function imageDimensions(string $fullPath, string $mediaType): array
    {
        if ($mediaType !== 'image' || !is_file($fullPath)) {
            return ['width' => null, 'height' => null];
        }

        $size = @getimagesize($fullPath);

        if ($size === false) {
            return ['width' => null, 'height' => null];
        }

        return [
            'width' => isset($size[0]) ? (int) $size[0] : null,
            'height' => isset($size[1]) ? (int) $size[1] : null,
        ];
    }

    /**
     * Creates a SHA-256 hash for integrity and duplicate detection.
     *
     * @param string $fullPath
     * @return string|null
     */
    public function sha256(string $fullPath): ?string
    {
        if (!is_file($fullPath)) {
            return null;
        }

        $hash = hash_file('sha256', $fullPath);

        return is_string($hash) ? $hash : null;
    }

    /**
     * Normalises nullable text.
     *
     * @param mixed $value
     * @return string|null
     */
    public function nullableText(mixed $value): ?string
    {
        if ($value === null || is_array($value)) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    /**
     * Normalises a flag to 0 or 1.
     *
     * @param mixed $value
     * @return int
     */
    public function flag(mixed $value): int
    {
        return ((int) $value === 1) ? 1 : 0;
    }

    /**
     * Generates a random UUID-like value for local use.
     *
     * @return string
     */
    public function uuid(): string
    {
        return bin2hex(random_bytes(16));
    }
}
