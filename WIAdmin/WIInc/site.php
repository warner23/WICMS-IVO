<?php
$settingsTabs = [
    'tabs-1'  => ['label' => 'Website',              'file' => 'WIInc/site/Site/website.php'],
    'tabs-2'  => ['label' => 'Database',             'file' => 'WIInc/site/Site/database.php'],
    'tabs-3'  => ['label' => 'Email',                'file' => 'WIInc/site/Site/email.php'],
    'tabs-4'  => ['label' => 'Sessions',             'file' => 'WIInc/site/Site/session.php'],
    'tabs-5'  => ['label' => 'Login',                'file' => 'WIInc/site/Site/login.php'],
    'tabs-6'  => ['label' => 'Password Security',    'file' => 'WIInc/site/Site/security.php'],
    'tabs-7'  => ['label' => 'Social Set Up',        'file' => 'WIInc/site/Site/social.php'],
    'tabs-8'  => ['label' => 'Multilingual Settings','file' => 'WIInc/site/Site/lang.php'],
    'tabs-9'  => ['label' => 'Password Salt',        'file' => 'WIInc/site/Site/salt.php'],
    'tabs-10' => ['label' => 'Email Verification',   'file' => 'WIInc/site/Site/verification.php'],
    'tabs-11' => ['label' => 'Version Control',      'file' => 'WIInc/site/Site/version.php'],
];
?>

<script>
$(function () {
    var storageKey = 'wi_settings_active_tab';
    var dataStore = window.sessionStorage;
    var oldIndex = 0;

    try {
        var savedIndex = dataStore.getItem(storageKey);
        oldIndex = savedIndex !== null ? parseInt(savedIndex, 10) : 0;

        if (isNaN(oldIndex)) {
            oldIndex = 0;
        }
    } catch (e) {
        oldIndex = 0;
    }

    $("#wi-settings-tabs").tabs({
        active: oldIndex,
        activate: function (event, ui) {
            var newIndex = ui.newTab.parent().children().index(ui.newTab);

            try {
                dataStore.setItem(storageKey, newIndex);
            } catch (e) {}
        }
    });
});
</script>

<aside class="right-side">
    <div class="wi-admin-header">
        <h2>Settings</h2>
        <p class="text-muted">Manage core site, login, session, email and system settings.</p>
    </div>

    <section class="content wi-settings-shell">
        <div class="wi-admin-panel">
            <div class="wi-section-head">
                <div>
                    <h3>Site Settings</h3>
                    <p>Control the core configuration for your WICMS installation.</p>
                </div>
            </div>

            <div class="wi-settings-tabs-wrap">
                <div id="wi-settings-tabs">
                    <ul>
                        <?php foreach ($settingsTabs as $tabId => $tab): ?>
                            <li>
                                <a href="#<?php echo htmlspecialchars((string)$tabId, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars((string)$tab['label'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <?php foreach ($settingsTabs as $tabId => $tab): ?>
                        <div id="<?php echo htmlspecialchars((string)$tabId, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>" class="wi-settings-tab-panel">
                            <div class="wi-settings-tab-card">
                                <?php
                                if (is_file($tab['file'])) {
                                    include $tab['file'];
                                } else {
                                    echo '<div class="alert alert-warning">Missing settings include: '
                                        . htmlspecialchars((string)$tab['file'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
                                        . '</div>';
                                }
                                ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <script type="text/javascript" src="WICore/WIJ/WICore.js"></script>
    <script type="text/javascript" src="WICore/WIJ/WISite.js"></script>
    <script type="text/javascript" src="WICore/WIJ/WIDatabase.js"></script>
    <script type="text/javascript" src="WICore/WIJ/WIEmail.js"></script>
    <script type="text/javascript" src="WICore/WIJ/WILogin_Settings.js"></script>
    <script type="text/javascript" src="WICore/WIJ/WISecurity.js"></script>
    <script type="text/javascript" src="WICore/WIJ/WISession.js"></script>
    <script type="text/javascript" src="WICore/WIJ/WISocial.js"></script>
    <script type="text/javascript" src="WICore/WIJ/WILang.js"></script>
</aside>