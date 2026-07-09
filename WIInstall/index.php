<?php
include_once 'WICore/init.php';

function wi_installer_random_string($bytes = 32) {
    try {
        return bin2hex(random_bytes($bytes));
    } catch (Exception $e) {
        return hash('sha256', uniqid((string) mt_rand(), true) . microtime(true));
    }
}

$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$scriptName = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '/WIInstall/index.php';
$installDir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
$basePath = rtrim(str_replace('\\', '/', dirname($installDir)), '/');
if ($basePath === '' || $basePath === '.') {
    $basePath = '';
}
$detectedSiteUrl = $scheme . '://' . $host . $basePath;
$configPath = dirname(__DIR__) . '/WICore/WIClass/WIConfig.php';
$lockPath = __DIR__ . '/install.lock';
$isInstalled = file_exists($lockPath);
$forceInstall = isset($_GET['force']) && $_GET['force'] === '1';
$defaultSalt = wi_installer_random_string(24);
$defaultSecurityKey = wi_installer_random_string(32);
$defaultCookiePrefix = strtolower(preg_replace('/[^a-z0-9_]+/i', '_', trim(basename($basePath) ?: 'wicms'))) . '_';
$installAssetBase = $installDir ?: '/WIInstall';
$siteAssetBase = $basePath ?: '';
$assetVersion = '2.3.0';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WICMS Installer v3.0</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($siteAssetBase, ENT_QUOTES, 'UTF-8'); ?>/WITheme/WICMS/site/css/frameworks/bootstrap.css?v=<?php echo $assetVersion; ?>" type="text/css" />
    <link rel="stylesheet" href="<?php echo htmlspecialchars($siteAssetBase, ENT_QUOTES, 'UTF-8'); ?>/WITheme/WICMS/site/css/font-awesome.css?v=<?php echo $assetVersion; ?>" type="text/css" />
    <link rel="stylesheet" href="<?php echo htmlspecialchars($installAssetBase, ENT_QUOTES, 'UTF-8'); ?>/WIInc/css/install.css?v=<?php echo $assetVersion; ?>" type="text/css" />
    <style id="wi-install-v2-css-fallback"><?php $wiInstallCss = __DIR__ . '/WIInc/css/install.css'; if (is_file($wiInstallCss)) { echo file_get_contents($wiInstallCss); } ?></style>
    <script type="text/javascript" src="<?php echo htmlspecialchars($siteAssetBase, ENT_QUOTES, 'UTF-8'); ?>/WITheme/WICMS/site/js/frameworks/JQuery.js?v=<?php echo $assetVersion; ?>"></script>
    <script type="text/javascript">
        if (!window.jQuery) {
            document.write('<script type="text/javascript" src="<?php echo htmlspecialchars($installAssetBase, ENT_QUOTES, 'UTF-8'); ?>/WIInc/js/jquery-1.12.4.js?v=<?php echo $assetVersion; ?>"><\/script>');
        }
    </script>
    <script type="text/javascript" src="<?php echo htmlspecialchars($siteAssetBase, ENT_QUOTES, 'UTF-8'); ?>/WITheme/WICMS/site/js/frameworks/bootstrap.js?v=<?php echo $assetVersion; ?>"></script>
</head>
<body class="wi-install-v2">
<?php if ($isInstalled && !$forceInstall): ?>
    <main class="wi-installed-guard">
        <section class="wi-installed-card">
            <div class="wi-brand-mark">W</div>
            <h1>WICMS is already installed</h1>
            <p>The installer detected an install lock. This protects your site from being overwritten accidentally.</p>
            <div class="wi-installed-actions">
                <a class="wi-btn wi-btn-primary" href="../alogin.php"><i class="fa fa-sign-in"></i> Go to admin login</a>
                <a class="wi-btn wi-btn-muted" href="index.php?force=1"><i class="fa fa-wrench"></i> Developer reinstall mode</a>
            </div>
            <p class="wi-installed-warning"><i class="fa fa-shield"></i> Only use developer reinstall mode on a backed-up local/testing copy.</p>
        </section>
    </main>
