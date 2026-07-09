<?php
/**
 * WICMS Core Pages Manager route catch.
 *
 * The old New Page screen is routed into the modern Pages manager so old
 * sidebar links/bookmarks do not show the legacy table/options/page-elements
 * flow. Module scaffolding remains owned by the Modules area.
 */

if (!class_exists('WIPage')) {
    $classPath = dirname(__DIR__) . '/WICore/WIClass/WIPage.php';
    if (is_file($classPath)) {
        require_once $classPath;
    }
}

require __DIR__ . '/pages.php';
