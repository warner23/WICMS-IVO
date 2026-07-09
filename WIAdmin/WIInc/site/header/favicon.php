<?php
declare(strict_types=1);

$faviconData = is_array($headerFooterData['favicon'] ?? null) ? $headerFooterData['favicon'] : [];
$tokens = is_array($headerFooterData['tokens'] ?? null) ? $headerFooterData['tokens'] : [];
$uploadToken = (string)($tokens['upload'] ?? (class_exists('WIToken') ? WIToken::getToken('wicms_header_footer_upload') : ''));
$faviconUrl = (string)($faviconData['favicon_url'] ?? '');
?>
<div class="wi-hf-grid wi-hf-grid-single">
    <div class="wi-hf-card-panel">
        <div class="wi-hf-card-title">
            <h4>Favicon Upload</h4>
            <p>Upload a square icon for browser tabs, bookmarks and shortcuts. ICO, PNG, SVG, JPG and WebP are supported by the media layer.</p>
        </div>

        <div class="wi-hf-upload-zone wi-hf-favicon-zone" data-wi-hf-upload-zone data-asset-type="favicon" data-csrf-token="<?php echo htmlspecialchars($uploadToken, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="file" accept="image/png,image/jpeg,image/webp,image/gif,image/svg+xml,image/x-icon" data-wi-hf-file hidden>
            <div class="wi-hf-preview wi-hf-preview-favicon" data-preview-for="favicon">
                <?php if ($faviconUrl !== ''): ?>
                    <img src="<?php echo htmlspecialchars($faviconUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="Current favicon">
                <?php else: ?>
                    <span>No favicon uploaded</span>
                <?php endif; ?>
            </div>
            <div class="wi-hf-drop-copy">
                <strong>Drop favicon here</strong>
                <span>or click to choose an icon</span>
            </div>
            <button type="button" class="wi-hf-btn wi-hf-btn-secondary" data-wi-hf-choose>Choose Favicon</button>
        </div>

        <div class="wi-hf-actions">
            <span class="wi-hf-result" id="wi-hf-favicon-result" aria-live="polite"></span>
        </div>
    </div>
</div>