<?php else: ?>
<div class="wi-installer-shell" data-current-step="1">
    <aside class="wi-sidebar">
        <div class="wi-brand">
            <div class="wi-brand-mark">W</div>
            <div>
                <h1>WICMS</h1>
                <span>Installer <strong>v2.7</strong></span>
            </div>
        </div>

        <nav class="wi-side-steps" aria-label="Installation steps">
            <button type="button" class="wi-side-step is-active" data-step-item="1"><i class="fa fa-home"></i><span>1</span><strong>Welcome</strong></button>
            <button type="button" class="wi-side-step" data-step-item="2"><i class="fa fa-check-square-o"></i><span>2</span><strong>Requirements</strong></button>
            <button type="button" class="wi-side-step" data-step-item="3"><i class="fa fa-database"></i><span>3</span><strong>Database</strong></button>
            <button type="button" class="wi-side-step" data-step-item="4"><i class="fa fa-pencil"></i><span>4</span><strong>Site Setup</strong></button>
            <button type="button" class="wi-side-step" data-step-item="5"><i class="fa fa-shield"></i><span>5</span><strong>Security</strong></button>
            <button type="button" class="wi-side-step" data-step-item="6"><i class="fa fa-download"></i><span>6</span><strong>Install</strong></button>
            <button type="button" class="wi-side-step" data-step-item="7"><i class="fa fa-check-circle"></i><span>7</span><strong>Complete</strong></button>
        </nav>

        <div class="wi-help-card">
            <i class="fa fa-life-ring"></i>
            <h2>Need help?</h2>
            <p>Work through each step in order. Use local/staging mode while testing ECMA.</p>
        </div>
    </aside>

    <main class="wi-main">
        <header class="wi-topbar">
            <div class="wi-top-steps">
                <button type="button" class="wi-top-step is-active" data-step-item="1"><span>1</span><strong>Welcome</strong></button>
                <button type="button" class="wi-top-step" data-step-item="2"><span>2</span><strong>Requirements</strong></button>
                <button type="button" class="wi-top-step" data-step-item="3"><span>3</span><strong>Database</strong></button>
                <button type="button" class="wi-top-step" data-step-item="4"><span>4</span><strong>Site Setup</strong></button>
                <button type="button" class="wi-top-step" data-step-item="5"><span>5</span><strong>Security</strong></button>
                <button type="button" class="wi-top-step" data-step-item="6"><span>6</span><strong>Install</strong></button>
                <button type="button" class="wi-top-step" data-step-item="7"><span>7</span><strong>Complete</strong></button>
            </div>
            <div class="wi-top-actions">
                <a href="?lang=en" title="English">EN</a>
                <button type="button" class="wi-icon-btn" id="wiRegenerateAll" title="Regenerate security values"><i class="fa fa-refresh"></i></button>
            </div>
        </header>

        <div class="wi-content-grid">
            <section class="wi-card wi-card-main">
                <form id="wiInstallerForm" autocomplete="off">
                    <section class="wi-step-panel is-active" data-step="1">
                        <div class="wi-card-heading">
                            <div class="wi-heading-icon"><i class="fa fa-shield"></i></div>
                            <div>
                                <h2>Welcome to WICMS Installer v3</h2>
                                <p>A cleaner, safer installer for WICMS, ECMA, Showcase, WILabs, and future WI systems.</p>
                            </div>
                        </div>
                        <div class="wi-intro-grid">
                            <article><i class="fa fa-lock"></i><h3>Secure by default</h3><p>Configurable salt, hashing, sessions, cookies, and install lock protection.</p></article>
                            <article><i class="fa fa-database"></i><h3>Database ready</h3><p>Tests the connection and creates the database when the user has permission.</p></article>
                            <article><i class="fa fa-cogs"></i><h3>Configurable</h3><p>Set site details, admin login, environment mode, and optional module choices.</p></article>
                        </div>
                        <div class="wi-actions wi-actions-end">
                            <button type="button" class="wi-btn wi-btn-primary" data-next="2">Start installation <i class="fa fa-arrow-right"></i></button>
                        </div>
                    </section>

                    <section class="wi-step-panel" data-step="2">
                        <div class="wi-card-heading">
                            <div class="wi-heading-icon"><i class="fa fa-check-square-o"></i></div>
                            <div>
                                <h2>System Requirements</h2>
                                <p>Check that PHP, PDO, MySQL, cURL, and write access are ready.</p>
                            </div>
                        </div>
                        <div id="requirementsAlert" class="wi-alert wi-alert-warning wi-hidden"></div>
                        <ul class="wi-requirements" id="requirementsList">
                            <li id="requirements-php-version"><span>PHP version</span><em>Waiting</em></li>
                            <li id="requirements-pdo"><span>PDO extension</span><em>Waiting</em></li>
                            <li id="requirements-mysql"><span>PDO MySQL extension</span><em>Waiting</em></li>
                            <li id="requirements-curl"><span>cURL extension</span><em>Waiting</em></li>
                            <li id="requirements-write"><span>WICMS folder writable</span><em>Waiting</em></li>
                        </ul>
                        <div class="wi-actions">
                            <button type="button" class="wi-btn wi-btn-muted" data-prev="1"><i class="fa fa-arrow-left"></i> Previous</button>
                            <button type="button" class="wi-btn wi-btn-ghost" id="runRequirements"><i class="fa fa-refresh"></i> Recheck</button>
                            <button type="button" class="wi-btn wi-btn-primary" id="requirementsNext" data-next="3" disabled>Next <i class="fa fa-arrow-right"></i></button>
                        </div>
                    </section>

                    <section class="wi-step-panel" data-step="3">
                        <div class="wi-card-heading">
                            <div class="wi-heading-icon"><i class="fa fa-database"></i></div>
                            <div>
                                <h2>Database Configuration</h2>
                                <p>Enter the database details for this WICMS installation.</p>
                            </div>
                        </div>
                        <div class="wi-form-grid two">
                            <label>Database Type<select id="db_type"><option value="mysql">MySQL / MariaDB</option></select></label>
                            <label>Database Host<input type="text" id="host" value="localhost" placeholder="localhost"></label>
                            <label>Database Port<input type="text" id="db_port" value="3306" placeholder="3306"></label>
                            <label>Database Name<input type="text" id="db_name" value="ecma" placeholder="ecma"></label>
                            <label>Database User<input type="text" id="username" value="root" placeholder="root"></label>
                            <label>Database Password<span class="wi-password-wrap"><input type="password" id="password" value=""><button type="button" class="wi-field-icon" data-toggle-password="#password"><i class="fa fa-eye"></i></button></span></label>
                        </div>
                        <details class="wi-details">
                            <summary><i class="fa fa-sliders"></i> Advanced database options</summary>
                            <div class="wi-form-grid two">
                                <label>Table Prefix<input type="text" id="table_prefix" value="wi_" placeholder="wi_"></label>
                                <label>SQL Mode<select id="sql_mode"><option value="recommended">Recommended</option><option value="strict">Strict</option><option value="legacy">Legacy compatibility</option></select></label>
                            </div>
                            <p class="wi-note">Table prefix and SQL mode are kept ready in the installer UI. The current WICMS install backend still creates the standard <code>wi_</code> core tables.</p>
                        </details>
                        <div id="dbMessage" class="wi-alert wi-hidden"></div>
                        <div class="wi-actions">
                            <button type="button" class="wi-btn wi-btn-muted" data-prev="2"><i class="fa fa-arrow-left"></i> Previous</button>
                            <button type="button" class="wi-btn wi-btn-primary" id="testDatabase"><i class="fa fa-plug"></i> Test database & continue</button>
                        </div>
                    </section>

                    <section class="wi-step-panel" data-step="4">
                        <div class="wi-card-heading">
                            <div class="wi-heading-icon"><i class="fa fa-pencil"></i></div>
                            <div>
                                <h2>Site & Admin Setup</h2>
                                <p>Set the site identity and first administrator account.</p>
                            </div>
                        </div>
                        <div class="wi-form-grid two">
                            <label>Website Name<input type="text" id="website_name" value="WICMS" placeholder="WICMS"></label>
                            <label>Website Domain<input type="text" id="domain" value="<?php echo htmlspecialchars($host, ENT_QUOTES, 'UTF-8'); ?>"></label>
                            <label>Site URL<input type="text" id="script" value="<?php echo htmlspecialchars($detectedSiteUrl, ENT_QUOTES, 'UTF-8'); ?>"></label>
                            <label>Timezone<select id="timezone"><option value="Europe/London">Europe/London</option><option value="Europe/Amsterdam">Europe/Amsterdam</option><option value="UTC">UTC</option></select></label>
                            <label>Language<select id="default_language"><option value="en">English</option></select></label>
                            <label>Bootstrap Version<input type="text" id="bootstrap_version" value="1"></label>
                            <label>Admin Username<input type="text" id="admin_username" value="admin"></label>
                            <label>Admin Email<input type="email" id="email_address" value="admin@example.com"></label>
                            <label class="wide">Admin Password<span class="wi-password-wrap"><input type="password" id="admin_password" value=""><button type="button" class="wi-field-icon" data-toggle-password="#admin_password"><i class="fa fa-eye"></i></button></span></label>
                        </div>
                        <div class="wi-actions">
                            <button type="button" class="wi-btn wi-btn-muted" data-prev="3"><i class="fa fa-arrow-left"></i> Previous</button>
                            <button type="button" class="wi-btn wi-btn-primary" data-next="5">Next <i class="fa fa-arrow-right"></i></button>
                        </div>
                    </section>

                    <section class="wi-step-panel" data-step="5">
                        <div class="wi-card-heading">
                            <div class="wi-heading-icon"><i class="fa fa-shield"></i></div>
                            <div>
                                <h2>Security Configuration</h2>
                                <p>Configure security, sessions, and environment settings before install.</p>
                            </div>
                        </div>
                        <div class="wi-form-grid two">
                            <label>Password Hash Algorithm<select id="encryption"><option value="bcrypt">bcrypt recommended</option><option value="sha512">sha512 legacy</option></select></label>
                            <label>Bcrypt Cost<select id="bcrypt_cost"><option>10</option><option selected>12</option><option>13</option><option>14</option></select></label>
                            <label class="wi-hidden">SHA512 Iterations<select id="costing_sha512"><option>35000</option></select></label>
                            <label class="wide">Password Salt<span class="wi-password-wrap"><input type="text" id="salt" value="<?php echo htmlspecialchars($defaultSalt, ENT_QUOTES, 'UTF-8'); ?>"><button type="button" class="wi-field-icon" data-random-target="#salt" data-random-bytes="24"><i class="fa fa-refresh"></i></button></span></label>
                            <label class="wide">Security Key<span class="wi-password-wrap"><input type="text" id="security_key" value="<?php echo htmlspecialchars($defaultSecurityKey, ENT_QUOTES, 'UTF-8'); ?>"><button type="button" class="wi-field-icon" data-random-target="#security_key" data-random-bytes="32"><i class="fa fa-refresh"></i></button></span></label>
                            <label>Cookie Prefix<input type="text" id="cookie_prefix" value="<?php echo htmlspecialchars($defaultCookiePrefix, ENT_QUOTES, 'UTF-8'); ?>"></label>
                            <label>Session Prefix<input type="text" id="session_prefix" value="wi_session_"></label>
                            <label>Environment Mode<select id="environment_mode"><option value="local">Local</option><option value="staging" selected>Staging</option><option value="production">Production</option></select></label>
                            <label>Session Timeout<input type="number" id="session_timeout" value="60" min="5"> <small>minutes</small></label>
                            <label>Max Login Attempts<input type="number" id="max_login_attempts" value="5" min="1"></label>
                            <label>Redirect After Login<input type="text" id="redirect_after_login" value="WIMembers/profile.php"></label>
                        </div>
                        <div class="wi-switch-grid">
                            <label class="wi-switch-row"><input type="checkbox" id="secure_session"><span></span><strong>Force HTTPS session</strong><em>Enable on staging/production with SSL.</em></label>
                            <label class="wi-switch-row"><input type="checkbox" id="session_http_only" checked><span></span><strong>HTTP-only cookies</strong><em>Reduce JavaScript cookie exposure.</em></label>
                            <label class="wi-switch-row"><input type="checkbox" id="session_regenerate" checked><span></span><strong>Regenerate session ID</strong><em>Recommended after login.</em></label>
                            <label class="wi-switch-row"><input type="checkbox" id="cookieonly" checked><span></span><strong>Use only cookies</strong><em>Disable URL-based session IDs.</em></label>
                            <label class="wi-switch-row"><input type="checkbox" id="login_fingerprint" checked><span></span><strong>Login fingerprint</strong><em>Extra login session protection.</em></label>
                            <label class="wi-switch-row"><input type="checkbox" id="allow_demo_data"><span></span><strong>Allow demo data</strong><em>Useful for local testing only.</em></label>
                        </div>
                        <input type="hidden" id="mailer" value="mail">
                        <div class="wi-module-grid">
                            <h3>Optional Modules</h3>
                            <label><input type="checkbox" checked> WIMembers</label>
                            <label><input type="checkbox"> Marketplace</label>
                            <label><input type="checkbox" checked> Bug Reporter</label>
                            <label><input type="checkbox"> WIForum</label>
                            <label><input type="checkbox"> WITickets</label>
                            <label><input type="checkbox"> API / Webhooks</label>
                            <label><input type="checkbox" checked> Multilingual</label>
                        </div>
                        <p class="wi-note">The salt, hash, session and login settings are passed to the current WICMS install backend. Extra module choices are UI-ready for the next backend wiring pass.</p>
                        <div class="wi-actions">
                            <button type="button" class="wi-btn wi-btn-muted" data-prev="4"><i class="fa fa-arrow-left"></i> Previous</button>
                            <button type="button" class="wi-btn wi-btn-primary" data-next="6">Next <i class="fa fa-arrow-right"></i></button>
                        </div>
                    </section>

                    <section class="wi-step-panel" data-step="6">
                        <div class="wi-card-heading">
                            <div class="wi-heading-icon"><i class="fa fa-download"></i></div>
                            <div>
                                <h2>Ready to Install</h2>
                                <p>Review the summary, then run the installer.</p>
                            </div>
                        </div>
                        <div class="wi-summary" id="installSummary"></div>
                        <div class="wi-install-timeline" id="installTimeline" aria-live="polite">
                            <div class="wi-install-stage" data-install-stage="validate"><i class="fa fa-circle-o"></i><div><strong>Validate install settings</strong><span>Checking required fields and security options.</span></div></div>
                            <div class="wi-install-stage" data-install-stage="database"><i class="fa fa-circle-o"></i><div><strong>Prepare database</strong><span>Connecting, creating database if needed, and selecting schema.</span></div></div>
                            <div class="wi-install-stage" data-install-stage="schema"><i class="fa fa-circle-o"></i><div><strong>Install WICMS core modules</strong><span>Creating core tables, admin tools, menu rows, pages, members, language records, and plugin shell.</span></div></div>
                            <div class="wi-install-stage" data-install-stage="settings"><i class="fa fa-circle-o"></i><div><strong>Save site/security modules</strong><span>Saving site URL, salt, hashing, sessions, cookies, login rules, and optional module choices.</span></div></div>
                            <div class="wi-install-stage" data-install-stage="config"><i class="fa fa-circle-o"></i><div><strong>Write config files</strong><span>Generating WIConfig.php and .env without loading the app too early.</span></div></div>
                            <div class="wi-install-stage" data-install-stage="admin"><i class="fa fa-circle-o"></i><div><strong>Create owner account</strong><span>Hashing the admin password and creating the first admin user.</span></div></div>
                            <div class="wi-install-stage" data-install-stage="lock"><i class="fa fa-circle-o"></i><div><strong>Lock installer</strong><span>Creating install.lock so the installer cannot rerun casually.</span></div></div>
                        </div>
                        <div id="results_install" class="wi-alert wi-hidden"></div>
                        <div class="wi-actions">
                            <button type="button" class="wi-btn wi-btn-muted" data-prev="5"><i class="fa fa-arrow-left"></i> Previous</button>
                            <button type="button" class="wi-btn wi-btn-primary" id="runInstall"><span id="install"><i class="fa fa-play"></i> Install WICMS</span><span id="installing" class="wi-hidden"><i class="fa fa-circle-o-notch fa-spin"></i> Installing</span></button>
                        </div>
                    </section>

                    <section class="wi-step-panel" data-step="7">
                        <div class="wi-complete">
                            <div class="wi-complete-icon"><i class="fa fa-check"></i></div>
                            <h2>Installation complete</h2>
                            <p>WICMS has been installed and protected with the generated configuration.</p>
                            <a class="wi-btn wi-btn-primary" href="../alogin.php"><i class="fa fa-sign-in"></i> Go to admin login</a>
                        </div>
                    </section>
                </form>
            </section>

            <aside class="wi-right-rail">
                <section class="wi-info-card wi-progress-card">
                    <h3><i class="fa fa-tasks"></i> Installation Progress</h3>
                    <div class="wi-progress-ring"><span id="progressFraction">1/7</span><small>Steps</small></div>
                    <div class="wi-progress-bar"><span id="progressBar" style="width:14%"></span></div>
                    <p id="progressText">Welcome — start the guided installation.</p>
                </section>
                <section class="wi-info-card">
                    <h3><i class="fa fa-info-circle"></i> System Information</h3>
                    <dl>
                        <dt>WICMS Version</dt><dd>2.5 installer</dd>
                        <dt>PHP Version</dt><dd><?php echo htmlspecialchars(PHP_VERSION, ENT_QUOTES, 'UTF-8'); ?></dd>
                        <dt>Database</dt><dd>MySQL / MariaDB</dd>
                        <dt>Environment</dt><dd id="railEnvironment">Staging</dd>
                    </dl>
                </section>
                <section class="wi-info-card wi-security-card">
                    <h3><i class="fa fa-lock"></i> Security Notes</h3>
                    <p>The password salt is critical. You can regenerate it here during install, but do not change it casually after users exist.</p>
                    <p><strong>Local:</strong> easier testing. <strong>Production:</strong> stricter HTTPS/cookie choices.</p>
                </section>
            </aside>
        </div>

        <footer class="wi-footer"><i class="fa fa-shield"></i> Safe. Secure. Simple.</footer>
    </main>
