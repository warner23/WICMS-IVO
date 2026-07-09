<?php
declare(strict_types=1);

/**
 * FILE:
 * wikichencompli/WIAdmin/WIInc/WI_header.php
 *
 * PURPOSE:
 * Admin header partial
 *
 * NOTES:
 * - PHP 8.2 safe
 * - No styling changes
 * - No structural/div changes
 * - Adds guards for missing globals/classes/constants
 */

$userId = 0;
if (class_exists('WISession') && method_exists('WISession', 'get')) {
    $userId = (int) WISession::get('user_id', 0);
}

$userName = '';
$userPic  = '';

if (isset($Info) && $Info !== null) {
    if (method_exists($Info, 'admin_name')) {
        $userName = (string) $Info->admin_name($userId);
    }

    if (method_exists($Info, 'admin_pic')) {
        $userPic = (string) $Info->admin_pic($userId);
    }
}

if ($userName === '') {
    $userName = 'Admin Panel';
}

$siteName = defined('WEBSITE_NAME') ? (string) WEBSITE_NAME : 'Admin Panel';

$notificationsCount = 0;
$tasksCount = 0;

if (isset($site) && $site !== null) {
    if (method_exists($site, 'notifications_badge')) {
        $notificationsCount = (int) $site->notifications_badge();
    }

    if (method_exists($site, 'TaskBagde')) {
        $tasksCount = (int) $site->TaskBagde();
    }
}
?>

<link rel="stylesheet" href="WIInc/css/font-awesome.css">
<link rel="stylesheet" href="WIInc/css/admin-core.css">

<header class="wi-topbar" role="banner">
    <div class="wi-topbar__inner">

        <div class="wi-topbar__left">
            <a href="dashboard.php" class="wi-topbar__brand" title="<?php echo htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8'); ?> Admin Panel">
                <span class="wi-topbar__brand-icon"><i class="fa fa-cog"></i></span>
                <span class="wi-topbar__brand-text"><?php echo htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8'); ?></span>
            </a>

            <a href="../index.php" class="wi-topbar__visit-site" title="Visit Site">
                <i class="fa fa-home"></i>
                <span>Visit Site</span>
            </a>
        </div>

        <div class="wi-topbar__center">
            <nav class="wi-topbar__nav" aria-label="Admin navigation">
                <?php
                if (isset($web) && $web !== null && method_exists($web, 'AdminMenu')) {
                    $web->AdminMenu();
                }
                ?>
            </nav>
        </div>

        <div class="wi-topbar__right">
            <div class="wi-topbar__actions">

                <div class="dropdown wi-topbar__dropdown wi-topbar__dropdown--notifications">
                    <a href="#" class="wi-topbar__icon-btn dropdown-toggle" data-toggle="dropdown" onclick="if (typeof WIDashboard !== 'undefined' && typeof WIDashboard.Notifications === 'function') { WIDashboard.Notifications(); } return false;" aria-label="Notifications">
                        <i class="fa fa-bell-o"></i>
                        <span class="wi-topbar__badge" id="not_badge"></span>
                    </a>

                    <div class="dropdown-menu wi-topbar__menu">
                        <div class="wi-topbar__menu-card">
                            <div class="wi-topbar__menu-head">
                                You have <?php echo $notificationsCount; ?> notifications
                            </div>
                            <div class="wi-topbar__menu-body" id="Notifications"></div>
                            <div class="wi-topbar__menu-foot">
                                <a href="WINotifications.php">View all</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dropdown wi-topbar__dropdown wi-topbar__dropdown--messages">
                    <a href="#" class="wi-topbar__icon-btn dropdown-toggle" data-toggle="dropdown" onclick="if (typeof WIDashboard !== 'undefined' && typeof WIDashboard.messages === 'function') { WIDashboard.messages(); } return false;" aria-label="Messages">
                        <i class="fa fa-envelope-o"></i>
                        <span class="wi-topbar__badge" id="mess_badge"></span>
                    </a>

                    <div class="dropdown-menu wi-topbar__menu">
                        <div class="wi-topbar__menu-card">
                            <div class="wi-topbar__menu-head">Messages</div>
                            <div class="wi-topbar__menu-body" id="cmessages"></div>
                            <div class="wi-topbar__menu-foot" id="e_msg">
                                <a href="WIMailbox.php">See All Messages</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dropdown wi-topbar__dropdown wi-topbar__dropdown--tasks">
                    <a href="#" class="wi-topbar__icon-btn dropdown-toggle" data-toggle="dropdown" onclick="if (typeof WIDashboard !== 'undefined' && typeof WIDashboard.tasks === 'function') { WIDashboard.tasks(); } return false;" aria-label="Tasks">
                        <i class="fa fa-flag-o"></i>
                        <span class="wi-topbar__badge wi-topbar__badge--danger" id="task_badge"></span>
                    </a>

                    <div class="dropdown-menu wi-topbar__menu">
                        <div class="wi-topbar__menu-card">
                            <div class="wi-topbar__menu-head">
                                Tasks: You have <?php echo $tasksCount; ?> tasks
                            </div>
                            <div class="wi-topbar__menu-body" id="tasks"></div>
                            <div class="wi-topbar__menu-foot" id="e_tasks">
                                <a href="WITasks.php">View all tasks</a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="dropdown wi-topbar__user">
                <a href="#" class="wi-topbar__user-toggle dropdown-toggle" data-toggle="dropdown" aria-label="User menu">
                    <span class="wi-topbar__user-avatar">
                        <?php echo $userPic; ?>
                    </span>
                    <span class="wi-topbar__user-meta">
                        <span class="wi-topbar__user-name"><?php echo htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?></span>
                        <span class="wi-topbar__user-role">Administrator</span>
                    </span>
                    <span class="wi-topbar__user-caret"><i class="fa fa-chevron-down"></i></span>
                </a>

                <ul class="dropdown-menu wi-topbar__user-menu">
                    <li class="wi-topbar__user-menu-header">
                        <div class="wi-topbar__user-menu-avatar">
                            <?php echo $userPic; ?>
                        </div>
                        <div class="wi-topbar__user-menu-text">
                            <strong><?php echo htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?></strong>
                            <span>Manage your account</span>
                        </div>
                    </li>

                    <li>
                        <a href="../WIMembers/profile.php">
                            <i class="fa fa-user"></i> Profile
                        </a>
                    </li>

                    <li>
                        <a href="logout.php">
                            <i class="fa fa-sign-out"></i> Sign out
                        </a>
                    </li>
                </ul>
            </div>

            <a href="#" class="wi-topbar__icon-btn wi-topbar__settings-btn" data-toggle="control-sidebar" aria-label="Control sidebar">
                <i class="fa fa-gears"></i>
            </a>
        </div>
    </div>
</header>

<script type="text/javascript" src="WICore/WIJ/WIDashboard.js"></script>

<?php
/* Site-wide WICMS admin helpers: fixed-position tools such as WIBugReporter. */
if (!defined('WI_ADMIN_PAGE_TOOLS_RENDERED')) {
    define('WI_ADMIN_PAGE_TOOLS_RENDERED', true);
    $wiAdminPageTools = __DIR__ . '/../WICore/WIClass/WIAdminPageTools.php';
    if (is_file($wiAdminPageTools)) {
        require_once $wiAdminPageTools;
        if (class_exists('WIAdminPageTools')) {
            WIAdminPageTools::render([
                'area_type' => 'admin',
                'module_name' => 'WIAdmin',
                'ajax_url' => 'WICore/WIClass/WIAjax.php',
            ]);
        }
    }
}
?>
