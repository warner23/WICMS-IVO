<?php
declare(strict_types=1);

$headerData = is_array($headerFooterData['header'] ?? null) ? $headerFooterData['header'] : [];
$tokens = is_array($headerFooterData['tokens'] ?? null) ? $headerFooterData['tokens'] : [];
$saveToken = (string)($tokens['save'] ?? (class_exists('WIToken') ? WIToken::getToken('wicms_header_footer_save') : ''));
$uploadToken = (string)($tokens['upload'] ?? (class_exists('WIToken') ? WIToken::getToken('wicms_header_footer_upload') : ''));

$logoUrl = (string)($headerData['logo_url'] ?? '');
$headerImageUrl = (string)($headerData['header_image_url'] ?? '');
?>
<div class="wi-hf-grid">
    <div class="wi-hf-card-panel">
        <div class="wi-hf-card-title">
            <h4>Header Logo</h4>
            <p>Upload the main public site logo. Files are stored as WICMS core images, not Compliance evidence.</p>
        </div>

        <div class="wi-hf-upload-zone" data-wi-hf-upload-zone data-asset-type="header_logo" data-csrf-token="<?php echo htmlspecialchars($uploadToken, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="file" accept="image/png,image/jpeg,image/webp,image/gif,image/svg+xml,image/x-icon" data-wi-hf-file hidden>
            <div class="wi-hf-preview wi-hf-preview-logo" data-preview-for="header_logo">
                <?php if ($logoUrl !== ''): ?>
                    <img src="<?php echo htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="Current header logo">
                <?php else: ?>
                    <span>No logo uploaded</span>
                <?php endif; ?>
            </div>
            <div class="wi-hf-drop-copy">
                <strong>Drop logo here</strong>
                <span>or click to choose an image</span>
            </div>
            <button type="button" class="wi-hf-btn wi-hf-btn-secondary" data-wi-hf-choose>Choose Logo</button>
        </div>
    </div>

    <div class="wi-hf-card-panel">
        <div class="wi-hf-card-title">
            <h4>Header Image</h4>
            <p>Optional larger header/hero image. Leave blank if the active theme does not use one.</p>
        </div>

        <div class="wi-hf-upload-zone" data-wi-hf-upload-zone data-asset-type="header_image" data-csrf-token="<?php echo htmlspecialchars($uploadToken, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="file" accept="image/png,image/jpeg,image/webp,image/gif,image/svg+xml" data-wi-hf-file hidden>
            <div class="wi-hf-preview wi-hf-preview-wide" data-preview-for="header_image">
                <?php if ($headerImageUrl !== ''): ?>
                    <img src="<?php echo htmlspecialchars($headerImageUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="Current header image">
                <?php else: ?>
                    <span>No header image uploaded</span>
                <?php endif; ?>
            </div>
            <div class="wi-hf-drop-copy">
                <strong>Drop header image here</strong>
                <span>or click to choose an image</span>
            </div>
            <button type="button" class="wi-hf-btn wi-hf-btn-secondary" data-wi-hf-choose>Choose Header Image</button>
        </div>
    </div>
</div>

<form class="wi-hf-form" data-wi-hf-form data-result="#wi-hf-header-result">
    <input type="hidden" name="action" value="wicms_header_footer_save">
    <input type="hidden" name="section" value="header">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($saveToken, ENT_QUOTES, 'UTF-8'); ?>" data-wi-hf-save-token>

    <div class="wi-hf-card-panel wi-hf-form-panel">
        <div class="wi-hf-card-title">
            <h4>Header Text</h4>
            <p>Optional text fields for themes that display header copy or slogans.</p>
        </div>

        <label class="wi-hf-field">
            <span>Header Content</span>
            <input type="text" name="header_content" maxlength="255" value="<?php echo htmlspecialchars((string)($headerData['header_content'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
        </label>

        <label class="wi-hf-field">
            <span>Header Slogan</span>
            <input type="text" name="header_slogan" maxlength="255" value="<?php echo htmlspecialchars((string)($headerData['header_slogan'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
        </label>

        <div class="wi-hf-actions">
            <button type="submit" class="wi-hf-btn wi-hf-btn-primary">Save Header Settings</button>
            <span class="wi-hf-result" id="wi-hf-header-result" aria-live="polite"></span>
        </div>
    </div>
</form>