</div>
<?php endif; ?>
<script type="text/javascript" src="<?php echo htmlspecialchars($installAssetBase, ENT_QUOTES, 'UTF-8'); ?>/WICore/WIJ/WICore.js?v=<?php echo $assetVersion; ?>"></script>
<script type="text/javascript" src="<?php echo htmlspecialchars($installAssetBase, ENT_QUOTES, 'UTF-8'); ?>/WICore/WIJ/sha512.js?v=<?php echo $assetVersion; ?>"></script>
<script type="text/javascript" src="<?php echo htmlspecialchars($installAssetBase, ENT_QUOTES, 'UTF-8'); ?>/WICore/WIJ/WIInstall.js?v=<?php echo $assetVersion; ?>"></script>
<script type="text/javascript">
(function(){
    function qsa(sel){ return Array.prototype.slice.call(document.querySelectorAll(sel)); }
    function go(step){
        var n = parseInt(step, 10);
        if (!n || window.jQuery) { return; }
        var shell = document.querySelector('.wi-installer-shell');
        if (shell) { shell.setAttribute('data-current-step', n); }
        qsa('.wi-step-panel').forEach(function(panel){ panel.classList.toggle('is-active', panel.getAttribute('data-step') == String(n)); });
        qsa('[data-step-item]').forEach(function(item){
            var sn = parseInt(item.getAttribute('data-step-item'), 10);
            item.classList.toggle('is-active', sn === n);
            item.classList.toggle('is-complete', sn < n);
        });
        var frac = document.getElementById('progressFraction'); if (frac) { frac.textContent = n + '/7'; }
        var bar = document.getElementById('progressBar'); if (bar) { bar.style.width = Math.round((n / 7) * 100) + '%'; }
    }
    document.addEventListener('click', function(e){
        var next = e.target.closest && e.target.closest('[data-next]');
        var prev = e.target.closest && e.target.closest('[data-prev]');
        if (next && !window.jQuery) { e.preventDefault(); go(next.getAttribute('data-next')); }
        if (prev && !window.jQuery) { e.preventDefault(); go(prev.getAttribute('data-prev')); }
    });
})();
</script>

</body>
</html>
