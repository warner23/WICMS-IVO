<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS / WICOS / WIKitchenCompli
| Class: WIWidget
| File: WIWidget.php
| Location: /WIAdmin/WICore/WIClass/WIWidget.php
| Type: Widget / Module Loader
| Layer: Admin UI
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Purpose
|--------------------------------------------------------------------------
| Shared widget and module loader helper.
|
| Notes:
| - Keeps legacy public API
| - Adds safer path and class validation
| - Uses WIdb only for future compatibility if needed
|--------------------------------------------------------------------------
*/

class WIWidget
{
    protected WIdb $WIdb;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
    }

    public function getWidget(string $widget): void
    {
        $widget = $this->sanitizeName($widget);

        if ($widget === '') {
            throw new RuntimeException('Invalid widget name.');
        }

        $file = $this->buildWidgetPath($widget);

        if (!is_file($file)) {
            throw new RuntimeException('Widget file not found: ' . $widget);
        }

        require_once $file;

        if (!class_exists($widget)) {
            throw new RuntimeException('Widget class not found: ' . $widget);
        }

        $instance = new $widget();

        if (!method_exists($instance, 'mod_name')) {
            throw new RuntimeException('Widget does not implement mod_name(): ' . $widget);
        }

        $instance->mod_name();
    }

    public function getModMain(string $mod, string $page, string $module): void
    {
        $mod = $this->sanitizeName($mod);

        if ($mod === '') {
            throw new RuntimeException('Invalid module name.');
        }

        $file = $this->buildModulePath($mod);

        if (!is_file($file)) {
            throw new RuntimeException('Module file not found: ' . $mod);
        }

        require_once $file;

        if (!class_exists($mod)) {
            throw new RuntimeException('Module class not found: ' . $mod);
        }

        $instance = new $mod();

        if (!method_exists($instance, 'mod_name')) {
            throw new RuntimeException('Module does not implement mod_name(): ' . $mod);
        }

        $instance->mod_name($module, $page);
    }

    protected function sanitizeName(string $value): string
    {
        $value = trim($value);

        if (!preg_match('/^[A-Za-z0-9_\\-]+$/', $value)) {
            return '';
        }

        return $value;
    }

    protected function buildWidgetPath(string $widget): string
    {
        return 'WIAdmin/WIWidget/' . $widget . '/' . $widget . '.php';
    }

    protected function buildModulePath(string $mod): string
    {
        return 'WIAdmin/WIModule/' . $mod . '/' . $mod . '.php';
    }
}