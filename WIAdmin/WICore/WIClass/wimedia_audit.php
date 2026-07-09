<?php
/**
 * WIMedia Audit Tool
 *
 * Location: /tools/wimedia_audit.php
 * Purpose: Finds old upload classes and common direct upload patterns before production lock.
 * Run from CLI only.
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    exit("CLI only.\n");
}

$root = realpath(__DIR__ . '/..') ?: getcwd();
$patterns = [
    'move_uploaded_file',
    '$_FILES',
    'FaviconImageUpload',
    'HeaderImageUpload',
    'LangImageUpload',
    'MediaImageUpload',
    'PageImageUpload',
    'TeamMediaUpload',
    'TrainMediaUpload',
    'MediaUploads',
];

$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
$matches = [];

foreach ($rii as $file) {
    if (!$file->isFile()) {
        continue;
    }

    $path = $file->getPathname();

    if (!preg_match('/\.(php|js)$/i', $path)) {
        continue;
    }

    $content = file_get_contents($path) ?: '';

    foreach ($patterns as $pattern) {
        if (stripos($content, $pattern) !== false) {
            $matches[] = [$path, $pattern];
        }
    }
}

echo "WIMedia audit results\n";
echo "=====================\n";

foreach ($matches as [$path, $pattern]) {
    echo $pattern . " => " . str_replace($root . DIRECTORY_SEPARATOR, '', $path) . "\n";
}

echo "\nReview any active direct upload paths before production lock.\n";
