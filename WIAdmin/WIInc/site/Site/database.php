<?php
declare(strict_types=1);
$esc = static fn(mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>

<form class="wi-settings-form" id="database-form" data-wi-settings-form data-result="#dresults">
    <?php echo WIToken::csrfField('database_settings'); ?>
    <input type="hidden" name="action" value="database_settings">

    <div class="wi-settings-section-head">
        <div class="wi-settings-section-icon">🛢</div>
        <div>
            <h2>Database</h2>
            <p>Review database ownership and safe configuration rules.</p>
        </div>
    </div>

    <div class="wi-settings-grid wi-settings-grid--two-one">
        <div class="wi-settings-card">
            <h3>Environment Controlled</h3>
            <p class="wi-settings-muted">Database host, username, password and database name should stay outside editable admin forms.</p>

            <div class="wi-settings-alert wi-settings-alert--warning">
                Database credentials are managed through the server environment configuration. Editing credentials from the browser is disabled to prevent accidental lockout or system failure.
            </div>

            <div class="wi-settings-readonly-list">
                <div><span>Connection layer</span><strong>WIdb / PDO</strong></div>
                <div><span>Credential editing</span><strong>Disabled in admin UI</strong></div>
                <div><span>Recommended update method</span><strong>Server environment / installer</strong></div>
            </div>
        </div>

        <aside class="wi-settings-side-card">
            <h3>Security Note</h3>
            <p>Keeping database credentials out of editable admin screens protects the system if an admin session or browser is compromised.</p>
        </aside>
    </div>

    <div class="wi-settings-actions">
        <button type="button" class="wi-settings-save" disabled>Environment Managed</button>
        <div class="wi-settings-result" id="dresults" aria-live="polite"></div>
    </div>
</form>
