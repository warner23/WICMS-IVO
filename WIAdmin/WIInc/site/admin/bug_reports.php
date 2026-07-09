<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS / WI Shared Core
| Project: WI Ecosystem
| File: bug_reports.php
| Location: /WIAdmin/WIInc/site/admin/
| Type: Admin View
| Layer: UI Only
| Purpose Area: Admin bug report list and thread view
| Version: 1.1.0
| Created: 2026-04-16
| Last Updated: 2026-06-22
| Status: Active
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
| Admin-side bug report workspace. Rendering is UI-only; report loading,
| replies and status changes are handled by WIBugReporter through WIAjax.
*/

if (!defined('WI_ADMIN')) {
    exit;
}
?>

<div class="wi-bug-admin">
    <div class="wi-bug-admin-left">
        <div class="wi-bug-admin-head">
            <h3>Bug Reports</h3>
            <p>Local reports submitted by users, workers, members and admins.</p>
        </div>

        <div class="wi-bug-filters">
            <label for="wiBugStatusFilter">Status</label>
            <select id="wiBugStatusFilter">
                <option value="">All</option>
                <option value="open">Open</option>
                <option value="in_progress">In Progress</option>
                <option value="resolved">Resolved</option>
                <option value="closed">Closed</option>
            </select>
        </div>

        <div id="wiBugList"></div>
    </div>

    <div class="wi-bug-admin-right">
        <div id="wiBugThread">
            <p class="wi-bug-admin-empty">Select a report to view the thread.</p>
        </div>
    </div>
</div>
