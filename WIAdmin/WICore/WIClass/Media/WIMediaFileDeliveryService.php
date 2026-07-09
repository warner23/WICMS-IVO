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
| File: WIMediaFileDeliveryService.php
| Location: /root/WIAdmin/WICore/WIClass/Media/WIMediaFileDeliveryService.php
| Type: PHP Delivery Service
| Layer: Shared Media Delivery
| Purpose Area: Safe File Streaming
| Version: 1.1.0
| Created: 2026-05-05
| Last Updated: 2026-05-05
| Status: Batch 1 Refactor
|--------------------------------------------------------------------------
| Summary:
| Shared WIMedia file delivery service.
| - Streams media files with safe response headers
| - Supports inline view and forced download modes
| - Resolves paths through WIMediaStorageService
| - Contains no database logic and no compliance dependency
|--------------------------------------------------------------------------
*/

final class WIMediaFileDeliveryService
{
    private WIMediaStorageService $Storage;

    /**
     * Creates the file delivery service.
     *
     * @param WIMediaStorageService|null $Storage Optional storage service.
     */
    public function __construct(?WIMediaStorageService $Storage = null)
    {
        $this->Storage = $Storage ?? new WIMediaStorageService();
    }

    /**
     * Delivers a media row.
     *
     * @param array<string, mixed> $media Media row.
     * @param bool $download Whether to force download.
     *
     * @return never
     */
    public function deliver(array $media, bool $download = false): never
    {
        $relativePath = (string)($media['file_path'] ?? '');

        if ($relativePath === '') {
            $this->abort(404, 'Media file path is missing.');
        }

        $absolutePath = $this->Storage->absolutePathFromRelative($relativePath);

        if (!is_file($absolutePath) || !is_readable($absolutePath)) {
            $this->abort(404, 'Media file was not found.');
        }

        $mimeType = (string)($media['mime_type'] ?? 'application/octet-stream');
        $originalName = (string)($media['original_name'] ?? basename($absolutePath));
        $fileSize = filesize($absolutePath);

        if ($fileSize === false) {
            $this->abort(500, 'Unable to read media file size.');
        }

        $disposition = $download ? 'attachment' : 'inline';
        $safeName = $this->safeDownloadName($originalName);

        if (ob_get_level() > 0) {
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
        }

        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . (string)$fileSize);
        header('Content-Disposition: ' . $disposition . '; filename="' . $safeName . '"');
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: private, max-age=3600');
        header('Pragma: private');

        readfile($absolutePath);
        exit;
    }

    /**
     * Creates a safe download filename.
     *
     * @param string $name Original name.
     *
     * @return string
     */
    private function safeDownloadName(string $name): string
    {
        $name = trim($name);

        if ($name === '') {
            return 'download';
        }

        $name = preg_replace('/[^a-zA-Z0-9._ -]/', '_', $name) ?? 'download';

        return trim($name, '._ -') !== '' ? $name : 'download';
    }

    /**
     * Aborts request.
     *
     * @param int $code Status code.
     * @param string $message Message.
     *
     * @return never
     */
    private function abort(int $code, string $message): never
    {
        http_response_code($code);
        header('Content-Type: text/plain; charset=utf-8');
        echo $message;
        exit;
    }
}