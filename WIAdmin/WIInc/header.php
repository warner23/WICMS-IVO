<?php
$headerTabs = [
    'tabs-1' => [
        'label'       => 'Header',
        'title'       => 'Site Header',
        'description' => 'Manage the main front-end header content, logo and presentation area.',
        'file'        => 'WIInc/site/header/header.php',
    ],
    'tabs-2' => [
        'label'       => 'Footer',
        'title'       => 'Site Footer',
        'description' => 'Manage the front-end footer content and supporting footer settings.',
        'file'        => 'WIInc/site/header/footer.php',
    ],
    'tabs-3' => [
        'label'       => 'Favicon',
        'title'       => 'Favicon',
        'description' => 'Manage the site favicon used in the browser and bookmarks.',
        'file'        => 'WIInc/site/header/favicon.php',
    ],
];
?>

<script>
$(function () {
    var storageKey = 'wi_header_active_tab';
    var oldIndex = 0;

    try {
        var savedIndex = window.sessionStorage.getItem(storageKey);
        oldIndex = savedIndex !== null ? parseInt(savedIndex, 10) : 0;

        if (isNaN(oldIndex)) {
            oldIndex = 0;
        }
    } catch (e) {
        oldIndex = 0;
    }

    $("#wi-header-tabs").tabs({
        active: oldIndex,
        activate: function (event, ui) {
            var newIndex = ui.newTab.parent().children().index(ui.newTab);

            try {
                window.sessionStorage.setItem(storageKey, newIndex);
            } catch (e) {}
        }
    });
});
</script>

<aside class="right-side">
    <div class="wi-admin-header">
        <h2>Header Settings</h2>
        <p>Manage the front-end header, footer and favicon so the site presentation stays consistent and easy to maintain.</p>
    </div>

    <section class="content wi-settings-shell">
        <div class="wi-admin-panel">
            <div class="wi-section-head">
                <div>
                    <h3>Site Presentation</h3>
                    <p>Update key presentation areas from one place without changing the current structure.</p>
                </div>
            </div>

            <div class="wi-settings-tabs-wrap">
                <div id="wi-header-tabs" class="wi-header-tabs">
                    <ul>
                        <?php foreach ($headerTabs as $tabId => $tab): ?>
                            <li>
                                <a href="#<?php echo htmlspecialchars((string) $tabId, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars((string) $tab['label'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <?php foreach ($headerTabs as $tabId => $tab): ?>
                        <div id="<?php echo htmlspecialchars((string) $tabId, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>" class="wi-settings-tab-panel">
                            <div class="wi-settings-tab-card wi-header-settings-card">
                                <div class="wi-section-head wi-header-tab-head">
                                    <h3><?php echo htmlspecialchars((string) $tab['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></h3>
                                    <p><?php echo htmlspecialchars((string) $tab['description'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></p>
                                </div>

                                <?php
                                if (is_file($tab['file'])) {
                                    include $tab['file'];
                                } else {
                                    echo '<div class="alert alert-warning">Missing header include: '
                                        . htmlspecialchars((string) $tab['file'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
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
</aside>

<script type="text/javascript" src="WICore/WIJ/WICore.js"></script>
<script type="text/javascript" src="WICore/WIJ/WIMedia.js"></script>
<script type="text/javascript" src="WICore/WIJ/WIMediaCenter.js"></script>