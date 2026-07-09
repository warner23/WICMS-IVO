<?php
/**
 * WICMS Core Pages Manager route catch.
 *
 * The old Edit Page screen used legacy modal/module assignment behaviour. It is
 * now routed into the modern Pages manager. Module creation/scaffolding remains
 * owned by the Modules area.
 */

if (!class_exists('WIPage')) {
    $classPath = dirname(__DIR__) . '/WICore/WIClass/WIPage.php';
    if (is_file($classPath)) {
        require_once $classPath;
    }
}

require __DIR__ . '/pages.php';
