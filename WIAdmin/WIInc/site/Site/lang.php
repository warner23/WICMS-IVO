<?php
declare(strict_types=1);

if (!class_exists('WICMSLanguageSettingsService')) {
    $servicePath = __DIR__ . '/../../../WICore/WIClass/WICMSLanguageSettingsService.php';
    if (is_file($servicePath)) {
        require_once $servicePath;
    }
}

$languageService = class_exists('WICMSLanguageSettingsService') ? new WICMSLanguageSettingsService() : null;
$languageData = $languageService ? $languageService->dashboard() : [
    'settings' => [],
    'languages' => [],
    'translations' => [],
    'health' => [],
    'summary' => [],
];

$languageSettings = $languageData['settings'] ?? [];
$languages = $languageData['languages'] ?? [];
$translations = $languageData['translations'] ?? [];
$health = $languageData['health'] ?? [];
$summary = $languageData['summary'] ?? [];
$esc = $esc ?? static fn(mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

$selected = static function (mixed $a, mixed $b): string {
    return (string) $a === (string) $b ? 'selected' : '';
};

$checked = static function (mixed $value): string {
    return (string) $value === 'on' ? 'checked' : '';
};
?>

<div class="wi-settings-section-head wi-language-head">
    <div class="wi-settings-section-icon">🌍</div>
    <div>
        <h2>Multilingual Settings</h2>
        <p>Keep language support optional, clean and safe without forcing full translation work before launch.</p>
    </div>
</div>

<div class="wi-language-summary" aria-label="Multilingual summary">
    <article><span>Mode</span><strong><?php echo ((string) ($summary['enabled'] ?? 'off') === 'on') ? 'Enabled' : 'Disabled'; ?></strong></article>
    <article><span>Installed languages</span><strong><?php echo $esc($summary['installed_count'] ?? count($languages)); ?></strong></article>
    <article><span>Translation records</span><strong><?php echo $esc($summary['translation_count'] ?? count($translations)); ?></strong></article>
    <article><span>Fallback</span><strong><?php echo $esc($summary['fallback'] ?? 'en'); ?></strong></article>
</div>

<div class="wi-settings-grid wi-settings-grid--two-one wi-language-layout">
    <section class="wi-settings-card wi-language-card">
        <h3>Language Mode</h3>
        <form id="wi-language-settings-form" class="wi-settings-form" data-wi-language-form data-result="#wi-language-settings-result">
            <?php echo WIToken::csrfField('wicms_language_settings'); ?>
            <input type="hidden" name="action" value="wicms_language_settings_save">

            <label class="wi-switch-row">
                <span><strong>Enable multilingual mode</strong><small>Turns on WICMS language controls. Keep off until the site is ready.</small></span>
                <input type="hidden" name="language[enabled]" value="off">
                <input class="wi-switch-input" type="checkbox" name="language[enabled]" value="on" <?php echo $checked($languageSettings['enabled'] ?? 'off'); ?>>
                <i class="wi-switch-ui" aria-hidden="true"></i>
            </label>

            <label class="wi-switch-row">
                <span><strong>Public language switcher</strong><small>Allows visitors to change supported public language later.</small></span>
                <input type="hidden" name="language[public_switcher]" value="off">
                <input class="wi-switch-input" type="checkbox" name="language[public_switcher]" value="on" <?php echo $checked($languageSettings['public_switcher'] ?? 'off'); ?>>
                <i class="wi-switch-ui" aria-hidden="true"></i>
            </label>

            <label class="wi-switch-row">
                <span><strong>Admin language switcher</strong><small>Allows admin UI language selection when translations are ready.</small></span>
                <input type="hidden" name="language[admin_switcher]" value="off">
                <input class="wi-switch-input" type="checkbox" name="language[admin_switcher]" value="on" <?php echo $checked($languageSettings['admin_switcher'] ?? 'off'); ?>>
                <i class="wi-switch-ui" aria-hidden="true"></i>
            </label>

            <div class="wi-field-grid wi-language-fields">
                <label class="wi-field">
                    <span>Default public language</span>
                    <select name="language[default_public_lang]">
                        <?php foreach ($languages as $language): ?>
                            <option value="<?php echo $esc($language['lang'] ?? ''); ?>" <?php echo $selected($languageSettings['default_public_lang'] ?? 'en', $language['lang'] ?? ''); ?>><?php echo $esc(($language['name'] ?? '') . ' (' . ($language['lang'] ?? '') . ')'); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label class="wi-field">
                    <span>Default admin language</span>
                    <select name="language[default_admin_lang]">
                        <?php foreach ($languages as $language): ?>
                            <option value="<?php echo $esc($language['lang'] ?? ''); ?>" <?php echo $selected($languageSettings['default_admin_lang'] ?? 'en', $language['lang'] ?? ''); ?>><?php echo $esc(($language['name'] ?? '') . ' (' . ($language['lang'] ?? '') . ')'); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label class="wi-field">
                    <span>Fallback language</span>
                    <select name="language[fallback_lang]">
                        <?php foreach ($languages as $language): ?>
                            <option value="<?php echo $esc($language['lang'] ?? ''); ?>" <?php echo $selected($languageSettings['fallback_lang'] ?? 'en', $language['lang'] ?? ''); ?>><?php echo $esc(($language['name'] ?? '') . ' (' . ($language['lang'] ?? '') . ')'); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small>English should usually stay as fallback until translations are complete.</small>
                </label>

                <label class="wi-field">
                    <span>Translation engine</span>
                    <select name="language[translation_engine]">
                        <option value="wilang" <?php echo $selected($languageSettings['translation_engine'] ?? 'wilang', 'wilang'); ?>>WI internal translations</option>
                        <option value="google" <?php echo $selected($languageSettings['translation_engine'] ?? 'wilang', 'google'); ?>>Google Translate helper</option>
                    </select>
                </label>

                <label class="wi-field">
                    <span>Date format</span>
                    <input type="text" name="language[date_format]" value="<?php echo $esc($languageSettings['date_format'] ?? 'd/m/Y'); ?>">
                </label>

                <label class="wi-field">
                    <span>Time format</span>
                    <input type="text" name="language[time_format]" value="<?php echo $esc($languageSettings['time_format'] ?? 'H:i'); ?>">
                </label>

                <label class="wi-field">
                    <span>Number format</span>
                    <select name="language[number_format]">
                        <option value="uk" <?php echo $selected($languageSettings['number_format'] ?? 'uk', 'uk'); ?>>UK / Canada style</option>
                        <option value="us" <?php echo $selected($languageSettings['number_format'] ?? 'uk', 'us'); ?>>US style</option>
                        <option value="eu" <?php echo $selected($languageSettings['number_format'] ?? 'uk', 'eu'); ?>>European style</option>
                    </select>
                </label>

                <label class="wi-switch-row wi-language-rtl">
                    <span><strong>RTL support</strong><small>Reserve support for Arabic, Hebrew and similar languages.</small></span>
                    <input type="hidden" name="language[rtl_support]" value="off">
                    <input class="wi-switch-input" type="checkbox" name="language[rtl_support]" value="on" <?php echo $checked($languageSettings['rtl_support'] ?? 'off'); ?>>
                    <i class="wi-switch-ui" aria-hidden="true"></i>
                </label>
            </div>

            <div class="wi-settings-actions">
                <button type="submit" class="wi-settings-save">Save Multilingual Settings</button>
                <div class="wi-settings-result" id="wi-language-settings-result" aria-live="polite"></div>
            </div>
        </form>
    </section>

    <aside class="wi-settings-side-card wi-language-note-card">
        <h3>Launch-safe approach</h3>
        <p>Keep multilingual mode available, but do not force full translation work yet. For Compliance/legal/safety wording, translated content should be reviewed before being used with customers.</p>
        <div class="wi-settings-alert wi-settings-alert--info">This is a free WICMS core module. Compliance pack translations can come later as industry content.</div>
    </aside>
</div>

<section class="wi-settings-card wi-language-card wi-language-manager" data-wi-language-manager>
    <div class="wi-language-card-head">
        <div>
            <h3>Installed Languages</h3>
            <p>Manage language records without relying on the old language upload/modals.</p>
        </div>
        <button type="button" class="wi-language-secondary" data-wi-language-clear>New Language</button>
    </div>

    <div class="wi-language-manager-grid">
        <form class="wi-language-edit-form" data-wi-language-form data-result="#wi-language-record-result">
            <?php echo WIToken::csrfField('wicms_language_save'); ?>
            <input type="hidden" name="action" value="wicms_language_save">
            <input type="hidden" name="language_record[id]" value="" data-lang-field="id">

            <label class="wi-field"><span>Language code</span><input type="text" name="language_record[lang]" maxlength="2" placeholder="en" data-lang-field="lang"><small>Use two-letter codes only.</small></label>
            <label class="wi-field"><span>Language name</span><input type="text" name="language_record[name]" placeholder="English" data-lang-field="name"></label>
            <label class="wi-field"><span>Flag filename</span><input type="text" name="language_record[lang_flag]" placeholder="en.png" data-lang-field="lang_flag"><small>Stored as a filename only, not a full upload here.</small></label>
            <label class="wi-field"><span>Switcher href</span><input type="text" name="language_record[href]" placeholder="?lang=en" data-lang-field="href"></label>

            <div class="wi-settings-actions">
                <button type="submit" class="wi-settings-save">Save Language</button>
                <div class="wi-settings-result" id="wi-language-record-result" aria-live="polite"></div>
            </div>
        </form>

        <div class="wi-language-list-wrap">
            <div class="wi-language-filter-row">
                <input type="search" placeholder="Filter languages..." data-wi-language-filter>
            </div>
            <ul class="wi-language-list" data-wi-language-list>
                <?php foreach ($languages as $language): ?>
                    <li class="wi-language-item" data-language-item data-search="<?php echo $esc(strtolower(($language['name'] ?? '') . ' ' . ($language['lang'] ?? ''))); ?>">
                        <div>
                            <strong><?php echo $esc($language['name'] ?? ''); ?></strong>
                            <small><?php echo $esc($language['lang'] ?? ''); ?> · <?php echo $esc($language['translation_count'] ?? 0); ?> translations</small>
                        </div>
                        <div class="wi-language-actions">
                            <button type="button" data-edit-language='<?php echo $esc(json_encode($language, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)); ?>'>Edit</button>
                            <form data-wi-language-form data-result="#wi-language-record-result" class="wi-language-inline-delete">
                                <?php echo WIToken::csrfField('wicms_language_delete'); ?>
                                <input type="hidden" name="action" value="wicms_language_delete">
                                <input type="hidden" name="id" value="<?php echo $esc($language['id'] ?? 0); ?>">
                                <button type="submit" class="is-danger" data-confirm="Delete this language record?">Delete</button>
                            </form>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</section>

<section class="wi-settings-card wi-language-card wi-translation-manager">
    <div class="wi-language-card-head">
        <div>
            <h3>Translation Records</h3>
            <p>Small CRUD area for core translation keys. Full page/content translation can come later.</p>
        </div>
        <button type="button" class="wi-language-secondary" data-wi-translation-clear>New Translation</button>
    </div>

    <div class="wi-language-manager-grid">
        <form class="wi-language-edit-form" data-wi-language-form data-result="#wi-translation-result">
            <?php echo WIToken::csrfField('wicms_translation_save'); ?>
            <input type="hidden" name="action" value="wicms_translation_save">
            <input type="hidden" name="translation[id]" value="" data-trans-field="id">

            <label class="wi-field"><span>Language</span><select name="translation[lang]" data-trans-field="lang"><?php foreach ($languages as $language): ?><option value="<?php echo $esc($language['lang'] ?? ''); ?>"><?php echo $esc(($language['name'] ?? '') . ' (' . ($language['lang'] ?? '') . ')'); ?></option><?php endforeach; ?></select></label>
            <label class="wi-field"><span>Keyword</span><input type="text" name="translation[keyword]" placeholder="home" data-trans-field="keyword"></label>
            <label class="wi-field"><span>Translation</span><textarea name="translation[translation]" rows="4" data-trans-field="translation"></textarea></label>

            <div class="wi-settings-actions">
                <button type="submit" class="wi-settings-save">Save Translation</button>
                <div class="wi-settings-result" id="wi-translation-result" aria-live="polite"></div>
            </div>
        </form>

        <div class="wi-language-list-wrap">
            <div class="wi-language-filter-row">
                <input type="search" placeholder="Filter translations..." data-wi-translation-filter>
            </div>
            <ul class="wi-language-list wi-translation-list" data-wi-translation-list>
                <?php foreach ($translations as $translation): ?>
                    <li class="wi-language-item" data-translation-item data-search="<?php echo $esc(strtolower(($translation['lang'] ?? '') . ' ' . ($translation['keyword'] ?? '') . ' ' . ($translation['translation'] ?? ''))); ?>">
                        <div>
                            <strong><?php echo $esc($translation['keyword'] ?? ''); ?></strong>
                            <small><?php echo $esc($translation['lang'] ?? ''); ?> · <?php echo $esc($translation['translation'] ?? ''); ?></small>
                        </div>
                        <div class="wi-language-actions">
                            <button type="button" data-edit-translation='<?php echo $esc(json_encode($translation, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)); ?>'>Edit</button>
                            <form data-wi-language-form data-result="#wi-translation-result" class="wi-language-inline-delete">
                                <?php echo WIToken::csrfField('wicms_translation_delete'); ?>
                                <input type="hidden" name="action" value="wicms_translation_delete">
                                <input type="hidden" name="id" value="<?php echo $esc($translation['id'] ?? 0); ?>">
                                <button type="submit" class="is-danger" data-confirm="Delete this translation?">Delete</button>
                            </form>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</section>

<section class="wi-settings-card wi-language-card wi-language-health">
    <h3>Language File Health</h3>
    <p class="wi-settings-muted">This checks whether admin/public language files exist and look like the format WICMS expects. It does not claim translations are complete.</p>
    <ul class="wi-language-health-list">
        <?php foreach ($health as $row): ?>
            <?php
            $adminOk = ($row['admin_exists'] ?? false) && ($row['admin_readable'] ?? false) && (string) ($row['admin_format'] ?? '') === 'ok';
            $publicOk = ($row['public_exists'] ?? false) && ($row['public_readable'] ?? false) && (string) ($row['public_format'] ?? '') === 'ok';
            ?>
            <li>
                <div><strong><?php echo $esc($row['name'] ?? ''); ?></strong><small><?php echo $esc($row['lang'] ?? ''); ?></small></div>
                <span class="wi-health-pill <?php echo $adminOk ? 'is-ok' : 'is-warning'; ?>">Admin: <?php echo $esc($row['admin_format'] ?? 'missing'); ?></span>
                <span class="wi-health-pill <?php echo $publicOk ? 'is-ok' : 'is-warning'; ?>">Public: <?php echo $esc($row['public_format'] ?? 'missing'); ?></span>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
