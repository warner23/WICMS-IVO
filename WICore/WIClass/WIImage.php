<?php
declare(strict_types=1);

/**
 * Image Class
 * Created by Warner Infinity
 * Author Jules Warner
 */

#[\AllowDynamicProperties]
class WIImage
{

    private const MAX_SIZE = 5242880; // 5MB

    private const ALLOWED_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp'
    ];

    private string $uploadDir = 'WIMedia/Img/';

    private function randomName(string $extension): string
    {
        return bin2hex(random_bytes(16)) . '.' . $extension;
    }

    private function validateUpload(array $file): array
    {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return ['error' => 'Invalid upload'];
        }

        if ($file['size'] > self::MAX_SIZE) {
            return ['error' => 'File too large'];
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);

        if (!isset(self::ALLOWED_TYPES[$mime])) {
            return ['error' => 'Invalid file type'];
        }

        $extension = self::ALLOWED_TYPES[$mime];

        return [
            'tmp' => $file['tmp_name'],
            'extension' => $extension
        ];
    }

    private function reEncodeImage(string $tmp, string $extension, string $target): bool
    {
        switch ($extension) {

            case 'jpg':
                $img = imagecreatefromjpeg($tmp);
                imagejpeg($img, $target, 90);
                break;

            case 'png':
                $img = imagecreatefrompng($tmp);
                imagepng($img, $target);
                break;

            case 'gif':
                $img = imagecreatefromgif($tmp);
                imagegif($img, $target);
                break;

            case 'webp':
                $img = imagecreatefromwebp($tmp);
                imagewebp($img, $target);
                break;

            default:
                return false;
        }

        imagedestroy($img);

        return true;
    }

    public function upload(array $file): array
    {
        $validation = $this->validateUpload($file);

        if (isset($validation['error'])) {
            return [
                "status" => "error",
                "message" => $validation['error']
            ];
        }

        $extension = $validation['extension'];
        $tmp = $validation['tmp'];

        $filename = $this->randomName($extension);

        $target = $this->uploadDir . $filename;

        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }

        if (!$this->reEncodeImage($tmp, $extension, $target)) {
            return [
                "status" => "error",
                "message" => "Image processing failed"
            ];
        }

        return [
            "status" => "success",
            "filename" => $filename,
            "path" => $target
        ];
    }

    public function resize(string $path, int $width, int $height): bool
    {
        if (!file_exists($path)) {
            return false;
        }

        $info = getimagesize($path);

        if (!$info) {
            return false;
        }

        [$origWidth, $origHeight] = $info;

        $src = imagecreatefromstring(file_get_contents($path));

        $dst = imagecreatetruecolor($width, $height);

        imagecopyresampled(
            $dst,
            $src,
            0,
            0,
            0,
            0,
            $width,
            $height,
            $origWidth,
            $origHeight
        );

        imagejpeg($dst, $path, 90);

        imagedestroy($src);
        imagedestroy($dst);

        return true;
    }

}
?>