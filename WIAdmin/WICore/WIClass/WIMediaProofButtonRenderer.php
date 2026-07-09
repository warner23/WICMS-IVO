<?php
declare(strict_types=1);

/**
 * File Information
 * ----------------
 * Written By: WI Labs
 * Company: WI Labs
 * Product: WICMS / WICOS
 * Project: WIMedia Shared Proof System
 * File: WIMediaProofButtonRenderer.php
 * Location: root/WIAdmin/WICore/WIClass/WIMediaProofButtonRenderer.php
 * Type: Class
 * Layer: Shared Admin UI Renderer
 * Purpose Area: Media Proof Button Rendering
 * Version: 1.0.0
 * Created: 2026-05-14
 * Last Updated: 2026-05-14
 * Status: Production-ready first pass
 *
 * Summary:
 * Renders a standard WIMedia proof button. This is UI-only and does not
 * perform uploads, DB calls, or business logic.
 */

require_once __DIR__ . '/Media/WIMediaProofContext.php';

final class WIMediaProofButtonRenderer
{
    /**
     * Render an Add Photo / Add Evidence button.
     *
     * @param array<string,mixed> $context Context.
     * @param array<string,mixed> $options Render options.
     *
     * @return string
     */
    public static function render(array $context, array $options = []): string
    {
        $context = WIMediaProofContext::normalise($context);

        $label = trim((string)($options['label'] ?? $context['label'] ?? 'Add proof'));
        $label = $label !== '' ? $label : 'Add proof';

        $variant = self::cleanClass((string)($options['variant'] ?? 'primary'));
        $size = self::cleanClass((string)($options['size'] ?? 'md'));
        $icon = trim((string)($options['icon'] ?? '📷'));
        $extraClass = trim((string)($options['class'] ?? ''));

        $classes = trim(sprintf(
            'wi-media-proof-btn wi-media-proof-btn--%s wi-media-proof-btn--%s %s',
            $variant,
            $size,
            $extraClass
        ));

        return sprintf(
            '<button type="button" class="%s" data-wimedia-proof-trigger="1" data-wimedia-context="%s"><span class="wi-media-proof-btn__icon">%s</span><span class="wi-media-proof-btn__label">%s</span></button>',
            self::esc($classes),
            self::esc(WIMediaProofContext::toJson($context)),
            self::esc($icon),
            self::esc($label)
        );
    }

    /**
     * Render placeholder container for attached proof.
     *
     * @param array<string,mixed> $context Context.
     *
     * @return string
     */
    public static function renderAttachmentList(array $context): string
    {
        $context = WIMediaProofContext::normalise($context);

        return sprintf(
            '<div class="wi-media-proof-attachments" data-wimedia-proof-attachments="1" data-wimedia-context="%s"></div>',
            self::esc(WIMediaProofContext::toJson($context))
        );
    }

    /**
     * Escape HTML.
     *
     * @param string $value Value.
     *
     * @return string
     */
    private static function esc(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Clean CSS modifier.
     *
     * @param string $value Value.
     *
     * @return string
     */
    private static function cleanClass(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9_\-]/', '', $value) ?? '';

        return $value !== '' ? $value : 'default';
    }
}