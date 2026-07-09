<?php
declare(strict_types=1);

$footerData = is_array($headerFooterData['footer'] ?? null) ? $headerFooterData['footer'] : [];
$tokens = is_array($headerFooterData['tokens'] ?? null) ? $headerFooterData['tokens'] : [];
$saveToken = (string)($tokens['save'] ?? (class_exists('WIToken') ? WIToken::getToken('wicms_header_footer_save') : ''));
?>
<form class="wi-hf-form" data-wi-hf-form data-result="#wi-hf-footer-result">
    <input type="hidden" name="action" value="wicms_header_footer_save">
    <input type="hidden" name="section" value="footer">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($saveToken, ENT_QUOTES, 'UTF-8'); ?>" data-wi-hf-save-token>

    <div class="wi-hf-grid">
        <div class="wi-hf-card-panel wi-hf-form-panel">
            <div class="wi-hf-card-title">
                <h4>Footer Identity</h4>
                <p>Controls the public footer name and optional supporting copy.</p>
            </div>

            <label class="wi-hf-field">
                <span>Website / Footer Name</span>
                <input type="text" name="website_name" maxlength="255" value="<?php echo htmlspecialchars((string)($footerData['website_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
            </label>

            <label class="wi-hf-field">
                <span>Footer Content</span>
                <textarea name="footer_content" maxlength="255" rows="3"><?php echo htmlspecialchars((string)($footerData['footer_content'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea>
            </label>

            <label class="wi-hf-field">
                <span>Footer Links / Supporting Text</span>
                <textarea name="footer_linking" maxlength="255" rows="3"><?php echo htmlspecialchars((string)($footerData['footer_linking'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea>
            </label>
        </div>

        <div class="wi-hf-card-panel wi-hf-footer-preview-card">
            <div class="wi-hf-card-title">
                <h4>Footer Preview</h4>
                <p>Simple preview using the current saved values.</p>
            </div>

            <div class="wi-hf-footer-preview">
                <strong data-wi-hf-preview-name><?php echo htmlspecialchars((string)($footerData['website_name'] ?? 'WICMS'), ENT_QUOTES, 'UTF-8'); ?></strong>
                <p data-wi-hf-preview-content><?php echo htmlspecialchars((string)($footerData['footer_content'] ?? 'Core WICMS website footer.'), ENT_QUOTES, 'UTF-8'); ?></p>
                <small data-wi-hf-preview-links><?php echo htmlspecialchars((string)($footerData['footer_linking'] ?? 'All rights reserved.'), ENT_QUOTES, 'UTF-8'); ?></small>
                <span>&copy; <?php echo htmlspecialchars(date('Y'), ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        </div>
    </div>

    <div class="wi-hf-actions">
        <button type="submit" class="wi-hf-btn wi-hf-btn-primary">Save Footer Settings</button>
        <span class="wi-hf-result" id="wi-hf-footer-result" aria-live="polite"></span>
    </div>
</form>
