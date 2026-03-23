<link rel="stylesheet" type="text/css" href="WIInc/css/modules.css">
<link rel="stylesheet" type="text/css" href="WIInc/css/editor.css">
<link rel="stylesheet" type="text/css" href="WIInc/css/toolbox.css">
<link rel="stylesheet" type="text/css" href="WIInc/css/docs.min.css">

<script>
$(function () {
    var indexKey = 'wi_modules_active_tab';
    var dataStore = window.sessionStorage;
    var activeTab = 0;

    try {
        activeTab = parseInt(dataStore.getItem(indexKey), 10);
        if (isNaN(activeTab)) {
            activeTab = 0;
        }
    } catch (e) {
        activeTab = 0;
    }

    $("#tabs5").tabs({
        active: activeTab,
        activate: function (event, ui) {
            var newIndex = ui.newTab.parent().children().index(ui.newTab);
            dataStore.setItem(indexKey, newIndex);
        }
    });
});
</script>

<aside class="right-side">
    <?php echo WIToken::csrfField('wi_ajax'); ?>
    <div class="wi-admin-header">
        <h2>Modules & Elements</h2>
        <p class="text-muted">Browse, install, manage and build modules and elements in a cleaner store experience.</p>
    </div>

    <section class="content wi-module-shell">
        <div class="wi-admin-panel">
            <div class="wi-module-intro">
                <h3>Module & Element Center</h3>
                <p>Keep store actions separate from builder actions, while making the admin experience cleaner and easier to use.</p>
            </div>

            <div id="tabs5" class="wi-module-tabs">
                <ul>
                    <li><a href="#tabs-1">Elements Store</a></li>
                    <li><a href="#tabs-2">Installed Elements</a></li>
                    <li><a href="#tabs-3">Modules Store</a></li>
                    <li><a href="#tabs-4">Installed Modules</a></li>
                    <li><a href="#tabs-5">Settings</a></li>
                    <li><a href="#tabs-6">Module Builder</a></li>
                </ul>

                <div id="tabs-1">
                    <?php include_once 'WIInc/site/modules/install_elements.php'; ?>
                </div>

                <div id="tabs-2">
                    <?php include_once 'WIInc/site/modules/available_elements.php'; ?>
                </div>

                <div id="tabs-3">
                    <?php include_once 'WIInc/site/modules/install.php'; ?>
                </div>

                <div id="tabs-4">
                    <?php include_once 'WIInc/site/modules/available_modules.php'; ?>
                </div>

                <div id="tabs-5">
                    <?php include_once 'WIInc/site/modules/modules_settings.php'; ?>
                </div>

                <div id="tabs-6">
                    <?php include_once 'WIInc/site/modules/edit_modules.php'; ?>
                </div>
            </div>
        </div>
    </section>

    <script type="text/javascript" src="WICore/WIJ/WICore.js"></script>
    <script type="text/javascript" src="WICore/WIJ/WIMedia.js"></script>
    <script type="text/javascript" src="WICore/WIJ/WIMediaCenter.js"></script>
    <script type="text/javascript" src="WICore/WIJ/WIMod.js"></script>
    <script type="text/javascript" src="WICore/WIJ/WIImage.js"></script>
    <script type="text/javascript" src="WICore/WIJ/WIPageBuilder.js"></script>
    <script type="text/javascript" src="WIInc/js/jquery.ui.touch-punch.min.js"></script>
    <script type="text/javascript" src="WIInc/js/jquery.htmlClean.js"></script>
    <script type="text/javascript" src="WICore/WIJ/WIScripts.js"></script>
    <script type="text/javascript" src="WIInc/js/FileSaver.js"></script>
    <script type="text/javascript" src="WIInc/js/blob.js"></script>
    <script type="text/javascript" src="WICore/WIJ/wysiwyg.js"></script>

    <?php
    $modal->moduleModal('element_enable', 'Element Enabler', 'WIMod', 'enabler', 'Enable all elements', '');
    ?>
</aside>