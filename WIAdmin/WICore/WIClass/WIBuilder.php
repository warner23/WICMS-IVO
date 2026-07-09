<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WI Ecosystem
| Project: WIKitchenCompli / WICOS
| File: WIBuilder.php
| Location: /root/WIAdmin/WICore/WIClass/WIBuilder.php
| Type: PHP Class
| Layer: Shared UI Support
| Purpose Area: Shared Builder Foundation
| Version: 1.0.0
| Created: 2026-04-23
| Last Updated: 2026-04-23
| Status: Production Ready
|--------------------------------------------------------------------------
| Summary:
| Shared builder helper for modal-based builders.
| - Normalises builder sizing values
| - Provides safe option lists for UI controls
| - Reusable later for page/module/form/checklist builders
|--------------------------------------------------------------------------
*/

final class WIBuilder
{
    /**
     * Return supported width options for outer wrapper sizing.
     *
     * @return array<int, string>
     */
    public function getWrapperWidthOptions(): array
    {
        return [
            25 => '25%',
            33 => '33%',
            50 => '50%',
            66 => '66%',
            75 => '75%',
            100 => '100%',
        ];
    }

    /**
     * Return supported width options for field/input sizing.
     *
     * @return array<int, string>
     */
    public function getInputWidthOptions(): array
    {
        return [
            25 => '25%',
            33 => '33%',
            50 => '50%',
            66 => '66%',
            75 => '75%',
            100 => '100%',
        ];
    }

    /**
     * Return supported textarea row options.
     *
     * @return array<int, string>
     */
    public function getInputRowOptions(): array
    {
        return [
            1 => '1 row',
            2 => '2 rows',
            3 => '3 rows',
            4 => '4 rows',
            5 => '5 rows',
            6 => '6 rows',
            8 => '8 rows',
            10 => '10 rows',
            12 => '12 rows',
        ];
    }

    /**
     * Return supported wrapper min-height options.
     *
     * @return array<int, string>
     */
    public function getWrapperHeightOptions(): array
    {
        return [
            0 => 'Auto',
            80 => '80px',
            120 => '120px',
            160 => '160px',
            200 => '200px',
            240 => '240px',
            320 => '320px',
        ];
    }

    /**
     * Normalise a width percentage to a safe builder value.
     *
     * @param mixed $value Raw value.
     * @param int $default Default value.
     * @return int
     */
    public function normaliseWidth($value, int $default = 50): int
    {
        $allowed = array_keys($this->getInputWidthOptions());
        $value = (int) $value;

        return in_array($value, $allowed, true) ? $value : $default;
    }

    /**
     * Normalise textarea rows to a safe builder value.
     *
     * @param mixed $value Raw value.
     * @param int $default Default value.
     * @return int
     */
    public function normaliseRows($value, int $default = 3): int
    {
        $allowed = array_keys($this->getInputRowOptions());
        $value = (int) $value;

        return in_array($value, $allowed, true) ? $value : $default;
    }

    /**
     * Normalise wrapper min-height to a safe builder value.
     *
     * @param mixed $value Raw value.
     * @param int $default Default value.
     * @return int
     */
    public function normaliseHeight($value, int $default = 0): int
    {
        $allowed = array_keys($this->getWrapperHeightOptions());
        $value = (int) $value;

        return in_array($value, $allowed, true) ? $value : $default;
    }
}