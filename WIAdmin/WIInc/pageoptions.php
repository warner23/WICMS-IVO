<?php
/**
 * WICMS Core Pages Manager route catch.
 *
 * The old Page Options screen used inline CSS, old switches and page-elements
 * behaviour. It is intentionally routed into the modern Pages manager so older
 * sidebar links/bookmarks do not show the legacy table/options page.
 */

if (!class_exists('WIPage')) {
    $classPath = dirname(__DIR__) . '/WICore/WIClass/WIPage.php';
    if (is_file($classPath)) {
        require_once $classPath;
    }
}

require __DIR__ . '/pages.php';
