<?php
$menuTabs = [
    'tabs-1' => [
        'label'       => 'Site Menu',
        'title'       => 'Front-End Navigation',
        'description' => 'Manage the main site navigation shown to visitors.',
        'file'        => 'WIInc/site/menu/menu.php',
    ],
    'tabs-2' => [
        'label'       => 'Admin Menu',
        'title'       => 'Top Admin Navigation',
        'description' => 'Manage the main admin navigation used across the admin area.',
        'file'        => 'WIInc/site/menu/Admin_menu.php',
    ],
    'tabs-3' => [
        'label'       => 'Sidebar',
        'title'       => 'Admin Sidebar Navigation',
        'description' => 'Manage the grouped sidebar structure used throughout the admin zone.',
        'file'        => 'WIInc/site/menu/sidebar_menu.php',
    ],
];
?>

<link rel="stylesheet" type="text/css" href="WIInc/css/settings.css">

<script>
$(function () {
    var storageKey = 'wi_menu_active_tab';
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

    $("#wi-menu-tabs").tabs({
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
        <h2>Menu Settings</h2>
        <p class="text-muted">Manage site, admin and sidebar navigation so the menu system stays consistent and easy to use.</p>
    </div>

    <section class="content wi-settings-shell">
        <div class="wi-admin-panel">
            <div class="wi-section-head">
                <div>
                    <h3>Navigation Manager</h3>
                    <p>Update your front-end menu, admin menu and sidebar navigation from one place.</p>
                </div>
            </div>

            <div class="wi-settings-tabs-wrap">
                <div id="wi-menu-tabs" class="wi-menu-tabs">
                    <ul>
                        <?php foreach ($menuTabs as $tabId => $tab): ?>
                            <li>
                                <a href="#<?php echo htmlspecialchars((string)$tabId, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars((string)$tab['label'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <?php foreach ($menuTabs as $tabId => $tab): ?>
                        <div id="<?php echo htmlspecialchars((string)$tabId, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>" class="wi-settings-tab-panel">
                            <div class="wi-settings-tab-card wi-menu-tab-card">
                                <div class="wi-section-head wi-menu-tab-head">
                                    <h3><?php echo htmlspecialchars((string)$tab['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></h3>
                                    <p><?php echo htmlspecialchars((string)$tab['description'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></p>
                                </div>

                                <?php
                                if (is_file($tab['file'])) {
                                    include $tab['file'];
                                } else {
                                    echo '<div class="alert alert-warning">Missing menu include: '
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
    <script type="text/javascript" src="WICore/WIJ/WIMenu.js"></script>
</aside>