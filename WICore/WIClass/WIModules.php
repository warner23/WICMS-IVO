<?php
declare(strict_types=1);

/**
 * Modules Class
 * Created by Warner Infinity
 * Author Jules Warner
 */

#[\AllowDynamicProperties]
class WIModules
{
    private $WIdb;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
    }

    private function e($value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }


     public function getMod($mod)
    {
        //echo $mod;
        //echo   'WIAdmin/WIModule/' .$mod.'/'.$mod.'.php';
        require_once  'WIAdmin/WIModule/modules/' .$mod.'/'.$mod.'.php';
        
       // echo $mod;
        $mod = new $mod;

        $mod->mod_name();
    }



    public function getModMain($mod, $page, $module)
    {
        $dir = 'WIAdmin/WIModule/pages/' .$mod.'/'.$mod.'.php';
        
        
        if(file_exists($dir)){
        require_once  'WIAdmin/WIModule/pages/' .$mod.'/'.$mod.'.php';
        $mod = new $mod;
        $mod->mod_name($page); 
        }else{
        require_once  'WIAdmin/WIModule/pages/notfound/notfound.php';
        $notfound = new notfound;
        $notfound->mod_name($page); 
        }
    }
    /**
     * Get installed modules
     */
    public function getInstalled(): array
    {
        $result = $this->WIdb->bindfree(
            "SELECT * FROM `wi_modules` ORDER BY `id` ASC"
        );

        return is_array($result) ? $result : [];
    }

    /**
     * Check module enabled
     */
    public function isEnabled(string $module): bool
    {
        $value = $this->WIdb->selectColumn(
            "SELECT * FROM `wi_modules` WHERE `name` = :name LIMIT 1",
            [
                "name" => $module
            ],
            "enabled"
        );

        return (int)$value === 1;
    }

    /**
     * Render module
     */
    public function render(string $module)
    {
        if (!$this->isEnabled($module)) {
            return;
        }

        $module = basename($module);

        $path = dirname(__DIR__) . "/WIModules/" . $module . "/" . $module . ".php";

        if (!file_exists($path)) {
            echo "<!-- Module {$this->e($module)} not found -->";
            return;
        }

        include $path;
    }

    /**
     * Get module information
     */
    public function getModule(string $module): ?array
    {
        $result = $this->WIdb->select(
            "SELECT * FROM `wi_modules` WHERE `name` = :name LIMIT 1",
            [
                "name" => $module
            ]
        );

        return $result[0] ?? null;
    }

    /**
     * List available modules from filesystem
     */
    public function scanModules(): array
    {
        $dir = dirname(__DIR__) . "/WIModules/";

        if (!is_dir($dir)) {
            return [];
        }

        $folders = scandir($dir);

        $modules = [];

        foreach ($folders as $folder) {

            if ($folder === '.' || $folder === '..') {
                continue;
            }

            $path = $dir . $folder;

            if (!is_dir($path)) {
                continue;
            }

            $modules[] = $folder;
        }

        return $modules;
    }

    /**
     * Install module
     */
    public function install(string $module): bool
    {
        $module = basename($module);

        $exists = $this->getModule($module);

        if ($exists) {
            return false;
        }

        return $this->WIdb->insert(
            "wi_modules",
            [
                "name" => $module,
                "enabled" => 1
            ]
        );
    }

    /**
     * Enable module
     */
    public function enable(string $module): bool
    {
        return $this->WIdb->update(
            "wi_modules",
            [
                "enabled" => 1
            ],
            "`name` = :name",
            [
                "name" => $module
            ]
        );
    }

    /**
     * Disable module
     */
    public function disable(string $module): bool
    {
        return $this->WIdb->update(
            "wi_modules",
            [
                "enabled" => 0
            ],
            "`name` = :name",
            [
                "name" => $module
            ]
        );
    }

    /**
     * Uninstall module
     */
    public function uninstall(string $module): bool
    {
        return $this->WIdb->delete(
            "wi_modules",
            "`name` = :name",
            [
                "name" => $module
            ]
        );
    }

    /**
     * Render modules assigned to page
     */
    public function renderPageModules(string $page)
    {
        $modules = $this->WIdb->select(
            "SELECT * FROM `wi_page_modules`
             WHERE `page` = :page
             ORDER BY `position` ASC",
            [
                "page" => $page
            ]
        );

        if (!$modules) {
            return;
        }

        foreach ($modules as $module) {

            $name = $module['module'] ?? '';

            if (!$name) {
                continue;
            }

            $this->render($name);
        }
    }

}
?>