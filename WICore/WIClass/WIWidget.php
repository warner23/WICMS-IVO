<?php
declare(strict_types=1);

/**
 * Widget Class
 * Created by Warner Infinity
 */

#[\AllowDynamicProperties]
class WIWidget
{
    private WIdb $WIdb;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
    }

    private function e($value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }

    public function render(string $widget): void
    {
        $result = $this->WIdb->select(
            "SELECT * FROM wi_widgets WHERE name = :name LIMIT 1",
            ["name" => $widget]
        );

        if (!$result) {
            echo '<div class="alert alert-warning">Widget not found</div>';
            return;
        }

        $data = $result[0];

        echo '<div class="wi-widget">';
        echo $data['content'] ?? '';
        echo '</div>';
    }

    public function getAll(): array
    {
        $result = $this->WIdb->bindfree(
            "SELECT * FROM wi_widgets ORDER BY id ASC"
        );

        return is_array($result) ? $result : [];
    }
}
?>