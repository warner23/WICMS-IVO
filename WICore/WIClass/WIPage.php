<?php
declare(strict_types=1);

/**
 * Page Class
 * Created by Warner Infinity
 * Author Jules Warner
 */

#[\AllowDynamicProperties]
class WIPage
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

    /**
     * Get page row safely
     */
    private function getPageRow(string $page): ?array
    {
        $result = $this->WIdb->select(
            "SELECT * FROM `wi_page` WHERE `name` = :page LIMIT 1",
            [
                "page" => $page
            ]
        );

        return $result[0] ?? null;
    }

    /**
     * Get page column value
     */
    public function PageMod($page, $column)
    {
        $value = $this->WIdb->selectColumn(
            "SELECT * FROM `wi_page` WHERE `name` = :page LIMIT 1",
            [
                "page" => $page
            ],
            $column
        );
        

        return $value;
    }

    /**
     * Check if page module enabled
     */
    public function PageModPower($page, $column): int
    {
        $value = $this->PageMod($page, $column);

        if ($value === null || $value === '') {
            return 0;
        }

        if (is_numeric($value)) {
            return (int)$value;
        }

        return strlen((string)$value) > 0 ? 1 : 0;
    }

    /**
     * Load page template
     */
    public function loadPage($page)
    {
        $pageRow = $this->getPageRow($page);

        if (!$pageRow) {
            echo '<h2>Page not found</h2>';
            return;
        }

        $template = $pageRow['template'] ?? '';
        $template = basename((string)$template);

$path = dirname(dirname(dirname(__FILE__))) . "/WITheme/" . $template;
print($path);

        if (!file_exists($path)) {
            echo '<h2>Template not found</h2>';
            return;
        }

        include $path;
    }

    /**
     * Get page title
     */
    public function getTitle($page)
    {
        $row = $this->getPageRow($page);

        if (!$row) {
            return '';
        }

        return $this->e($row['title'] ?? '');
    }

    /**
     * Get page content
     */
    public function getContent($page)
    {
        $row = $this->getPageRow($page);

        if (!$row) {
            return '';
        }

        return $row['content'] ?? '';
    }

    /**
     * Output page content
     */
    public function render($page)
    {
        $content = $this->getContent($page);

        echo $content;
    }

    /**
     * Page exists check
     */
    public function exists($page): bool
    {
        $row = $this->getPageRow($page);

        return $row !== null;
    }

    /**
     * Get all pages
     */
    public function getAll(): array
    {
        $result = $this->WIdb->bindfree(
            "SELECT * FROM `wi_page` ORDER BY `id` ASC"
        );

        return is_array($result) ? $result : [];
    }
}
?>