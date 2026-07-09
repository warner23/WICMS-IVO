<?php
declare(strict_types=1);

/**
 * FILE:
 * WICMS-IVO/WICore/WIClass/WISite.php
 *
 * Canonical site data helper for front-end/core use.
 */
final class WISite
{
    private WIdb $WIdb;
    private ?WISettings $settings;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
        $this->settings = class_exists('WISettings') ? new WISettings() : null;
    }

    public function Website_Info(string $column): mixed
    {
        $row = $this->getSiteRow();

        return $row[$column] ?? null;
    }

    public function Theme_Info(string $column): mixed
    {
        $row = $this->getActiveThemeRow();

        return $row[$column] ?? null;
    }

    public function Countries(): void
    {
        echo $this->buildCountrySelect('countries', '', 'countries');
    }

    public function Country($id, $country): void
    {
        $elementId = 'countries' . preg_replace('/[^A-Za-z0-9_-]/', '', (string) $id);
        echo $this->buildCountrySelect($elementId, (string) $country, 'countries');
    }

    public function getSiteRow(): array
    {
        if ($this->settings !== null) {
            $row = $this->settings->getRow('wi_site', 1);
            if ($row !== []) {
                return $row;
            }
        }

        $result = $this->WIdb->select(
            'SELECT * FROM `wi_site` WHERE `id` = :id LIMIT 1',
            ['id' => 1]
        );

        return $result[0] ?? [];
    }

    public function getActiveThemeRow(): array
    {
        $result = $this->WIdb->select(
            'SELECT * FROM `wi_theme` WHERE `in_use` = :in_use ORDER BY `id` ASC LIMIT 1',
            ['in_use' => 1]
        );

        if (!empty($result[0])) {
            return $result[0];
        }

        $fallback = $this->WIdb->select(
            'SELECT * FROM `wi_theme` WHERE `id` = :id LIMIT 1',
            ['id' => 1]
        );

        return $fallback[0] ?? [];
    }

    public function siteName(string $default = 'WICMS'): string
    {
        $value = $this->Website_Info('site_name');

        return $this->stringValue($value, $default);
    }

    public function contactEmail(string $default = ''): string
    {
        return $this->stringValue($this->Website_Info('contact_email'), $default);
    }

    public function contactNumber(string $default = ''): string
    {
        return $this->stringValue($this->Website_Info('contact_no'), $default);
    }

    public function favicon(string $default = ''): string
    {
        return $this->stringValue($this->Website_Info('favicon'), $default);
    }

    public function logo(string $default = ''): string
    {
        $themeRow = $this->getActiveThemeRow();

        if (isset($themeRow['logo']) && trim((string) $themeRow['logo']) !== '') {
            return trim((string) $themeRow['logo']);
        }

        return $default;
    }

    public function getCountries(): array
    {
        $result = $this->WIdb->select(
            'SELECT `country_code`, `country_name` FROM `wi_countries` ORDER BY `country_name` ASC',
            []
        );

        return is_array($result) ? $result : [];
    }

    private function buildCountrySelect(string $id, string $selectedCountry = '', string $class = ''): string
    {
        $countries = $this->getCountries();
        $safeId = htmlspecialchars($id, ENT_QUOTES, 'UTF-8');
        $safeClass = htmlspecialchars(trim($class), ENT_QUOTES, 'UTF-8');

        $html = '<select id="' . $safeId . '"' . ($safeClass !== '' ? ' class="' . $safeClass . '"' : '') . '>';

        foreach ($countries as $country) {
            $code = htmlspecialchars((string) ($country['country_code'] ?? ''), ENT_QUOTES, 'UTF-8');
            $name = htmlspecialchars((string) ($country['country_name'] ?? ''), ENT_QUOTES, 'UTF-8');
            $selected = ((string) ($country['country_name'] ?? '') === $selectedCountry) ? ' selected="selected"' : '';

            $html .= '<option value="' . $code . '" title="' . $name . '"' . $selected . '>' . $name . '</option>';
        }

        $html .= '</select>';

        return $html;
    }

    private function stringValue(mixed $value, string $default = ''): string
    {
        if ($value === null) {
            return $default;
        }

        if (is_scalar($value)) {
            $trimmed = trim((string) $value);
            return $trimmed !== '' ? $trimmed : $default;
        }

        return $default;
    }
}