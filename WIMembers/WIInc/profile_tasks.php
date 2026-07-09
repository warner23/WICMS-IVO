<?php
/**
 * File Information
 * Written By: Warner Infinity
 * Company: Warner Infinity
 * Product: WIProfile / WITaskEngine
 * Project: WI Ecosystem
 * File: profile_tasks.php
 * Location: /WIMembers/WIInc/profile_tasks.php
 * Type: Include / Widget
 * Layer: Member workspace UI
 * Purpose Area: Worker/member task dashboard powered by WITaskEngine.
 * Version: 1.4.0-bridge-batch-4
 * Created: 2026-06-03
 * Last Updated: 2026-06-03
 * Status: Production-ready bridge batch 4
 */

declare(strict_types=1);

$csrfToken = class_exists('WICsrf', false) ? WICsrf::getToken() : '';
?>
<link rel="stylesheet" href="WICore/WICSS/WIProfileTasks.css?v=014">
<section class="wi-profile-task-panel" data-wi-profile-tasks data-ajax-endpoint="WICore/WIAjax/WIMembersAjax.php" data-csrf-token="<?= htmlspecialchars((string)$csrfToken, ENT_QUOTES, 'UTF-8') ?>">
    <div class="wi-profile-task-head">
        <div>
            <h2>My Tasks</h2>
            <p>Your assigned work, proof requests, overdue actions and follow-ups from WITaskEngine.</p>
        </div>
        <div class="wi-profile-task-tools">
            <select name="profile_task_status" data-profile-task-filter>
                <option value="all">All statuses</option>
                <option value="open">Open</option>
                <option value="in_progress">In progress</option>
                <option value="blocked">Blocked</option>
                <option value="completed">Completed</option>
            </select>
            <select name="profile_task_priority" data-profile-task-filter>
                <option value="all">All priorities</option>
                <option value="critical">Critical</option>
                <option value="high">High</option>
                <option value="normal">Normal</option>
                <option value="low">Low</option>
            </select>
            <button type="button" data-profile-task-action="refresh">Refresh</button>
        </div>
    </div>
    <div class="wi-profile-task-summary" data-profile-task-summary></div>
    <div class="wi-profile-task-list" data-profile-task-list>
        <div class="wi-profile-task-empty">Loading tasks…</div>
    </div>
</section>
<script src="WICore/WIJ/WIProfileTasks.js?v=014" defer></script>
